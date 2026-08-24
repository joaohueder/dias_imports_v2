<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductAccessLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'visitor_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pageview',
            ],
            'utm_source' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'utm_medium' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'utm_campaign' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'referrer' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'device_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'mobile',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['product_id', 'created_at'], false, false, 'idx_product_access_logs_prod_created');
        $this->forge->addKey(['product_id', 'event_type'], false, false, 'idx_product_access_logs_prod_event');
        $this->forge->addKey(['product_id', 'visitor_id'], false, false, 'idx_product_access_logs_prod_visitor');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE', 'fk_product_access_logs_product_id');

        $this->forge->createTable('product_access_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_access_logs', true);
    }
}
