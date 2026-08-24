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
$stopSignalFile = $realtimeDir . DIRECTORY_SEPARATOR . 'stop.signal';
$logFile       = $realtimeDir . DIRECTORY_SEPARATOR . 'realtime.log';

// Remove sinal de parada anterior ao iniciar nova execução
if (file_exists($stopSignalFile)) {
    @unlink($stopSignalFile);
}

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

$db = null;

try {
    // 5. Bootstrap do CodeIgniter 4 antes de emitir headers para evitar conflito com session.ini
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

    // Carrega o intervalo configurado de sleep (ou fallback para 5s)
    try {
        $settingRow = $db->table('app_settings')->where('setting_key', 'realtime_sleep_seconds')->get()->getRow();
        if ($settingRow && !empty($settingRow->setting_value)) {
            $configuredSleep = (int) $settingRow->setting_value;
            if ($configuredSleep > 0) {
                $intervalSeconds = $configuredSleep;
            }
        }
    } catch (\Throwable) {}

    // 8. Inicializa instâncias de serviços necessários
    $jobCenterService = new \App\Services\JobCenterService();
    $snapshotService  = new \App\Services\RealtimeSnapshotService();

    echo "[" . date('H:i:s') . "] Conexão única MySQL estabelecida. Iniciando ciclo contínuo de {$intervalSeconds}s (sleep configurado)...\n";
    echo str_repeat('-', 60) . "\n";
    flush();

    // 9. Loop de processamento Realtime
    $cycle = 0;
    $totalProcessed = 0;
    $totalFailed = 0;

    while ((microtime(true) - $startedAt) + $intervalSeconds <= $maxRuntimeSeconds) {
        // Verifica se houve solicitação manual de parada
        if (file_exists($stopSignalFile)) {
            @unlink($stopSignalFile);
            echo "\n[" . date('H:i:s') . "] 🛑 Sinal de parada manual recebido. Encerrando worker realtime...\n";
            flush();
            break;
        }

        $cycle++;
        $cycleStart = microtime(true);

        try {
            // 1. Processa a fila de tarefas em background
            $results = $jobCenterService->processPendingQueue(10);
            $procCount = ($results['processed'] ?? 0);
            $failCount = ($results['failed'] ?? 0);
            $totalProcessed += $procCount;
            $totalFailed += $failCount;

            // 2. Atualiza snapshots de dados das telas ativas em tempo real
            $snapshotResults = $snapshotService->generateAllSnapshots();
            $snapshotCount = is_array($snapshotResults) ? count($snapshotResults) : $snapshotResults;

            // 3. Atualiza arquivo de status de saúde para o dashboard
            $snapshotService->updateWorkerStatus([
                'cycle' => $cycle,
                'last_error' => null,
                'jobs_processed' => $totalProcessed,
                'jobs_failed' => $totalFailed,
                'uptime_seconds' => round(microtime(true) - $startedAt),
            ]);

            // Atualiza o heartbeat após processamento bem-sucedido
            @touch($heartbeatFile);

            $cycleDuration = round((microtime(true) - $cycleStart) * 1000, 1);
            $uptime = round(microtime(true) - $startedAt);

            echo "[" . date('H:i:s') . "] Ciclo #{$cycle} ({$cycleDuration}ms | uptime {$uptime}s): "
                . "Tarefas: {$procCount} proc / {$failCount} falhas | "
                . "Snapshots gerados: {$snapshotCount} telas (OK)\n";
            flush();
        } catch (\Throwable $cycleException) {
            $writeLog('Erro no ciclo de processamento: ' . $cycleException->getMessage());
            echo "[" . date('H:i:s') . "] ❌ ERRO no ciclo #{$cycle}: " . $cycleException->getMessage() . "\n";
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

    echo "\n" . str_repeat('=', 60) . "\n";
    echo "[" . date('H:i:s') . "] Worker finalizado com sucesso após " . round(microtime(true) - $startedAt) . "s de execução ({$cycle} ciclos).\n";
    echo "Total de tarefas processadas: {$totalProcessed} | Total de falhas: {$totalFailed}\n";
    echo str_repeat('=', 60) . "\n";
    flush();
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
