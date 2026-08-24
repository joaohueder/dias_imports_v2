<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDbConnectionLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at', false, false, 'idx_db_conn_created');
        $this->forge->createTable('db_connection_logs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('db_connection_logs', true);
    }
}
