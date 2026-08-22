<?php if (empty($groups)): ?>
    <div class="users-empty-search col-span-full" data-groups-empty-state style="grid-column: 1 / -1; width: 100%; padding: 60px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: rgb(var(--muted));">
        <div class="empty-icon" style="width: 64px; height: 64px; border-radius: 16px; background: rgb(var(--surface-2)); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
            <i class="ti ti-inbox" style="font-size: 32px; color: rgb(var(--muted));"></i>
        </div>
        <p class="empty-title" style="margin: 0; font-size: 15px; font-weight: 600; color: rgb(var(--text));">Nenhum grupo encontrado para os filtros selecionados.</p>
        <span class="empty-desc" style="font-size: 14px; margin-bottom: 12px;">Novos grupos sincronizados do WhatsApp aparecerão aqui automaticamente.</span>
        <button type="button" class="button secondary" data-clear-filters style="display: none; margin-top: 4px;">
            <i class="ti ti-filter-off" aria-hidden="true"></i>
            <span>Limpar Filtros</span>
        </button>
        <?php if (\App\Libraries\UserPermissions::hasPermission('whatsapp_groups', 'create')): ?>
            <button type="button" class="button primary" data-new-group-btn onclick="document.getElementById('btn-open-new-group-modal')?.click()" style="margin-top: 4px;">
                <i class="ti ti-plus" aria-hidden="true"></i>
                <span>Novo Grupo</span>
            </button>
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php foreach ($groups as $group): ?>
        <article class="user-card-item group-item-card <?= $group['status'] === 'inactive' ? 'is-inactive' : '' ?>"
                 data-group-card
                 data-id="<?= esc($group['id']) ?>"
                 data-name="<?= esc(mb_strtolower($group['name'])) ?>"
                 data-description="<?= esc(mb_strtolower($group['description'] ?? '')) ?>"
                 data-category="<?= esc(mb_strtolower($group['category'] ?? '')) ?>"
                 data-status="<?= esc($group['status']) ?>">
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
                    <button type="button" class="btn-card-action btn-refresh-group" data-id="<?= esc($group['id']) ?>" data-name="<?= esc($group['name']) ?>" <?= empty($isSyncJobActive) ? 'disabled title="A rotina de atualização de grupos está desativada nas configurações da Central de Trabalho."' : 'title="Atualizar dados, nome e quantidade de participantes"' ?>>
                        <i class="ti ti-refresh"></i>
                        <span>Atualizar</span>
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
