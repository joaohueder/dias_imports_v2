<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLandingLeadSettingsAndLeadsTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'headline' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'Garanta Descontos Secretos e Ofertas VIP no WhatsApp',
            ],
            'subheadline' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'badge_text' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => '🔥 GRUPO VIP EXCLUSIVO • VAGAS LIMITADAS',
            ],
            'button_text' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'QUERO MEU ACESSO VIP AGORA',
            ],
            'button_subtext' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'default' => '🔒 Acesso 100% gratuito e sem spam',
            ],
            'whatsapp_group_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'https://chat.whatsapp.com/',
            ],
            'card1_icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'ti-discount-check',
            ],
            'card1_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Até 50% de Desconto Real',
            ],
            'card1_desc' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'Preços exclusivos de atacado e varejo direto para membros do grupo.',
            ],
            'card2_icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'ti-flame',
            ],
            'card2_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Ofertas Relâmpago e Primeira Mão',
            ],
            'card2_desc' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'Novidades e lançamentos liberados no grupo antes de todo mundo.',
            ],
            'card3_icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'ti-shield-lock',
            ],
            'card3_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => '100% Original e com Garantia',
            ],
            'card3_desc' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'Importados com nota fiscal, procedência garantida e suporte humanizado.',
            ],
            'modal_title' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'default' => '🎉 Parabéns! Seu Acesso VIP Está Liberado',
            ],
            'modal_desc' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'modal_button_text' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'ENTRAR NO GRUPO VIP DO WHATSAPP',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('landing_lead_settings', true);

        // Inserir registro padrão se não existir
        $db = \Config\Database::connect();
        $builder = $db->table('landing_lead_settings');
        if ($builder->countAllResults() === 0) {
            $builder->insert([
                'id' => 1,
                'headline' => 'Garanta Descontos Secretos e Ofertas VIP no WhatsApp',
                'subheadline' => 'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.',
                'badge_text' => '🔥 GRUPO VIP EXCLUSIVO • VAGAS LIMITADAS',
                'button_text' => 'QUERO MEU ACESSO VIP AGORA',
                'button_subtext' => '🔒 Acesso 100% gratuito e sem spam',
                'whatsapp_group_link' => 'https://chat.whatsapp.com/',
                'card1_icon' => 'ti-discount-check',
                'card1_title' => 'Até 50% de Desconto Real',
                'card1_desc' => 'Preços exclusivos de atacado e varejo direto para membros do grupo.',
                'card2_icon' => 'ti-flame',
                'card2_title' => 'Ofertas Relâmpago e Primeira Mão',
                'card2_desc' => 'Novidades e lançamentos liberados no grupo antes de todo mundo.',
                'card3_icon' => 'ti-shield-lock',
                'card3_title' => '100% Original e com Garantia',
                'card3_desc' => 'Importados com nota fiscal, procedência garantida e suporte humanizado.',
                'modal_title' => '🎉 Parabéns! Seu Acesso VIP Está Liberado',
                'modal_desc' => 'Seu cadastro foi realizado com sucesso. Clique no botão abaixo para entrar agora no Grupo VIP Oficial no WhatsApp e não perder nenhuma oportunidade.',
                'modal_button_text' => 'ENTRAR NO GRUPO VIP DO WHATSAPP',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Tabela de Leads
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('phone', false, false, 'leads_phone_index');
        $this->forge->createTable('leads', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('leads', true);
        $this->forge->dropTable('landing_lead_settings', true);
    }
}
