<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTemplateModelAndColorPaletteToLandingLeadSettings extends Migration
{
    public function up(): void
    {
        $fields = [
            'template_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'model-1',
                'after'      => 'id',
            ],
            'color_palette' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'palette-aurora',
                'after'      => 'template_model',
            ],
        ];

        $this->forge->addColumn('landing_lead_settings', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('landing_lead_settings', ['template_model', 'color_palette']);
    }
}
