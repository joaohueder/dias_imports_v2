<?php

namespace App\Controllers;

use App\Libraries\EvolutionApiService;
use App\Models\AppSettingModel;
use App\Models\CompanyProfileModel;
use App\Models\CompanyWhatsappModel;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;

class Home extends BaseController
{
    private const LAYOUT_SETTING_KEY = 'layout_max_width';
    private const DEFAULT_LAYOUT_WIDTH = '1200';
    private const BRAZILIAN_STATES = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG',
        'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
    ];

    protected $helpers = ['form', 'url'];

    private const NAVIGATION = [
        'overview' => [
            'label' => 'Visão Geral',
            'mobileLabel' => 'Início',
            'path' => '/',
            'icon' => 'ti-layout-dashboard',
            'description' => 'Acesso central aos módulos administrativos.',
        ],
        'whatsapp' => [
            'label' => 'Grupos de WhatsApp',
            'mobileLabel' => 'Grupos',
            'path' => 'grupos-whatsapp',
            'icon' => 'ti-brand-whatsapp',
            'description' => 'Organização dos grupos de relacionamento.',
        ],
        'products' => [
            'label' => 'Produtos',
            'mobileLabel' => 'Produtos',
            'path' => 'produtos',
            'icon' => 'ti-package',
            'description' => 'Gerenciamento do catálogo de produtos.',
        ],
        'vip' => [
            'label' => 'Leads VIP',
            'mobileLabel' => 'Leads',
            'path' => 'leads-vip',
            'icon' => 'ti-diamond',
            'description' => 'Acompanhamento dos contatos prioritários.',
        ],
        'users' => [
            'label' => 'Usuários',
            'mobileLabel' => 'Usuários',
            'path' => 'usuarios',
            'icon' => 'ti-users',
            'description' => 'Administração dos acessos ao sistema.',
        ],
        'settings' => [
            'label' => 'Configurações',
            'mobileLabel' => 'Ajustes',
            'path' => 'configuracoes',
            'icon' => 'ti-settings',
            'description' => 'Preferências e parâmetros da plataforma.',
        ],
    ];

    public function index(): string
    {
        return $this->renderPage('overview');
    }

    public function whatsappGroups(): string
    {
        return $this->renderPage('whatsapp');
    }

    public function products(): string
    {
        return $this->renderPage('products');
    }

    public function vipLeads(): string
    {
        return $this->renderPage('vip');
    }

    public function users(): string
    {
        return $this->renderPage('users');
    }

    public function settings(): string
    {
        return $this->renderPage('settings');
    }

    public function saveLayoutSettings(): RedirectResponse
    {
        $layoutWidth = trim((string) $this->request->getPost('layout_max_width'));
        $numericWidth = filter_var($layoutWidth, FILTER_VALIDATE_INT);
        $isValid = $layoutWidth === 'fluid'
            || ($numericWidth !== false && $numericWidth >= 900 && $numericWidth <= 1800);

        if (! $isValid) {
            return redirect()->back()->withInput()->with('error', 'Escolha uma largura válida entre 900px e 1800px.');
        }

        $settingModel = new AppSettingModel();
        $setting = $settingModel->where('setting_key', self::LAYOUT_SETTING_KEY)->first();

        if ($setting === null) {
            $settingModel->insert([
                'setting_key' => self::LAYOUT_SETTING_KEY,
                'setting_value' => $layoutWidth,
            ]);
        } else {
            $settingModel->update($setting['id'], ['setting_value' => $layoutWidth]);
        }

        return redirect()->to('/configuracoes')->with('success', 'Configuração de layout salva.');
    }

    public function saveCompanySettings(): RedirectResponse
    {
        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'address' => trim((string) $this->request->getPost('address')),
            'number' => trim((string) $this->request->getPost('number')),
            'district' => trim((string) $this->request->getPost('district')),
            'city' => trim((string) $this->request->getPost('city')),
            'state' => mb_strtoupper(trim((string) $this->request->getPost('state'))),
            'public_url' => trim((string) $this->request->getPost('public_url')),
        ];
        $rules = [
            'name' => 'required|max_length[120]',
            'address' => 'required|max_length[190]',
            'number' => 'required|max_length[20]',
            'district' => 'required|max_length[100]',
            'city' => 'required|max_length[100]',
            'public_url' => 'required|max_length[255]|valid_url_strict[https]',
        ];
        $urlHost = parse_url($data['public_url'], PHP_URL_HOST);

        if (! $this->validateData($data, $rules)
            || ! in_array($data['state'], self::BRAZILIAN_STATES, true)
            || ! is_string($urlHost)
            || $urlHost === ''
            || in_array(mb_strtolower($urlHost), ['localhost', '127.0.0.1', '::1'], true)
        ) {
            return redirect()->to('/configuracoes?tab=empresa')->withInput()->with('error', 'Revise os dados da empresa e informe uma URL pública HTTPS válida.');
        }

        $model = new CompanyProfileModel();
        $profile = $model->first();
        $profile === null ? $model->insert($data) : $model->update($profile['id'], $data);

        return redirect()->to('/configuracoes?tab=empresa')->with('success', 'Dados da empresa salvos.');
    }

    public function saveCompanyWhatsapp(): RedirectResponse
    {
        $id = filter_var($this->request->getPost('whatsapp_id'), FILTER_VALIDATE_INT);
        $name = trim((string) $this->request->getPost('whatsapp_name'));
        $phone = preg_replace('/\D+/', '', (string) $this->request->getPost('whatsapp_phone')) ?? '';

        if ($name === '' || mb_strlen($name) > 80 || ! preg_match('/^(?:55)?\d{10,11}$/', $phone)) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'Informe um nome e um WhatsApp válido com DDD.');
        }

        $model = new CompanyWhatsappModel();
        $existingPhone = $model->where('phone', $phone)->first();
        if ($existingPhone !== null && ($id === false || (int) $existingPhone['id'] !== $id)) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'Este WhatsApp já está cadastrado.');
        }

        if ($id !== false) {
            $whatsapp = $model->find($id);
            if ($whatsapp === null) {
                return redirect()->to('/configuracoes?tab=empresa')->with('error', 'WhatsApp não encontrado.');
            }
            $model->update($id, ['name' => $name, 'phone' => $phone]);
        } else {
            $isFirst = $model->countAllResults() === 0;
            $model->insert(['name' => $name, 'phone' => $phone, 'is_default' => $isFirst ? 1 : 0, 'is_active' => 1]);
        }

        return redirect()->to('/configuracoes?tab=empresa')->with('success', 'WhatsApp salvo.');
    }

    public function setDefaultCompanyWhatsapp(int $id): RedirectResponse
    {
        $model = new CompanyWhatsappModel();
        $whatsapp = $model->find($id);
        if ($whatsapp === null || (int) $whatsapp['is_active'] !== 1) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'Somente um WhatsApp ativo pode ser definido como padrão.');
        }

        $db = db_connect();
        $db->transStart();
        $db->table('company_whatsapps')->set('is_default', 0)->update();
        $db->table('company_whatsapps')->where('id', $id)->update(['is_default' => 1]);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'Não foi possível atualizar o WhatsApp padrão.');
        }

        return redirect()->to('/configuracoes?tab=empresa')->with('success', 'WhatsApp padrão atualizado.');
    }

    public function toggleCompanyWhatsapp(int $id): RedirectResponse
    {
        $model = new CompanyWhatsappModel();
        $whatsapp = $model->find($id);
        if ($whatsapp === null) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'WhatsApp não encontrado.');
        }

        $newStatus = (int) $whatsapp['is_active'] === 1 ? 0 : 1;
        if ((int) $whatsapp['is_default'] === 1 && $newStatus === 0) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'Defina outro WhatsApp como padrão antes de inativar este número.');
        }
        $model->update($id, ['is_active' => $newStatus]);

        return redirect()->to('/configuracoes?tab=empresa')->with('success', $newStatus === 1 ? 'WhatsApp ativado.' : 'WhatsApp inativado.');
    }

    public function deleteCompanyWhatsapp(int $id): RedirectResponse
    {
        $model = new CompanyWhatsappModel();
        $whatsapp = $model->find($id);
        if ($whatsapp === null) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'WhatsApp não encontrado.');
        }
        if ((int) $whatsapp['is_default'] === 1) {
            return redirect()->to('/configuracoes?tab=empresa')->with('error', 'Defina outro WhatsApp como padrão antes de excluir este número.');
        }
        $model->delete($id);

        return redirect()->to('/configuracoes?tab=empresa')->with('success', 'WhatsApp excluído.');
    }

    public function saveMessageTemplate(): RedirectResponse
    {
        $id = $this->request->getPost('template_id');
        $name = trim((string) $this->request->getPost('name'));
        $content = trim((string) $this->request->getPost('content'));

        if ($name === '' || $content === '') {
            return redirect()->to('/configuracoes?tab=modelos-mensagens')->with('error', 'Preencha o nome e o conteúdo do modelo.');
        }

        $model = new \App\Models\MessageTemplateModel();
        $data = [
            'name' => $name,
            'content' => $content,
        ];

        if ($id) {
            $model->update($id, $data);
            return redirect()->to('/configuracoes?tab=modelos-mensagens')->with('success', 'Modelo atualizado com sucesso.');
        }

        $data['is_active'] = 1;
        $data['send_count'] = 0;
        $model->insert($data);

        return redirect()->to('/configuracoes?tab=modelos-mensagens')->with('success', 'Modelo criado com sucesso.');
    }

    public function toggleMessageTemplate(int $id): RedirectResponse
    {
        $model = new \App\Models\MessageTemplateModel();
        $template = $model->find($id);
        if ($template === null) {
            return redirect()->to('/configuracoes?tab=modelos-mensagens')->with('error', 'Modelo não encontrado.');
        }
        $newStatus = (int) $template['is_active'] === 1 ? 0 : 1;
        $model->update($id, ['is_active' => $newStatus]);

        return redirect()->to('/configuracoes?tab=modelos-mensagens')->with('success', $newStatus === 1 ? 'Modelo ativado.' : 'Modelo inativado.');
    }

    public function deleteMessageTemplate(int $id): RedirectResponse
    {
        $model = new \App\Models\MessageTemplateModel();
        $template = $model->find($id);
        if ($template === null) {
            return redirect()->to('/configuracoes?tab=modelos-mensagens')->with('error', 'Modelo não encontrado.');
        }
        $model->delete($id);

        return redirect()->to('/configuracoes?tab=modelos-mensagens')->with('success', 'Modelo excluído com sucesso.');
    }

    public function saveLandingLeadSettings(): RedirectResponse
    {
        $validModels = ['model-1', 'model-2', 'model-3', 'model-4', 'model-5', 'model-6'];
        $validPalettes = ['palette-aurora', 'palette-emerald', 'palette-amber', 'palette-ocean', 'palette-crimson', 'palette-obsidian'];
        $validBgAnimations = ['bg-particles', 'bg-mesh-gradient', 'bg-cyber-grid', 'bg-radial-pulse', 'bg-floating-shapes', 'bg-minimal-static'];
        $validBtnAnimations = ['btn-pulse', 'btn-shimmer', 'btn-shake', 'btn-bounce', 'btn-glow-expand', 'btn-none'];

        $templateModel = (string) $this->request->getPost('template_model');
        $colorPalette = (string) $this->request->getPost('color_palette');
        $bgAnimation = (string) $this->request->getPost('bg_animation');
        $btnAnimation = (string) $this->request->getPost('btn_animation');

        $data = [
            'template_model' => in_array($templateModel, $validModels, true) ? $templateModel : 'model-1',
            'color_palette' => in_array($colorPalette, $validPalettes, true) ? $colorPalette : 'palette-aurora',
            'bg_animation' => in_array($bgAnimation, $validBgAnimations, true) ? $bgAnimation : 'bg-particles',
            'btn_animation' => in_array($btnAnimation, $validBtnAnimations, true) ? $btnAnimation : 'btn-pulse',
            'seo_title' => trim((string) $this->request->getPost('seo_title')),
            'seo_description' => trim((string) $this->request->getPost('seo_description')),
            'headline' => trim((string) $this->request->getPost('headline')),
            'subheadline' => trim((string) $this->request->getPost('subheadline')),
            'badge_text' => trim((string) $this->request->getPost('badge_text')),
            'button_text' => trim((string) $this->request->getPost('button_text')),
            'button_subtext' => trim((string) $this->request->getPost('button_subtext')),
            'whatsapp_group_link' => trim((string) $this->request->getPost('whatsapp_group_link')),
            'card1_icon' => trim((string) $this->request->getPost('card1_icon')) ?: 'ti-discount-check',
            'card1_title' => trim((string) $this->request->getPost('card1_title')),
            'card1_desc' => trim((string) $this->request->getPost('card1_desc')),
            'card2_icon' => trim((string) $this->request->getPost('card2_icon')) ?: 'ti-flame',
            'card2_title' => trim((string) $this->request->getPost('card2_title')),
            'card2_desc' => trim((string) $this->request->getPost('card2_desc')),
            'card3_icon' => trim((string) $this->request->getPost('card3_icon')) ?: 'ti-shield-lock',
            'card3_title' => trim((string) $this->request->getPost('card3_title')),
            'card3_desc' => trim((string) $this->request->getPost('card3_desc')),
            'modal_title' => trim((string) $this->request->getPost('modal_title')),
            'modal_desc' => trim((string) $this->request->getPost('modal_desc')),
            'modal_button_text' => trim((string) $this->request->getPost('modal_button_text')),
        ];

        if ($data['headline'] === '' || $data['button_text'] === '' || $data['whatsapp_group_link'] === '') {
            return redirect()->to('/configuracoes?tab=landing-leads')->withInput()->with('error', 'Preencha o título principal, o texto do botão e o link do grupo do WhatsApp.');
        }

        $model = new \App\Models\LandingLeadSettingModel();
        $existing = $model->first();
        if ($existing === null) {
            $model->insert($data);
        } else {
            $model->update($existing['id'], $data);
        }

        return redirect()->to('/configuracoes?tab=landing-leads')->with('success', 'Configurações da Landing Page salvas com sucesso.');
    }

    private function renderPage(string $activePage): string
    {
        $item = self::NAVIGATION[$activePage];
        $userName = trim((string) session()->get('user_name')) ?: 'Usuário';
        $nameParts = preg_split('/\s+/', $userName) ?: [$userName];
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '';
        $userInitials = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1));
        $setting = (new AppSettingModel())->where('setting_key', self::LAYOUT_SETTING_KEY)->first();
        $storedLayoutWidth = $setting['setting_value'] ?? self::DEFAULT_LAYOUT_WIDTH;
        $storedNumericWidth = filter_var($storedLayoutWidth, FILTER_VALIDATE_INT);
        $layoutMaxWidth = $storedLayoutWidth === 'fluid'
            || ($storedNumericWidth !== false && $storedNumericWidth >= 900 && $storedNumericWidth <= 1800)
            ? $storedLayoutWidth
            : self::DEFAULT_LAYOUT_WIDTH;
        $view = $activePage === 'settings' ? 'admin/settings' : 'admin/page';
        $companyProfile = null;
        $companyWhatsapps = [];
        $evolutionSettings = [];
        $evolutionInstances = [];
        $evolutionLoadError = null;
        $evolutionEncryptionReady = false;
        $metaAdsSettings = [];
        $metaAdsEncryptionReady = false;
        $messageTemplates = [];
        $landingLeadSetting = null;
        $activeSettingsTab = 'layout';
        if ($activePage === 'settings') {
            $companyProfile = (new CompanyProfileModel())->first();
            $companyWhatsapps = (new CompanyWhatsappModel())->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->findAll();
            $requestedTab = (string) $this->request->getGet('tab');
            $activeSettingsTab = in_array($requestedTab, ['empresa', 'evolution', 'meta-ads', 'modelos-mensagens', 'landing-leads'], true) ? $requestedTab : 'layout';

            try {
                $messageTemplates = (new \App\Models\MessageTemplateModel())->orderBy('id', 'DESC')->findAll();
            } catch (\Throwable) {
                $messageTemplates = [];
            }

            try {
                $landingLeadSetting = (new \App\Models\LandingLeadSettingModel())->first();
            } catch (\Throwable) {
                $landingLeadSetting = null;
            }

            $evolution = new EvolutionApiService();
            $evolutionSettings = $evolution->getSettings();
            $evolutionEncryptionReady = $evolution->isEncryptionReady();
            if ($activeSettingsTab === 'evolution' && $evolution->isConfigured($evolutionSettings)) {
                try {
                    $evolutionInstances = $evolution->fetchInstances();
                } catch (RuntimeException $exception) {
                    $evolutionLoadError = $exception->getMessage();
                }
            }

            $metaAds = new \App\Libraries\MetaAdsService();
            $metaAdsSettings = $metaAds->getSettings();
            $metaAdsEncryptionReady = $metaAds->isEncryptionReady();
        }

        return view($view, [
            'activePage' => $activePage,
            'activeSettingsTab' => $activeSettingsTab,
            'brazilianStates' => self::BRAZILIAN_STATES,
            'companyProfile' => $companyProfile,
            'companyWhatsapps' => $companyWhatsapps,
            'evolutionEncryptionReady' => $evolutionEncryptionReady,
            'evolutionInstances' => $evolutionInstances,
            'evolutionLoadError' => $evolutionLoadError,
            'evolutionSettings' => $evolutionSettings,
            'firstName' => $firstName,
            'landingLeadSetting' => $landingLeadSetting,
            'layoutMaxWidth' => $layoutMaxWidth,
            'messageTemplates' => $messageTemplates,
            'metaAdsEncryptionReady' => $metaAdsEncryptionReady,
            'metaAdsSettings' => $metaAdsSettings,
            'navigation' => self::NAVIGATION,
            'pageDescription' => $item['description'],
            'pageIcon' => $item['icon'],
            'pageTitle' => $item['label'],
            'userEmail' => (string) session()->get('user_email'),
            'userInitials' => $userInitials,
            'userName' => $userName,
        ]);
    }
}
