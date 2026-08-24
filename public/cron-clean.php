<?php

/**
 * Script autônomo para limpar jobs travados na fila da Central de Trabalho via Web / Cron / URL direta.
 * Altera status 'processing' de volta para 'pending' e limpa bloqueios (locks).
 * Local: /public/cron-clean.php
 */

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

require FCPATH . '../app/Config/Paths.php';

$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

// Inicialização segura usando a extensão do Boot
class CronCleanBoot extends CodeIgniter\Boot
{
    public static function init(Config\Paths $paths): void
    {
        static::definePathConstants($paths);
        if (! defined('APP_NAMESPACE')) {
            static::loadConstants();
        }
        static::checkMissingExtensions();
        static::loadDotEnv($paths);
        static::defineEnvironment();
        static::loadEnvironmentBootstrap($paths);
        static::loadCommonFunctions();
        static::loadAutoloader();
        static::setExceptionHandler();
        static::initializeKint();
        static::autoloadHelpers();
        static::initializeCodeIgniter();
    }
}

CronCleanBoot::init($paths);

header('Content-Type: application/json; charset=utf-8');

$envToken = env('app.cronToken') ?: env('app_cronToken') ?: 'dias_imports_cron_secret_2026';
$providedToken = (string) ($_GET['token'] ?? $_POST['token'] ?? ($_SERVER['HTTP_X_CRON_TOKEN'] ?? ''));

if ($providedToken === '' || !hash_equals($envToken, $providedToken)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Acesso negado: token inválido ou ausente.',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $db = \Config\Database::connect();
    $db->reconnect();
    $builder = $db->table('system_job_queue');

    // Identifica quantos estavam como 'processing'
    $countProcessing = $builder->where('status', 'processing')->countAllResults(false);

    $updated = 0;
    if ($countProcessing > 0) {
        $builder->where('status', 'processing')->update([
            'status'        => 'pending',
            'error_message' => null,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $updated = $countProcessing;
    }

    // Libera qualquer lock de execução no cache
    $cache = \Config\Services::cache();
    $cache->delete('job_center_running_lock');

    echo json_encode([
        'success'  => true,
        'message'  => "Limpeza concluída com sucesso. {$updated} tarefa(s) em execução foram redefinidas para pendente.",
        'unlocked' => $updated,
        'timestamp'=> date('Y-m-d H:i:s'),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao redefinir tarefas: ' . $e->getMessage(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
