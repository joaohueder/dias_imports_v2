<?php

namespace App\Controllers;

use App\Libraries\UserPermissions;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Users extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (! UserPermissions::hasPermission('users', 'view')) {
            header('Location: ' . site_url('/'));
            exit;
        }
    }

    public function index(): string
    {
        $userModel = new UserModel();
        $users = $userModel->orderBy('id', 'ASC')->findAll();

        $currentUserId = (int) session()->get('user_id');
        $counts = [
            'total' => count($users),
            'active' => 0,
            'inactive' => 0,
            'admin' => 0,
        ];

        foreach ($users as $u) {
            if ((int) $u['is_active'] === 1) {
                $counts['active']++;
            } else {
                $counts['inactive']++;
            }
            if (($u['role'] ?? 'user') === 'admin') {
                $counts['admin']++;
            }
        }

        $layoutMaxWidth = $this->getLayoutMaxWidth();

        $userName = (string) session()->get('user_name');
        $userEmail = (string) session()->get('user_email');
        $firstName = trim(explode(' ', $userName)[0] ?? 'Usuário');
        $userInitials = $this->extractInitials($userName);

        $realtimeModel = new \App\Models\RealtimeScreenSettingModel();
        $isRealtimeActive = $realtimeModel->isScreenActive('users');
        $realtimeInterval = $realtimeModel->getInterval('users');

        return view('admin/users/index', [
            'pageTitle' => 'Usuários',
            'pageDescription' => 'Controle quem acessa o painel e quais áreas cada pessoa pode usar.',
            'pageIcon' => 'ti-users',
            'activePage' => 'users',
            'layoutMaxWidth' => $layoutMaxWidth,
            'isRealtimeActive' => $isRealtimeActive,
            'realtimeInterval' => $realtimeInterval,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'firstName' => $firstName,
            'userInitials' => $userInitials,
            'navigation' => Home::getNavigationList(),
            'users' => $users,
            'counts' => $counts,
            'currentUserId' => $currentUserId,
        ]);
    }

    public function feed(): \CodeIgniter\HTTP\ResponseInterface
    {
        $currentUserId = (int) session()->get('user_id');
        $snapshotService = new \App\Services\RealtimeSnapshotService();
        $snapshot = $snapshotService->getSnapshot('users');

        if ($snapshot !== null && !empty($snapshot['data'])) {
            $counts = $snapshot['data']['counts'];
            $users = $snapshot['data']['users'];
        } else {
            $userModel = new UserModel();
            $users = $userModel->orderBy('id', 'ASC')->findAll();

            $counts = [
                'total' => count($users),
                'active' => 0,
                'inactive' => 0,
                'admin' => 0,
            ];

            foreach ($users as $u) {
                if ((int) $u['is_active'] === 1) {
                    $counts['active']++;
                } else {
                    $counts['inactive']++;
                }
                if (($u['role'] ?? 'user') === 'admin') {
                    $counts['admin']++;
                }
            }
        }

        $htmlCards = view('admin/users/_cards', [
            'users' => $users,
            'currentUserId' => $currentUserId,
        ]);

        helper('telemetry');
        $telemetry = get_footer_telemetry();

        return $this->response->setJSON([
            'success' => true,
            'counts' => $counts,
            'htmlCards' => $htmlCards,
            'totalResults' => count($users),
            'footerHtml' => $telemetry['html'],
            'telemetry' => [
                'connectionsLastHour' => $telemetry['connectionsLastHour'],
                'maxConnectionsPerHour' => $telemetry['maxConnectionsPerHour'],
                'loadTime' => $telemetry['loadTime'],
            ],
        ]);
    }

    public function create(): string|RedirectResponse
    {
        if (! UserPermissions::hasPermission('users', 'create')) {
            return redirect()->to('/usuarios')->with('error', 'Você não tem permissão para cadastrar novos usuários.');
        }

        return $this->renderForm();
    }

    public function edit(int $id): string|RedirectResponse
    {
        if (! UserPermissions::hasPermission('users', 'edit')) {
            return redirect()->to('/usuarios')->with('error', 'Você não tem permissão para editar usuários.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            return redirect()->to('/usuarios')->with('error', 'Usuário não encontrado.');
        }

        return $this->renderForm($user);
    }

    private function renderForm(?array $user = null): string
    {
        $layoutMaxWidth = $this->getLayoutMaxWidth();

        $userName = (string) session()->get('user_name');
        $userEmail = (string) session()->get('user_email');
        $firstName = trim(explode(' ', $userName)[0] ?? 'Usuário');
        $userInitials = $this->extractInitials($userName);

        $userPermissions = [];
        if ($user && !empty($user['permissions'])) {
            $userPermissions = json_decode($user['permissions'], true) ?? [];
        }

        return view('admin/users/form', [
            'pageTitle' => $user ? 'Editar Usuário' : 'Novo Usuário',
            'pageDescription' => 'Defina os dados de acesso e marque exatamente o que a pessoa pode fazer em cada área do painel.',
            'pageIcon' => 'ti-users',
            'activePage' => 'users',
            'layoutMaxWidth' => $layoutMaxWidth,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'firstName' => $firstName,
            'userInitials' => $userInitials,
            'navigation' => Home::getNavigationList(),
            'user' => $user,
            'userPermissions' => $userPermissions,
            'permissionGroups' => UserPermissions::MODULE_GROUPS,
            'isSelf' => $user && ((int) $user['id'] === (int) session()->get('user_id')),
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! UserPermissions::hasPermission('users', 'create')) {
            return redirect()->to('/usuarios')->with('error', 'Você não tem permissão para cadastrar novos usuários.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[120]',
            'email' => 'required|valid_email|max_length[190]|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[255]',
            'role' => 'required|in_list[admin,user]',
        ];

        if (! $this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $firstError = reset($errors);
            return redirect()->back()->withInput()->with('error', $firstError ?: 'Dados inválidos.');
        }

        $role = (string) $this->request->getPost('role');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;
        $permissions = $this->request->getPost('permissions') ?? [];

        $userModel = new UserModel();
        $userModel->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'email' => mb_strtolower(trim((string) $this->request->getPost('email'))),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $role,
            'permissions' => $role === 'admin' ? null : json_encode($permissions),
            'is_active' => $isActive,
        ]);

        return redirect()->to('/usuarios')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function update(int $id): RedirectResponse
    {
        if (! UserPermissions::hasPermission('users', 'edit')) {
            return redirect()->to('/usuarios')->with('error', 'Você não tem permissão para editar usuários.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            return redirect()->to('/usuarios')->with('error', 'Usuário não encontrado.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[120]',
            'role' => 'required|in_list[admin,user]',
        ];

        if (! $this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $firstError = reset($errors);
            return redirect()->back()->withInput()->with('error', $firstError ?: 'Dados inválidos.');
        }

        $currentUserId = (int) session()->get('user_id');
        $isSelf = ((int) $user['id'] === $currentUserId);

        $role = (string) $this->request->getPost('role');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        // Se for o próprio usuário, não permite se auto-inativar ou rebaixar seu próprio papel de admin
        if ($isSelf) {
            $isActive = 1;
            if ($user['role'] === 'admin') {
                $role = 'admin';
            }
        }

        $permissions = $this->request->getPost('permissions') ?? [];

        $updateData = [
            'name' => trim((string) $this->request->getPost('name')),
            'role' => $role,
            'permissions' => $role === 'admin' ? null : json_encode($permissions),
            'is_active' => $isActive,
        ];

        $userModel->update($id, $updateData);

        // Se o próprio usuário editou seus dados, atualiza a sessão
        if ($isSelf) {
            session()->set([
                'user_name' => $updateData['name'],
                'user_role' => $role,
                'user_permissions' => $role === 'admin' ? [] : $permissions,
            ]);
        }

        return redirect()->to('/usuarios')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function resetPassword(int $id): RedirectResponse
    {
        if (! UserPermissions::hasPermission('users', 'edit')) {
            return redirect()->to('/usuarios')->with('error', 'Você não tem permissão para redefinir senhas de usuários.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            return redirect()->to('/usuarios')->with('error', 'Usuário não encontrado.');
        }

        $rules = [
            'new_password' => 'required|min_length[6]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/usuarios')->with('error', 'A nova senha deve ter no mínimo 6 caracteres.');
        }

        $newPassword = (string) $this->request->getPost('new_password');
        $userModel->update($id, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        // Revogar tokens de "lembrar-me" do usuário
        (new \App\Models\RememberTokenModel())->where('user_id', $id)->delete();

        return redirect()->to('/usuarios')->with('success', 'Senha redefinida com sucesso para ' . $user['name'] . '!');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        if (! UserPermissions::hasPermission('users', 'edit')) {
            return redirect()->to('/usuarios')->with('error', 'Você não tem permissão para alterar o status de usuários.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            return redirect()->to('/usuarios')->with('error', 'Usuário não encontrado.');
        }

        $currentUserId = (int) session()->get('user_id');
        if ((int) $user['id'] === $currentUserId) {
            return redirect()->to('/usuarios')->with('error', 'Você não pode inativar seu próprio usuário.');
        }

        $newStatus = (int) $user['is_active'] === 1 ? 0 : 1;
        $userModel->update($id, ['is_active' => $newStatus]);

        if ($newStatus === 0) {
            // Revogar tokens de "lembrar-me"
            (new \App\Models\RememberTokenModel())->where('user_id', $id)->delete();
        }

        $actionText = $newStatus === 1 ? 'ativado' : 'inativado';
        return redirect()->to('/usuarios')->with('success', "Usuário {$user['name']} foi {$actionText} com sucesso!");
    }

    public function delete(int $id): RedirectResponse
    {
        if (! UserPermissions::hasPermission('users', 'delete')) {
            return redirect()->to('/usuarios')->with('error', 'Você não tem permissão para excluir usuários.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            return redirect()->to('/usuarios')->with('error', 'Usuário não encontrado.');
        }

        $currentUserId = (int) session()->get('user_id');
        if ((int) $user['id'] === $currentUserId) {
            return redirect()->to('/usuarios')->with('error', 'Você não pode excluir sua própria conta.');
        }

        // Revogar tokens antes
        (new \App\Models\RememberTokenModel())->where('user_id', $id)->delete();
        $userModel->delete($id);

        return redirect()->to('/usuarios')->with('success', "Usuário {$user['name']} foi excluído com sucesso!");
    }

    private function extractInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (! $parts || $parts[0] === '') {
            return 'DI';
        }

        if (count($parts) === 1) {
            return mb_strtoupper(mb_substr($parts[0], 0, 2));
        }

        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
    }
}

