<?php

if (! function_exists('get_footer_telemetry')) {
    /**
     * Retorna o HTML da telemetria do rodapé e os dados brutos.
     *
     * @return array
     */
    function get_footer_telemetry(): array
    {
        $db = \Config\Database::connect();
        
        $connLogModel = new \App\Models\DbConnectionLogModel();

        // Total de conexões na última hora (60 minutos)
        $connectionsLastHour = $connLogModel->getConnectionsLastHour();
        $maxConnectionsPerHour = 500;

        $connColor = 'rgb(var(--success))';
        $connClass = '';
        if ($connectionsLastHour > 450) {
            $connColor = 'rgb(var(--danger))';
            $connClass = 'blink-warning';
        } elseif ($connectionsLastHour >= 301) {
            $connColor = 'rgb(var(--warning))';
        }

        $dbHost = config('Database')->default['hostname'] ?? env('database.default.hostname', 'localhost');
        $loadTime = number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4);
        
        // Obter estatísticas da fila de jobs
        $queueModel = new \App\Models\SystemJobQueueModel();
        $queueStats = $queueModel->getQueueStats();
        
        $pending = $queueStats['pending'] ?? 0;
        $processing = $queueStats['processing'] ?? 0;
        $failed = $queueStats['failed'] ?? 0;
        
        $queueHtml = " | Fila: <span style=\"color: rgb(var(--warning));\" title=\"Tarefas pendentes\">{$pending}</span> / <span style=\"color: rgb(var(--primary));\" title=\"Tarefas em execução\">{$processing}</span> / <span style=\"color: rgb(var(--danger));\" title=\"Tarefas com falha\">{$failed}</span>";

        $footerHtml = "Banco: <strong>{$dbHost}</strong> | Conexões: <strong class=\"{$connClass}\" style=\"color: {$connColor};\" title=\"Total de conexões nos últimos 60 min / Limite por hora ({$maxConnectionsPerHour})\">{$connectionsLastHour}/{$maxConnectionsPerHour}</strong> | Tempo: <strong>{$loadTime}s</strong>{$queueHtml}";

        return [
            'html' => $footerHtml,
            'connectionsLastHour' => $connectionsLastHour,
            'maxConnectionsPerHour' => $maxConnectionsPerHour,
            'loadTime' => $loadTime,
            'queueStats' => $queueStats
        ];
    }
}
