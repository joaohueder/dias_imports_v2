<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="leads-module-wrap" data-leads-module>

    <!-- Barra de regressão / atualização automática 10s no topo do painel -->
    <div class="leads-auto-refresh-bar" data-refresh-bar-container>
        <div class="refresh-bar-content">
            <div class="refresh-status-badge">
                <span class="status-pulse-dot" aria-hidden="true"></span>
                <span>Sincronização em tempo real</span>
                <span class="refresh-timer-info">(atualiza em <strong data-refresh-countdown>10s</strong>)</span>
            </div>
            <button type="button" class="btn-refresh-manual" data-refresh-now title="Atualizar agora">
                <i class="ti ti-refresh" aria-hidden="true"></i>
                <span>Atualizar agora</span>
            </button>
        </div>
        <div class="refresh-progress-track">
            <div class="refresh-progress-fill" data-refresh-fill style="width: 100%;"></div>
        </div>
    </div>

    <!-- Dashboard de Métricas -->
    <section class="leads-dashboard-section" data-dashboard-container aria-label="Dashboard de Leads">
        <?= view('admin/leads/_metrics', $metrics) ?>
    </section>

    <!-- Card de Tabela e Filtros de Lista -->
    <div class="leads-table-card">
        <div class="leads-list-toolbar">
            <div class="toolbar-title-group">
                <h3>Contatos Captados</h3>
                <p>Lista consolidada dos contatos capturados pela landing page</p>
            </div>

            <div class="toolbar-filters-group">
                <div class="filter-input-wrap">
                    <i class="ti ti-search" aria-hidden="true"></i>
                    <input type="text" id="leads-search-input" placeholder="Buscar por contato ou WhatsApp..." value="<?= esc($searchQuery) ?>" aria-label="Buscar leads por nome ou WhatsApp" data-leads-search>
                    <?php if ($searchQuery !== ''): ?>
                        <button type="button" class="btn-clear-filter" data-clear-search aria-label="Limpar busca"><i class="ti ti-x"></i></button>
                    <?php endif; ?>
                </div>

                <div class="filter-input-wrap">
                    <i class="ti ti-calendar" aria-hidden="true"></i>
                    <input type="date" id="leads-date-input" value="<?= esc($dateFilter) ?>" aria-label="Filtrar por data exata" data-leads-date>
                    <?php if ($dateFilter !== ''): ?>
                        <button type="button" class="btn-clear-filter" data-clear-date aria-label="Limpar data"><i class="ti ti-x"></i></button>
                    <?php endif; ?>
                </div>

                <div class="leads-counter-pill">
                    <i class="ti ti-users" aria-hidden="true"></i>
                    <span><strong data-leads-count><?= count($leads) ?></strong> contatos</span>
                </div>
            </div>
        </div>

        <div class="leads-table-container">
            <table class="leads-data-table" aria-label="Listagem de contatos captados">
                <thead>
                    <tr>
                        <th style="width: 160px;">Data / Hora</th>
                        <th>Contato</th>
                        <th style="width: 200px;">WhatsApp</th>
                        <th style="width: 150px;">Origem</th>
                        <th style="width: 100px; text-align: right;">Ações</th>
                    </tr>
                </thead>
                <tbody data-leads-tbody>
                    <?= view('admin/leads/_table_rows', ['leads' => $leads]) ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal de Edição de Lead -->
<dialog class="modal-dialog-lead" data-edit-lead-dialog hidden>
    <div class="modal-dialog-lead-card">
        <div class="modal-icon-header">
            <i class="ti ti-user-edit"></i>
        </div>
        <h3 class="modal-title">Editar Contato</h3>
        <p class="modal-subtitle">Atualize o nome e o WhatsApp do lead captado.</p>

        <form method="post" data-edit-lead-form action="">
            <?= csrf_field() ?>
            <div class="pwd-field-group">
                <label for="lead_edit_name">Nome do Contato</label>
                <div class="input-with-toggle">
                    <input type="text" id="lead_edit_name" name="name" required maxlength="120" placeholder="Ex: João da Silva">
                </div>
            </div>

            <div class="pwd-field-group">
                <label for="lead_edit_phone">WhatsApp (com DDD)</label>
                <div class="input-with-toggle">
                    <input type="tel" id="lead_edit_phone" name="phone" required maxlength="20" placeholder="(11) 99999-9999" data-mask-phone>
                </div>
            </div>

            <div class="modal-actions-row">
                <button type="button" class="button secondary" data-close-lead-edit>Cancelar</button>
                <button type="submit" class="button primary">
                    <i class="ti ti-device-floppy" aria-hidden="true"></i>
                    Salvar
                </button>
            </div>
        </form>
    </div>
</dialog>
<?= $this->endSection() ?>
