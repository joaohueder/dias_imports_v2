<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetAdminPassword extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'user:reset-admin';
    protected $description = 'Resets admin password to 123456';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $db->table('users')->where('email', 'joaohueder@gmail.com')->update([
            'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
            'role' => 'admin',
            'is_active' => 1
        ]);
        CLI::write('Admin password set to 123456', 'green');
    }
}
