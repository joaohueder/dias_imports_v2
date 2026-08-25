<?php

namespace App\Controllers;

use App\Libraries\MetaAdsService;
use App\Models\CompanyProfileModel;
use App\Models\LandingLeadSettingModel;
use App\Models\LeadModel;
use App\Models\MetaAdsSettingModel;
use CodeIgniter\HTTP\ResponseInterface;

class Landing extends BaseController
{
    public function index(): string
    {
        $metaAds = (new MetaAdsSettingModel())->first();
        $settingModel = new LandingLeadSettingModel();
        $landing = $settingModel->first() ?? [
            'template_model' => 'model-1',
            'color_palette' => 'palette-aurora',
            'bg_animation' => 'bg-particles',
            'btn_animation' => 'btn-pulse',
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
        ];

        $company = (new CompanyProfileModel())->first();

        // Registra visualização de página diretamente no acesso ao /leads
        try {
            $logModel = new \App\Models\ProductAccessLogModel();
            $logModel->recordEvent(0, 'pageview');
        } catch (\Throwable $e) {
            log_message('error', 'Erro ao gravar visualização de página na landing de leads: ' . $e->getMessage());
        }

        return view('landing/leads', [
            'landing' => $landing,
            'company' => $company,
            'metaAds' => $metaAds,
        ]);
    }

    public function recordPageView(): ResponseInterface
    {
        try {
            $logModel = new \App\Models\ProductAccessLogModel();
            $logModel->recordEvent(0, 'pageview');
            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            log_message('error', 'Erro ao gravar visualização de página na rota leads/pageview: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function submitLead(): ResponseInterface
    {
        $name = trim((string) $this->request->getPost('name'));
        $rawPhone = (string) $this->request->getPost('phone');
        $phone = preg_replace('/\D+/', '', $rawPhone) ?? '';

        if ($name === '' || mb_strlen($name) > 120) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Por favor, informe seu nome completo.',
            ])->setStatusCode(400);
        }

        if (! preg_match('/^(?:55)?\d{10,11}$/', $phone)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Informe um número de WhatsApp válido com DDD.',
            ])->setStatusCode(400);
        }

        $ip = $this->request->getIPAddress();
        $ua = (string) $this->request->getUserAgent();

        $leadModel = new LeadModel();
        $leadModel->insert([
            'name' => $name,
            'phone' => $phone,
            'ip_address' => $ip,
            'user_agent' => mb_substr($ua, 0, 255),
        ]);

        // Envia Conversions API do Meta Ads em background / servidor para garantir entrega
        try {
            $metaService = new MetaAdsService();
            $metaService->sendLead([
                'name' => $name,
                'phone' => $phone,
                'ip' => $ip,
                'user_agent' => $ua,
                'url' => (string) current_url(),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao despachar evento Lead Meta Conversions API: ' . $e->getMessage());
        }

        $settingModel = new LandingLeadSettingModel();
        $landing = $settingModel->first();
        $groupLink = $landing['whatsapp_group_link'] ?? 'https://chat.whatsapp.com/';

        return $this->response->setJSON([
            'success' => true,
            'group_link' => $groupLink,
            'modal_title' => $landing['modal_title'] ?? '🎉 Parabéns! Seu Acesso VIP Está Liberado',
            'modal_desc' => $landing['modal_desc'] ?? 'Seu cadastro foi realizado com sucesso. Clique no botão abaixo para entrar agora no Grupo VIP Oficial no WhatsApp e não perder nenhuma oportunidade.',
            'modal_button_text' => $landing['modal_button_text'] ?? 'ENTRAR NO GRUPO VIP DO WHATSAPP',
        ]);
    }
}
