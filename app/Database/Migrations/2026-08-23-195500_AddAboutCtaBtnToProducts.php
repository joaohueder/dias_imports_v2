<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAboutCtaBtnToProducts extends Migration
{
    public function up()
    {
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

    public function down()
    {
        $this->forge->dropColumn('products', 'about_cta_btn');
    }
}
