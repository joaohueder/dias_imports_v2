document.addEventListener('DOMContentLoaded', () => {
    const groupsContainer = document.getElementById('groups-container');
    const searchInput = document.getElementById('groups-search-input');
    const btnClearSearch = document.getElementById('btn-clear-search');
    const filterBtns = document.querySelectorAll('.users-filter-pills .filter-pill');
    const btnSyncGroups = document.getElementById('btn-sync-groups');
    
    function getBaseUrl() {
        return window.getAppBaseUrl ? window.getAppBaseUrl() : (document.querySelector('meta[name="base-url"]')?.getAttribute('content') || window.location.origin);
    }

    // Modal Novo Grupo / Selecionar da Instância
    const btnOpenNewGroupModal = document.getElementById('btn-open-new-group-modal');
    const dialogNewGroup = document.getElementById('dialog-new-group');
    const formSaveSelectedGroup = document.getElementById('form-save-selected-group');
    const btnSaveGroup = document.getElementById('btn-save-group');
    const instanceGroupsSearch = document.getElementById('instance-groups-search');
    const instanceGroupsLoading = document.getElementById('instance-groups-loading');
    const instanceGroupsEmpty = document.getElementById('instance-groups-empty');
    const instanceGroupsItems = document.getElementById('instance-groups-items');
    const selectedGroupFeedback = document.getElementById('selected-group-feedback');
    const selectedGroupNameText = document.getElementById('selected-group-name-text');

    let instanceGroupsData = [];
    let selectedGroupItem = null;

    let currentStatus = 'all';
    let searchTimeout = null;

    // Listener para o toggle do switch nos cards de trabalho (se houver)
    document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(toggle => {
        toggle.addEventListener('change', () => {
            const labelText = toggle.closest('.toggle-switch')?.querySelector('.toggle-label-text');
            if (labelText) {
                labelText.textContent = toggle.checked ? 'Ativo' : 'Inativo';
            }
        });
    });

    // Filtros de status
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');
            currentStatus = btn.dataset.status;
            applyFilters();
        });
    });

    // Busca rápida
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            applyFilters();
        });
    }

    if (btnClearSearch) {
        btnClearSearch.addEventListener('click', () => {
            searchInput.value = '';
            btnClearSearch.remove();
            applyFilters();
        });
    }

    function applyFilters() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const cards = document.querySelectorAll('[data-group-card]');
        const dynamicEmpty = document.querySelector('[data-groups-client-empty]');
        const serverEmpty = document.querySelector('[data-groups-empty-state]');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const desc = card.dataset.description || '';
            const cat = card.dataset.category || '';
            const status = card.dataset.status || '';

            const matchesSearch = !query || name.includes(query) || desc.includes(query) || cat.includes(query);
            
            let matchesStatus = true;
            if (currentStatus === 'active') {
                matchesStatus = (status === 'active');
            } else if (currentStatus === 'inactive') {
                matchesStatus = (status === 'inactive');
            }

            const visible = matchesSearch && matchesStatus;
            card.style.display = visible ? 'flex' : 'none';
            if (visible) visibleCount++;
        });

        if (dynamicEmpty) {
            dynamicEmpty.style.display = (visibleCount === 0 && cards.length > 0) ? 'flex' : 'none';
        }

        if (serverEmpty) {
            const hasFilter = Boolean(query || currentStatus !== 'all');
            const clearBtn = serverEmpty.querySelector('[data-clear-filters]');
            const newBtn = serverEmpty.querySelector('[data-new-group-btn]');
            const desc = serverEmpty.querySelector('.empty-desc');
            
            if (clearBtn) clearBtn.style.display = hasFilter ? 'inline-flex' : 'none';
            if (newBtn) newBtn.style.display = hasFilter ? 'none' : 'inline-flex';
            if (desc) desc.textContent = hasFilter ? 'Tente ajustar a busca ou o status selecionado.' : 'Novos grupos sincronizados do WhatsApp aparecerão aqui automaticamente.';
        }
    }

    const resetFilters = () => {
        if (searchInput) searchInput.value = '';
        if (btnClearSearch) btnClearSearch.remove();
        currentStatus = 'all';
        filterBtns.forEach(b => {
            if (b.dataset.status === 'all') {
                b.classList.add('active');
                b.setAttribute('aria-selected', 'true');
            } else {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            }
        });
        applyFilters();
    };

    document.querySelectorAll('[data-clear-filters]').forEach(btn => {
        btn.addEventListener('click', resetFilters);
    });

    function openModal(dialog) {
        if (!dialog) return;
        dialog.hidden = false;
        dialog.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => dialog.classList.add('open'));
    }

    function closeModal(dialog) {
        if (!dialog) return;
        dialog.classList.remove('open');
        dialog.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            if (!dialog.classList.contains('open')) {
                dialog.hidden = true;
            }
        }, 200);
    }

    // Abertura / Fechamento de Modais
    if (btnOpenNewGroupModal && dialogNewGroup) {
        btnOpenNewGroupModal.addEventListener('click', () => {
            openModal(dialogNewGroup);
            fetchInstanceGroups();
        });
    }

    // Fechar modais ao clicar no botão de fechar
    document.querySelectorAll('[data-close-dialog]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const dialog = e.target.closest('.template-dialog');
            if (dialog) closeModal(dialog);
        });
    });

    // Expor função global para chamada pelo empty-state
    window.openGroupModal = function() {
        if (btnOpenNewGroupModal) {
            btnOpenNewGroupModal.click();
        } else if (dialogNewGroup) {
            openModal(dialogNewGroup);
            fetchInstanceGroups();
        }
    };

    // Busca rápida no modal de seleção de grupos
    if (instanceGroupsSearch) {
        instanceGroupsSearch.addEventListener('input', () => {
            renderInstanceGroupsList(instanceGroupsSearch.value.trim().toLowerCase());
        });
    }

    async function fetchInstanceGroups() {
        if (!instanceGroupsLoading || !instanceGroupsItems) return;
        
        // Dispara a tela de bloqueio e processamento global do sistema
        const processingScreen = document.querySelector('[data-processing-screen]');
        const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
        const processingMessage = processingScreen?.querySelector('[data-processing-message]');
        const appShell = document.querySelector('.app-shell');

        if (processingScreen && processingHeading && processingMessage) {
            processingHeading.textContent = 'Buscando grupos';
            processingMessage.textContent = 'Consultando a Evolution API para listar os grupos da instância ativa. Isso pode levar alguns segundos.';
            processingScreen.hidden = false;
            processingScreen.setAttribute('aria-hidden', 'false');
            appShell?.setAttribute('inert', '');
            document.body.classList.add('processing-locked');
            document.body.style.overflow = 'hidden';
        }

        instanceGroupsLoading.style.display = 'flex';
        instanceGroupsEmpty.style.display = 'none';
        instanceGroupsItems.innerHTML = '';
        if (selectedGroupFeedback) selectedGroupFeedback.style.display = 'none';
        if (btnSaveGroup) btnSaveGroup.disabled = true;
        selectedGroupItem = null;
        if (instanceGroupsSearch) instanceGroupsSearch.value = '';

        try {
            const response = await fetch(`${getBaseUrl()}/grupos-whatsapp/evolution-list`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            instanceGroupsLoading.style.display = 'none';

            if (data.success && Array.isArray(data.groups) && data.groups.length > 0) {
                instanceGroupsData = data.groups;
                renderInstanceGroupsList();
            } else {
                instanceGroupsData = [];
                instanceGroupsEmpty.style.display = 'flex';
                const hint = instanceGroupsEmpty.querySelector('span');
                if (hint) {
                    hint.textContent = data.message || 'Nenhum grupo encontrado na instância.';
                }
            }
        } catch (error) {
            console.error('Erro ao consultar a Evolution API para listar grupos:', error);
            instanceGroupsLoading.style.display = 'none';
            instanceGroupsEmpty.style.display = 'flex';
            const hint = instanceGroupsEmpty.querySelector('span');
            if (hint) hint.textContent = 'Erro de comunicação ao consultar a Evolution API.';
        } finally {
            // Remove a tela de bloqueio
            if (processingScreen) {
                processingScreen.hidden = true;
                processingScreen.setAttribute('aria-hidden', 'true');
                appShell?.removeAttribute('inert');
                document.body.classList.remove('processing-locked');
                document.body.style.overflow = '';
            }
        }
    }

    function renderInstanceGroupsList(filterQuery = '') {
        if (!instanceGroupsItems) return;

        instanceGroupsItems.innerHTML = '';
        const filtered = instanceGroupsData.filter(g => {
            if (!filterQuery) return true;
            return g.name.toLowerCase().includes(filterQuery) || g.group_jid.toLowerCase().includes(filterQuery);
        });

        if (filtered.length === 0) {
            instanceGroupsEmpty.style.display = 'flex';
            return;
        }

        instanceGroupsEmpty.style.display = 'none';

        filtered.forEach(item => {
            const row = document.createElement('div');
            row.className = 'instance-group-select-item';
            row.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 10px; background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); cursor: pointer; transition: all 0.15s ease;';
            
            const isSelected = selectedGroupItem && selectedGroupItem.group_jid === item.group_jid;
            if (isSelected) {
                row.style.borderColor = 'rgb(var(--primary))';
                row.style.background = 'rgb(var(--primary) / .08)';
            }

            const avatarContent = item.avatar_url 
                ? `<img src="${escapeHtml(item.avatar_url)}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">`
                : `<div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(37, 211, 102, 0.15); color: #25d366; display: flex; align-items: center; justify-content: center;"><i class="ti ti-brand-whatsapp" style="font-size: 20px;"></i></div>`;

            const alreadyBadge = item.is_already_added 
                ? `<span style="font-size: 11px; padding: 2px 8px; border-radius: 999px; background: rgb(var(--surface-secondary)); color: rgb(var(--muted)); font-weight: 500;">Já cadastrado</span>`
                : '';

            row.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1;">
                    ${avatarContent}
                    <div style="min-width: 0; flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: rgb(var(--foreground)); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(item.name)}</h4>
                            ${alreadyBadge}
                        </div>
                        <p style="margin: 2px 0 0 0; font-size: 12px; color: rgb(var(--muted)); display: flex; align-items: center; gap: 8px;">
                            <span><i class="ti ti-users"></i> ${item.participants_count} membros</span>
                            <span>•</span>
                            <span style="font-family: monospace; font-size: 11px;">${escapeHtml(item.group_jid)}</span>
                        </p>
                    </div>
                </div>
                <div class="radio-indicator" style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid ${isSelected ? 'rgb(var(--primary))' : 'rgb(var(--border))'}; display: flex; align-items: center; justify-content: center; margin-left: 12px;">
                    ${isSelected ? '<div style="width: 10px; height: 10px; border-radius: 50%; background: rgb(var(--primary));"></div>' : ''}
                </div>
            `;

            row.addEventListener('click', () => {
                selectGroup(item);
            });

            instanceGroupsItems.appendChild(row);
        });
    }

    function selectGroup(item) {
        selectedGroupItem = item;
        
        document.getElementById('selected_group_jid').value = item.group_jid;
        document.getElementById('selected_group_name').value = item.name;
        document.getElementById('selected_group_description').value = item.description || '';
        document.getElementById('selected_group_participants').value = item.participants_count || 0;
        document.getElementById('selected_group_avatar').value = item.avatar_url || '';
        document.getElementById('selected_group_instance').value = item.instance_name || '';

        if (selectedGroupFeedback && selectedGroupNameText) {
            selectedGroupFeedback.style.display = 'flex';
            selectedGroupNameText.textContent = `Selecionado: ${item.name} (${item.participants_count} participantes)`;
        }

        if (btnSaveGroup) {
            btnSaveGroup.disabled = false;
        }

        renderInstanceGroupsList(instanceGroupsSearch ? instanceGroupsSearch.value.trim().toLowerCase() : '');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Salvar Grupo Selecionado
    if (formSaveSelectedGroup) {
        formSaveSelectedGroup.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (btnSaveGroup.disabled) return;

            const processingScreen = document.querySelector('[data-processing-screen]');
            const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
            const processingMessage = processingScreen?.querySelector('[data-processing-message]');
            const appShell = document.querySelector('.app-shell');

            if (processingScreen && processingHeading && processingMessage) {
                processingHeading.textContent = 'Salvando grupo';
                processingMessage.textContent = 'Gravando as informações do grupo no sistema...';
                processingScreen.hidden = false;
                processingScreen.setAttribute('aria-hidden', 'false');
                appShell?.setAttribute('inert', '');
                document.body.classList.add('processing-locked');
                document.body.style.overflow = 'hidden';
            }

            const formData = new FormData(formSaveSelectedGroup);
            btnSaveGroup.disabled = true;
            btnSaveGroup.innerHTML = '<i class="ti ti-loader rotate"></i> <span>Salvando...</span>';

            try {
                const response = await fetch(formSaveSelectedGroup.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.success) {
                    showToast('Sucesso', data.message, 'success');
                    closeModal(dialogNewGroup);
                    loadGroups();
                } else {
                    showToast('Erro', data.message || 'Erro ao salvar grupo.', 'error');
                }
            } catch (error) {
                showToast('Erro', 'Falha na comunicação com o servidor.', 'error');
            } finally {
                btnSaveGroup.disabled = false;
                btnSaveGroup.innerHTML = '<i class="ti ti-device-floppy"></i> <span>Salvar Grupo</span>';
                if (processingScreen) {
                    processingScreen.hidden = true;
                    processingScreen.setAttribute('aria-hidden', 'true');
                    appShell?.removeAttribute('inert');
                    document.body.classList.remove('processing-locked');
                    document.body.style.overflow = '';
                }
            }
        });
    }

    // Ações nos cards (delegadas)
    if (groupsContainer) {
        groupsContainer.addEventListener('click', async (e) => {
            const btnRefresh = e.target.closest('.btn-refresh-group');
            const btnToggle = e.target.closest('.btn-toggle-status');
            const btnDelete = e.target.closest('.btn-delete-group');

            if (btnRefresh) {
                const id = btnRefresh.dataset.id;
                const card = btnRefresh.closest('.group-item-card');
                const name = card?.querySelector('.user-full-name')?.textContent || btnRefresh.dataset.name || 'este grupo';
                refreshGroupData(id, name);
            }

            if (btnToggle) {
                const id = btnToggle.dataset.id;
                const status = btnToggle.dataset.status;
                const card = btnToggle.closest('.group-item-card');
                const name = card?.querySelector('.user-full-name')?.textContent || 'este grupo';
                const actionKey = status === 'active' ? 'group-status-inativar' : 'group-status-ativar';

                if (typeof window.triggerActionConfirm === 'function') {
                    window.triggerActionConfirm(actionKey, name, () => {
                        toggleStatus(id);
                    });
                } else {
                    toggleStatus(id);
                }
            }

            if (btnDelete) {
                const id = btnDelete.dataset.id;
                const name = btnDelete.dataset.name;
                deleteGroup(id, name);
            }
        });
    }

    async function loadGroups() {
        const q = searchInput ? searchInput.value.trim() : '';
        const url = `${getBaseUrl()}/grupos-whatsapp/feed?status=${currentStatus}&q=${encodeURIComponent(q)}`;

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if (data.success) {
                groupsContainer.innerHTML = data.htmlCards;
                applyFilters();
                
                // Atualiza contadores
                const badgeAll = document.getElementById('badge-all');
                const badgeActive = document.getElementById('badge-active');
                const badgeInactive = document.getElementById('badge-inactive');

                if (badgeAll) badgeAll.textContent = data.metrics.total;
                if (badgeActive) badgeActive.textContent = data.metrics.active;
                if (badgeInactive) badgeInactive.textContent = data.metrics.inactive;
            }
        } catch (error) {
            console.error('Erro ao carregar grupos:', error);
        }
    }

    if (btnSyncGroups) {
        btnSyncGroups.addEventListener('click', () => {
            if (typeof window.triggerActionConfirm === 'function') {
                window.triggerActionConfirm('group-sync-all', 'todos os grupos', () => {
                    syncGroups();
                });
            } else {
                syncGroups();
            }
        });
    }

    async function syncGroups() {
        const btn = document.getElementById('btn-sync-groups') || document.getElementById('btn-sync-empty');
        if (!btn) return;

        const processingScreen = document.querySelector('[data-processing-screen]');
        const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
        const processingMessage = processingScreen?.querySelector('[data-processing-message]');
        const appShell = document.querySelector('.app-shell');

        if (processingScreen && processingHeading && processingMessage) {
            processingHeading.textContent = 'Enfileirando Atualização';
            processingMessage.textContent = 'Enviando grupos para a Central de Trabalho...';
            processingScreen.hidden = false;
            processingScreen.setAttribute('aria-hidden', 'false');
            appShell?.setAttribute('inert', '');
            document.body.classList.add('processing-locked');
            document.body.style.overflow = 'hidden';
        }

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader rotate"></i> <span>Sincronizando...</span>';

        try {
            const response = await fetch(`${getBaseUrl()}/grupos-whatsapp/sincronizar`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `csrf_test_name=${getCsrfToken()}`
            });
            const data = await response.json();

            if (data.success) {
                showToast('Atualização Enfileirada', data.message, 'success');
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1200);
                } else {
                    loadGroups();
                }
            } else {
                showToast('Rotina de Trabalho Desativada', data.message || 'Erro ao sincronizar grupos.', 'warning');
            }
        } catch (error) {
            showToast('Erro', 'Falha na comunicação com o servidor.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (processingScreen) {
                processingScreen.hidden = true;
                processingScreen.setAttribute('aria-hidden', 'true');
                appShell?.removeAttribute('inert');
                document.body.classList.remove('processing-locked');
                document.body.style.overflow = '';
            }
        }
    }

    async function toggleStatus(id) {
        const processingScreen = document.querySelector('[data-processing-screen]');
        const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
        const processingMessage = processingScreen?.querySelector('[data-processing-message]');
        const appShell = document.querySelector('.app-shell');

        if (processingScreen && processingHeading && processingMessage) {
            processingHeading.textContent = 'Atualizando status';
            processingMessage.textContent = 'Alterando a disponibilidade do grupo...';
            processingScreen.hidden = false;
            processingScreen.setAttribute('aria-hidden', 'false');
            appShell?.setAttribute('inert', '');
            document.body.classList.add('processing-locked');
            document.body.style.overflow = 'hidden';
        }

        try {
            const response = await fetch(`${getBaseUrl()}/grupos-whatsapp/${id}/status`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `csrf_test_name=${getCsrfToken()}`
            });
            const data = await response.json();

            if (data.success) {
                showToast('Sucesso', data.message, 'success');
                loadGroups();
            } else {
                showToast('Erro', data.message || 'Erro ao alterar status.', 'error');
            }
        } catch (error) {
            showToast('Erro', 'Falha na comunicação com o servidor.', 'error');
        } finally {
            if (processingScreen) {
                processingScreen.hidden = true;
                processingScreen.setAttribute('aria-hidden', 'true');
                appShell?.removeAttribute('inert');
                document.body.classList.remove('processing-locked');
                document.body.style.overflow = '';
            }
        }
    }

    async function refreshGroupData(id, name) {
        const processingScreen = document.querySelector('[data-processing-screen]');
        const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
        const processingMessage = processingScreen?.querySelector('[data-processing-message]');
        const appShell = document.querySelector('.app-shell');

        if (processingScreen && processingHeading && processingMessage) {
            processingHeading.textContent = 'Enfileirando Atualização';
            processingMessage.textContent = `Enviando atualização de "${name}" para a Central de Trabalho...`;
            processingScreen.hidden = false;
            processingScreen.setAttribute('aria-hidden', 'false');
            appShell?.setAttribute('inert', '');
            document.body.classList.add('processing-locked');
            document.body.style.overflow = 'hidden';
        }

        try {
            const response = await fetch(`${getBaseUrl()}/grupos-whatsapp/${id}/atualizar-dados`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `csrf_test_name=${getCsrfToken()}`
            });
            const data = await response.json();

            if (data.success) {
                showToast('Atualização Enfileirada', data.message, 'success');
            } else {
                showToast('Rotina de Trabalho Desativada', data.message || 'Erro ao atualizar dados do grupo.', 'warning');
            }
        } catch (error) {
            showToast('Erro', 'Falha na comunicação com o servidor.', 'error');
        } finally {
            if (processingScreen) {
                processingScreen.hidden = true;
                processingScreen.setAttribute('aria-hidden', 'true');
                appShell?.removeAttribute('inert');
                document.body.classList.remove('processing-locked');
                document.body.style.overflow = '';
            }
        }
    }

    function deleteGroup(id, name) {
        if (typeof window.triggerActionConfirm === 'function') {
            window.triggerActionConfirm('group-delete', name, () => {
                executeDelete(id);
            });
        } else if (confirm(`Tem certeza que deseja excluir o grupo "${name}" do sistema?`)) {
            executeDelete(id);
        }
    }

    async function executeDelete(id) {
        const processingScreen = document.querySelector('[data-processing-screen]');
        const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
        const processingMessage = processingScreen?.querySelector('[data-processing-message]');
        const appShell = document.querySelector('.app-shell');

        if (processingScreen && processingHeading && processingMessage) {
            processingHeading.textContent = 'Excluindo grupo';
            processingMessage.textContent = 'Removendo o grupo cadastrado do sistema...';
            processingScreen.hidden = false;
            processingScreen.setAttribute('aria-hidden', 'false');
            appShell?.setAttribute('inert', '');
            document.body.classList.add('processing-locked');
            document.body.style.overflow = 'hidden';
        }

        try {
            const response = await fetch(`${getBaseUrl()}/grupos-whatsapp/${id}/excluir`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `csrf_test_name=${getCsrfToken()}`
            });
            const data = await response.json();

            if (data.success) {
                showToast('Sucesso', data.message, 'success');
                loadGroups();
            } else {
                showToast('Erro', data.message || 'Erro ao excluir grupo.', 'error');
            }
        } catch (error) {
            showToast('Erro', 'Falha na comunicação com o servidor.', 'error');
        } finally {
            if (processingScreen) {
                processingScreen.hidden = true;
                processingScreen.setAttribute('aria-hidden', 'true');
                appShell?.removeAttribute('inert');
                document.body.classList.remove('processing-locked');
                document.body.style.overflow = '';
            }
        }
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || 
               document.querySelector('input[name="csrf_test_name"]')?.value || '';
    }

    function showToast(title, message, type = 'info') {
        if (type === 'error' || type === 'warning') {
            console.error(`[${title}] ${message}`);
        }
        const container = document.querySelector('[data-toast-container]');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `app-toast is-${type}`;
        toast.innerHTML = `
            <div class="toast-indicator" aria-hidden="true"></div>
            <div class="toast-body">
                <p class="toast-title">${title}</p>
                <p class="toast-message">${message}</p>
            </div>
            <button class="toast-close" type="button" aria-label="Fechar">&times;</button>
        `;

        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('is-visible'));

        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => removeToast(toast));
        }

        setTimeout(() => removeToast(toast), 4000);
    }

    function removeToast(toast) {
        toast.classList.remove('is-visible');
        setTimeout(() => toast.remove(), 250);
    }
});
