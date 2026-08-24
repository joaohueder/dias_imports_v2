<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFaqAndFeaturesToProducts extends Migration
{
    public function up()
    {
        $fields = [
            'shipping_info' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => 'Entrega rápida em Barretos e região',
                'after' => 'benefits'
            ],
            'payment_info' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => 'Pagamento no PIX ou cartão',
                'after' => 'shipping_info'
            ],
            'guarantee_info' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => 'Produto conferido antes do envio',
                'after' => 'payment_info'
            ],
            'about_title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => 'Sobre o produto',
                'after' => 'guarantee_info'
            ],
            'about_content' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'about_title'
            ],
            'faq' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'about_content'
            ],
            'checkout_title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => 'Fechar pedido pelo WhatsApp',
                'after' => 'faq'
            ],
            'checkout_subtitle' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => 'A conversa abre com o produto e o preço já escritos. Você só confirma.',
                'after' => 'checkout_title'
            ]
        ];

        $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', [
            'shipping_info',
            'payment_info',
            'guarantee_info',
            'about_title',
            'about_content',
            'faq',
            'checkout_title',
            'checkout_subtitle'
        ]);
    }
}

