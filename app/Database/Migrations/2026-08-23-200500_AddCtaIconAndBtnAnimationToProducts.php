<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCtaIconAndBtnAnimationToProducts extends Migration
{
    public function up()
    {
        $fields = [];
        
        // Verifica se btn_animation existe
        if (!$this->db->fieldExists('btn_animation', 'products')) {
            $fields['btn_animation'] = [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'default'    => 'btn-pulse',
                'after'      => 'color_palette',
            ];
        }

        // Verifica se cta_icon existe
        if (!$this->db->fieldExists('cta_icon', 'products')) {
            $fields['cta_icon'] = [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'default'    => 'ti-brand-whatsapp',
                'after'      => 'button_text',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('products', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('cta_icon', 'products')) {
            $this->forge->dropColumn('products', 'cta_icon');
        }
    }
}
