<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="whatsapp-groups-wrap" data-groups-module>
    <div class="users-header-actions" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; flex: 1;">
            <div class="users-search-box">
                <i class="ti ti-search users-search-icon" aria-hidden="true"></i>
                <input type="text" id="groups-search-input" class="users-search-input" placeholder="Filtrar pelo nome..." value="<?= esc($searchQuery) ?>" aria-label="Filtrar grupos pelo nome">
                <?php if ($searchQuery !== ''): ?>
                    <button type="button" class="btn-clear-search" id="btn-clear-search" aria-label="Limpar busca"><i class="ti ti-x"></i></button>
                <?php endif; ?>
            </div>

            <div class="users-filter-pills" role="tablist" aria-label="Filtros de status de grupos">
                <button type="button" class="filter-pill <?= $currentStatus === 'all' ? 'active' : '' ?>" data-status="all" role="tab" aria-selected="<?= $currentStatus === 'all' ? 'true' : 'false' ?>">
                    Todos <span class="pill-badge" id="badge-all"><?= esc($metrics['total']) ?></span>
                </button>
                <button type="button" class="filter-pill <?= $currentStatus === 'active' ? 'active' : '' ?>" data-status="active" role="tab" aria-selected="<?= $currentStatus === 'active' ? 'true' : 'false' ?>">
                    Ativos <span class="pill-badge" id="badge-active"><?= esc($metrics['active']) ?></span>
                </button>
                <button type="button" class="filter-pill <?= $currentStatus === 'inactive' ? 'active' : '' ?>" data-status="inactive" role="tab" aria-selected="<?= $currentStatus === 'inactive' ? 'true' : 'false' ?>">
                    Inativos <span class="pill-badge" id="badge-inactive"><?= esc($metrics['inactive']) ?></span>
                </button>
            </div>
        </div>

        <div class="users-create-action" style="display: flex; gap: 10px; margin-left: auto;">
            <?php if (\App\Libraries\UserPermissions::hasPermission('whatsapp_groups', 'create') || \App\Libraries\UserPermissions::hasPermission('whatsapp_groups', 'edit')): ?>
                <button type="button" class="button secondary" id="btn-sync-groups">
                    <i class="ti ti-refresh" aria-hidden="true"></i>
                    <span>Atualizar Grupos</span>
                </button>
            <?php endif; ?>
            <?php if (\App\Libraries\UserPermissions::hasPermission('whatsapp_groups', 'create')): ?>
                <button type="button" class="button primary" id="btn-open-new-group-modal">
                    <i class="ti ti-plus" aria-hidden="true"></i>
                    <span>Novo Grupo</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid de cards dos grupos -->
    <div class="users-grid" id="groups-container">
        <?= view('admin/groups/_cards', ['groups' => $groups]) ?>
    </div>
</div>

