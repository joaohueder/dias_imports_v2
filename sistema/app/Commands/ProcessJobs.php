<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\JobCenterService;

class ProcessJobs extends BaseCommand
{
    protected $group       = 'Jobs';
    protected $name        = 'jobs:process';
    protected $description = 'Processa a fila de trabalhos pendentes da Central de Trabalho (ideal para Cronjob).';
    protected $usage       = 'jobs:process [options]';
    protected $arguments   = [];
    protected $options     = [
        '-l' => 'Limite de tarefas a processar por ciclo (default: 50)',
    ];

    public function run(array $params)
    {
        $limit = (int) ($params['l'] ?? CLI::getOption('l') ?? 50);
        if ($limit <= 0) {
            $limit = 50;
        }

        CLI::write("=== Central de Trabalho - Motor de Execução ===", 'green');
        CLI::write("Iniciado em: " . date('Y-m-d H:i:s'), 'yellow');

        $service = new JobCenterService();
        $results = $service->processPendingQueue($limit, function (string $msg) {
            CLI::write("[LOG] " . $msg);
        });

        CLI::write("Concluído. Processados: {$results['processed']} | Falhas: {$results['failed']}", 'cyan');
    }
}
