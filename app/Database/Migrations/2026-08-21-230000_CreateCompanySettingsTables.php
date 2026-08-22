<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanySettingsTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'address' => ['type' => 'VARCHAR', 'constraint' => 190],
            'number' => ['type' => 'VARCHAR', 'constraint' => 20],
            'district' => ['type' => 'VARCHAR', 'constraint' => 100],
            'city' => ['type' => 'VARCHAR', 'constraint' => 100],
            'state' => ['type' => 'CHAR', 'constraint' => 2],
            'public_url' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('company_profile', true);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 80],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20],
            'is_default' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('phone');
        $this->forge->createTable('company_whatsapps', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('company_whatsapps', true);
        $this->forge->dropTable('company_profile', true);
    }
}
