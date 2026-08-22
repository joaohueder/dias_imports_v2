<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobCenterTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'job_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'min_delay_seconds' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 5,
            ],
            'max_delay_seconds' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 20,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('job_key');
        $this->forge->createTable('system_jobs', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'job_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'item_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
                'null'       => true,
            ],
            'payload' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'completed', 'failed'],
                'default'    => 'pending',
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'attempts' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'scheduled_at'], false, false, 'idx_status_scheduled');
        $this->forge->addKey('job_key', false, false, 'idx_job_key');
        $this->forge->addKey('item_reference', false, false, 'idx_item_reference');
        $this->forge->createTable('system_job_queue', true);

        // Preencher registro inicial
        $db = \Config\Database::connect();
        $builder = $db->table('system_jobs');
        $exists = $builder->where('job_key', 'sync_whatsapp_groups')->countAllResults();
        if ($exists === 0) {
            $now = date('Y-m-d H:i:s');
            $builder->insert([
                'job_key'           => 'sync_whatsapp_groups',
                'name'              => 'Atualizar Todos os Grupos do WhatsApp',
                'description'       => 'Busca informações detalhadas, participantes e foto de cada grupo do WhatsApp cadastrado no sistema via Evolution API.',
                'is_active'         => 1,
                'min_delay_seconds' => 5,
                'max_delay_seconds' => 20,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('system_job_queue', true);
        $this->forge->dropTable('system_jobs', true);
    }
}
