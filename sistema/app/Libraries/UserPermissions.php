<?php

namespace App\Libraries;

class UserPermissions
{
    public const MODULE_GROUPS = [
        'groups' => [
            'label' => 'GRUPOS',
            'modules' => [
                'whatsapp_groups' => [
                    'label' => 'Grupos de WhatsApp',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
            ],
        ],
        'products' => [
            'label' => 'PRODUTOS',
            'modules' => [
                'products' => [
                    'label' => 'Produtos',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'product_send' => [
                    'label' => 'Envio de Produtos',
                    'actions' => ['view', 'create'], // editar/excluir desabilitados (-)
                ],
            ],
        ],
        'leads' => [
            'label' => 'LEADS',
            'modules' => [
                'vip_leads' => [
                    'label' => 'Leads VIP',
                    'actions' => ['view', 'edit', 'delete'], // criar desabilitado (-)
                ],
            ],
        ],
        'settings' => [
            'label' => 'CONFIGURAÇÕES',
            'modules' => [
                'layout' => [
                    'label' => 'Layout',
                    'actions' => ['view', 'edit'],
                ],
                'company' => [
                    'label' => 'Empresa',
                    'actions' => ['view', 'edit'],
                ],
                'evolution' => [
                    'label' => 'Evolution API',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'message_templates' => [
                    'label' => 'Modelos de Mensagens',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'meta_ads' => [
                    'label' => 'Meta Ads',
                    'actions' => ['view', 'create', 'edit', 'delete'],
                ],
                'landing_leads' => [
                    'label' => 'Landing Page de Leads',
                    'actions' => ['view', 'edit'],
                ],
            ],
        ],
    ];

    public static function hasPermission(string $module, string $action = 'view'): bool
    {
        $role = session()->get('user_role');
        if ($role === 'admin') {
            return true;
        }

        $permissions = session()->get('user_permissions') ?? [];
        return !empty($permissions[$module][$action]);
    }
}
