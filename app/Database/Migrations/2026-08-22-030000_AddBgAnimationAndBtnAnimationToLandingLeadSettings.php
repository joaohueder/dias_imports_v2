<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBgAnimationAndBtnAnimationToLandingLeadSettings extends Migration
{
    public function up(): void
    {
        $fields = [
            'bg_animation' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'bg-particles',
                'after'      => 'color_palette',
            ],
            'btn_animation' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'btn-pulse',
                'after'      => 'bg_animation',
            ],
        ];

        $this->forge->addColumn('landing_lead_settings', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('landing_lead_settings', ['bg_animation', 'btn_animation']);
    }
}
