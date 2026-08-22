<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessageTemplatesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'send_count' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
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
        $this->forge->createTable('message_templates', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('message_templates', true);
    }
}
