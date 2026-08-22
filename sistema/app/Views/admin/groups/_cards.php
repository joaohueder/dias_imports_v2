<?php if (empty($groups)): ?>
    <div class="users-empty-search col-span-full" style="padding: 48px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; color: rgb(var(--muted));">
        <div class="empty-icon" style="margin-bottom: 4px;">
            <i class="ti ti-inbox" style="font-size: 36px;"></i>
        </div>
        <p style="margin: 0; font-size: 14px; font-weight: 600;">Nenhum grupo encontrado para os filtros selecionados.</p>
        <span style="font-size: 14px;">Novos grupos sincronizados do WhatsApp aparecerão aqui automaticamente.</span>
    </div>
<?php else: ?>
    <?php foreach ($groups as $group): ?>
        <article class="user-card-item group-item-card <?= $group['status'] === 'inactive' ? 'is-inactive' : '' ?>" data-id="<?= esc($group['id']) ?>">
            <div class="user-card-header">
                <?php if (!empty($group['avatar_url'])): ?>
                    <img src="<?= esc($group['avatar_url']) ?>" alt="<?= esc($group['name']) ?>" class="group-avatar-img">
                <?php else: ?>
                    <div class="user-avatar-circle" style="background: rgba(37, 211, 102, 0.15); color: #25d366;">
                        <i class="ti ti-brand-whatsapp" style="font-size: 1.25rem;"></i>
                    </div>
                <?php endif; ?>
                <div class="user-title-block">
                    <div class="user-name-row">
                        <h3 class="user-full-name" title="<?= esc($group['name']) ?>"><?= esc($group['name']) ?></h3>
                    </div>
                    <span class="user-email-text" style="display: flex; align-items: center; gap: 4px;">
                        <i class="ti ti-users"></i> <?= (int)$group['participants_count'] ?> participantes
                    </span>
                </div>
            </div>

            <div class="user-badges-row">
                <span class="status-chip <?= $group['status'] === 'active' ? 'chip-active' : 'chip-inactive' ?>">
                    <?= $group['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                </span>
                <?php if (!empty($group['category'])): ?>
                    <span class="status-chip chip-role-user">
                        <?= esc($group['category']) ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($group['is_admin_only'])): ?>
                    <span class="status-chip chip-role-admin">
                        Somente admins
                    </span>
                <?php endif; ?>
                <?php if (!empty($group['is_restricted'])): ?>
                    <span class="status-chip" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25);">
                        Restrito
                    </span>
                <?php endif; ?>
            </div>

            <div class="user-permissions-block" style="min-height: 48px;">
                <span class="perm-title">DESCRIÇÃO</span>
                <p class="perm-desc" title="<?= esc($group['description'] ?? '') ?>">
                    <?php if (!empty($group['description'])): ?>
                        <?= esc(mb_strimwidth($group['description'], 0, 110, '...')) ?>
                    <?php else: ?>
                        <span style="color: rgb(var(--muted));">Sem descrição cadastrada.</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="user-footer-meta">
                <span class="user-created-date">
                    Cadastrado em <?= date('d/m/Y', strtotime($group['created_at'])) ?>
                    <?php if (!empty($group['last_synced_at'])): ?>
                        <br>Sincronizado em <?= date('d/m/Y, H:i', strtotime($group['last_synced_at'])) ?>
                    <?php endif; ?>
                </span>
            </div>

            <div class="user-card-actions">
                <?php if (\App\Libraries\UserPermissions::hasPermission('whatsapp_groups', 'edit')): ?>
                    <button type="button" class="btn-card-action btn-test-send" data-id="<?= esc($group['id']) ?>" data-name="<?= esc($group['name']) ?>">
                        <i class="ti ti-send"></i>
                        <span>Testar</span>
                    </button>
                    <button type="button" class="btn-card-action btn-toggle-status" data-id="<?= esc($group['id']) ?>" data-status="<?= esc($group['status']) ?>">
                        <i class="ti ti-power"></i>
                        <span><?= $group['status'] === 'active' ? 'Inativar' : 'Ativar' ?></span>
                    </button>
                <?php endif; ?>
                <?php if (\App\Libraries\UserPermissions::hasPermission('whatsapp_groups', 'delete')): ?>
                    <button type="button" class="btn-card-action btn-delete-group" data-id="<?= esc($group['id']) ?>" data-name="<?= esc($group['name']) ?>">
                        <i class="ti ti-trash"></i>
                        <span>Excluir</span>
                    </button>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
