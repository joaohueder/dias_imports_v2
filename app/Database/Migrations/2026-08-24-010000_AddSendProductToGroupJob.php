<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSendProductToGroupJob extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('system_jobs');

        $exists = $builder->where('job_key', 'send_product_to_group')->countAllResults();
        if ($exists === 0) {
            $builder->insert([
                'job_key'           => 'send_product_to_group',
                'name'              => 'Disparar Produto para Grupos do WhatsApp',
                'description'       => 'Envia mensagens e fotos do produto para os grupos ativos do WhatsApp selecionados com intervalo randômico anti-bloqueio.',
                'is_active'         => 1,
                'min_delay_seconds' => 10,
                'max_delay_seconds' => 45,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('system_jobs')->where('job_key', 'send_product_to_group')->delete();
    }
}
