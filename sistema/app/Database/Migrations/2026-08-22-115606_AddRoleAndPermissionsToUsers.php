<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleAndPermissionsToUsers extends Migration
{
    public function up(): void
    {
        $fields = [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default' => 'user',
                'null' => false,
                'after' => 'password_hash',
            ],
            'permissions' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'role',
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Garantir que os administradores existentes fiquem como admin
        $this->db->table('users')->where('id', 1)->update(['role' => 'admin']);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', ['role', 'permissions']);
    }
}