<!-- Modal Novo Grupo / Selecionar da Instância -->
<div class="template-dialog" id="dialog-new-group" hidden aria-hidden="true">
    <section class="template-dialog-card" role="dialog" aria-modal="true" aria-labelledby="new-group-title" style="max-width: 620px;">
        <button class="template-dialog-close" type="button" data-close-dialog aria-label="Fechar modal"><i class="ti ti-x" aria-hidden="true"></i></button>
        
        <div class="template-dialog-header">
            <div class="template-dialog-icon" aria-hidden="true"><i class="ti ti-brand-whatsapp"></i></div>
            <div class="template-dialog-title-group">
                <p class="template-dialog-kicker">Novo Grupo</p>
                <h2 class="template-dialog-title" id="new-group-title">Adicionar Grupo do WhatsApp</h2>
                <p class="template-dialog-desc">Selecione um grupo da instância ativa do WhatsApp para cadastrar no sistema.</p>
            </div>
        </div>

        <div class="dialog-body-content" style="display: flex; flex-direction: column; gap: 16px; margin-top: 8px;">
            <!-- Campo de Pesquisa de Grupos -->
            <div class="users-search-box" style="max-width: 100%; width: 100%;">
                <i class="ti ti-search users-search-icon" aria-hidden="true"></i>
                <input type="text" id="instance-groups-search" class="users-search-input" placeholder="Pesquisar nos grupos da instância..." aria-label="Pesquisar grupos da instância">
            </div>

            <!-- Lista de Grupos da Instância -->
            <div id="instance-groups-list-container" style="min-height: 220px; max-height: 320px; overflow-y: auto; border: 1px solid rgb(var(--border)); border-radius: 12px; background: rgb(var(--surface-secondary) / .3); padding: 8px;">
                <div id="instance-groups-loading" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 200px; gap: 12px; color: rgb(var(--muted));">
                    <i class="ti ti-loader rotate" style="font-size: 28px; color: rgb(var(--primary));"></i>
                    <span style="font-size: 13px;">Carregando grupos da instância ativa...</span>
                </div>

                <div id="instance-groups-empty" style="display: none; flex-direction: column; align-items: center; justify-content: center; height: 200px; gap: 8px; color: rgb(var(--muted)); text-align: center; padding: 20px;">
                    <i class="ti ti-inbox" style="font-size: 32px;"></i>
                    <p style="margin: 0; font-size: 14px; font-weight: 600;">Nenhum grupo encontrado.</p>
                    <span style="font-size: 12px;">Verifique se a instância está conectada ou tente outros termos na busca.</span>
                </div>

                <div id="instance-groups-items" style="display: flex; flex-direction: column; gap: 6px;"></div>
            </div>

            <div id="selected-group-feedback" style="display: none; align-items: center; gap: 8px; font-size: 13px; color: rgb(var(--primary)); font-weight: 500; padding: 8px 12px; border-radius: 8px; background: rgb(var(--primary) / .08);">
                <i class="ti ti-check"></i>
                <span id="selected-group-name-text">Grupo selecionado</span>
            </div>
        </div>

        <form id="form-save-selected-group" action="<?= site_url('grupos-whatsapp/salvar-selecionado') ?>" method="post" style="margin-top: 8px;">
            <?= csrf_field() ?>
            <input type="hidden" name="group_jid" id="selected_group_jid">
            <input type="hidden" name="name" id="selected_group_name">
            <input type="hidden" name="description" id="selected_group_description">
            <input type="hidden" name="participants_count" id="selected_group_participants">
            <input type="hidden" name="avatar_url" id="selected_group_avatar">
            <input type="hidden" name="instance_name" id="selected_group_instance">

            <div class="template-dialog-actions" style="margin-top: 16px;">
                <button type="button" class="button button-outline" data-close-dialog>Cancelar</button>
                <button type="submit" class="button button-primary" id="btn-save-group" disabled>
                    <i class="ti ti-device-floppy"></i>
                    <span>Salvar Grupo</span>
                </button>
            </div>
        </form>
    </section>
</div>

<!-- Modal Testar Envio -->
<div class="template-dialog" id="dialog-test-send" hidden aria-hidden="true">
    <section class="template-dialog-card" role="dialog" aria-modal="true" aria-labelledby="test-send-title">
        <button class="template-dialog-close" type="button" data-close-dialog aria-label="Fechar modal"><i class="ti ti-x" aria-hidden="true"></i></button>
        
        <div class="template-dialog-header">
            <div class="template-dialog-icon" aria-hidden="true"><i class="ti ti-send"></i></div>
            <div class="template-dialog-title-group">
                <p class="template-dialog-kicker">Validação</p>
                <h2 class="template-dialog-title" id="test-send-title">Testar Envio de Mensagem</h2>
                <p class="template-dialog-desc" id="test-send-group-name">Envio de validação para o grupo</p>
            </div>
        </div>

        <form id="form-test-send" action="" method="post">
            <?= csrf_field() ?>
            <div class="field-group">
                <label for="test_send_phone">Número do WhatsApp *</label>
                <input type="text" id="test_send_phone" name="phone" required placeholder="Ex: 5511999999999" data-mask="phone">
                <small class="field-hint">Digite o número com DDI e DDD (somente números).</small>
            </div>
            <div class="field-group">
                <label for="test_send_message">Mensagem *</label>
                <textarea id="test_send_message" name="message" rows="4" required placeholder="Digite a mensagem de teste...">🚀 *Mensagem de Teste*&#10;Esta é uma mensagem de validação de envio do painel Dias Imports.</textarea>
            </div>

            <div class="template-dialog-actions">
                <button type="button" class="button button-outline" data-close-dialog>Cancelar</button>
                <button type="submit" class="button button-primary" id="btn-submit-test-send">
                    <i class="ti ti-send"></i>
                    <span>Enviar Agora</span>
                </button>
            </div>
        </form>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/groups.js') ?>"></script>
<?= $this->endSection() ?>
