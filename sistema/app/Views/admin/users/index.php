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
        <?php foreach ($users as $u): ?>
            <?php
                $isSelf = ((int) $u['id'] === $currentUserId);
                $isAdmin = (($u['role'] ?? 'user') === 'admin');
                $isActive = ((int) $u['is_active'] === 1);
                $initials = $helperInitials($u['name']);
                $createdAtFormatted = !empty($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '--/--/----';

                // Permissões resumo
                $permissionsSummary = 'Acesso personalizado ao painel.';
                if ($isAdmin) {
                    $permissionsSummary = 'Acesso total ao painel.';
                } else {
                    $perms = !empty($u['permissions']) ? json_decode($u['permissions'], true) : [];
                    if (empty($perms)) {
                        $permissionsSummary = 'Apenas áreas públicas e perfil.';
                    } else {
                        $countPerms = 0;
                        foreach ($perms as $p) {
                            $countPerms += count($p);
                        }
                        $permissionsSummary = "Acesso configurado a {$countPerms} recurso(s).";
                    }
                }
            ?>
            <article class="user-card-item <?= ! $isActive ? 'is-inactive' : '' ?>"
                     data-user-card
                     data-id="<?= esc($u['id']) ?>"
                     data-name="<?= esc(mb_strtolower($u['name'])) ?>"
                     data-email="<?= esc(mb_strtolower($u['email'])) ?>"
                     data-status="<?= $isActive ? 'active' : 'inactive' ?>"
                     data-role="<?= $isAdmin ? 'admin' : 'user' ?>">

                <div class="user-card-header">
                    <div class="user-avatar-circle <?= $isAdmin ? 'is-admin' : '' ?>">
                        <?= esc($initials) ?>
                    </div>
                    <div class="user-title-block">
                        <div class="user-name-row">
                            <h3 class="user-full-name"><?= esc($u['name']) ?></h3>
                            <?php if ($isSelf): ?>
                                <span class="badge-self">você</span>
                            <?php endif; ?>
                        </div>
                        <span class="user-email-text"><?= esc($u['email']) ?></span>
                    </div>
                </div>

                <div class="user-badges-row">
                    <?php if ($isAdmin): ?>
                        <span class="status-chip chip-role-admin">Administrador</span>
                    <?php else: ?>
                        <span class="status-chip chip-role-user">Usuário</span>
                    <?php endif; ?>

                    <?php if ($isActive): ?>
                        <span class="status-chip chip-active">Ativo</span>
                    <?php else: ?>
                        <span class="status-chip chip-inactive">Inativo</span>
                    <?php endif; ?>
                </div>

                <div class="user-permissions-block">
                    <span class="perm-title">PERMISSÕES</span>
                    <p class="perm-desc"><?= esc($permissionsSummary) ?></p>
                </div>

                <div class="user-footer-meta">
                    <span class="user-created-date">Cadastrado em <?= esc($createdAtFormatted) ?></span>
                </div>

                <div class="user-card-actions">
                    <?php if (\App\Libraries\UserPermissions::hasPermission('users', 'edit')): ?>
                        <a href="<?= site_url('usuarios/' . $u['id'] . '/editar') ?>" class="btn-card-action">
                            <i class="ti ti-pencil"></i>
                            <span>Editar</span>
                        </a>

                        <button type="button" class="btn-card-action" data-open-reset-pwd data-user-id="<?= esc($u['id']) ?>" data-user-name="<?= esc($u['name']) ?>">
                            <i class="ti ti-key"></i>
                            <span>Redefinir Senha</span>
                        </button>

                        <?php if (! $isSelf): ?>
                            <form action="<?= site_url('usuarios/' . $u['id'] . '/status') ?>" method="post" data-confirm-action="user-status-<?= $isActive ? 'inativar' : 'ativar' ?>" data-action-name="<?= esc($u['name']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-card-action">
                                    <i class="ti ti-power"></i>
                                    <span><?= $isActive ? 'Inativar' : 'Ativar' ?></span>
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn-card-action disabled" disabled title="Não é possível alterar seu próprio status">
                                <i class="ti ti-power"></i>
                                <span>Inativar</span>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (\App\Libraries\UserPermissions::hasPermission('users', 'delete')): ?>
                        <?php if (! $isSelf): ?>
                            <form action="<?= site_url('usuarios/' . $u['id'] . '/excluir') ?>" method="post" data-confirm-action="user-delete" data-action-name="<?= esc($u['name']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-card-action text-danger">
                                    <i class="ti ti-trash"></i>
                                    <span>Excluir</span>
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn-card-action disabled" disabled title="Não é possível excluir seu próprio usuário">
                                <i class="ti ti-trash"></i>
                                <span>Excluir</span>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Empty search state -->
    <div class="users-empty-search" data-users-empty-search style="display: none;">
        <div class="empty-icon"><i class="ti ti-user-search"></i></div>
        <h3>Nenhum usuário encontrado</h3>
        <p>Tente ajustar os filtros ou os termos digitados na busca.</p>
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
