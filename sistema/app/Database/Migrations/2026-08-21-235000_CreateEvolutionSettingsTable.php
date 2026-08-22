<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvolutionSettingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'base_url' => ['type' => 'VARCHAR', 'constraint' => 255],
            'api_key_encrypted' => ['type' => 'TEXT'],
            'min_delay_seconds' => ['type' => 'INT', 'unsigned' => true, 'default' => 5],
            'max_delay_seconds' => ['type' => 'INT', 'unsigned' => true, 'default' => 30],
            'default_instance_name' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'last_test_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'last_tested_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('evolution_settings', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('evolution_settings', true);
    }
}
