<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="whatsapp-groups-wrap" data-groups-module>
    <div class="users-header-actions">
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

        <div class="users-create-action" style="display: flex; gap: 10px;">
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

<!-- Modal Novo Grupo -->
<div class="template-dialog" id="dialog-new-group" hidden aria-hidden="true">
    <section class="template-dialog-card" role="dialog" aria-modal="true" aria-labelledby="new-group-title">
        <button class="template-dialog-close" type="button" data-close-dialog aria-label="Fechar modal"><i class="ti ti-x" aria-hidden="true"></i></button>
        
        <div class="template-dialog-header">
            <div class="template-dialog-icon" aria-hidden="true"><i class="ti ti-brand-whatsapp"></i></div>
            <div class="template-dialog-title-group">
                <p class="template-dialog-kicker">Novo Grupo</p>
                <h2 class="template-dialog-title" id="new-group-title">Criar Grupo de WhatsApp</h2>
                <p class="template-dialog-desc">Crie um novo grupo no WhatsApp e vincule à plataforma.</p>
            </div>
        </div>

        <form id="form-new-group" action="<?= site_url('grupos-whatsapp/novo') ?>" method="post">
            <?= csrf_field() ?>
            <div class="field-group">
                <label for="new_group_name">Nome do Grupo *</label>
                <input type="text" id="new_group_name" name="name" required maxlength="100" placeholder="Ex: DIAS IMPORTS GRUPO VIP">
            </div>

            <div class="field-group">
                <label for="new_group_desc">Descrição do Grupo</label>
                <textarea id="new_group_desc" name="description" rows="3" placeholder="Descrição que aparecerá no WhatsApp..."></textarea>
            </div>

            <div class="field-group">
                <label for="new_group_category">Categoria / Tag</label>
                <input type="text" id="new_group_category" name="category" value="Dias Imports" placeholder="Ex: Dias Imports">
            </div>

            <div class="field-group">
                <label for="new_group_participants">Participantes Iniciais (Opcional)</label>
                <textarea id="new_group_participants" name="participants" rows="2" placeholder="Um número por linha (com DDD). Ex: 17999998888"></textarea>
                <small class="field-hint">A instância conectada será adicionada automaticamente como administradora.</small>
            </div>

            <div class="template-dialog-actions">
                <button type="button" class="button button-outline" data-close-dialog>Cancelar</button>
                <button type="submit" class="button button-primary" id="btn-save-group">
                    <i class="ti ti-plus"></i>
                    <span>Criar Grupo</span>
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
