<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginProcess', ['filter' => 'csrf']);
$routes->post('logout', 'Auth::logout', ['filter' => ['auth', 'csrf']]);
$routes->post('recarregar-permissoes', 'Auth::refreshPermissions', ['filter' => ['auth', 'csrf']]);

// Landing page pública de captura de leads e submissão
$routes->get('leads', 'Landing::index');
$routes->post('leads/capture', 'Landing::submitLead', ['filter' => 'csrf']);

// Landing page pública do produto (slug)
$routes->get('p/(:segment)', 'ProductLanding::show/$1');

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Home::index');
    $routes->get('health/status', 'Health::check');

    // Grupos de WhatsApp
    $routes->get('grupos-whatsapp', 'WhatsappGroups::index', ['filter' => 'permission:whatsapp_groups,view']);
    $routes->get('grupos-whatsapp/feed', 'WhatsappGroups::feed', ['filter' => 'permission:whatsapp_groups,view']);
    $routes->get('grupos-whatsapp/evolution-list', 'WhatsappGroups::evolutionList', ['filter' => 'permission:whatsapp_groups,create']);
    $routes->post('grupos-whatsapp/salvar-selecionado', 'WhatsappGroups::saveSelected', ['filter' => ['permission:whatsapp_groups,create', 'csrf']]);
    $routes->post('grupos-whatsapp/sincronizar', 'WhatsappGroups::sync', ['filter' => ['permission:whatsapp_groups,create', 'csrf']]);
    $routes->post('grupos-whatsapp/novo', 'WhatsappGroups::create', ['filter' => ['permission:whatsapp_groups,create', 'csrf']]);
    $routes->post('grupos-whatsapp/(:num)/status', 'WhatsappGroups::toggleStatus/$1', ['filter' => ['permission:whatsapp_groups,edit', 'csrf']]);
    $routes->post('grupos-whatsapp/(:num)/atualizar-dados', 'WhatsappGroups::updateData/$1', ['filter' => ['permission:whatsapp_groups,edit', 'csrf']]);
    $routes->post('grupos-whatsapp/(:num)/excluir', 'WhatsappGroups::delete/$1', ['filter' => ['permission:whatsapp_groups,delete', 'csrf']]);

    // Produtos
    $routes->get('produtos', 'Admin\Products::index', ['filter' => 'permission:products,view']);
    $routes->get('produtos/novo', 'Admin\Products::create', ['filter' => 'permission:products,create']);
    $routes->post('produtos/novo', 'Admin\Products::store', ['filter' => ['permission:products,create', 'csrf']]);
    $routes->get('produtos/(:num)/editar', 'Admin\Products::edit/$1', ['filter' => 'permission:products,edit']);
    $routes->post('produtos/(:num)/editar', 'Admin\Products::update/$1', ['filter' => ['permission:products,edit', 'csrf']]);
    $routes->post('produtos/(:num)/status', 'Admin\Products::toggleStatus/$1', ['filter' => ['permission:products,edit', 'csrf']]);
    $routes->post('produtos/(:num)/excluir', 'Admin\Products::delete/$1', ['filter' => ['permission:products,delete', 'csrf']]);
    $routes->post('produtos/upload-imagem', 'Admin\Products::uploadImage', ['filter' => ['permission:products,edit', 'csrf']]);
    $routes->post('produtos/excluir-imagem', 'Admin\Products::deleteImage', ['filter' => ['permission:products,edit', 'csrf']]);
    $routes->post('produtos/ordenar-imagens', 'Admin\Products::reorderImages', ['filter' => ['permission:products,edit', 'csrf']]);
    $routes->post('produtos/capa-imagem', 'Admin\Products::setCoverImage', ['filter' => ['permission:products,edit', 'csrf']]);

    // Leads VIP
    $routes->get('leads-vip', 'VipLeads::index', ['filter' => 'permission:vip_leads,view']);
    $routes->get('leads-vip/feed', 'VipLeads::feed', ['filter' => 'permission:vip_leads,view']);
    $routes->post('leads-vip/(:num)/editar', 'VipLeads::update/$1', ['filter' => ['permission:vip_leads,edit', 'csrf']]);
    $routes->post('leads-vip/(:num)/excluir', 'VipLeads::delete/$1', ['filter' => ['permission:vip_leads,delete', 'csrf']]);

    // Central de Trabalho
    $routes->get('central-trabalho', 'JobCenter::index', ['filter' => 'permission:job_center,view']);
    $routes->get('central-trabalho/feed', 'JobCenter::feed', ['filter' => 'permission:job_center,view']);
    $routes->post('central-trabalho/reprocessar-falhas', 'JobCenter::retryFailed', ['filter' => ['permission:job_center,edit', 'csrf']]);
    $routes->post('central-trabalho/limpar-concluidas', 'JobCenter::clearCompleted', ['filter' => ['permission:job_center,delete', 'csrf']]);
    $routes->post('central-trabalho/executar-agora', 'JobCenter::runNow', ['filter' => ['permission:job_center,edit', 'csrf']]);
    $routes->post('central-trabalho/(:num)/executar', 'JobCenter::runSingleJob/$1', ['filter' => ['permission:job_center,edit', 'csrf']]);
    $routes->post('central-trabalho/excluir-selecionados', 'JobCenter::deleteSelected', ['filter' => ['permission:job_center,delete', 'csrf']]);
    $routes->post('central-trabalho/(:num)/excluir', 'JobCenter::deleteJob/$1', ['filter' => ['permission:job_center,delete', 'csrf']]);

    // Usuários (Apenas administradores)
    $routes->get('usuarios', 'Users::index');
    $routes->get('usuarios/novo', 'Users::create');
    $routes->post('usuarios/novo', 'Users::store', ['filter' => 'csrf']);
    $routes->get('usuarios/(:num)/editar', 'Users::edit/$1');
    $routes->post('usuarios/(:num)/editar', 'Users::update/$1', ['filter' => 'csrf']);
    $routes->post('usuarios/(:num)/redefinir-senha', 'Users::resetPassword/$1', ['filter' => 'csrf']);
    $routes->post('usuarios/(:num)/status', 'Users::toggleStatus/$1', ['filter' => 'csrf']);
    $routes->post('usuarios/(:num)/excluir', 'Users::delete/$1', ['filter' => 'csrf']);

    // Configurações gerais
    $routes->get('configuracoes', 'Home::settings');
    $routes->post('configuracoes/layout', 'Home::saveLayoutSettings', ['filter' => ['permission:layout,edit', 'csrf']]);
    $routes->post('configuracoes/empresa', 'Home::saveCompanySettings', ['filter' => ['permission:company,edit', 'csrf']]);
    $routes->post('configuracoes/empresa/whatsapp', 'Home::saveCompanyWhatsapp', ['filter' => ['permission:company,create', 'csrf']]);
    $routes->post('configuracoes/empresa/whatsapp/(:num)/padrao', 'Home::setDefaultCompanyWhatsapp/$1', ['filter' => ['permission:company,edit', 'csrf']]);
    $routes->post('configuracoes/empresa/whatsapp/(:num)/status', 'Home::toggleCompanyWhatsapp/$1', ['filter' => ['permission:company,edit', 'csrf']]);
    $routes->post('configuracoes/empresa/whatsapp/(:num)/excluir', 'Home::deleteCompanyWhatsapp/$1', ['filter' => ['permission:company,delete', 'csrf']]);
    $routes->post('configuracoes/evolution', 'Evolution::saveSettings', ['filter' => ['permission:evolution,edit', 'csrf']]);
    $routes->post('configuracoes/evolution/testar', 'Evolution::testConnection', ['filter' => ['permission:evolution,view', 'csrf']]);
    $routes->get('configuracoes/evolution/instancias/status', 'Evolution::instanceStatuses', ['filter' => 'permission:evolution,view']);
    $routes->post('configuracoes/evolution/instancias', 'Evolution::createInstance', ['filter' => ['permission:evolution,create', 'csrf']]);
    $routes->post('configuracoes/evolution/instancias/conectar', 'Evolution::connectInstance', ['filter' => ['permission:evolution,edit', 'csrf']]);
    $routes->post('configuracoes/evolution/instancias/padrao', 'Evolution::setDefaultInstance', ['filter' => ['permission:evolution,edit', 'csrf']]);
    $routes->post('configuracoes/evolution/instancias/testar-envio', 'Evolution::sendTestMessage', ['filter' => ['permission:evolution,edit', 'csrf']]);
    $routes->post('configuracoes/evolution/instancias/desconectar', 'Evolution::logoutInstance', ['filter' => ['permission:evolution,edit', 'csrf']]);
    $routes->post('configuracoes/evolution/instancias/excluir', 'Evolution::deleteInstance', ['filter' => ['permission:evolution,delete', 'csrf']]);
    $routes->post('configuracoes/meta-ads', 'MetaAds::saveSettings', ['filter' => ['permission:meta_ads,edit', 'csrf']]);
    $routes->post('configuracoes/meta-ads/testar', 'MetaAds::testConnection', ['filter' => ['permission:meta_ads,view', 'csrf']]);
    $routes->post('configuracoes/modelos-mensagens', 'Home::saveMessageTemplate', ['filter' => ['permission:message_templates,create', 'csrf']]);
    $routes->post('configuracoes/modelos-mensagens/(:num)/status', 'Home::toggleMessageTemplate/$1', ['filter' => ['permission:message_templates,edit', 'csrf']]);
    $routes->post('configuracoes/modelos-mensagens/(:num)/excluir', 'Home::deleteMessageTemplate/$1', ['filter' => ['permission:message_templates,delete', 'csrf']]);
    $routes->get('landing-leads', 'Home::landingLeadsSettings', ['filter' => 'permission:landing_leads,view']);
    $routes->post('landing-leads', 'Home::saveLandingLeadSettings', ['filter' => ['permission:landing_leads,edit', 'csrf']]);
    $routes->post('configuracoes/central-trabalho', 'JobCenter::saveJobSettings', ['filter' => ['permission:central_trabalho,edit', 'csrf']]);
});

