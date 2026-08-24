<?php

/**
 * Worker autônomo de Realtime para execução via URL Web / Cron Job / CLI.
 * Mantém uma única conexão MySQL ativa, reutilizando-a a cada ciclo para
 * processamento das filas/rotinas sem sobrecarregar o limite de conexões por hora.
 *
 * Local: /dias_imports_v2/public/cron-realtime.php
 */

// 1. Define o FCPATH e ajusta o diretório de trabalho
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// 2. Garante diretório de runtime em writable/realtime
$writablePath = realpath(__DIR__ . '/../writable');
if (! $writablePath) {
    $writablePath = __DIR__ . '/../writable';
}
$realtimeDir = $writablePath . DIRECTORY_SEPARATOR . 'realtime';
if (! is_dir($realtimeDir)) {
    @mkdir($realtimeDir, 0755, true);
}

$lockFile      = $realtimeDir . DIRECTORY_SEPARATOR . 'realtime.lock';
$heartbeatFile = $realtimeDir . DIRECTORY_SEPARATOR . 'heartbeat';
$logFile       = $realtimeDir . DIRECTORY_SEPARATOR . 'realtime.log';

// 3. Proteção de Lock exclusivo com flock() não-bloqueante
$lockHandle = fopen($lockFile, 'c');
if (! $lockHandle || ! flock($lockHandle, LOCK_EX | LOCK_NB)) {
    if (!headers_sent() && PHP_SAPI !== 'cli') {
        header('Content-Type: text/plain; charset=utf-8');
    }
    echo "Worker realtime já em execução ativa. Processo ignorado.\n";
    if ($lockHandle) {
        fclose($lockHandle);
    }
    exit(0);
}

// Função de log de fallback
$writeLog = static function (string $message) use ($logFile) {
    if (function_exists('log_message')) {
        try {
            log_message('error', '[Cron-Realtime] ' . $message);
            return;
        } catch (\Throwable) {
        }
    }
    $entry = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
};

// 4. Parâmetros operacionais e limites de execução
$intervalSeconds   = 5;
$maxRuntimeSeconds = 290;
$startedAt         = microtime(true);

@ini_set('max_execution_time', '300');
@set_time_limit(300);

// Desabilita buffers para permitir streaming de saída em tempo real se chamado via Web
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

if (!headers_sent() && PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('X-Content-Type-Options: nosniff');
}

$db = null;

try {
    // 5. Bootstrap do CodeIgniter 4
    require FCPATH . '../app/Config/Paths.php';
    $paths = new Config\Paths();
    require $paths->systemDirectory . '/Boot.php';

    class CronRealtimeBoot extends CodeIgniter\Boot
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

    CronRealtimeBoot::init($paths);

    // 6. Validação de segurança para chamadas via Web/URL
    $isCli = (PHP_SAPI === 'cli');
    $envToken = env('app.cronToken') ?: env('app_cronToken') ?: 'dias_imports_cron_secret_2026';
    $providedToken = (string) ($_GET['token'] ?? $_POST['token'] ?? ($_SERVER['HTTP_X_CRON_TOKEN'] ?? ''));

    if (!$isCli && ($providedToken === '' || !hash_equals($envToken, $providedToken))) {
        http_response_code(403);
        echo "Acesso negado: token inválido ou ausente.\n";
        exit(1);
    }

    echo "==================================================\n";
    echo " INICIANDO WORKER REALTIME - " . date('Y-m-d H:i:s') . "\n";
    echo "==================================================\n\n";

    // 7. Abre UMA ÚNICA conexão com MySQL antes do loop principal
    $db = \Config\Database::connect();
    $db->initialize();

    // 8. Inicializa instâncias de serviços necessários
    $jobCenterService = new \App\Services\JobCenterService();
    $snapshotService  = new \App\Services\RealtimeSnapshotService();

    // 9. Loop de processamento Realtime
    $cycle = 0;
    $totalProcessed = 0;
    $totalFailed = 0;

    while ((microtime(true) - $startedAt) + $intervalSeconds <= $maxRuntimeSeconds) {
        $cycle++;
        try {
            // 1. Processa a fila de tarefas em background
            $results = $jobCenterService->processPendingQueue(10);
            $totalProcessed += ($results['processed'] ?? 0);
            $totalFailed += ($results['failed'] ?? 0);

            // 2. Atualiza snapshots de dados das telas ativas em tempo real
            $snapshotService->generateAllSnapshots();

            // 3. Atualiza arquivo de status de saúde para o dashboard
            $snapshotService->updateWorkerStatus([
                'cycle' => $cycle,
                'last_error' => null,
                'jobs_processed' => $totalProcessed,
                'jobs_failed' => $totalFailed,
                'uptime_seconds' => round(microtime(true) - $startedAt),
            ]);

            if (!empty($results['processed']) || !empty($results['failed'])) {
                echo "[" . date('H:i:s') . "] Ciclo #{$cycle}: Processados: {$results['processed']} | Falhas: {$results['failed']}\n";
                flush();
            }

            // Atualiza o heartbeat após processamento bem-sucedido
            @touch($heartbeatFile);
        } catch (\Throwable $cycleException) {
            $writeLog('Erro no ciclo de processamento: ' . $cycleException->getMessage());
            echo "[" . date('H:i:s') . "] Erro no ciclo #{$cycle}: " . $cycleException->getMessage() . "\n";
            flush();

            $snapshotService->updateWorkerStatus([
                'cycle' => $cycle,
                'last_error' => $cycleException->getMessage(),
                'jobs_processed' => $totalProcessed,
                'jobs_failed' => $totalFailed,
                'uptime_seconds' => round(microtime(true) - $startedAt),
            ]);
        }

        // Aguarda o intervalo configurado
        sleep($intervalSeconds);
    }

    echo "\n[" . date('H:i:s') . "] Worker finalizado com sucesso após ~" . round(microtime(true) - $startedAt) . "s de execução.\n";
} catch (\Throwable $e) {
    $writeLog('Falha crítica no worker: ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());
    echo "\n[ERRO CRÍTICO] " . $e->getMessage() . "\n";
} finally {
    // 10. Finalização segura e liberação de recursos
    if (isset($db) && $db instanceof \CodeIgniter\Database\BaseConnection) {
        try {
            $db->close();
        } catch (\Throwable) {
        }
    }

    if (isset($lockHandle) && is_resource($lockHandle)) {
        try {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        } catch (\Throwable) {
        }
    }
}

exit(0);
