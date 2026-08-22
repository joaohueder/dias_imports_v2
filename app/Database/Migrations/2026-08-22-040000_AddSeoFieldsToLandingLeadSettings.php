<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSeoFieldsToLandingLeadSettings extends Migration
{
    public function up()
    {
        $fields = [
            'seo_title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'default'    => 'Grupo VIP Dias Imports | Ofertas e Descontos Exclusivos',
                'after'      => 'btn_animation',
            ],
            'seo_description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'seo_title',
            ],
        ];

        $this->forge->addColumn('landing_lead_settings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('landing_lead_settings', ['seo_title', 'seo_description']);
    }
}
