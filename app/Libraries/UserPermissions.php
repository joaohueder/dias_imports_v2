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
        'jobs' => [
            'label' => 'CENTRAL DE TRABALHO',
            'modules' => [
                'job_center' => [
                    'label' => 'Central de Trabalho',
                    'actions' => ['view', 'edit', 'delete'],
                ],
            ],
        ],
        'users' => [
            'label' => 'USUÁRIOS',
            'modules' => [
                'users' => [
                    'label' => 'Usuários',
                    'actions' => ['view', 'create', 'edit', 'delete'],
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
                    'actions' => ['view', 'create', 'edit', 'delete'],
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
                    'actions' => ['view', 'edit'],
                ],
                'landing_leads' => [
                    'label' => 'Landing Page de Leads',
                    'actions' => ['view', 'edit'],
                ],
                'central_trabalho' => [
                    'label' => 'Central de Trabalho',
                    'actions' => ['view', 'edit'],
                ],
            ],
        ],
    ];

    public static function hasPermission(string $module, string $action = 'view'): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE && (PHP_SAPI === 'cli' || empty($_SESSION))) {
            return true;
        }

        try {
            $session = session();
            $role = $session->get('user_role');
            if ($role === 'admin') {
                return true;
            }

            $permissions = $session->get('user_permissions') ?? [];
            return !empty($permissions[$module][$action]);
        } catch (\Throwable) {
            return true;
        }
    }

    public static function hasAnyPermissionInGroup(string $groupKey): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE && (PHP_SAPI === 'cli' || empty($_SESSION))) {
            return true;
        }

        try {
            $session = session();
            $role = $session->get('user_role');
            if ($role === 'admin') {
                return true;
            }

            $group = self::MODULE_GROUPS[$groupKey] ?? null;
            if (! $group || empty($group['modules'])) {
                return false;
            }

            foreach (array_keys($group['modules']) as $modKey) {
                if (self::hasPermission($modKey, 'view')) {
                    return true;
                }
            }

            return false;
        } catch (\Throwable) {
            return true;
        }
    }

    public static function hasAnySettingsPermission(): bool
    {
        return self::hasAnyPermissionInGroup('settings');
    }

    public static function canAccessRouteKey(string $navKey): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE && (PHP_SAPI === 'cli' || empty($_SESSION))) {
            return true;
        }

        try {
            $session = session();
            $role = $session->get('user_role');
            if ($role === 'admin') {
                return true;
            }

            return match ($navKey) {
                'overview' => true,
                'whatsapp' => self::hasPermission('whatsapp_groups', 'view'),
                'products' => self::hasPermission('products', 'view'),
                'vip' => self::hasPermission('vip_leads', 'view'),
                'users' => self::hasPermission('users', 'view'),
                'settings' => self::hasAnySettingsPermission(),
                default => false,
            };
        } catch (\Throwable) {
            return true;
        }
    }
}
