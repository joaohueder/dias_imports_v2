<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSeoImageToLandingLeadSettings extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('seo_image', 'landing_lead_settings')) {
            $fields = [
                'seo_image' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                    'after'      => 'seo_description',
                ],
            ];

            $this->forge->addColumn('landing_lead_settings', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('landing_lead_settings', 'seo_image');
    }
}
