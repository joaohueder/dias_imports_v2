<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLandingLeadAccessLogsTable extends Migration
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
        $this->forge->addKey('created_at', false, false, 'idx_lead_access_logs_created');
        $this->forge->addKey('event_type', false, false, 'idx_lead_access_logs_event');
        $this->forge->addKey('visitor_id', false, false, 'idx_lead_access_logs_visitor');

        $this->forge->createTable('landing_lead_access_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('landing_lead_access_logs', true);
    }
}
