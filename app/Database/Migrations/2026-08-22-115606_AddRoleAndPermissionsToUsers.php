<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleAndPermissionsToUsers extends Migration
{
    public function up(): void
    {
        $fields = [];
        if (!$this->db->fieldExists('role', 'users')) {
            $fields['role'] = [
                'type' => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default' => 'user',
                'null' => false,
                'after' => 'password_hash',
            ];
        }
        if (!$this->db->fieldExists('permissions', 'users')) {
            $fields['permissions'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'role',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('users', $fields);
        }

        // Garantir que os administradores existentes fiquem como admin
        $this->db->table('users')->where('id', 1)->update(['role' => 'admin']);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', ['role', 'permissions']);
    }
}

