<?php

namespace App\Controllers;

use App\Models\SystemJobModel;
use App\Models\SystemJobQueueModel;
use App\Services\JobCenterService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class JobCenter extends BaseController
{
    protected $helpers = ['text', 'form', 'url'];
    protected SystemJobModel $jobModel;
    protected SystemJobQueueModel $queueModel;
    protected JobCenterService $jobCenterService;

    public function __construct()
    {
        $this->jobModel = new SystemJobModel();
        $this->queueModel = new SystemJobQueueModel();
        $this->jobCenterService = new JobCenterService();
    }

    public function index(): string
    {
        $stats = $this->queueModel->getQueueStats();
        $jobs = $this->jobModel->findAll();
        $queueItems = $this->queueModel
            ->orderBy('id', 'DESC')
            ->findAll(100);

        $cronPath = env('app.PathCronJob') ?: ROOTPATH;

        return $this->renderPage('jobs', [
            'stats'           => $stats,
            'jobs'            => $jobs,
            'queueItems'      => $queueItems,
            'cronPath'        => $cronPath,
        ]);
    }

    private function renderPage(string $activePage, array $extraData = []): string
    {
        $item = Home::NAVIGATION[$activePage];
        $userName = trim((string) session()->get('user_name')) ?: 'Usuário';
        $nameParts = preg_split('/\s+/', $userName) ?: [$userName];
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '';
        $userInitials = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1));
        $layoutMaxWidth = $this->getLayoutMaxWidth();

        $data = array_merge([
            'activePage' => $activePage,
            'layoutMaxWidth' => $layoutMaxWidth,
            'navigation' => Home::NAVIGATION,
            'pageDescription' => $item['description'],
            'pageIcon' => $item['icon'],
            'pageTitle' => $item['label'],
            'userEmail' => (string) session()->get('user_email'),
            'userInitials' => $userInitials,
            'userName' => $userName,
        ], $extraData);

        return view('admin/jobs/index', $data);
    }

    public function feed(): ResponseInterface
    {
        $stats = $this->queueModel->getQueueStats();
        $queueItems = $this->queueModel
            ->orderBy('id', 'DESC')
            ->findAll(100);

        return $this->response->setJSON([
            'success' => true,
            'stats'   => $stats,
            'items'   => $queueItems,
        ]);
    }

    public function retryFailed(): RedirectResponse
    {
        $count = $this->jobCenterService->retryFailedJobs();
        if ($count > 0) {
            return redirect()->to(site_url('central-trabalho'))
                ->with('success', "{$count} tarefas com falha foram reagendadas para execução imediata.");
        }

        return redirect()->to(site_url('central-trabalho'))
            ->with('info', 'Não há tarefas com falha para reprocessar.');
    }

    public function clearCompleted(): RedirectResponse
    {
        $count = $this->jobCenterService->clearCompletedJobs();
        return redirect()->to(site_url('central-trabalho'))
            ->with('success', "{$count} tarefas concluídas foram removidas do histórico da fila.");
    }

    public function deleteJob(int $id): RedirectResponse
    {
        $job = $this->queueModel->find($id);
        if (!$job) {
            return redirect()->to(site_url('central-trabalho'))
                ->with('error', 'Trabalho não encontrado na fila.');
        }

        if ($job['status'] === 'completed') {
            return redirect()->to(site_url('central-trabalho'))
                ->with('error', 'Trabalhos concluídos não podem ser excluídos individualmente (use "Limpar Concluídas").');
        }

        $this->queueModel->delete($id);
        return redirect()->to(site_url('central-trabalho'))
            ->with('success', 'Trabalho removido da fila com sucesso.');
    }

    public function deleteSelected(): RedirectResponse
    {
        $ids = $this->request->getPost('ids');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->to(site_url('central-trabalho'))
                ->with('error', 'Nenhum trabalho selecionado para exclusão.');
        }

        // Filtra apenas IDs que não estejam concluídos
        $validJobs = $this->queueModel
            ->whereIn('id', $ids)
            ->where('status !=', 'completed')
            ->findAll();

        if (empty($validJobs)) {
            return redirect()->to(site_url('central-trabalho'))
                ->with('info', 'Nenhum dos trabalhos selecionados pode ser excluído (tarefas concluídas não são permitidas).');
        }

        $validIds = array_column($validJobs, 'id');
        $this->queueModel->delete($validIds);
        $count = count($validIds);

        return redirect()->to(site_url('central-trabalho'))
            ->with('success', "{$count} tarefa(s) não concluída(s) foram excluída(s) com sucesso.");
    }

    public function runNow(): RedirectResponse
    {
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);

        $res = $this->jobCenterService->processPendingQueue(10);
        return redirect()->to(site_url('central-trabalho'))
            ->with('success', "Ciclo de execução processado: {$res['processed']} sucesso(s), {$res['failed']} falha(s).");
    }

    public function runSingleJob(int $id): RedirectResponse
    {
        @ini_set('max_execution_time', '180');
        @set_time_limit(180);

        $job = $this->queueModel->find($id);
        if (!$job) {
            return redirect()->to(site_url('central-trabalho'))
                ->with('error', 'Trabalho não encontrado na fila.');
        }

        if ($job['status'] !== 'pending') {
            return redirect()->to(site_url('central-trabalho'))
                ->with('error', 'Apenas tarefas com status "Pendente" podem ser executadas manualmente.');
        }

        // Força a data agendada para agora caso esteja no futuro e executa limit 1
        $this->queueModel->update($id, ['scheduled_at' => date('Y-m-d H:i:s')]);
        
        $res = $this->jobCenterService->processPendingQueue(1);
        
        $updatedJob = $this->queueModel->find($id);
        if ($updatedJob && $updatedJob['status'] === 'completed') {
            return redirect()->to(site_url('central-trabalho'))
                ->with('success', "Tarefa #{$id} executada com sucesso.");
        }

        $errMsg = !empty($updatedJob['error_message']) ? ': ' . $updatedJob['error_message'] : '.';
        return redirect()->to(site_url('central-trabalho'))
            ->with('error', "Falha ao executar tarefa #{$id}{$errMsg}");
    }

    public function saveJobSettings(): RedirectResponse
    {
        $jobKey = (string) $this->request->getPost('job_key');
        $job = $this->jobModel->getByKey($jobKey);

        if (!$job) {
            return redirect()->to(site_url('configuracoes?tab=central-trabalho'))
                ->with('error', 'Trabalho informado não foi localizado.');
        }

        $isActive = $this->request->getPost('is_active') ? 1 : 0;
        $minDelay = max(1, (int) $this->request->getPost('min_delay_seconds'));
        $maxDelay = max($minDelay, (int) $this->request->getPost('max_delay_seconds'));

        $this->jobModel->update($job['id'], [
            'is_active'         => $isActive,
            'min_delay_seconds' => $minDelay,
            'max_delay_seconds' => $maxDelay,
        ]);

        return redirect()->to(site_url('configuracoes?tab=central-trabalho'))
            ->with('success', "Configurações do trabalho '{$job['name']}' atualizadas com sucesso.");
    }
    public function statusSummary(): ResponseInterface
    {
        $queueStats = [
            'pending'      => 0,
            'processing'   => 0,
            'failed'       => 0,
            'completed'    => 0,
            'total_active' => 0,
        ];

        try {
            $stats = $this->queueModel->getQueueStats();
            $pending = (int) ($stats['pending'] ?? 0);
            $processing = (int) ($stats['processing'] ?? 0);
            $failed = (int) ($stats['failed'] ?? 0);
            $completed = (int) ($stats['completed'] ?? 0);

            $queueStats = [
                'pending'      => $pending,
                'processing'   => $processing,
                'failed'       => $failed,
                'completed'    => $completed,
                'total_active' => $pending + $processing + $failed,
            ];
        } catch (\Throwable $e) {
            // Em caso de erro, mantém estrutura zerada
        }

        return $this->response->setJSON([
            'queue' => $queueStats,
        ]);
    }}
