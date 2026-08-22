<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php if ($activePage === 'overview'): ?>
    <section class="welcome-panel" aria-labelledby="welcome-title">
        <div>
            <p class="eyebrow">Central de operação</p>
            <h2 class="welcome-title" id="welcome-title">Olá, <?= esc($firstName) ?>.</h2>
            <p class="welcome-text">Acesse os módulos do marketing da Dias Imports em um só lugar.</p>
        </div>
        <div class="welcome-icon" aria-hidden="true"><i class="ti ti-sparkles"></i></div>
    </section>

    <div class="section-heading">
        <h2>Módulos do sistema</h2>
        <span>Selecione para acessar</span>
    </div>
    <section class="module-grid" aria-label="Módulos administrativos">
        <?php foreach ($navigation as $key => $item): ?>
            <?php if ($key === 'overview') continue; ?>
            <?php if (! \App\Libraries\UserPermissions::canAccessRouteKey($key)) continue; ?>
            <a class="module-card" href="<?= site_url($item['path']) ?>">
                <div class="module-card-top">
                    <span class="module-icon" aria-hidden="true"><i class="ti <?= esc($item['icon']) ?>"></i></span>
                    <i class="ti ti-arrow-right module-arrow" aria-hidden="true"></i>
                </div>
                <h3><?= esc($item['label']) ?></h3>
                <p><?= esc($item['description']) ?></p>
            </a>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <section class="empty-state" aria-labelledby="module-state-title">
        <div class="empty-state-inner">
            <div class="empty-icon" aria-hidden="true"><i class="ti <?= esc($pageIcon) ?>"></i></div>
            <h2 id="module-state-title"><?= esc($pageTitle) ?></h2>
            <p><?= esc($pageDescription) ?> A estrutura do módulo está pronta para receber suas funcionalidades.</p>
            <span class="empty-badge">Módulo em preparação</span>
        </div>
    </section>
<?php endif; ?>
<?= $this->endSection() ?>
