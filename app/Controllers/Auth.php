<?php

namespace App\Controllers;

use App\Models\RememberTokenModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    private const REMEMBER_COOKIE = 'dias_remember';
    private const REMEMBER_SECONDS = 2_592_000;

    protected $helpers = ['cookie', 'form'];

    public function login(): string|RedirectResponse
    {
        if (session()->get('authenticated')) {
            return redirect()->to('/');
        }

        if ($this->restoreRememberedSession()) {
            return redirect()->to('/');
        }

        return view('auth/login');
    }

    public function loginProcess(): RedirectResponse
    {
        $throttleKey = 'login_' . hash('sha256', $this->request->getIPAddress());
        if (! service('throttler')->check($throttleKey, 5, 60)) {
            return redirect()->back()->withInput()->with('error', 'Muitas tentativas. Aguarde um minuto e tente novamente.');
        }

        $data = [
            'email' => mb_strtolower(trim((string) $this->request->getPost('email'))),
            'password' => (string) $this->request->getPost('password'),
        ];
        $rules = [
            'email' => 'required|valid_email|max_length[190]',
            'password' => 'required|max_length[255]',
        ];

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('error', 'Informe um e-mail e uma senha válidos.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->where('is_active', 1)->first();

        if ($user === null || ! password_verify($data['password'], $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'E-mail ou senha inválidos.');
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $userModel->update($user['id'], ['password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)]);
        }

        $this->startSession($user);
        $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        if ($this->request->getPost('remember') !== null) {
            $this->persistRememberToken((int) $user['id']);
        }

        return redirect()->to('/')->with('success', 'Login realizado com sucesso.');
    }

    public function refreshPermissions(): RedirectResponse
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return redirect()->to('/login');
        }

        $user = (new UserModel())->find($userId);
        if (! $user || (int) $user['is_active'] !== 1) {
            return $this->logout();
        }

        session()->set([
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'] ?? 'user',
            'user_permissions' => !empty($user['permissions']) ? json_decode($user['permissions'], true) : [],
        ]);

        return redirect()->back()->with('success', 'Permissões atualizadas com sucesso.');
    }

    public function logout(): RedirectResponse
    {
        $userId = session()->get('user_id');
        if ($userId !== null) {
            (new RememberTokenModel())->where('user_id', $userId)->delete();
        }

        session()->destroy();
        delete_cookie(self::REMEMBER_COOKIE);

        return redirect()->to('/login');
    }

    private function startSession(array $user): void
    {
        session()->regenerate(true);
        session()->set([
            'authenticated' => true,
            'user_id' => (int) $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'] ?? 'user',
            'user_permissions' => !empty($user['permissions']) ? json_decode($user['permissions'], true) : [],
        ]);
    }

    private function persistRememberToken(int $userId): void
    {
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(32));
        $expiresAt = time() + self::REMEMBER_SECONDS;

        $tokenModel = new RememberTokenModel();
        $tokenModel->where('user_id', $userId)->delete();
        $tokenModel->insert([
            'user_id' => $userId,
            'selector' => $selector,
            'token_hash' => hash('sha256', $validator),
            'expires_at' => date('Y-m-d H:i:s', $expiresAt),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        set_cookie(
            self::REMEMBER_COOKIE,
            $selector . ':' . $validator,
            self::REMEMBER_SECONDS,
            '',
            '/',
            '',
            $this->request->isSecure(),
            true,
            'Lax',
        );
    }

    private function restoreRememberedSession(): bool
    {
        $cookie = get_cookie(self::REMEMBER_COOKIE);
        if (! is_string($cookie) || ! preg_match('/^([a-f0-9]{24}):([a-f0-9]{64})$/', $cookie, $parts)) {
            return false;
        }

        $tokenModel = new RememberTokenModel();
        $token = $tokenModel->where('selector', $parts[1])->first();
        if ($token === null || strtotime($token['expires_at']) <= time()
            || ! hash_equals($token['token_hash'], hash('sha256', $parts[2]))) {
            $tokenModel->where('selector', $parts[1])->delete();
            delete_cookie(self::REMEMBER_COOKIE);
            return false;
        }

        $user = (new UserModel())->where('id', $token['user_id'])->where('is_active', 1)->first();
        if ($user === null) {
            $tokenModel->delete($token['id']);
            delete_cookie(self::REMEMBER_COOKIE);
            return false;
        }

        $tokenModel->delete($token['id']);
        $this->startSession($user);
        $this->persistRememberToken((int) $user['id']);

        return true;
    }
}
