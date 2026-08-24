<?php
$helperInitials = static function(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    if (!$parts || $parts[0] === '') return 'US';
    if (count($parts) === 1) return mb_strtoupper(mb_substr($parts[0], 0, 2));
    return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
};
?>
<?php foreach ($users as $u): ?>
    <?php
        $isSelf = ((int) $u['id'] === (int) ($currentUserId ?? 0));
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
                    <i class="ti ti-edit" aria-hidden="true"></i>
                    <span>Editar</span>
                </a>
                <form action="<?= site_url('usuarios/' . $u['id'] . '/status') ?>" method="post" data-confirm-action="<?= $isActive ? 'user-deactivate' : 'user-activate' ?>" data-user-name="<?= esc($u['name']) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-card-action">
                        <i class="ti <?= $isActive ? 'ti-user-off' : 'ti-user-check' ?>" aria-hidden="true"></i>
                        <span><?= $isActive ? 'Inativar' : 'Ativar' ?></span>
                    </button>
                </form>
            <?php endif; ?>
            <?php if (! $isSelf && \App\Libraries\UserPermissions::hasPermission('users', 'delete')): ?>
                <form action="<?= site_url('usuarios/' . $u['id'] . '/excluir') ?>" method="post" data-confirm-action="user-delete" data-user-name="<?= esc($u['name']) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-card-action is-danger">
                        <i class="ti ti-trash" aria-hidden="true"></i>
                        <span>Excluir</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </article>
<?php endforeach; ?>
