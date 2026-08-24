<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppSettingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 100],
            'setting_value' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('app_settings', true);

        $exists = $this->db->table('app_settings')->where('setting_key', 'layout_max_width')->countAllResults();
        if ($exists === 0) {
            $this->db->table('app_settings')->insert([
                'setting_key' => 'layout_max_width',
                'setting_value' => '1200',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('app_settings', true);
    }
}
