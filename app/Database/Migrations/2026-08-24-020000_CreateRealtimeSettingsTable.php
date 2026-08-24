<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRealtimeSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'screen_key' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'screen_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'screen_description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'route_path' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'refresh_interval_seconds' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 5,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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

        // Inserir telas padrão
        $db = \Config\Database::connect();
        $db->table('realtime_screen_settings')->ignore(true)->insertBatch([
            [
                'screen_key' => 'whatsapp_groups',
                'screen_name' => 'Grupos de WhatsApp',
                'screen_description' => 'Atualização automática de cards, status de conexão, membros e métricas dos grupos.',
                'route_path' => 'whatsapp-grupos',
                'refresh_interval_seconds' => 5,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'screen_key' => 'products',
                'screen_name' => 'Produtos',
                'screen_description' => 'Atualização automática do catálogo, visualizações, envios e status dos produtos.',
                'route_path' => 'produtos',
                'refresh_interval_seconds' => 5,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('realtime_screen_settings', true);
    }
}
