<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="job-center-container">
    <!-- Grid de Métricas / Cards de Status -->
    <div class="job-stats-grid">
        <div class="job-stat-card">
            <div class="job-stat-icon pending">
                <i class="ti ti-clock-pause"></i>
            </div>
            <div class="job-stat-content">
                <span class="job-stat-label">Pendentes</span>
                <span class="job-stat-value" id="stat-pending"><?= esc($stats['pending']) ?></span>
            </div>
        </div>

        <div class="job-stat-card">
            <div class="job-stat-icon processing">
                <i class="ti ti-loader rotate"></i>
            </div>
            <div class="job-stat-content">
                <span class="job-stat-label">Em Execução</span>
                <span class="job-stat-value" id="stat-processing"><?= esc($stats['processing']) ?></span>
            </div>
        </div>

        <div class="job-stat-card">
            <div class="job-stat-icon completed">
                <i class="ti ti-check"></i>
            </div>
            <div class="job-stat-content">
                <span class="job-stat-label">Concluídas</span>
                <span class="job-stat-value" id="stat-completed"><?= esc($stats['completed']) ?></span>
            </div>
        </div>

        <div class="job-stat-card">
            <div class="job-stat-icon failed">
                <i class="ti ti-alert-triangle"></i>
            </div>
            <div class="job-stat-content">
                <span class="job-stat-label">Falhas</span>
                <span class="job-stat-value" id="stat-failed"><?= esc($stats['failed']) ?></span>
            </div>
        </div>
    </div>

    <!-- Informações do Agendador (Cron Job Linux) -->
    <div class="job-cron-card">
        <div class="job-cron-header">
            <i class="ti ti-terminal-2"></i>
            <span>Agendador de Tarefas do Servidor (Cron Job Linux)</span>
        </div>
        <p class="job-cron-desc">
            Para que a Central de Trabalho processe a fila em segundo plano de forma contínua e autônoma, adicione a linha abaixo ao <strong>crontab</strong> do seu servidor Linux (executa a cada minuto):
        </p>
        <div class="job-cron-input-group">
            <input type="text" class="job-cron-input" readonly id="cron-command-input" value="* * * * * cd <?= esc($cronPath ?? (env('app.PathCronJob') ?: ROOTPATH)) ?> && php spark jobs:process >> /dev/null 2>&1">
            <button class="button secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('cron-command-input').value); alert('Comando copiado!');">
                <i class="ti ti-copy" aria-hidden="true"></i>
                <span>Copiar</span>
            </button>
        </div>
    </div>

    <!-- Card da Tabela de Fila -->
    <div class="job-table-card">
        <div class="job-table-header">
            <div class="job-table-title-wrap">
                <h3 class="job-table-title">Fila de Execução</h3>
                <p class="job-table-subtitle">Últimas 100 tarefas registradas no sistema.</p>
            </div>
            <div class="job-table-actions">
                <form action="<?= site_url('central-trabalho/executar-agora') ?>" method="post" data-processing-title="Executando Tarefas" data-processing-message="Processando lote de tarefas da fila agora...">
                    <?= csrf_field() ?>
                    <button type="submit" class="button primary">
                        <i class="ti ti-player-play" aria-hidden="true"></i>
                        <span>Executar Agora Manualmente</span>
                    </button>
                </form>
                <?php if ($stats['failed'] > 0): ?>
                <form action="<?= site_url('central-trabalho/reprocessar-falhas') ?>" method="post" data-processing-title="Reagendando" data-processing-message="Reenviando tarefas com falha para a fila...">
                    <?= csrf_field() ?>
                    <button type="submit" class="button secondary">
                        <i class="ti ti-reload" aria-hidden="true"></i>
                        <span>Reprocessar Falhas</span>
                    </button>
                </form>
                <?php endif; ?>
                <?php if ($stats['completed'] > 0): ?>
                <form action="<?= site_url('central-trabalho/limpar-concluidas') ?>" method="post" data-processing-title="Limpando" data-processing-message="Removendo histórico concluído...">
                    <?= csrf_field() ?>
                    <button type="submit" class="button outline">
                        <i class="ti ti-trash" aria-hidden="true"></i>
                        <span>Limpar Concluídas</span>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filtros em tempo real -->
        <div class="job-toolbar">
            <div class="job-search-box">
                <i class="ti ti-search" aria-hidden="true"></i>
                <input type="text" id="job-search-input" placeholder="Buscar por trabalho, referência ou erro..." autocomplete="off">
            </div>
            <div class="users-filter-pills" role="tablist" aria-label="Filtro de status de trabalhos">
                <button type="button" class="filter-pill active" data-status-filter="" role="tab">Todos</button>
                <button type="button" class="filter-pill" data-status-filter="pending" role="tab">Pendentes</button>
                <button type="button" class="filter-pill" data-status-filter="processing" role="tab">Em Execução</button>
                <button type="button" class="filter-pill" data-status-filter="completed" role="tab">Concluídas</button>
                <button type="button" class="filter-pill" data-status-filter="failed" role="tab">Falhas</button>
            </div>
        </div>

        <!-- Tabela -->
        <form id="form-delete-multiple-jobs" action="<?= site_url('central-trabalho/excluir-selecionados') ?>" method="post" onsubmit="return confirm('Deseja realmente excluir as tarefas selecionadas que não estejam concluídas?');">
            <?= csrf_field() ?>

            <!-- Ações em Lote (oculta por padrão) -->
            <div id="batch-actions-bar" style="display: none; align-items: center; justify-content: space-between; background: rgb(var(--surface-secondary)); border: 1px solid rgb(var(--border)); border-radius: 8px; padding: 10px 16px; margin-bottom: 12px;">
                <div style="font-size: 13px; font-weight: 600; color: rgb(var(--foreground));">
                    <span id="selected-count">0</span> tarefa(s) selecionada(s)
                </div>
                <button type="submit" class="button danger" style="padding: 6px 14px; font-size: 13px;">
                    <i class="ti ti-trash"></i>
                    <span>Excluir Selecionadas</span>
                </button>
            </div>

            <div class="job-table-wrapper">
                <table class="job-table" id="job-queue-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="select-all-jobs" title="Selecionar todas tarefas não concluídas" style="cursor: pointer;">
                            </th>
                            <th style="width: 70px;">ID</th>
                            <th>Trabalho / Referência</th>
                            <th>Status</th>
                            <th>Horários (Agendamento / Conclusão)</th>
                            <th style="text-align: center; width: 90px;">Tentativas</th>
                            <th>Resultado / Mensagem</th>
                            <th style="width: 50px; text-align: right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="job-queue-tbody">
                        <?php if (empty($queueItems)): ?>
                            <tr id="empty-state-row">
                                <td colspan="8" style="text-align: center; padding: 48px 20px; color: rgb(var(--muted));">
                                    <i class="ti ti-inbox" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                                    Nenhuma tarefa na fila de trabalho no momento.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($queueItems as $item): ?>
                                <tr data-job-row
                                    data-search-text="<?= esc(strtolower($item['job_key'] . ' ' . $item['item_reference'] . ' ' . ($item['error_message'] ?? ''))) ?>"
                                    data-status="<?= esc($item['status']) ?>">
                                    <td style="text-align: center;">
                                        <?php if ($item['status'] !== 'completed'): ?>
                                            <input type="checkbox" name="ids[]" value="<?= esc($item['id']) ?>" class="job-checkbox" style="cursor: pointer;">
                                        <?php else: ?>
                                            <span title="Concluídos não podem ser selecionados" style="color: rgb(var(--muted)); font-size: 11px;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span style="color: rgb(var(--muted)); font-weight: 600;">#<?= esc($item['id']) ?></span></td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 4px;">
                                            <strong style="color: rgb(var(--foreground)); font-size: 13px;">
                                                <?php
                                                if ($item['job_key'] === 'sync_whatsapp_groups') {
                                                    echo 'Atualizar Grupo WhatsApp';
                                                } else {
                                                    echo esc($item['job_key']);
                                                }
                                                ?>
                                            </strong>
                                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                                <span style="font-size: 11px; color: rgb(var(--muted));">Ref:</span>
                                                <code style="background: rgb(var(--surface-secondary)); padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid rgb(var(--border)); font-weight: 600; color: rgb(var(--foreground));"><?= esc($item['item_reference'] ?: '-') ?></code>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] === 'pending'): ?>
                                            <span class="job-badge pending"><i class="ti ti-clock"></i>Pendente</span>
                                        <?php elseif ($item['status'] === 'processing'): ?>
                                            <span class="job-badge processing"><i class="ti ti-loader rotate"></i>Processando</span>
                                        <?php elseif ($item['status'] === 'completed'): ?>
                                            <span class="job-badge completed"><i class="ti ti-check"></i>Concluído</span>
                                        <?php elseif ($item['status'] === 'failed'): ?>
                                            <span class="job-badge failed"><i class="ti ti-alert-triangle"></i>Falha</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 2px; font-size: 12px; color: rgb(var(--muted));">
                                            <div><strong style="color: rgb(var(--foreground) / .8);">Agendado:</strong> <?= esc($item['scheduled_at'] ? date('d/m/Y H:i:s', strtotime($item['scheduled_at'])) : '-') ?></div>
                                            <div><strong style="color: rgb(var(--foreground) / .8);">Concluído:</strong> <?= esc($item['completed_at'] ? date('d/m/Y H:i:s', strtotime($item['completed_at'])) : '-') ?></div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 999px; background: rgb(var(--surface-secondary)); border: 1px solid rgb(var(--border)); font-size: 11px; font-weight: 700;"><?= esc($item['attempts']) ?></span>
                                    </td>
                                    <td style="font-size: 12px;">
                                        <?php if (!empty($item['error_message'])): ?>
                                            <span style="color: rgb(var(--danger)); display: inline-flex; align-items: center; gap: 4px;" title="<?= esc($item['error_message']) ?>">
                                                <i class="ti ti-alert-circle"></i> <?= esc(character_limiter($item['error_message'], 60)) ?>
                                            </span>
                                        <?php elseif ($item['status'] === 'completed'): ?>
                                            <span style="color: rgb(var(--success)); display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="ti ti-check"></i> Executado com sucesso
                                            </span>
                                        <?php else: ?>
                                            <span style="color: rgb(var(--muted));">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php if ($item['status'] !== 'completed' && \App\Libraries\UserPermissions::hasPermission('job_center', 'delete')): ?>
                                            <form action="<?= site_url('central-trabalho/' . $item['id'] . '/excluir') ?>" method="post" style="display: inline-block;" onsubmit="return confirm('Tem certeza que deseja excluir este trabalho da fila?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="button danger" style="padding: 4px 8px; min-height: unset; font-size: 12px;" title="Excluir Trabalho">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('job-search-input');
    const filterPills = document.querySelectorAll('[data-status-filter]');
    const rows = document.querySelectorAll('[data-job-row]');
    let currentStatus = '';

    // Lógica de seleção em lote
    const selectAllCheckbox = document.getElementById('select-all-jobs');
    const jobCheckboxes = document.querySelectorAll('.job-checkbox');
    const batchActionsBar = document.getElementById('batch-actions-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBatchActions() {
        const checkedCount = document.querySelectorAll('.job-checkbox:checked').length;
        if (checkedCount > 0) {
            selectedCountSpan.textContent = checkedCount;
            batchActionsBar.style.display = 'flex';
        } else {
            batchActionsBar.style.display = 'none';
        }
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkedCount > 0 && checkedCount === jobCheckboxes.length;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            jobCheckboxes.forEach(cb => {
                // Só marca se a linha estiver visível (não filtrada)
                const row = cb.closest('tr');
                if (row.style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            updateBatchActions();
        });
    }

    jobCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBatchActions);
    });

    filterPills.forEach(pill => {
        pill.addEventListener('click', function () {
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentStatus = this.getAttribute('data-status-filter') || '';
            filterTable();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }

    function filterTable() {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.getAttribute('data-search-text') || '';
            const rowStatus = row.getAttribute('data-status') || '';
            const matchesText = !query || text.includes(query);
            const matchesStatus = !currentStatus || rowStatus === currentStatus;

            if (matchesText && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
                // Desmarca checkbox se a linha for ocultada
                const cb = row.querySelector('.job-checkbox');
                if (cb) cb.checked = false;
            }
        });
        
        updateBatchActions();
    }

    // Auto-atualização periódica a cada 8 segundos se houver pendentes ou processando
    const statPending = parseInt(document.getElementById('stat-pending')?.innerText || '0', 10);
    const statProcessing = parseInt(document.getElementById('stat-processing')?.innerText || '0', 10);

    if (statPending > 0 || statProcessing > 0) {
        setInterval(function() {
            fetch('<?= site_url('central-trabalho/feed') ?>')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.stats) {
                        const elP = document.getElementById('stat-pending');
                        const elPr = document.getElementById('stat-processing');
                        const elC = document.getElementById('stat-completed');
                        const elF = document.getElementById('stat-failed');
                        if (elP) elP.innerText = data.stats.pending;
                        if (elPr) elPr.innerText = data.stats.processing;
                        if (elC) elC.innerText = data.stats.completed;
                        if (elF) elF.innerText = data.stats.failed;
                    }
                })
                .catch(() => {});
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>
