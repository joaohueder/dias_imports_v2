<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) getenv('INITIAL_ADMIN_NAME'));
        $email = mb_strtolower(trim((string) getenv('INITIAL_ADMIN_EMAIL')));
        $password = (string) getenv('INITIAL_ADMIN_PASSWORD');

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            throw new RuntimeException('Defina nome, e-mail válido e senha com ao menos 6 caracteres.');
        }

        $model = new UserModel();
        if ($model->where('email', $email)->first() !== null) {
            throw new RuntimeException('Já existe um usuário com este e-mail.');
        }

        $model->insert([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);
    }
}
