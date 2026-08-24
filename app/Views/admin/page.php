<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php if ($activePage === 'overview'): ?>
    <?= $this->include('admin/overview') ?>
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
