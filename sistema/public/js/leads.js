document.addEventListener('DOMContentLoaded', () => {
    const leadsContainer = document.querySelector('[data-leads-module]');
    if (!leadsContainer) return;

    const refreshCountdownEl = document.querySelector('[data-refresh-countdown]');
    const refreshFillEl = document.querySelector('[data-refresh-fill]');
    const refreshNowBtn = document.querySelector('[data-refresh-now]');
    const dashboardContainer = document.querySelector('[data-dashboard-container]');
    const leadsTbody = document.querySelector('[data-leads-tbody]');
    const searchInput = document.querySelector('[data-leads-search]');
    const dateInput = document.querySelector('[data-leads-date]');
    const leadsCounter = document.querySelector('[data-leads-count]');
    const clearSearchBtn = document.querySelector('[data-clear-search]');
    const clearDateBtn = document.querySelector('[data-clear-date]');

    // Helper para obter o CSRF token do cookie ou meta tag
    const getCsrfToken = () => {
        const name = 'csrf_cookie_name=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        const meta = document.querySelector('meta[name="csrf-hash"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const updateCsrfToken = (newHash) => {
        if (!newHash) return;
        const meta = document.querySelector('meta[name="csrf-hash"]');
        if (meta) meta.setAttribute('content', newHash);
        document.querySelectorAll('input[name="csrf_test_name"]').forEach(input => {
            input.value = newHash;
        });
    };

    let currentPeriod = 7;
    const initialActivePeriodBtn = document.querySelector('[data-period-btn].active');
    if (initialActivePeriodBtn) {
        currentPeriod = parseInt(initialActivePeriodBtn.dataset.periodBtn, 10) || 7;
    }

    const REFRESH_INTERVAL_MS = 10000; // 10 segundos
    let remainingMs = REFRESH_INTERVAL_MS;
    let timerId = null;
    let isFetching = false;

    const maskPhoneInput = (input) => {
        if (!input) return;
        input.addEventListener('input', () => {
            let v = input.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 10) {
                input.value = `(${v.substring(0, 2)}) ${v.substring(2, 7)}-${v.substring(7)}`;
            } else if (v.length > 6) {
                input.value = `(${v.substring(0, 2)}) ${v.substring(2, 6)}-${v.substring(6)}`;
            } else if (v.length > 2) {
                input.value = `(${v.substring(0, 2)}) ${v.substring(2)}`;
            } else if (v.length > 0) {
                input.value = `(${v}`;
            } else {
                input.value = '';
            }
        });
    };

    const fetchLeads = async () => {
        if (isFetching) return;
        isFetching = true;

        const params = new URLSearchParams();
        params.set('period', String(currentPeriod));
        if (searchInput && searchInput.value.trim()) {
            params.set('q', searchInput.value.trim());
        }
        if (dateInput && dateInput.value.trim()) {
            params.set('date', dateInput.value.trim());
        }

        try {
            const res = await fetch(`/leads-vip/feed?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            if (!res.ok) throw new Error('Erro ao sincronizar');
            const data = await res.json();
            if (data.success) {
                if (dashboardContainer && data.htmlMetrics) {
                    dashboardContainer.innerHTML = data.htmlMetrics;
                    bindPeriodButtons();
                }
                if (leadsTbody && data.htmlTable) {
                    leadsTbody.innerHTML = data.htmlTable;
                }
                if (leadsCounter) {
                    leadsCounter.textContent = String(data.totalResults ?? 0);
                }
            }
        } catch (e) {
            console.error('Falha na sincronização de leads:', e);
        } finally {
            isFetching = false;
            remainingMs = REFRESH_INTERVAL_MS;
        }
    };

    const startTimer = () => {
        const stepMs = 100;
        clearInterval(timerId);
        remainingMs = REFRESH_INTERVAL_MS;

        timerId = setInterval(() => {
            remainingMs -= stepMs;
            if (remainingMs <= 0) {
                remainingMs = 0;
                updateProgressBar();
                fetchLeads();
            } else {
                updateProgressBar();
            }
        }, stepMs);
    };

    const updateProgressBar = () => {
        const pct = Math.max(0, Math.min(100, (remainingMs / REFRESH_INTERVAL_MS) * 100));
        if (refreshFillEl) {
            refreshFillEl.style.width = `${pct}%`;
        }
        if (refreshCountdownEl) {
            const seconds = Math.ceil(remainingMs / 1000);
            refreshCountdownEl.textContent = `${seconds}s`;
        }
    };

    const bindPeriodButtons = () => {
        document.querySelectorAll('[data-period-btn]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('[data-period-btn]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentPeriod = parseInt(btn.dataset.periodBtn, 10) || 7;
                remainingMs = REFRESH_INTERVAL_MS;
                fetchLeads();
            });
        });
    };

    let searchDebounce = null;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => {
                remainingMs = REFRESH_INTERVAL_MS;
                fetchLeads();
            }, 300);
        });
    }

    if (dateInput) {
        dateInput.addEventListener('change', () => {
            remainingMs = REFRESH_INTERVAL_MS;
            fetchLeads();
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            clearSearchBtn.remove();
            remainingMs = REFRESH_INTERVAL_MS;
            fetchLeads();
        });
    }

    if (clearDateBtn) {
        clearDateBtn.addEventListener('click', () => {
            if (dateInput) dateInput.value = '';
            clearDateBtn.remove();
            remainingMs = REFRESH_INTERVAL_MS;
            fetchLeads();
        });
    }

    if (refreshNowBtn) {
        refreshNowBtn.addEventListener('click', () => {
            remainingMs = REFRESH_INTERVAL_MS;
            updateProgressBar();
            fetchLeads();
        });
    }

    // Modal de Edição de Lead
    const editDialog = document.querySelector('[data-edit-lead-dialog]');
    const editForm = document.querySelector('[data-edit-lead-form]');
    const editNameInput = document.querySelector('#lead_edit_name');
    const editPhoneInput = document.querySelector('#lead_edit_phone');
    const closeEditBtn = document.querySelector('[data-close-lead-edit]');

    if (editPhoneInput) {
        maskPhoneInput(editPhoneInput);
    }

    const openEditModal = (id, name, phone) => {
        if (!editDialog || !editForm) return;
        editForm.action = `/leads-vip/${id}/editar`;
        if (editNameInput) editNameInput.value = name || '';
        if (editPhoneInput) {
            let digits = String(phone || '').replace(/\D/g, '');
            if (digits.length === 11) {
                editPhoneInput.value = `(${digits.substring(0, 2)}) ${digits.substring(2, 7)}-${digits.substring(7)}`;
            } else if (digits.length === 10) {
                editPhoneInput.value = `(${digits.substring(0, 2)}) ${digits.substring(2, 6)}-${digits.substring(6)}`;
            } else {
                editPhoneInput.value = phone || '';
            }
        }
        editDialog.hidden = false;
        if (typeof editDialog.showModal === 'function') {
            editDialog.showModal();
        }
    };

    const closeEditModal = () => {
        if (!editDialog) return;
        if (typeof editDialog.close === 'function') {
            editDialog.close();
        }
        editDialog.hidden = true;
    };

    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('[data-edit-lead]');
        if (editBtn) {
            e.preventDefault();
            const id = editBtn.dataset.id;
            const name = editBtn.dataset.name;
            const phone = editBtn.dataset.phone;
            openEditModal(id, name, phone);
        }
    });

    if (closeEditBtn) {
        closeEditBtn.addEventListener('click', closeEditModal);
    }

    if (editDialog) {
        editDialog.addEventListener('click', (e) => {
            if (e.target === editDialog) {
                closeEditModal();
            }
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Fecha o modal imediatamente
            closeEditModal();

            // Dispara a tela de bloqueio e processamento global do sistema
            const processingScreen = document.querySelector('[data-processing-screen]');
            const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
            const processingMessage = processingScreen?.querySelector('[data-processing-message]');
            const processingJoke = processingScreen?.querySelector('[data-processing-joke]');
            const appShell = document.querySelector('.app-shell');

            if (processingScreen && processingHeading && processingMessage) {
                processingHeading.textContent = 'Atualizando lead';
                processingMessage.textContent = 'Salvando as informações do contato com segurança.';
                if (processingJoke) {
                    processingJoke.textContent = 'Dando um trato nos dados antes de devolvê-los brilhando.';
                }
                processingScreen.hidden = false;
                processingScreen.setAttribute('aria-hidden', 'false');
                appShell?.setAttribute('inert', '');
                document.body.classList.add('processing-locked');
                document.body.style.overflow = 'hidden';
            }

            try {
                const formData = new FormData(editForm);
                // Garante que o token CSRF mais recente está presente
                const tokenVal = getCsrfToken();
                if (tokenVal) {
                    formData.set('csrf_test_name', tokenVal);
                }

                const response = await fetch(editForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': tokenVal,
                    },
                });

                const result = await response.json();

                if (result.csrfHash) {
                    updateCsrfToken(result.csrfHash);
                }

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Erro ao atualizar o lead.');
                }

                // Atualiza a listagem e dashboard em tempo real
                await fetchLeads();
            } catch (err) {
                alert(err.message || 'Ocorreu um erro ao salvar o lead.');
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
        });
    }

    bindPeriodButtons();
    startTimer();
});
