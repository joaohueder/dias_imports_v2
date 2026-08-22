<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="job-center-container">
    <!-- Header com indicador de sincronização e Barra de Progresso -->
    <div style="background: rgb(var(--surface-secondary)); border: 1px solid rgb(var(--border)); border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: rgb(var(--foreground));">
                <i class="ti ti-refresh rotate" style="color: var(--primary, #6366f1); font-size: 16px;"></i>
                <span>Sincronização em Tempo Real</span>
            </div>
            <span style="font-size: 12px; color: rgb(var(--muted)); font-weight: 500;">Atualizando a cada 5 segundos</span>
        </div>
        <div style="height: 6px; background: rgba(99, 102, 241, 0.15); border-radius: 999px; overflow: hidden; position: relative;">
            <div id="refresh-progress-bar" style="height: 100%; width: 0%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: 999px;"></div>
        </div>
    </div>

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
                                            <?php if ($item['status'] === 'failed' || strpos(strtolower($item['error_message']), 'falha') !== false || strpos(strtolower($item['error_message']), 'erro') !== false): ?>
                                                <span style="color: rgb(var(--danger)); display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Clique para ver detalhes do erro" data-msg="<?= esc($item['error_message']) ?>" onclick="showJobResultModal(<?= esc($item['id']) ?>, this.getAttribute('data-msg'), 'failed')">
                                                    <i class="ti ti-alert-circle"></i> Falha na execução <i class="ti ti-chevron-right" style="font-size: 11px; opacity: 0.7;"></i>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: rgb(var(--success)); display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Clique para ver detalhes do que foi feito" data-msg="<?= esc($item['error_message']) ?>" onclick="showJobResultModal(<?= esc($item['id']) ?>, this.getAttribute('data-msg'), 'completed')">
                                                    <i class="ti ti-check"></i> Executado com sucesso <i class="ti ti-chevron-right" style="font-size: 11px; opacity: 0.7;"></i>
                                                </span>
                                            <?php endif; ?>
                                        <?php elseif ($item['status'] === 'completed'): ?>
                                            <span style="color: rgb(var(--success)); display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Clique para ver detalhes" onclick="showJobResultModal(<?= esc($item['id']) ?>, 'Tarefa concluída com sucesso.', 'completed')">
                                                <i class="ti ti-check"></i> Executado com sucesso <i class="ti ti-chevron-right" style="font-size: 11px; opacity: 0.7;"></i>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: rgb(var(--muted));">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php if ($item['status'] !== 'completed' && \App\Libraries\UserPermissions::hasPermission('job_center', 'delete')): ?>
                                            <button type="button" class="button danger" style="padding: 4px 8px; min-height: unset; font-size: 12px;" title="Excluir Trabalho" onclick="deleteSingleJob('<?= esc($item['id']) ?>')">
                                                <i class="ti ti-trash"></i>
                                            </button>
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

<!-- Formulário dinâmico para exclusão individual de job -->
<form id="form-delete-single-job" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
</form>

<!-- Modal de Resultado da Execução -->
<dialog class="modal-dialog-custom" id="job-result-modal" hidden>
    <div class="modal-dialog-custom-card" style="max-width: 520px; width: 100%;">
        <div class="modal-dialog-custom-header">
            <div class="modal-icon-badge" id="job-result-icon-badge" style="background: rgb(var(--surface-secondary)); color: rgb(var(--foreground));">
                <i class="ti ti-info-circle" id="job-result-icon" style="font-size: 24px;"></i>
            </div>
            <div>
                <h3 id="job-result-title">Detalhes da Execução</h3>
                <p>Relatório do processamento da tarefa <strong id="job-result-id"></strong></p>
            </div>
        </div>
        <div id="job-result-container" style="display: flex; flex-direction: column; gap: 12px; margin: 8px 0 16px 0;">
            <!-- Renderizado dinamicamente com visual limpo -->
        </div>
        <div class="modal-actions">
            <button type="button" class="button secondary" onclick="closeJobResultModal()">Fechar</button>
        </div>
    </div>
</dialog>

