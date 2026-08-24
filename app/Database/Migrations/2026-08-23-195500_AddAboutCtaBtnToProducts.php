<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAboutCtaBtnToProducts extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('about_cta_btn', 'products')) {
            $fields = [
                'about_cta_btn' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                    'default' => 'Garantir Meu Frasco no WhatsApp',
                    'after' => 'about_content'
                ],
            ];

            $this->forge->addColumn('products', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('about_cta_btn', 'products')) {
            $this->forge->dropColumn('products', 'about_cta_btn');
        }
    }
}
