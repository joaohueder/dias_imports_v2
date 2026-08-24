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

// Desabilita o buffer de saída para permitir streaming em tempo real
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

$isCli = (PHP_SAPI === 'cli');
$envToken = env('app.cronToken') ?: env('app_cronToken') ?: 'dias_imports_cron_secret_2026';
$providedToken = (string) ($_GET['token'] ?? $_POST['token'] ?? ($_SERVER['HTTP_X_CRON_TOKEN'] ?? ''));

if (!$isCli && ($providedToken === '' || !hash_equals($envToken, $providedToken))) {
    http_response_code(403);
    echo "Acesso negado: token inválido ou ausente.\n";
    exit;
}

echo "==================================================\n";
echo " INICIANDO CRON RUNNER - " . date('Y-m-d H:i:s') . "\n";
echo "==================================================\n\n";

try {
    // Força conexão nova/ativa com o banco de dados antes de instanciar o serviço
    $db = \Config\Database::connect();
    $db->reconnect();

    $limit = max(1, min(100, (int) ($_GET['limit'] ?? $_POST['limit'] ?? 50)));
    $service = new \App\Services\JobCenterService();
    
    // Passa um callback de logger para imprimir em tempo real
    $results = $service->processPendingQueue($limit, function($msg) {
        echo "[".date('H:i:s')."] " . $msg . "\n";
        flush();
    });

    if (!empty($results['skipped'])) {
        echo "\n" . ($results['message'] ?? 'Já existe uma execução ativa em andamento. Chamada ignorada.') . "\n";
    } else {
        echo "\n==================================================\n";
        echo " RESUMO DA EXECUÇÃO\n";
        echo "==================================================\n";
        echo "Processados com sucesso: " . $results['processed'] . "\n";
        echo "Falhas definitivas: " . $results['failed'] . "\n";
    }

    echo "\n[".date('H:i:s')."] Executando rotina de limpeza (cron-clean)...\n";
    
    // Executa a limpeza de fila travada (cron-clean) internamente
    $db = \Config\Database::connect();
    $db->reconnect();
    $builder = $db->table('system_job_queue');
    $countProcessing = $builder->where('status', 'processing')->countAllResults(false);
    
    if ($countProcessing > 0) {
        $builder->where('status', 'processing')->update([
            'status'        => 'pending',
            'error_message' => null,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        echo "[".date('H:i:s')."] Limpeza concluída: {$countProcessing} tarefa(s) travada(s) redefinida(s) para 'pending'.\n";
    } else {
        echo "[".date('H:i:s')."] Limpeza concluída: Nenhuma tarefa travada encontrada.\n";
    }
    
    $cache = \Config\Services::cache();
    $cache->delete('job_center_running_lock');
    
    echo "\n==================================================\n";
    echo " CRON RUNNER FINALIZADO - " . date('Y-m-d H:i:s') . "\n";
    echo "==================================================\n";

} catch (\Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo "\n[ERRO FATAL] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
