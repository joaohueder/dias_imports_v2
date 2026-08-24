<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Psr\Log\LoggerInterface;
use Throwable;

class DatabaseAutoMigrate extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = ['url'];
        parent::initController($request, $response, $logger);
    }

    /**
     * Tela de bloqueio com animação e barra de progresso
     */
    public function screen()
    {
        $returnUrl = $this->request->getGet('return_url') ?: site_url('/');
        // Sanitiza returnUrl para garantir que é relativo ou mesmo domínio
        if (str_starts_with($returnUrl, 'http') && !str_starts_with($returnUrl, base_url())) {
            $returnUrl = site_url('/');
        }

        return view('admin/database_migrating_screen', [
            'returnUrl' => $returnUrl,
            'pageTitle' => 'Atualização Estrutural do Banco de Dados'
        ]);
    }

    /**
     * Executa as migrations via AJAX e retorna o progresso / etapas
     */
    public function execute()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Requisição inválida.'
            ]);
        }

        try {
            $runner = Services::migrations();
            $runner->clearCliMessages();

            // Executa as migrações mais recentes
            $migrated = $runner->latest();

            $messages = $runner->getCliMessages();
            $cleanMessages = [];
            foreach ($messages as $msg) {
                $clean = trim(preg_replace('/\x1b\[[0-9;]*m/', '', $msg));
                if (!empty($clean)) {
                    $cleanMessages[] = $clean;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'migrated' => $migrated,
                'messages' => $cleanMessages,
                'message' => 'Migrações e estrutura do banco de dados sincronizadas com sucesso!'
            ]);
        } catch (Throwable $e) {
            log_message('critical', 'Auto Migration Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao executar migrações: ' . $e->getMessage()
            ]);
        }
    }
}
