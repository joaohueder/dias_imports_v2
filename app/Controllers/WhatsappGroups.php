<?php

namespace App\Controllers;

use App\Libraries\EvolutionApiService;
use App\Libraries\UserPermissions;
use App\Models\AppSettingModel;
use App\Models\WhatsappGroupModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class WhatsappGroups extends BaseController
{
    protected $helpers = ['form', 'url'];
    private WhatsappGroupModel $groupModel;
    private EvolutionApiService $evolutionService;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->groupModel = new WhatsappGroupModel();
        $this->evolutionService = new EvolutionApiService();

        if (! UserPermissions::hasPermission('whatsapp_groups', 'view')) {
            header('Location: ' . site_url('/'));
            exit;
        }
    }

    public function index(): string
    {
        $status = trim((string) ($this->request->getGet('status') ?? 'all'));
        $search = trim((string) ($this->request->getGet('q') ?? ''));

        $data = $this->getGroupsData($status, $search);

        $layoutMaxWidth = $this->getLayoutMaxWidth();

        $userName = (string) session()->get('user_name');
        $userEmail = (string) session()->get('user_email');
        $firstName = trim(explode(' ', $userName)[0] ?? 'Usuário');
        $userInitials = $this->extractInitials($userName);

        $systemJobModel = new \App\Models\SystemJobModel();
        $syncJob = $systemJobModel->getByKey('sync_whatsapp_groups');
        $isSyncJobActive = $syncJob && !empty($syncJob['is_active']);

        return view('admin/groups/index', array_merge($data, [
            'pageTitle' => 'Grupos de WhatsApp',
            'pageDescription' => 'Gestão e sincronização dos grupos de WhatsApp da empresa.',
            'pageIcon' => 'ti-brand-whatsapp',
            'activePage' => 'whatsapp',
            'layoutMaxWidth' => $layoutMaxWidth,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'firstName' => $firstName,
            'userInitials' => $userInitials,
            'navigation' => Home::getNavigationList(),
            'currentStatus' => $status,
            'searchQuery' => $search,
            'isSyncJobActive' => $isSyncJobActive,
        ]));
    }

    public function feed(): ResponseInterface
    {
        $status = trim((string) ($this->request->getGet('status') ?? 'all'));
        $search = trim((string) ($this->request->getGet('q') ?? ''));

        $data = $this->getGroupsData($status, $search);

        $htmlCards = view('admin/groups/_cards', ['groups' => $data['groups']]);

        helper('telemetry');
        $telemetry = get_footer_telemetry();

        return $this->response->setJSON([
            'success' => true,
            'metrics' => $data['metrics'],
            'htmlCards' => $htmlCards,
            'totalResults' => count($data['groups']),
            'footerHtml' => $telemetry['html'] ?? null,
            'telemetry' => [
                'connectionsLastHour' => $telemetry['connectionsLastHour'] ?? 0,
                'maxConnectionsPerHour' => $telemetry['maxConnectionsPerHour'] ?? 500,
                'loadTime' => $telemetry['loadTime'] ?? 0,
            ],
        ]);
    }

    public function evolutionList(): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para listar grupos da Evolution API.'])->setStatusCode(403);
        }

        try {
            $settings = $this->evolutionService->getSettings();
            $instanceName = trim((string) ($settings['default_instance_name'] ?? ''));

            if ($instanceName === '') {
                $instances = $this->evolutionService->fetchInstances();
                foreach ($instances as $inst) {
                    if (! empty($inst['connected'])) {
                        $instanceName = $inst['name'];
                        break;
                    }
                }
                if ($instanceName === '' && ! empty($instances[0]['name'])) {
                    $instanceName = $instances[0]['name'];
                }
            }

            if ($instanceName === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nenhuma instância da Evolution API configurada ou conectada.',
                    'groups' => [],
                ])->setStatusCode(400);
            }

            $remoteGroups = $this->evolutionService->fetchAllGroups($instanceName);
            
            // Buscar JIDs já cadastrados no banco para marcar no retorno
            $existingJids = array_column($this->groupModel->select('group_jid')->findAll(), 'group_jid');
            $existingJidMap = array_fill_keys($existingJids, true);

            $formatted = [];
            foreach ($remoteGroups as $item) {
                $groupJid = (string) ($item['id'] ?? $item['jid'] ?? '');
                if ($groupJid === '' || ! str_contains($groupJid, '@g.us')) {
                    continue;
                }

                $subject = trim((string) ($item['subject'] ?? $item['name'] ?? 'Grupo WhatsApp'));
                $description = (string) ($item['desc'] ?? $item['description'] ?? '');
                $participants = $item['participants'] ?? [];
                $participantsCount = is_array($participants) ? count($participants) : (int) ($item['size'] ?? 0);
                $avatarUrl = (string) ($item['pictureUrl'] ?? $item['profilePicUrl'] ?? '');

                $formatted[] = [
                    'group_jid' => $groupJid,
                    'instance_name' => $instanceName,
                    'name' => $subject,
                    'description' => $description,
                    'participants_count' => $participantsCount,
                    'avatar_url' => $avatarUrl,
                    'is_already_added' => isset($existingJidMap[$groupJid]),
                ];
            }

            // Ordenar alfabeticamente por nome
            usort($formatted, fn($a, $b) => strcasecmp($a['name'], $b['name']));

            return $this->response->setJSON([
                'success' => true,
                'instance_name' => $instanceName,
                'total' => count($formatted),
                'groups' => $formatted,
            ]);
        } catch (Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'groups' => [],
            ])->setStatusCode(200);
        }
    }

    public function saveSelected(): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para adicionar grupos.'])->setStatusCode(403);
        }

        $groupJid = trim((string) $this->request->getPost('group_jid'));
        $name = trim((string) $this->request->getPost('name'));
        $description = trim((string) $this->request->getPost('description'));
        $participantsCount = (int) $this->request->getPost('participants_count');
        $avatarUrl = trim((string) $this->request->getPost('avatar_url'));
        $instanceName = trim((string) $this->request->getPost('instance_name'));
        $category = trim((string) $this->request->getPost('category')) ?: 'Dias Imports';

        if ($groupJid === '' || ! str_contains($groupJid, '@g.us')) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID do grupo do WhatsApp inválido.'])->setStatusCode(400);
        }

        if ($name === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Nome do grupo obrigatório.'])->setStatusCode(400);
        }

        try {
            $existing = $this->groupModel->where('group_jid', $groupJid)->first();

            $groupData = [
                'group_jid' => $groupJid,
                'instance_name' => $instanceName ?: 'default',
                'name' => $name,
                'description' => $description !== '' ? $description : ($existing['description'] ?? null),
                'participants_count' => $participantsCount,
                'avatar_url' => $avatarUrl !== '' ? $avatarUrl : ($existing['avatar_url'] ?? null),
                'status' => 'active',
                'category' => $category,
                'last_synced_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $this->groupModel->update($existing['id'], $groupData);
                $message = "Grupo \"{$name}\" já existia e foi atualizado com sucesso!";
            } else {
                $this->groupModel->insert($groupData);
                $message = "Grupo \"{$name}\" adicionado com sucesso ao sistema!";
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
            ]);
        } catch (Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar grupo: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function sync(): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'create') && ! UserPermissions::hasPermission('whatsapp_groups', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para sincronizar grupos.'])->setStatusCode(403);
        }

        try {
            $jobService = new \App\Services\JobCenterService();
            $result = $jobService->enqueueWhatsappGroupsSync();

            if (!$result['success']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'],
                ])->setStatusCode(200); // Retorna 200 para o JS tratar como aviso, não como erro de servidor
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message'],
                'redirect' => site_url('grupos-whatsapp'),
            ]);
        } catch (Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao enfileirar sincronização: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function create(): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para criar grupos.'])->setStatusCode(403);
        }

        $subject = trim((string) $this->request->getPost('name'));
        $description = trim((string) $this->request->getPost('description'));
        $participantsRaw = trim((string) $this->request->getPost('participants'));
        $category = trim((string) $this->request->getPost('category')) ?: 'Dias Imports';

        if ($subject === '' || mb_strlen($subject) > 100) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nome do grupo inválido (máximo 100 caracteres).'])->setStatusCode(400);
        }

        $participants = [];
        if ($participantsRaw !== '') {
            $lines = preg_split('/[\r\n,;]+/', $participantsRaw);
            foreach ($lines as $line) {
                $cleaned = preg_replace('/\D+/', '', $line);
                if (strlen($cleaned) >= 10) {
                    $participants[] = $cleaned;
                }
            }
        }

        try {
            $settings = $this->evolutionService->getSettings();
            $instanceName = trim((string) ($settings['default_instance_name'] ?? ''));

            if ($instanceName === '') {
                $instances = $this->evolutionService->fetchInstances();
                foreach ($instances as $inst) {
                    if (! empty($inst['connected'])) {
                        $instanceName = $inst['name'];
                        break;
                    }
                }
                if ($instanceName === '' && ! empty($instances[0]['name'])) {
                    $instanceName = $instances[0]['name'];
                }
            }

            if ($instanceName === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nenhuma instância da Evolution API conectada.',
                ])->setStatusCode(400);
            }

            $res = $this->evolutionService->createGroup($instanceName, $subject, $participants, $description);
            $groupJid = (string) ($res['id'] ?? $res['jid'] ?? $res['groupJid'] ?? '');

            if ($groupJid === '') {
                $groupJid = uniqid('temp_group_') . '@g.us';
            }

            $this->groupModel->insert([
                'group_jid' => $groupJid,
                'instance_name' => $instanceName,
                'name' => $subject,
                'description' => $description,
                'participants_count' => count($participants) + 1,
                'status' => 'active',
                'category' => $category,
                'last_synced_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Grupo criado com sucesso no WhatsApp e cadastrado no sistema!',
            ]);
        } catch (Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar grupo no WhatsApp: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function toggleStatus(int $id): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para alterar status.'])->setStatusCode(403);
        }

        $group = $this->groupModel->find($id);
        if (! $group) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grupo não encontrado.'])->setStatusCode(404);
        }

        $newStatus = ($group['status'] === 'active') ? 'inactive' : 'active';
        $this->groupModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON([
            'success' => true,
            'newStatus' => $newStatus,
            'message' => $newStatus === 'active' ? 'Grupo ativado com sucesso!' : 'Grupo inativado com sucesso!',
        ]);
    }

    public function updateData(int $id): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para atualizar dados.'])->setStatusCode(403);
        }

        $systemJobModel = new \App\Models\SystemJobModel();
        $syncJob = $systemJobModel->getByKey('sync_whatsapp_groups');
        if (!$syncJob || empty($syncJob['is_active'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'A rotina de atualização de grupos está desativada nas configurações da Central de Trabalho.',
            ])->setStatusCode(400);
        }

        $group = $this->groupModel->find($id);
        if (! $group) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grupo não encontrado.'])->setStatusCode(404);
        }

        try {
            $queueModel = new \App\Models\SystemJobQueueModel();
            $groupName = !empty($group['name']) ? $group['name'] : $group['group_jid'];

            $queueModel->insert([
                'job_key'        => 'sync_whatsapp_groups',
                'item_reference' => $groupName,
                'payload'        => json_encode([
                    'group_id'      => $group['id'],
                    'group_jid'     => $group['group_jid'],
                    'instance_name' => $group['instance_name'] ?? '',
                    'name'          => $group['name'] ?? '',
                ], JSON_UNESCAPED_UNICODE),
                'status'         => 'pending',
                'scheduled_at'   => date('Y-m-d H:i:s'),
                'attempts'       => 0,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Atualização do grupo enfileirada com sucesso!',
            ]);
        } catch (Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao enfileirar atualização: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function sendTestMessage(int $id): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para testar envio.'])->setStatusCode(403);
        }

        $group = $this->groupModel->find($id);
        if (! $group) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grupo não encontrado.'])->setStatusCode(404);
        }

        $phone = preg_replace('/\D+/', '', (string) ($this->request->getPost('phone') ?? ''));
        $message = trim((string) ($this->request->getPost('message') ?? ''));
        if ($message === '') {
            $message = "🚀 *Mensagem de Teste*\nEsta é uma mensagem de validação de envio do painel Dias Imports.";
        }

        try {
            $instanceName = $group['instance_name'];
            if ($instanceName === '') {
                $settings = $this->evolutionService->getSettings();
                $instanceName = (string) ($settings['default_instance_name'] ?? '');
            }

            if ($instanceName === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Nenhuma instância configurada para o envio.'])->setStatusCode(400);
            }

            if ($phone !== '') {
                $this->evolutionService->sendTextMessage($instanceName, $phone, $message);
                $successMsg = "Mensagem de teste enviada com sucesso para {$phone}!";
            } else {
                $this->evolutionService->sendGroupTestMessage($instanceName, $group['group_jid'], $message);
                $successMsg = 'Mensagem de teste enviada com sucesso para o grupo!';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $successMsg,
            ]);
        } catch (Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao enviar mensagem: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function delete(int $id): ResponseInterface
    {
        if (! UserPermissions::hasPermission('whatsapp_groups', 'delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão para excluir grupos.'])->setStatusCode(403);
        }

        $group = $this->groupModel->find($id);
        if (! $group) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grupo não encontrado.'])->setStatusCode(404);
        }

        $this->groupModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Grupo excluído com sucesso do sistema!',
        ]);
    }

    private function getGroupsData(string $status = 'all', string $search = ''): array
    {
        $builder = $this->groupModel->orderBy('id', 'DESC');

        if ($search !== '') {
            $term = $search;
            $builder->groupStart()
                ->like('name', $term)
                ->orLike('description', $term)
                ->orLike('category', $term)
                ->groupEnd();
        }

        if ($status === 'active' || $status === 'inactive') {
            $builder->where('status', $status);
        }

        $groups = $builder->findAll();

        // Calcular métricas com uma única query agregada no banco
        $db = \Config\Database::connect();
        $metricsRow = $db->table('whatsapp_groups')
            ->select("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
            ")
            ->get()
            ->getRowArray();

        return [
            'groups' => $groups,
            'metrics' => [
                'total' => (int) ($metricsRow['total'] ?? 0),
                'active' => (int) ($metricsRow['active'] ?? 0),
                'inactive' => (int) ($metricsRow['inactive'] ?? 0),
            ],
        ];
    }

    private function extractInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (empty($parts) || $parts[0] === '') {
            return 'U';
        }
        $initials = mb_substr($parts[0], 0, 1);
        if (isset($parts[1]) && $parts[1] !== '') {
            $initials .= mb_substr($parts[1], 0, 1);
        }
        return mb_strtoupper($initials);
    }
}