<script>
function renderFormattedJobReport(message, status) {
    const container = document.getElementById('job-result-container');
    container.innerHTML = '';

    if (!message || message.trim() === '') {
        container.innerHTML = `<div style="padding: 12px; background: rgb(var(--surface-secondary)); border-radius: 8px; font-size: 13px; color: rgb(var(--muted));">Nenhuma informação adicional registrada.</div>`;
        return;
    }

    // Decodifica entidades HTML caso existam
    const txtArea = document.createElement('textarea');
    txtArea.innerHTML = message;
    const decodedMessage = txtArea.value;

    // Detecta padrão de sincronização de grupo
    // Ex: Grupo "Nome" (jid) sincronizado com sucesso.\nDetalhes: ...
    const groupMatch = decodedMessage.match(/^Grupo\s+["“](.+?)["”]\s*\((.+?)\)\s*(.+?)(?:\r?\nDetalhes:\s*(.+))?$/is);

    if (groupMatch) {
        const groupName = groupMatch[1];
        const groupJid = groupMatch[2];
        const statusSummary = groupMatch[3].trim();
        const rawDetails = groupMatch[4] ? groupMatch[4].trim() : '';

        const card = document.createElement('div');
        card.style.cssText = 'background: rgb(var(--surface-secondary)); border: 1px solid rgb(var(--border)); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px;';

        // Cabeçalho do Grupo
        const head = document.createElement('div');
        head.style.cssText = 'display: flex; flex-direction: column; gap: 4px;';
        head.innerHTML = `
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: rgb(var(--muted));">Grupo WhatsApp</div>
            <div style="font-size: 15px; font-weight: 700; color: rgb(var(--foreground));">${escapeHtml(groupName)}</div>
            <div style="font-size: 12px; color: rgb(var(--muted)); font-family: monospace;">${escapeHtml(groupJid)}</div>
        `;
        card.appendChild(head);

        // Divisor
        const divider = document.createElement('div');
        divider.style.cssText = 'height: 1px; background: rgb(var(--border)); margin: 2px 0;';
        card.appendChild(divider);

        // Seção de Detalhes
        const detailsBox = document.createElement('div');
        detailsBox.style.cssText = 'display: flex; flex-direction: column; gap: 8px;';

        if (rawDetails) {
            if (rawDetails.toLowerCase().includes('nenhuma alteração')) {
                detailsBox.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: rgb(var(--foreground)); background: rgba(var(--success), 0.08); border: 1px solid rgba(var(--success), 0.2); padding: 10px 12px; border-radius: 8px;">
                        <i class="ti ti-circle-check" style="color: rgb(var(--success)); font-size: 18px; flex-shrink: 0;"></i>
                        <span>Sincronizado com sucesso. Dados já estavam 100% atualizados.</span>
                    </div>
                `;
            } else {
                const changes = rawDetails.split('|').map(c => c.trim()).filter(Boolean);
                let itemsHtml = changes.map(ch => `
                    <li style="display: flex; align-items: flex-start; gap: 6px; font-size: 13px; color: rgb(var(--foreground));">
                        <i class="ti ti-point-filled" style="color: rgb(var(--primary)); font-size: 14px; margin-top: 2px; flex-shrink: 0;"></i>
                        <span>${escapeHtml(ch)}</span>
                    </li>
                `).join('');

                detailsBox.innerHTML = `
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: rgb(var(--muted));">Alterações Realizadas:</div>
                    <ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 6px;">
                        ${itemsHtml}
                    </ul>
                `;
            }
        } else {
            detailsBox.innerHTML = `
                <div style="font-size: 13px; color: rgb(var(--foreground));">${escapeHtml(statusSummary)}</div>
            `;
        }

        card.appendChild(detailsBox);
        container.appendChild(card);
        return;
    }

    // Exibição amigável para mensagens genéricas / erros
    const box = document.createElement('div');
    const isErr = status === 'failed';
    const bg = isErr ? 'rgba(var(--danger), 0.08)' : 'rgb(var(--surface-secondary))';
    const border = isErr ? 'rgba(var(--danger), 0.2)' : 'rgb(var(--border))';
    const color = isErr ? 'rgb(var(--danger))' : 'rgb(var(--foreground))';

    box.style.cssText = `background: ${bg}; border: 1px solid ${border}; border-radius: 12px; padding: 16px; font-size: 13px; line-height: 1.5; color: ${color}; white-space: pre-wrap; word-break: break-word; max-height: 320px; overflow-y: auto;`;
    box.textContent = decodedMessage;
    container.appendChild(box);
}

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showJobResultModal(id, message, status) {
    const modal = document.getElementById('job-result-modal');
    const title = document.getElementById('job-result-title');
    const idSpan = document.getElementById('job-result-id');
    const iconBadge = document.getElementById('job-result-icon-badge');
    const icon = document.getElementById('job-result-icon');

    idSpan.textContent = '#' + id;

    if (status === 'failed') {
        title.textContent = 'Falha na Execução';
        iconBadge.style.background = 'rgba(var(--danger), 0.1)';
        iconBadge.style.color = 'rgb(var(--danger))';
        icon.className = 'ti ti-alert-triangle';
    } else if (status === 'completed') {
        title.textContent = 'Execução Concluída';
        iconBadge.style.background = 'rgba(var(--success), 0.1)';
        iconBadge.style.color = 'rgb(var(--success))';
        icon.className = 'ti ti-check';
    } else {
        title.textContent = 'Detalhes da Execução';
        iconBadge.style.background = 'rgb(var(--surface-secondary))';
        iconBadge.style.color = 'rgb(var(--foreground))';
        icon.className = 'ti ti-info-circle';
    }

    renderFormattedJobReport(message, status);

    modal.removeAttribute('hidden');
    modal.showModal();
}

function closeJobResultModal() {
    const modal = document.getElementById('job-result-modal');
    if (modal) {
        modal.close();
        modal.setAttribute('hidden', '');
    }
}

function deleteSingleJob(id) {
    if (!confirm('Tem certeza que deseja excluir este trabalho da fila?')) {
        return;
    }
    const form = document.getElementById('form-delete-single-job');
    form.action = '<?= site_url('central-trabalho') ?>/' + id + '/excluir';
    form.submit();
}

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

    // Auto-atualização periódica a cada 5 segundos com barra de progresso suave
    const progressBar = document.getElementById('refresh-progress-bar');
    const refreshInterval = 5000; // 5 segundos
    const stepInterval = 50; // atualiza barra a cada 50ms
    let elapsed = 0;
    let progressTimer;

    function startProgress() {
        elapsed = 0;
        if (progressBar) progressBar.style.width = '0%';
        
        clearInterval(progressTimer);
        progressTimer = setInterval(() => {
            elapsed += stepInterval;
            const percentage = Math.min((elapsed / refreshInterval) * 100, 100);
            if (progressBar) progressBar.style.width = percentage + '%';
            
            if (elapsed >= refreshInterval) {
                elapsed = 0;
                if (progressBar) progressBar.style.width = '0%';
                fetchFeed();
            }
        }, stepInterval);
    }

    function renderBadge(status) {
        if (status === 'pending') {
            return '<span class="job-badge pending"><i class="ti ti-clock"></i>Pendente</span>';
        } else if (status === 'processing') {
            return '<span class="job-badge processing"><i class="ti ti-loader rotate"></i>Processando</span>';
        } else if (status === 'completed') {
            return '<span class="job-badge completed"><i class="ti ti-check"></i>Concluído</span>';
        } else if (status === 'failed') {
            return '<span class="job-badge failed"><i class="ti ti-alert-triangle"></i>Falha</span>';
        }
        return status;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR');
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function updateTableContent(items) {
        const tbody = document.getElementById('job-queue-tbody');
        if (!tbody) return;

        // Se o usuário estiver interagindo ou selecionando, não substitui o DOM
        const checkedCount = document.querySelectorAll('.job-checkbox:checked').length;
        if (checkedCount > 0) return;

        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-state-row">
                    <td colspan="8" style="text-align: center; padding: 48px 20px; color: rgb(var(--muted));">
                        <i class="ti ti-inbox" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                        Nenhuma tarefa na fila de trabalho no momento.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        items.forEach(item => {
            const jobTitle = item.job_key === 'sync_whatsapp_groups' ? 'Atualizar Grupo WhatsApp' : escapeHtml(item.job_key);
            const ref = item.item_reference ? escapeHtml(item.item_reference) : '-';
            const errorMsg = item.error_message ? escapeHtml(item.error_message) : '';
            const searchText = (item.job_key + ' ' + (item.item_reference || '') + ' ' + (item.error_message || '')).toLowerCase();

            let resultHtml = '<span style="color: rgb(var(--muted));">-</span>';
            if (errorMsg) {
                const safeErrorMsg = errorMsg.replace(/"/g, '&quot;');
                const isError = item.status === 'failed' || errorMsg.toLowerCase().includes('falha') || errorMsg.toLowerCase().includes('erro');
                if (isError) {
                    resultHtml = `<span style="color: rgb(var(--danger)); display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Clique para ver detalhes do erro" data-msg="${safeErrorMsg}" onclick="showJobResultModal(${item.id}, this.getAttribute('data-msg'), 'failed')"><i class="ti ti-alert-circle"></i> Falha na execução <i class="ti ti-chevron-right" style="font-size: 11px; opacity: 0.7;"></i></span>`;
                } else {
                    resultHtml = `<span style="color: rgb(var(--success)); display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Clique para ver detalhes do que foi feito" data-msg="${safeErrorMsg}" onclick="showJobResultModal(${item.id}, this.getAttribute('data-msg'), 'completed')"><i class="ti ti-check"></i> Executado com sucesso <i class="ti ti-chevron-right" style="font-size: 11px; opacity: 0.7;"></i></span>`;
                }
            } else if (item.status === 'completed') {
                resultHtml = `<span style="color: rgb(var(--success)); display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Clique para ver detalhes" onclick="showJobResultModal(${item.id}, 'Tarefa concluída com sucesso.', 'completed')"><i class="ti ti-check"></i> Executado com sucesso <i class="ti ti-chevron-right" style="font-size: 11px; opacity: 0.7;"></i></span>`;
            }

            const checkboxHtml = item.status !== 'completed' 
                ? `<input type="checkbox" name="ids[]" value="${item.id}" class="job-checkbox" style="cursor: pointer;">`
                : `<span title="Concluídos não podem ser selecionados" style="color: rgb(var(--muted)); font-size: 11px;">-</span>`;

            const actionHtml = (item.status !== 'completed')
                ? `<button type="button" class="button danger" style="padding: 4px 8px; min-height: unset; font-size: 12px;" title="Excluir Trabalho" onclick="deleteSingleJob('${item.id}')"><i class="ti ti-trash"></i></button>`
                : '';

            html += `
                <tr data-job-row data-search-text="${escapeHtml(searchText)}" data-status="${escapeHtml(item.status)}">
                    <td style="text-align: center;">${checkboxHtml}</td>
                    <td><span style="color: rgb(var(--muted)); font-weight: 600;">#${item.id}</span></td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <strong style="color: rgb(var(--foreground)); font-size: 13px;">${jobTitle}</strong>
                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                <span style="font-size: 11px; color: rgb(var(--muted));">Ref:</span>
                                <code style="background: rgb(var(--surface-secondary)); padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid rgb(var(--border)); font-weight: 600; color: rgb(var(--foreground));">${ref}</code>
                            </div>
                        </div>
                    </td>
                    <td>${renderBadge(item.status)}</td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 2px; font-size: 12px; color: rgb(var(--muted));">
                            <div><strong style="color: rgb(var(--foreground) / .8);">Agendado:</strong> ${formatDate(item.scheduled_at)}</div>
                            <div><strong style="color: rgb(var(--foreground) / .8);">Concluído:</strong> ${formatDate(item.completed_at)}</div>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span style="display: inline-block; padding: 2px 8px; border-radius: 999px; background: rgb(var(--surface-secondary)); border: 1px solid rgb(var(--border)); font-size: 11px; font-weight: 700;">${item.attempts}</span>
                    </td>
                    <td style="font-size: 12px;">${resultHtml}</td>
                    <td style="text-align: right;">${actionHtml}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        // Reanexa listeners dos novos checkboxes
        const newJobCheckboxes = document.querySelectorAll('.job-checkbox');
        newJobCheckboxes.forEach(cb => cb.addEventListener('change', updateBatchActions));

        // Reexecuta filtro atual se houver
        filterTable();
    }

    function fetchFeed() {
        fetch('<?= site_url('central-trabalho/feed') ?>')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.stats) {
                        const elP = document.getElementById('stat-pending');
                        const elPr = document.getElementById('stat-processing');
                        const elC = document.getElementById('stat-completed');
                        const elF = document.getElementById('stat-failed');
                        if (elP) elP.innerText = data.stats.pending;
                        if (elPr) elPr.innerText = data.stats.processing;
                        if (elC) elC.innerText = data.stats.completed;
                        if (elF) elF.innerText = data.stats.failed;
                    }
                    if (data.items) {
                        updateTableContent(data.items);
                    }
                }
            })
            .catch(() => {});
    }

    // Inicia o timer de progresso
    startProgress();
});
</script>
<?= $this->endSection() ?>
