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
        try {
            $evolutionService = new EvolutionApiService();
            $evoConfigured = $evolutionService->isConfigured();
            if ($evoConfigured) {
                // Testa com timeout reduzido para não travar o pooling
                $evolutionService->testConnection();
                $evoStatus = true;
                $evoMessage = 'online';
            } else {
                $evoMessage = 'unconfigured';
            }
        } catch (Throwable $e) {
            $evoStatus = false;
            $evoMessage = $e->getMessage();
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
                ],
                'timestamp' => time(),
            ]);
    }
}
