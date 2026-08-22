<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetaAdsSettingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'pixel_id' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => ''],
            'access_token_encrypted' => ['type' => 'TEXT'],
            'test_event_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'last_test_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'last_tested_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('meta_ads_settings', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('meta_ads_settings', true);
    }
}
