<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveRealtimeSettingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->dropTable('realtime_screen_settings', true);

        // Remove chave realtime_sleep_seconds de app_settings
        $db = \Config\Database::connect();
        $db->table('app_settings')->where('setting_key', 'realtime_sleep_seconds')->delete();
    }

    public function down(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'screen_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'screen_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'screen_description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'route_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'refresh_interval_seconds' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 5,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('screen_key', 'uk_realtime_screen_key');
        $this->forge->createTable('realtime_screen_settings', true);
    }
}
