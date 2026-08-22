<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginProcess', ['filter' => 'csrf']);
$routes->post('logout', 'Auth::logout', ['filter' => ['auth', 'csrf']]);

// Landing page pública de captura de leads e submissão
$routes->get('leads', 'Landing::index');
$routes->post('leads/capture', 'Landing::submitLead', ['filter' => 'csrf']);

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Home::index');
    $routes->get('grupos-whatsapp', 'Home::whatsappGroups');
    $routes->get('produtos', 'Home::products');
    $routes->get('leads-vip', 'Home::vipLeads');
    $routes->get('usuarios', 'Home::users');
    $routes->get('configuracoes', 'Home::settings');
    $routes->post('configuracoes/layout', 'Home::saveLayoutSettings', ['filter' => 'csrf']);
    $routes->post('configuracoes/empresa', 'Home::saveCompanySettings', ['filter' => 'csrf']);
    $routes->post('configuracoes/empresa/whatsapp', 'Home::saveCompanyWhatsapp', ['filter' => 'csrf']);
    $routes->post('configuracoes/empresa/whatsapp/(:num)/padrao', 'Home::setDefaultCompanyWhatsapp/$1', ['filter' => 'csrf']);
    $routes->post('configuracoes/empresa/whatsapp/(:num)/status', 'Home::toggleCompanyWhatsapp/$1', ['filter' => 'csrf']);
    $routes->post('configuracoes/empresa/whatsapp/(:num)/excluir', 'Home::deleteCompanyWhatsapp/$1', ['filter' => 'csrf']);
    $routes->post('configuracoes/evolution', 'Evolution::saveSettings', ['filter' => 'csrf']);
    $routes->post('configuracoes/evolution/testar', 'Evolution::testConnection', ['filter' => 'csrf']);
    $routes->get('configuracoes/evolution/instancias/status', 'Evolution::instanceStatuses');
    $routes->post('configuracoes/evolution/instancias', 'Evolution::createInstance', ['filter' => 'csrf']);
    $routes->post('configuracoes/evolution/instancias/conectar', 'Evolution::connectInstance', ['filter' => 'csrf']);
    $routes->post('configuracoes/evolution/instancias/padrao', 'Evolution::setDefaultInstance', ['filter' => 'csrf']);
    $routes->post('configuracoes/evolution/instancias/testar-envio', 'Evolution::sendTestMessage', ['filter' => 'csrf']);
    $routes->post('configuracoes/evolution/instancias/desconectar', 'Evolution::logoutInstance', ['filter' => 'csrf']);
    $routes->post('configuracoes/evolution/instancias/excluir', 'Evolution::deleteInstance', ['filter' => 'csrf']);
    $routes->post('configuracoes/meta-ads', 'MetaAds::saveSettings', ['filter' => 'csrf']);
    $routes->post('configuracoes/meta-ads/testar', 'MetaAds::testConnection', ['filter' => 'csrf']);
    $routes->post('configuracoes/modelos-mensagens', 'Home::saveMessageTemplate', ['filter' => 'csrf']);
    $routes->post('configuracoes/modelos-mensagens/(:num)/status', 'Home::toggleMessageTemplate/$1', ['filter' => 'csrf']);
    $routes->post('configuracoes/modelos-mensagens/(:num)/excluir', 'Home::deleteMessageTemplate/$1', ['filter' => 'csrf']);
    $routes->post('configuracoes/landing-leads', 'Home::saveLandingLeadSettings', ['filter' => 'csrf']);
});

