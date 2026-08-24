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

        // Obter status do Worker Realtime (cron-realtime) sem abrir conexões extras
        $realtimeService = new \App\Services\RealtimeSnapshotService();
        $workerStatus = $realtimeService->getWorkerStatus();
        $isOnline = !empty($workerStatus['is_online']);
        $workerColor = $isOnline ? 'rgb(var(--success))' : 'rgb(var(--danger))';
        $workerLabel = $isOnline ? 'ONLINE' : 'OFFLINE';
        $workerCycle = (int) ($workerStatus['cycle'] ?? 0);
        $workerSecAgo = $workerStatus['seconds_ago'] !== null ? "{$workerStatus['seconds_ago']}s" : '-';
        $workerTitle = $isOnline 
            ? "Worker Realtime (cron-realtime): ATIVO | Ciclo #{$workerCycle} | Último ping há {$workerSecAgo}" 
            : "Worker Realtime (cron-realtime): PARADO / INATIVO | Último ping: " . ($workerStatus['last_heartbeat'] ?? 'Nunca');

        $workerHtml = " | Cron Realtime: <strong style=\"color: {$workerColor};\" title=\"{$workerTitle}\">● {$workerLabel}</strong>";

        $footerHtml = "Banco: <strong>{$dbHost}</strong> | Conexões: <strong class=\"{$connClass}\" style=\"color: {$connColor};\" title=\"Total de conexões nos últimos 60 min / Limite por hora ({$maxConnectionsPerHour})\">{$connectionsLastHour}/{$maxConnectionsPerHour}</strong> | Tempo: <strong>{$loadTime}s</strong>{$queueHtml}{$workerHtml}";

        return [
            'html' => $footerHtml,
            'connectionsLastHour' => $connectionsLastHour,
            'maxConnectionsPerHour' => $maxConnectionsPerHour,
            'loadTime' => $loadTime,
            'queueStats' => $queueStats,
            'workerStatus' => $workerStatus,
        ];
    }
}
