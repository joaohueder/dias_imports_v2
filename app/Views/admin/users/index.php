<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$helperInitials = static function(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    if (!$parts || $parts[0] === '') return 'US';
    if (count($parts) === 1) return mb_strtoupper(mb_substr($parts[0], 0, 2));
    return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
};
?>
<section class="users-view-container" data-users-module>
    <!-- Top toolbar -->
    <div class="users-header-actions">
        <div class="users-search-box">
            <i class="ti ti-search users-search-icon" aria-hidden="true"></i>
            <input type="text" id="users-search-input" class="users-search-input" placeholder="Filtrar por nome ou e-mail..." aria-label="Filtrar usuários por nome ou e-mail" data-users-filter-input>
        </div>

        <div class="users-filter-pills" role="tablist" aria-label="Filtros de status de usuários">
            <button type="button" class="filter-pill active" data-filter="all" role="tab" aria-selected="true">
                Todos <span class="pill-badge"><?= $counts['total'] ?></span>
            </button>
            <button type="button" class="filter-pill" data-filter="active" role="tab" aria-selected="false">
                Ativos <span class="pill-badge"><?= $counts['active'] ?></span>
            </button>
            <button type="button" class="filter-pill" data-filter="inactive" role="tab" aria-selected="false">
                Inativos <span class="pill-badge"><?= $counts['inactive'] ?></span>
            </button>
            <button type="button" class="filter-pill" data-filter="admin" role="tab" aria-selected="false">
                Admins <span class="pill-badge"><?= $counts['admin'] ?></span>
            </button>
        </div>

        <?php if (\App\Libraries\UserPermissions::hasPermission('users', 'create')): ?>
        <div class="users-create-action">
            <a href="<?= site_url('usuarios/novo') ?>" class="button primary">
                <i class="ti ti-user-plus" aria-hidden="true"></i>
                <span>Novo Usuário</span>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cards Grid -->
    <div class="users-grid" data-users-grid>
        <?= view('admin/users/_cards', ['users' => $users, 'currentUserId' => $currentUserId]) ?>
    </div>

    <!-- Empty search state -->
    <div class="users-empty-search" data-users-empty-search style="display: none;">
        <div class="empty-icon"><i class="ti ti-user-search"></i></div>
        <h3>Nenhum usuário encontrado</h3>
        <p>Tente ajustar os filtros ou os termos digitados na busca.</p>
        <button type="button" class="button secondary" data-users-clear-filters style="margin-top: 12px;">
            <i class="ti ti-filter-off" aria-hidden="true"></i>
            <span>Limpar Filtros</span>
        </button>
    </div>
</section>

<!-- Modal Redefinir Senha -->
<dialog class="modal-dialog-pwd" data-reset-pwd-dialog hidden>
    <div class="modal-dialog-card">
        <div class="modal-icon-header">
            <i class="ti ti-key"></i>
        </div>
        <h3 class="modal-title">Redefinir Senha</h3>
        <p class="modal-subtitle">Digite a nova senha para <strong data-pwd-user-name></strong>.</p>

        <form method="post" data-pwd-form action="">
            <?= csrf_field() ?>
            <div class="pwd-field-group">
                <label for="new_password_input">Nova Senha (mínimo 6 caracteres)</label>
                <div class="input-with-toggle">
                    <input type="password" id="new_password_input" name="new_password" required minlength="6" autocomplete="new-password" placeholder="••••••••">
                    <button type="button" class="toggle-pwd-btn" data-toggle-pwd><i class="ti ti-eye"></i></button>
                </div>
            </div>

            <div class="modal-actions-row">
                <button type="button" class="button secondary" data-close-reset-pwd>Cancelar</button>
                <button type="submit" class="button primary">Salvar Nova Senha</button>
            </div>
        </form>
    </div>
</dialog>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/users.js?v=' . (defined('APP_VERSION') ? APP_VERSION : time())) ?>" defer></script>
<?= $this->endSection() ?>
