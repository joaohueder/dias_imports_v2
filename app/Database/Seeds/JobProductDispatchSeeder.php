<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JobProductDispatchSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('system_jobs');

        $exists = $builder->where('job_key', 'send_product_to_group')->countAllResults();
        if ($exists === 0) {
            $builder->insert([
                'job_key'           => 'send_product_to_group',
                'name'              => 'Disparar Produtos para Grupos do WhatsApp',
                'description'       => 'Envia mensagens e fotos do produto para os grupos ativos do WhatsApp selecionados com intervalo randômico anti-bloqueio.',
                'is_active'         => 1,
                'min_delay_seconds' => 10,
                'max_delay_seconds' => 45,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);
            echo "Job 'send_product_to_group' inserido com sucesso!\n";
        } else {
            echo "Job 'send_product_to_group' já existe no banco.\n";
        }
    }
}

