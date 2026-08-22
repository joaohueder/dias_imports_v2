<?php
$globalError = session()->getFlashdata('error');
$globalSuccess = session()->getFlashdata('success');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0c12">
    <title><?= esc($pageTitle) ?> | JH7 Marketing</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.1/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
</head>
<body>
<a class="skip-link" href="#main-content">Ir para o conteúdo</a>
<div class="app-shell" style="--layout-max-width: <?= $layoutMaxWidth === 'fluid' ? '100%' : esc($layoutMaxWidth) . 'px' ?>">
    <aside class="sidebar" aria-label="Barra lateral">
        <a class="brand" href="<?= site_url('/') ?>" aria-label="Dias Imports — Visão Geral">
            <span class="brand-mark" aria-hidden="true">DI</span>
            <span class="brand-copy">
                <span class="brand-name">Dias Imports</span>
                <span class="brand-area">Marketing</span>
            </span>
        </a>

        <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Recolher menu lateral" aria-expanded="true" title="Recolher menu lateral">
            <i class="ti ti-chevron-left" aria-hidden="true"></i>
        </button>

        <nav class="main-nav" aria-label="Navegação principal">
            <div class="nav-label">Administração</div>
            <ul class="nav-list">
                <?php foreach ($navigation as $key => $item): ?>
                    <li>
                        <a class="nav-link <?= $activePage === $key ? 'active' : '' ?>" href="<?= site_url($item['path']) ?>" data-tooltip="<?= esc($item['label']) ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?>>
                            <i class="ti <?= esc($item['icon']) ?>" aria-hidden="true"></i>
                            <span><?= esc($item['label']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <span class="avatar" aria-hidden="true"><?= esc($userInitials) ?></span>
                <span class="user-meta">
                    <span class="user-name"><?= esc($userName) ?></span>
                    <span class="user-email"><?= esc($userEmail) ?></span>
                </span>
                <form action="<?= site_url('logout') ?>" method="post" data-processing-title="Encerrando sua sessão" data-processing-message="Protegendo seus dados antes de sair.">
                    <?= csrf_field() ?>
                    <button class="logout-button" type="submit" aria-label="Sair do sistema" title="Sair">
                        <i class="ti ti-logout" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <header class="mobile-header">
        <a class="mobile-brand" href="<?= site_url('/') ?>">
            <span class="brand-mark" aria-hidden="true">DI</span>
            <span class="mobile-title"><small>Dias Imports</small><strong><?= esc($pageTitle) ?></strong></span>
        </a>
        <span class="avatar" aria-label="Usuário <?= esc($userName) ?>"><?= esc($userInitials) ?></span>
    </header>

    <div class="main-area">
        <header class="topbar">
            <div class="breadcrumb">Painel <span aria-hidden="true">/</span> <strong><?= esc($pageTitle) ?></strong></div>
            <div class="topbar-actions">
                <span class="status-pill"><span class="status-dot" aria-hidden="true"></span>Sistema online</span>
            </div>
        </header>

        <main class="page-content" id="main-content" tabindex="-1">
            <header class="page-header">
                <div>
                    <p class="eyebrow">Painel administrativo</p>
                    <h1><?= esc($pageTitle) ?></h1>
                    <p class="page-description"><?= esc($pageDescription) ?></p>
                </div>
            </header>
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="panel-footer">
            <span>&copy; <?= date('Y') ?> Dias Imports</span>
            <span>Painel Administrativo</span>
        </footer>
    </div>

    <nav class="bottom-nav" aria-label="Navegação móvel">
        <?php foreach (['overview', 'whatsapp', 'products', 'vip'] as $key): $item = $navigation[$key]; ?>
            <a class="bottom-link <?= $activePage === $key ? 'active' : '' ?>" href="<?= site_url($item['path']) ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?>>
                <i class="ti <?= esc($item['icon']) ?>" aria-hidden="true"></i><span><?= esc($item['mobileLabel']) ?></span>
            </a>
        <?php endforeach; ?>
        <button class="bottom-link <?= in_array($activePage, ['users', 'settings'], true) ? 'active' : '' ?>" type="button" data-open-more aria-haspopup="dialog">
            <i class="ti ti-dots" aria-hidden="true"></i><span>Mais</span>
        </button>
    </nav>
</div>

<div class="mobile-more" data-mobile-more aria-hidden="true">
    <section class="mobile-more-panel" role="dialog" aria-modal="true" aria-labelledby="more-title">
        <button class="icon-button" type="button" data-close-more aria-label="Fechar menu" style="position:absolute;right:14px;top:10px"><i class="ti ti-x" aria-hidden="true"></i></button>
        <div class="sheet-grabber" aria-hidden="true"></div>
        <h2 class="sheet-title" id="more-title">Mais opções</h2>
        <div class="sheet-links">
            <?php foreach (['users', 'settings'] as $key): $item = $navigation[$key]; ?>
                <a class="sheet-link <?= $activePage === $key ? 'active' : '' ?>" href="<?= site_url($item['path']) ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?>><i class="ti <?= esc($item['icon']) ?>" aria-hidden="true"></i><?= esc($item['label']) ?></a>
            <?php endforeach; ?>
        </div>
        <form class="sheet-logout" action="<?= site_url('logout') ?>" method="post" data-processing-title="Encerrando sua sessão" data-processing-message="Protegendo seus dados antes de sair.">
            <?= csrf_field() ?>
            <button class="logout-button" type="submit"><i class="ti ti-logout" aria-hidden="true"></i>Sair do sistema</button>
        </form>
    </section>
</div>

<div class="processing-screen" data-processing-screen hidden aria-hidden="true">
    <div class="processing-ambient processing-ambient-one" aria-hidden="true"></div>
    <div class="processing-ambient processing-ambient-two" aria-hidden="true"></div>
    <section class="processing-card" role="status" aria-live="polite" aria-atomic="true">
        <div class="processing-visual" aria-hidden="true">
            <span class="processing-orbit processing-orbit-one"></span>
            <span class="processing-orbit processing-orbit-two"></span>
            <span class="processing-icon"><i class="ti ti-sparkles"></i></span>
        </div>
        <p class="processing-kicker">Só um instante</p>
        <h2 data-processing-heading>Processando sua solicitação</h2>
        <p class="processing-message" data-processing-message>Estamos cuidando dos detalhes com segurança.</p>
        <div class="processing-progress" aria-hidden="true"><span></span></div>
        <p class="processing-joke"><i class="ti ti-mood-smile" aria-hidden="true"></i><span data-processing-joke>O sistema tomou um café e já está trabalhando.</span></p>
        <small>Não feche ou atualize esta página.</small>
    </section>
</div>

<?php if (is_string($globalError) && $globalError !== ''): ?>
<div class="error-dialog" data-error-dialog hidden aria-hidden="true">
    <section class="error-dialog-card" role="alertdialog" aria-modal="true" aria-labelledby="global-error-title" aria-describedby="global-error-message" tabindex="-1">
        <div class="error-dialog-icon" aria-hidden="true"><i class="ti ti-alert-triangle"></i></div>
        <p class="error-dialog-kicker">Algo não saiu como esperado</p>
        <h2 id="global-error-title">Não foi possível concluir</h2>
        <p id="global-error-message"><?= esc($globalError) ?></p>
        <button class="button danger-solid" type="button" data-close-error><i class="ti ti-x" aria-hidden="true"></i>Entendi</button>
    </section>
</div>
<?php endif; ?>

<?php if (is_string($globalSuccess) && $globalSuccess !== ''): ?>
<div class="success-dialog" data-success-dialog hidden aria-hidden="true">
    <section class="success-dialog-card" role="dialog" aria-modal="true" aria-labelledby="global-success-title" aria-describedby="global-success-message" tabindex="-1">
        <div class="success-dialog-icon" aria-hidden="true">
            <span class="success-dialog-ring" aria-hidden="true"></span>
            <i class="ti ti-mood-smile-beam"></i>
        </div>
        <p class="success-dialog-kicker">Tudo certo por aqui!</p>
        <h2 id="global-success-title">Operação realizada</h2>
        <p id="global-success-message"><?= esc($globalSuccess) ?></p>
        <button class="button success-solid" type="button" data-close-success><i class="ti ti-check" aria-hidden="true"></i>Maravilha, entendi!</button>
    </section>
</div>
<?php endif; ?>
<script src="<?= base_url('js/admin.js') ?>" defer></script>
</body>
</html>
