<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function (): void {
    if (ENVIRONMENT !== 'testing') {
        $value = ini_get('zlib.output_compression');

        if (filter_var($value, FILTER_VALIDATE_BOOLEAN) || (int) $value > 0) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();
        // Hot Reload route - responde imediatamente para evitar travamento em single thread PHP server
        service('routes')->get('__hot-reload', static function (): void {
            service('response')
                ->setHeader('Content-Type', 'text/event-stream')
                ->setHeader('Cache-Control', 'no-cache')
                ->setHeader('Connection', 'keep-alive')
                ->setBody("event: ping\ndata: {\"status\":\"ok\"}\n\n")
                ->send();
            exit(0);
        });
    }
});

// Listener global para registrar nova conexão na primeira query de cada requisição/conexão
Events::on('DBQuery', static function (): void {
    static $logged = false;
    if ($logged) {
        return;
    }
    $logged = true;

    try {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $db->simpleQuery("INSERT INTO `db_connection_logs` (`created_at`) VALUES ('{$now}')");

        // Limpeza periódica de conexões com mais de 2 horas (1 em cada 50 conexões)
        if (random_int(1, 50) === 1) {
            $cutoff = date('Y-m-d H:i:s', strtotime('-2 hours'));
            $db->simpleQuery("DELETE FROM `db_connection_logs` WHERE `created_at` < '{$cutoff}'");
        }
    } catch (\Throwable) {
    }
});
