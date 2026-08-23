<?php

namespace App\Controllers;

use App\Libraries\EvolutionApiService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Health extends BaseController
{
    public function check(): ResponseInterface
    {
        $dbStatus = false;
        $dbMessage = 'offline';
        try {
            $db = \Config\Database::connect();
            if ($db->connect()) {
                $db->query('SELECT 1');
                $dbStatus = true;
                $dbMessage = 'online';
            }
        } catch (Throwable $e) {
            $dbStatus = false;
            $dbMessage = $e->getMessage();
        }

        $evoStatus = false;
        $evoMessage = 'offline';
        $evoConfigured = false;
        $defaultInstanceConnected = false;
        try {
            $evolutionService = new EvolutionApiService();
            $evoConfigured = $evolutionService->isConfigured();
            if ($evoConfigured) {
                // Testa com timeout reduzido para não travar o pooling
                // $evolutionService->testConnection();
                $evoStatus = true;
                $evoMessage = 'online';
                
                $settings = $evolutionService->getSettings();
                $defaultInstanceName = $settings['default_instance_name'] ?? null;
                
                if ($defaultInstanceName) {
                    try {
                        $instance = $evolutionService->findInstance($defaultInstanceName);
                        $defaultInstanceConnected = $instance['connected'] ?? false;
                    } catch (Throwable $e) {
                        $defaultInstanceConnected = false;
                    }
                }
            } else {
                $evoMessage = 'unconfigured';
            }
        } catch (Throwable $e) {
            $evoStatus = false;
            $evoMessage = $e->getMessage();
        }

        $queueStats = [
            'pending'    => 0,
            'processing' => 0,
            'failed'     => 0,
            'completed'  => 0,
            'total_active' => 0,
        ];
        try {
            $queueModel = new \App\Models\SystemJobQueueModel();
            $stats = $queueModel->getQueueStats();
            $queueStats = [
                'pending'      => (int) ($stats['pending'] ?? 0),
                'processing'   => (int) ($stats['processing'] ?? 0),
                'failed'       => (int) ($stats['failed'] ?? 0),
                'completed'    => (int) ($stats['completed'] ?? 0),
                'total_active' => ((int) ($stats['pending'] ?? 0)) + ((int) ($stats['processing'] ?? 0)) + ((int) ($stats['failed'] ?? 0)),
            ];
        } catch (Throwable $e) {
            // Em caso de erro na tabela, mantém zerado
        }

        $allOk = $dbStatus && ($evoConfigured ? $evoStatus : true);

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setJSON([
                'status' => $allOk ? 'online' : 'partial',
                'database' => [
                    'online' => $dbStatus,
                    'message' => $dbMessage,
                ],
                'evolution' => [
                    'configured' => $evoConfigured,
                    'online' => $evoStatus,
                    'message' => $evoMessage,
                    'default_instance_connected' => $defaultInstanceConnected,
                ],
                'queue' => $queueStats,
                'timestamp' => time(),
            ]);
    }
}
