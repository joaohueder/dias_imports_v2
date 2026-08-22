<?php

/**
 * Runner autônomo para execução de fila da Central de Trabalho via Web / Cron / URL direta.
 * Local: /public/cron-runner.php
 */

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

require FCPATH . '../app/Config/Paths.php';

$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

// Inicialização segura usando a extensão do Boot
class CronRunnerBoot extends CodeIgniter\Boot
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

CronRunnerBoot::init($paths);

// Aumenta o tempo limite de execução para evitar timeout durante o processamento da fila
set_time_limit(300);

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
    $limit = max(1, min(100, (int) ($_GET['limit'] ?? $_POST['limit'] ?? 50)));
    $service = new \App\Services\JobCenterService();
    $results = $service->processPendingQueue($limit);

    if (!empty($results['skipped'])) {
        echo json_encode([
            'success'   => true,
            'skipped'   => true,
            'message'   => $results['message'] ?? 'Já existe uma execução ativa em andamento. Chamada ignorada.',
            'processed' => 0,
            'failed'    => 0,
            'timestamp' => date('Y-m-d H:i:s'),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    echo json_encode([
        'success'   => true,
        'message'   => 'Fila da Central de Trabalho processada com sucesso.',
        'processed' => $results['processed'],
        'failed'    => $results['failed'],
        'timestamp' => date('Y-m-d H:i:s'),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro durante o processamento: ' . $e->getMessage(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
