(() => {
    const sheet = document.querySelector('[data-mobile-more]');
    const openButton = document.querySelector('[data-open-more]');
    const closeButton = document.querySelector('[data-close-more]');
    let lastFocused = null;

    if (sheet && openButton && closeButton) {
        const closeSheet = () => {
            sheet.classList.remove('open');
            sheet.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            lastFocused?.focus();
        };
        const openSheet = () => {
            lastFocused = document.activeElement;
            sheet.classList.add('open');
            sheet.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            closeButton.focus();
        };
        openButton.addEventListener('click', openSheet);
        closeButton.addEventListener('click', closeSheet);
        sheet.addEventListener('click', (event) => event.target === sheet && closeSheet());
        document.addEventListener('keydown', (event) => event.key === 'Escape' && sheet.classList.contains('open') && closeSheet());
    }

    const appShell = document.querySelector('.app-shell');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    if (appShell && sidebarToggle) {
        const setSidebarCollapsed = (collapsed) => {
            appShell.classList.toggle('sidebar-collapsed', collapsed);
            sidebarToggle.setAttribute('aria-expanded', String(!collapsed));
            sidebarToggle.setAttribute('aria-label', collapsed ? 'Expandir menu lateral' : 'Recolher menu lateral');
            sidebarToggle.title = collapsed ? 'Expandir menu lateral' : 'Recolher menu lateral';
        };
        setSidebarCollapsed(localStorage.getItem('sidebar-collapsed') === 'true');
        sidebarToggle.addEventListener('click', () => {
            const collapsed = !appShell.classList.contains('sidebar-collapsed');
            setSidebarCollapsed(collapsed);
            localStorage.setItem('sidebar-collapsed', String(collapsed));
        });
    }

    const processingScreen = document.querySelector('[data-processing-screen]');
    const processingHeading = processingScreen?.querySelector('[data-processing-heading]');
    const processingMessage = processingScreen?.querySelector('[data-processing-message]');
    const processingJoke = processingScreen?.querySelector('[data-processing-joke]');
    const processingJokes = [
        'O sistema tomou um café e já está trabalhando.',
        'Organizando os bytes por ordem de importância.',
        'Convencendo o banco de dados a colaborar sem drama.',
        'Consultando o manual secreto dos códigos que funcionam.',
        'Apertando os parafusos digitais. Nenhum dado será machucado.',
        'Quase lá — até os servidores precisam respirar fundo.',
        'Transformando cliques em resultados. Ciência? Quase isso.',
        'Dando um trato nos dados antes de devolvê-los brilhando.',
    ];
    const processingOperations = [
        { match: '/logout', title: 'Encerrando sua sessão', message: 'Protegendo seus dados antes de sair.' },
        { match: '/configuracoes/layout', title: 'Salvando o layout', message: 'Aplicando a nova largura em todo o painel.' },
        { match: '/configuracoes/empresa/whatsapp/padrao', title: 'Definindo o WhatsApp padrão', message: 'Atualizando o número principal dos atendimentos.' },
        { match: '/configuracoes/empresa/whatsapp/status', title: 'Atualizando o status', message: 'Aplicando a nova disponibilidade do WhatsApp.' },
        { match: '/configuracoes/empresa/whatsapp/excluir', title: 'Excluindo o WhatsApp', message: 'Removendo o número dos dados de atendimento.' },
        { match: '/configuracoes/empresa/whatsapp', title: 'Salvando o WhatsApp', message: 'Validando e salvando os dados de atendimento.' },
        { match: '/configuracoes/empresa', title: 'Atualizando a empresa', message: 'Validando e salvando os dados institucionais.' },
        { match: '/configuracoes/evolution/testar', title: 'Testando a conexão', message: 'Conversando com a Evolution API e verificando as credenciais.' },
        { match: '/configuracoes/evolution/instancias/conectar', title: 'Conectando a instância', message: 'Solicitando uma sessão segura e preparando o QR Code.' },
        { match: '/configuracoes/evolution/instancias/desconectar', title: 'Desconectando a instância', message: 'Encerrando a sessão do WhatsApp com segurança.' },
        { match: '/configuracoes/evolution/instancias/excluir', title: 'Excluindo a instância', message: 'Removendo a instância e seus vínculos da Evolution API.' },
        { match: '/configuracoes/evolution/instancias/padrao', title: 'Atualizando a instância padrão', message: 'Salvando a preferência para os próximos envios.' },
        { match: '/configuracoes/evolution/instancias/testar-envio', title: 'Enviando a mensagem de teste', message: 'Validando a instância e entregando a mensagem pelo WhatsApp.' },
        { match: '/configuracoes/evolution/instancias', title: 'Criando a instância', message: 'Validando os dados e preparando a nova conexão.' },
        { match: '/configuracoes/evolution', title: 'Salvando a Evolution API', message: 'Protegendo as credenciais e atualizando a integração.' },
        { match: '/configuracoes/meta-ads/testar', title: 'Testando a API da Meta', message: 'Enviando evento de teste para o Pixel / Conversions API da Meta.' },
        { match: '/configuracoes/meta-ads', title: 'Salvando o Meta Ads', message: 'Criptografando o token e salvando o Pixel ID com segurança.' },
        { match: '/configuracoes/modelos-mensagens/excluir', title: 'Excluindo o modelo', message: 'Removendo o modelo reutilizável de mensagem.' },
        { match: '/configuracoes/modelos-mensagens/status', title: 'Atualizando o modelo', message: 'Alterando o status de disponibilidade do modelo.' },
        { match: '/configuracoes/modelos-mensagens', title: 'Salvando modelo de mensagem', message: 'Gravando os dados e tags do modelo com segurança.' },
        { match: '/configuracoes/landing-leads', title: 'Salvando Landing Page', message: 'Publicando as novas configurações da página de leads.' },
        { match: '/usuarios/status', title: 'Atualizando o usuário', message: 'Aplicando o novo status de acesso do usuário.' },
        { match: '/usuarios/excluir', title: 'Excluindo o usuário', message: 'Removendo permanentemente a conta de acesso.' },
        { match: '/usuarios/redefinir-senha', title: 'Redefinindo a senha', message: 'Criptografando e atualizando as novas credenciais.' },
        { match: '/usuarios/novo', title: 'Criando usuário', message: 'Cadastrando o novo usuário e configurando suas permissões.' },
        { match: '/usuarios/editar', title: 'Salvando alterações', message: 'Atualizando os dados cadastrais e permissões do usuário.' },
        { match: '/leads-vip/excluir', title: 'Excluindo lead', message: 'Removendo permanentemente o contato captado.' },
        { match: '/leads-vip/editar', title: 'Atualizando lead', message: 'Salvando as informações do contato.' },
    ];
    const showProcessingOverlay = (title, message) => {
        if (!processingScreen || !processingHeading || !processingMessage || !processingJoke) return;
        processingHeading.textContent = title;
        processingMessage.textContent = message;
        processingJoke.textContent = processingJokes[Math.floor(Math.random() * processingJokes.length)];
        processingScreen.hidden = false;
        processingScreen.setAttribute('aria-hidden', 'false');
        appShell?.setAttribute('inert', '');
        document.body.classList.add('processing-locked');
        document.body.style.overflow = 'hidden';
    };
    const showProcessing = (form, submitter = null) => {
        if (!form) return;
        const action = submitter?.formAction || form.action || form.getAttribute('action') || window.location.pathname;
        let pathname = '';
        try { pathname = new URL(action, window.location.href).pathname; } catch { pathname = ''; }
        const confirmedOperations = {
            default: { title: 'Definindo o WhatsApp padrão', message: 'Atualizando o número principal dos atendimentos.' },
            activate: { title: 'Ativando o WhatsApp', message: 'Liberando o número para os atendimentos.' },
            deactivate: { title: 'Inativando o WhatsApp', message: 'Atualizando a disponibilidade do número.' },
            delete: { title: 'Excluindo o WhatsApp', message: 'Removendo o número dos dados de atendimento.' },
            'evolution-default': { title: 'Atualizando a instância padrão', message: 'Salvando a preferência para os próximos envios.' },
            'evolution-logout': { title: 'Desconectando a instância', message: 'Encerrando a sessão do WhatsApp com segurança.' },
            'evolution-delete': { title: 'Excluindo a instância', message: 'Removendo a instância e seus vínculos da Evolution API.' },
        };
        const operation = confirmedOperations[form.dataset.confirmAction] || processingOperations.find(({ match }) => pathname.includes(match));
        form.dataset.processingActive = 'true';
        showProcessingOverlay(
            form.dataset.processingTitle || operation?.title || 'Processando sua solicitação',
            form.dataset.processingMessage || operation?.message || 'Validando e salvando as informações com segurança.',
        );
    };

    // Interceptar navegação de links do menu para mostrar tela de carregamento
    document.querySelectorAll('.nav-link, .bottom-link, .sheet-link, .brand').forEach(link => {
        link.addEventListener('click', (e) => {
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.defaultPrevented) return;
            
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

            const url = new URL(href, window.location.origin);
            if (url.origin !== window.location.origin) return;

            const path = url.pathname;
            let title = 'Carregando módulo';
            let message = 'Preparando a interface para você.';

            if (path.includes('/grupos-whatsapp')) {
                title = 'Carregando Grupos';
                message = 'Buscando os grupos de WhatsApp.';
            } else if (path.includes('/produtos')) {
                title = 'Carregando Produtos';
                message = 'Buscando o catálogo de produtos.';
            } else if (path.includes('/leads-vip')) {
                title = 'Carregando Leads';
                message = 'Buscando os contatos captados.';
            } else if (path.includes('/usuarios')) {
                title = 'Carregando Usuários';
                message = 'Buscando os usuários do sistema.';
            } else if (path.includes('/configuracoes')) {
                title = 'Carregando Configurações';
                message = 'Buscando as configurações do sistema.';
            } else if (path === '/' || path === '') {
                title = 'Carregando Visão Geral';
                message = 'Buscando os dados do dashboard.';
            }

            showProcessingOverlay(title, message);
        });
    });

    // Helper global para sincronizar CSRF em todos os formulários da página
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

    const syncFormCsrf = (form) => {
        if (!form) return;
        const currentToken = getCsrfToken();
        if (!currentToken) return;
        const csrfInput = form.querySelector('input[name="csrf_test_name"]');
        if (csrfInput) {
            csrfInput.value = currentToken;
        }
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (event.defaultPrevented || !(form instanceof HTMLFormElement) || form.method.toLowerCase() !== 'post') return;
        syncFormCsrf(form);
        if (form.matches('[data-qr-connect-form]') || form.matches('[data-confirm-action]')) return;
        if (form.dataset.processingActive === 'true') {
            event.preventDefault();
            return;
        }
        showProcessing(form, event.submitter);
    });

    const errorDialog = document.querySelector('[data-error-dialog]');
    const closeErrorButton = errorDialog?.querySelector('[data-close-error]');
    if (errorDialog && closeErrorButton) {
        const closeError = () => {
            errorDialog.classList.remove('open');
            errorDialog.setAttribute('aria-hidden', 'true');
            appShell?.removeAttribute('inert');
            document.body.classList.remove('processing-locked');
            document.body.style.overflow = '';
            window.setTimeout(() => { errorDialog.hidden = true; }, 200);
        };
        errorDialog.hidden = false;
        errorDialog.setAttribute('aria-hidden', 'false');
        appShell?.setAttribute('inert', '');
        document.body.classList.add('processing-locked');
        document.body.style.overflow = 'hidden';
        window.requestAnimationFrame(() => {
            errorDialog.classList.add('open');
            closeErrorButton.focus();
        });
        closeErrorButton.addEventListener('click', closeError);
        errorDialog.addEventListener('click', (event) => event.target === errorDialog && closeError());
        document.addEventListener('keydown', (event) => event.key === 'Escape' && errorDialog.classList.contains('open') && closeError());
    }

    const successDialog = document.querySelector('[data-success-dialog]');
    const closeSuccessButton = successDialog?.querySelector('[data-close-success]');
    if (successDialog && closeSuccessButton) {
        const closeSuccess = () => {
            successDialog.classList.remove('open');
            successDialog.setAttribute('aria-hidden', 'true');
            appShell?.removeAttribute('inert');
            document.body.classList.remove('processing-locked');
            document.body.style.overflow = '';
            window.setTimeout(() => { successDialog.hidden = true; }, 200);
        };
        successDialog.hidden = false;
        successDialog.setAttribute('aria-hidden', 'false');
        appShell?.setAttribute('inert', '');
        document.body.classList.add('processing-locked');
        document.body.style.overflow = 'hidden';
        window.requestAnimationFrame(() => {
            successDialog.classList.add('open');
            closeSuccessButton.focus();
        });
        closeSuccessButton.addEventListener('click', closeSuccess);
        successDialog.addEventListener('click', (event) => event.target === successDialog && closeSuccess());
        document.addEventListener('keydown', (event) => event.key === 'Escape' && successDialog.classList.contains('open') && closeSuccess());
    }

    const actionDialog = document.querySelector('[data-action-dialog]');
    const cancelAction = document.querySelector('[data-cancel-action]');
    const confirmAction = document.querySelector('[data-confirm-action-button]');
    const actionTitle = document.querySelector('[data-action-title]');
    const actionMessage = document.querySelector('[data-action-message]');
    const actionIcon = document.querySelector('[data-action-icon] i');
    const actionButtonIcon = document.querySelector('[data-action-button-icon]');
    const actionButtonLabel = document.querySelector('[data-action-button-label]');
    let pendingActionForm = null;
    let actionTrigger = null;
    if (actionDialog && cancelAction && confirmAction && actionTitle && actionMessage && actionIcon && actionButtonIcon && actionButtonLabel) {
        const actions = {
            default: { title: 'Tornar WhatsApp padrão?', message: (name) => `O WhatsApp “${name}” será usado como padrão nos atendimentos.`, label: 'Sim, tornar padrão', icon: 'ti-star', buttonIcon: 'ti-star', variant: 'primary' },
            activate: { title: 'Ativar este WhatsApp?', message: (name) => `O WhatsApp “${name}” voltará a ficar disponível para os atendimentos.`, label: 'Sim, ativar', icon: 'ti-circle-check', buttonIcon: 'ti-check', variant: 'success' },
            deactivate: { title: 'Inativar este WhatsApp?', message: (name) => `O WhatsApp “${name}” ficará indisponível até ser ativado novamente.`, label: 'Sim, inativar', icon: 'ti-plug-off', buttonIcon: 'ti-power', variant: 'warning' },
            delete: { title: 'Excluir este WhatsApp?', message: (name) => `O WhatsApp “${name}” será removido permanentemente. Essa ação não pode ser desfeita.`, label: 'Sim, excluir', icon: 'ti-trash', buttonIcon: 'ti-trash', variant: 'danger' },
            'evolution-default': { title: 'Tornar instância padrão?', message: (name) => `A instância “${name}” será usada como padrão nos novos envios.`, label: 'Sim, tornar padrão', icon: 'ti-star', buttonIcon: 'ti-star', variant: 'primary' },
            'evolution-logout': { title: 'Desconectar esta instância?', message: (name) => `A sessão de WhatsApp da instância “${name}” será encerrada. Será necessário ler outro QR Code para reconectar.`, label: 'Sim, desconectar', icon: 'ti-plug-off', buttonIcon: 'ti-plug-off', variant: 'warning' },
            'evolution-delete': { title: 'Excluir esta instância?', message: (name) => `A instância “${name}” e sua sessão serão removidas da Evolution API. Essa ação não pode ser desfeita.`, label: 'Sim, excluir', icon: 'ti-trash', buttonIcon: 'ti-trash', variant: 'danger' },
            'user-status-ativar': { title: 'Ativar este usuário?', message: (name) => `O usuário “${name}” voltará a ter acesso ao sistema.`, label: 'Sim, ativar', icon: 'ti-circle-check', buttonIcon: 'ti-check', variant: 'success' },
            'user-status-inativar': { title: 'Inativar este usuário?', message: (name) => `O usuário “${name}” perderá o acesso ao sistema até ser ativado novamente.`, label: 'Sim, inativar', icon: 'ti-plug-off', buttonIcon: 'ti-power', variant: 'warning' },
            'user-delete': { title: 'Excluir este usuário?', message: (name) => `O usuário “${name}” será removido permanentemente. Essa ação não pode ser desfeita.`, label: 'Sim, excluir', icon: 'ti-trash', buttonIcon: 'ti-trash', variant: 'danger' },
            'lead-delete': { title: 'Excluir este lead?', message: (name) => `O lead “${name}” será excluído permanentemente da lista. Essa ação não pode ser desfeita.`, label: 'Sim, excluir', icon: 'ti-trash', buttonIcon: 'ti-trash', variant: 'danger' },
            'group-status-ativar': { title: 'Ativar este grupo?', message: (name) => `O grupo “${name}” voltará a ficar ativo no sistema.`, label: 'Sim, ativar', icon: 'ti-circle-check', buttonIcon: 'ti-check', variant: 'success' },
            'group-status-inativar': { title: 'Inativar este grupo?', message: (name) => `O grupo “${name}” ficará inativo no sistema até ser ativado novamente.`, label: 'Sim, inativar', icon: 'ti-plug-off', buttonIcon: 'ti-power', variant: 'warning' },
            'group-delete': { title: 'Excluir este grupo?', message: (name) => `O grupo “${name}” será removido do sistema. (Não será excluído do WhatsApp).`, label: 'Sim, excluir', icon: 'ti-trash', buttonIcon: 'ti-trash', variant: 'danger' },
            'image-delete': { title: 'Excluir imagem?', message: () => 'A imagem será removida do produto permanentemente. Essa ação não pode ser desfeita.', label: 'Sim, excluir', icon: 'ti-trash', buttonIcon: 'ti-trash', variant: 'danger' },
            logout: { title: 'Deseja realmente sair?', message: () => 'Sua sessão atual será encerrada no navegador com segurança.', label: 'Sim, sair agora', icon: 'ti-logout', buttonIcon: 'ti-logout', variant: 'danger' },
        };
        const closeActionDialog = () => {
            actionDialog.classList.remove('open');
            actionDialog.hidden = true;
            document.body.style.overflow = '';
            pendingActionForm = null;
            window.__pendingActionCallback = null;
            actionTrigger?.focus();
        };

        window.triggerActionConfirm = (actionType, name, onConfirm) => {
            const config = actions[actionType];
            if (!config) {
                if (typeof onConfirm === 'function') onConfirm();
                return;
            }
            window.__pendingActionCallback = onConfirm;
            pendingActionForm = null;
            actionTitle.textContent = config.title;
            actionMessage.textContent = config.message(name || 'selecionado');
            actionButtonLabel.textContent = config.label;
            actionIcon.className = `ti ${config.icon}`;
            actionButtonIcon.className = `ti ${config.buttonIcon}`;
            actionDialog.dataset.variant = config.variant;
            confirmAction.className = `button ${config.variant === 'primary' ? 'primary' : `${config.variant}-solid`}`;
            confirmAction.disabled = false;
            actionDialog.hidden = false;
            actionDialog.classList.add('open');
            document.body.style.overflow = 'hidden';
            cancelAction.focus();
        };

        // Delegação global para formulários com confirmação (inclusive os inseridos dinamicamente)
        document.addEventListener('submit', (event) => {
            const form = event.target.closest('[data-confirm-action]');
            if (!form) return;
            event.preventDefault();
            const config = actions[form.dataset.confirmAction];
            if (!config) return;
            pendingActionForm = form;
            window.__pendingActionCallback = null;
            actionTrigger = event.submitter;
            const name = form.dataset.whatsappName || form.dataset.actionName || 'selecionado';
            actionTitle.textContent = config.title;
            actionMessage.textContent = config.message(name);
            actionButtonLabel.textContent = config.label;
            actionIcon.className = `ti ${config.icon}`;
            actionButtonIcon.className = `ti ${config.buttonIcon}`;
            actionDialog.dataset.variant = config.variant;
            confirmAction.className = `button ${config.variant === 'primary' ? 'primary' : `${config.variant}-solid`}`;
            confirmAction.disabled = false;
            actionDialog.hidden = false;
            actionDialog.classList.add('open');
            document.body.style.overflow = 'hidden';
            cancelAction.focus();
        });
        cancelAction.addEventListener('click', closeActionDialog);
        actionDialog.addEventListener('click', (event) => event.target === actionDialog && closeActionDialog());
        document.addEventListener('keydown', (event) => event.key === 'Escape' && actionDialog.classList.contains('open') && closeActionDialog());
        confirmAction.addEventListener('click', () => {
            if (typeof window.__pendingActionCallback === 'function') {
                const cb = window.__pendingActionCallback;
                closeActionDialog();
                cb();
                return;
            }
            if (!pendingActionForm) return;
            confirmAction.disabled = true;
            syncFormCsrf(pendingActionForm);
            showProcessing(pendingActionForm, actionTrigger);
            pendingActionForm.submit();
        });
    }

    // Toggle switch status dinamico nas configurações
    document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(toggle => {
        toggle.addEventListener('change', () => {
            const labelText = toggle.closest('.toggle-switch')?.querySelector('.toggle-label-text');
            if (labelText) {
                labelText.textContent = toggle.checked ? 'Ativo' : 'Inativo';
            }
        });
    });

    const settings = document.querySelector('[data-settings-root]');
    if (settings) {
        const tabs = [...settings.querySelectorAll('[data-settings-tab]')];
        const panels = [...settings.querySelectorAll('[data-settings-panel]')];
        const selectTab = (name) => {
            tabs.forEach((tab) => {
                const active = tab.dataset.settingsTab === name;
                tab.classList.toggle('active', active);
                tab.setAttribute('aria-selected', String(active));
            });
            panels.forEach((panel) => { panel.hidden = panel.dataset.settingsPanel !== name; });
            const url = new URL(window.location.href);
            name === 'layout' ? url.searchParams.delete('tab') : url.searchParams.set('tab', name);
            window.history.replaceState({}, '', url);
        };
        tabs.forEach((tab) => tab.addEventListener('click', () => {
            const name = tab.dataset.settingsTab;
            if (name === 'evolution' && settings.dataset.activeTab !== 'evolution') {
                showProcessingOverlay(
                    'Carregando instâncias',
                    'Sincronizando as instâncias com a Evolution API antes de mostrar os dados.',
                );
                const url = new URL(window.location.href);
                url.searchParams.set('tab', 'evolution');
                window.location.assign(url);
                return;
            }
            selectTab(name);
        }));

    const layoutForm = settings.querySelector('[data-layout-form]');
    const widthInput = settings.querySelector('[data-width-input]');
    const range = settings.querySelector('[data-width-range]');
    const output = settings.querySelector('[data-width-output]');
    const presets = [...settings.querySelectorAll('[data-width-preset]')];
    const saveBar = settings.querySelector('[data-save-bar]');
    const cancelButton = settings.querySelector('[data-cancel-layout]');
    const savedWidth = settings.dataset.savedWidth;

    if (layoutForm && appShell && widthInput && range && output && saveBar && cancelButton) {
        const displayValue = (value) => value === 'fluid' ? '100%' : `${value}px`;
        const applyPreview = (value) => {
            widthInput.value = value;
            output.value = displayValue(value);
            output.textContent = displayValue(value);
            appShell.style.setProperty('--layout-max-width', displayValue(value));
            presets.forEach((preset) => {
                const selected = preset.dataset.widthPreset === value;
                preset.classList.toggle('selected', selected);
                preset.setAttribute('aria-pressed', String(selected));
            });
            saveBar.hidden = value === savedWidth;
        };
        presets.forEach((preset) => preset.addEventListener('click', () => {
            const value = preset.dataset.widthPreset;
            if (value !== 'fluid') range.value = value;
            applyPreview(value);
        }));
        range.addEventListener('input', () => applyPreview(range.value));
        cancelButton.addEventListener('click', () => {
            range.value = savedWidth === 'fluid' ? range.max : savedWidth;
            applyPreview(savedWidth);
        });
        layoutForm.addEventListener('submit', () => { layoutForm.querySelector('button[type="submit"]').disabled = true; });
    }

    const dirtyForms = settings.querySelectorAll('[data-dirty-form]');
    dirtyForms.forEach((dirtyForm) => {
        const formSaveBar = dirtyForm.querySelector('[data-form-save-bar]');
        const cancelBtn = dirtyForm.querySelector('[data-cancel-form]');
        if (!formSaveBar) return;

        const getFormState = () => {
            const data = {};
            Array.from(dirtyForm.elements).forEach(el => {
                if (!el.name) return;
                if (el.type === 'checkbox') {
                    data[el.name] = el.checked ? el.value : '';
                } else {
                    data[el.name] = el.value;
                }
            });
            return JSON.stringify(data);
        };

        const initialState = getFormState();
        const checkDirty = () => {
            formSaveBar.hidden = getFormState() === initialState;
        };

        dirtyForm.addEventListener('input', checkDirty);
        dirtyForm.addEventListener('change', checkDirty);
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                dirtyForm.reset();
                // Força atualização visual do toggle switch se houver
                const toggle = dirtyForm.querySelector('.toggle-switch input[type="checkbox"]');
                const labelText = dirtyForm.querySelector('.toggle-label-text');
                if (toggle && labelText) {
                    labelText.textContent = toggle.checked ? 'Ativo' : 'Inativo';
                }
                formSaveBar.hidden = true;
            });
        }

        dirtyForm.addEventListener('submit', () => {
            dirtyForm.querySelector('button[type="submit"]')?.setAttribute('disabled', 'true');
        });
    });

    const formatPhone = (value) => {
        let digits = value.replace(/\D/g, '');
        if (digits.startsWith('55') && digits.length > 11) digits = digits.slice(2);
        digits = digits.slice(0, 11);
        if (digits.length <= 2) return digits ? `(${digits}` : '';
        const areaCode = digits.slice(0, 2);
        const number = digits.slice(2);
        if (number.length <= 4) return `(${areaCode}) ${number}`;
        const splitAt = number.length > 8 ? 5 : 4;
        return `(${areaCode}) ${number.slice(0, splitAt)}-${number.slice(splitAt)}`;
    };

    const editor = settings.querySelector('[data-whatsapp-editor]');
    if (editor) {
        const phoneInput = editor.querySelector('[data-whatsapp-phone]');
        phoneInput.addEventListener('input', () => { phoneInput.value = formatPhone(phoneInput.value); });

        const openEditor = (data = {}) => {
            editor.hidden = false;
            editor.querySelector('[data-whatsapp-id]').value = data.id || '';
            editor.querySelector('[data-whatsapp-name]').value = data.name || '';
            phoneInput.value = formatPhone(data.phone || '');
            editor.querySelector('[data-whatsapp-name]').focus();
        };
        settings.querySelector('[data-open-whatsapp]')?.addEventListener('click', () => openEditor());
        settings.querySelector('[data-close-whatsapp]')?.addEventListener('click', () => { editor.hidden = true; editor.reset(); });
        settings.querySelectorAll('[data-edit-whatsapp]').forEach((button) => button.addEventListener('click', () => openEditor(button.dataset)));
    }

    settings.querySelectorAll('[data-toggle-secret]').forEach((button) => button.addEventListener('click', () => {
        const input = button.closest('.secret-input')?.querySelector('[data-secret-input]');
        if (!input) return;
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.setAttribute('aria-label', visible ? 'Mostrar Global API Key' : 'Ocultar Global API Key');
        button.querySelector('i').className = `ti ${visible ? 'ti-eye' : 'ti-eye-off'}`;
    }));

    const instanceEditor = settings.querySelector('[data-instance-editor]');
    const openInstance = settings.querySelector('[data-open-instance]');
    const closeInstance = settings.querySelector('[data-close-instance]');
    if (instanceEditor && openInstance && closeInstance) {
        const instanceName = instanceEditor.querySelector('[data-instance-name]');
        const close = () => { instanceEditor.hidden = true; instanceEditor.reset(); openInstance.focus(); };
        instanceName?.addEventListener('input', () => {
            instanceName.value = instanceName.value.replace(/[^A-Za-z0-9_-]/g, '');
        });
        openInstance.addEventListener('click', () => {
            instanceEditor.hidden = false;
            instanceName?.focus();
        });
        closeInstance.addEventListener('click', close);
        instanceEditor.addEventListener('submit', () => { instanceEditor.querySelector('button[type="submit"]').disabled = true; });
    }

    const templateDialog = settings.querySelector('[data-template-dialog]');
    const templateEditor = templateDialog?.querySelector('[data-template-editor]');
    const openTemplate = settings.querySelector('[data-open-template]');
    const closeTemplate = templateDialog?.querySelector('[data-close-template]');
    const cancelTemplate = templateDialog?.querySelector('[data-cancel-template]');
    const modalTitle = templateDialog?.querySelector('[data-template-modal-title]');

    if (templateDialog && templateEditor && openTemplate && closeTemplate && cancelTemplate) {
        const nameInput = templateEditor.querySelector('[data-template-name]');
        const idInput = templateEditor.querySelector('[data-template-id]');
        const contentInput = templateEditor.querySelector('[data-template-content]');
        const bubbleText = templateDialog.querySelector('[data-template-bubble-text]');
        const getBackgroundElements = () => [...settings.children].filter((el) => el !== templateDialog);
        let templateTrigger = null;

        const formatWhatsAppText = (text) => {
            if (!text) return '';
            // Escape HTML
            let formatted = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Monospace / Code block ```texto```
            formatted = formatted.replace(/```([\s\S]*?)```/g, '<code class="wa-code-block">$1</code>');
            // Monospace inline `texto`
            formatted = formatted.replace(/`([^`\n]+)`/g, '<code class="wa-inline-code">$1</code>');
            // Bold *texto*
            formatted = formatted.replace(/(^|[\s\p{P}])\*([^\*\n]+)\*($|[\s\p{P}])/gu, '$1<strong>$2</strong>$3');
            // Italic _texto_
            formatted = formatted.replace(/(^|[\s\p{P}])_([^_\n]+)_($|[\s\p{P}])/gu, '$1<em>$2</em>$3');
            // Strikethrough ~texto~
            formatted = formatted.replace(/(^|[\s\p{P}])~([^~\n]+)~($|[\s\p{P}])/gu, '$1<del>$2</del>$3');

            return formatted;
        };

        const updatePreview = () => {
            if (!bubbleText) return;
            const raw = contentInput?.value || '';
            if (!raw.trim()) {
                bubbleText.innerHTML = 'Olá! Confira as novidades imperdíveis da nossa loja.';
                return;
            }
            // Substituição de exemplo das tags para o preview
            let preview = raw
                .replace(/\{\{nome\}\}/g, 'iPhone 15 Pro Max 256GB')
                .replace(/\{\{descricao\}\}/g, 'Titânio Natural, tela Super Retina XDR e chip A17 Pro.')
                .replace(/\{\{preco\}\}/g, 'R$ 7.890,00')
                .replace(/\{\{preco_promocional\}\}/g, 'R$ 6.990,00')
                .replace(/\{\{desconto\}\}/g, '12% OFF')
                .replace(/\{\{link\}\}/g, 'https://diasimports.com.br/p/iphone-15');

            bubbleText.innerHTML = formatWhatsAppText(preview);
        };

        const openTpl = (data = {}, trigger = null) => {
            templateTrigger = trigger;
            idInput.value = data.id || '';
            nameInput.value = data.name || '';
            contentInput.value = data.content || '';
            if (modalTitle) {
                modalTitle.textContent = data.id ? 'Editar Modelo' : 'Novo Modelo';
            }
            updatePreview();
            templateDialog.hidden = false;
            templateDialog.setAttribute('aria-hidden', 'false');
            getBackgroundElements().forEach((el) => el.setAttribute('inert', ''));
            document.body.style.overflow = 'hidden';
            window.requestAnimationFrame(() => {
                templateDialog.classList.add('open');
                nameInput.focus();
            });
        };

        const closeTpl = () => {
            templateDialog.classList.remove('open');
            templateDialog.setAttribute('aria-hidden', 'true');
            getBackgroundElements().forEach((el) => el.removeAttribute('inert'));
            document.body.style.overflow = '';
            window.setTimeout(() => {
                templateDialog.hidden = true;
                templateEditor.reset();
                idInput.value = '';
            }, 200);
            templateTrigger?.focus();
        };

        contentInput?.addEventListener('input', updatePreview);

        settings.querySelectorAll('[data-open-template]').forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                openTpl({}, e.currentTarget);
            });
        });
        closeTemplate.addEventListener('click', (e) => {
            e.preventDefault();
            closeTpl();
        });
        cancelTemplate.addEventListener('click', (e) => {
            e.preventDefault();
            closeTpl();
        });
        templateDialog.addEventListener('click', (e) => {
            if (e.target === templateDialog) closeTpl();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && templateDialog.classList.contains('open')) closeTpl();
        });

        settings.querySelectorAll('[data-edit-template]').forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                openTpl(button.dataset, e.currentTarget);
            });
        });

        templateEditor.querySelectorAll('[data-insert-tag]').forEach((button) => {
            button.addEventListener('click', () => {
                const tag = button.dataset.insertTag;
                if (!tag || !contentInput) return;
                const start = contentInput.selectionStart || 0;
                const end = contentInput.selectionEnd || 0;
                const text = contentInput.value;
                contentInput.value = text.substring(0, start) + tag + text.substring(end);
                contentInput.focus();
                contentInput.selectionStart = contentInput.selectionEnd = start + tag.length;
                updatePreview();
            });
        });

        const filterContainer = settings.querySelector('[data-template-filter]');
        if (filterContainer) {
            const filterBtns = filterContainer.querySelectorAll('.filter-btn');
            const templateCards = settings.querySelectorAll('.template-card');
            const emptyFilterState = settings.querySelector('[data-templates-empty-filter]');
            
            const applyTemplateFilter = (filter) => {
                let visibleCount = 0;
                templateCards.forEach(card => {
                    const isInactive = card.classList.contains('inactive');
                    let visible = true;
                    if (filter === 'active') {
                        visible = !isInactive;
                    } else if (filter === 'inactive') {
                        visible = isInactive;
                    }
                    card.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });

                if (emptyFilterState) {
                    emptyFilterState.style.display = (visibleCount === 0 && templateCards.length > 0) ? 'flex' : 'none';
                }
            };

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    applyTemplateFilter(btn.dataset.filter);
                });
            });

            const clearBtn = settings.querySelector('[data-templates-clear-filters]');
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    filterBtns.forEach(b => {
                        if (b.dataset.filter === 'all') {
                            b.classList.add('active');
                        } else {
                            b.classList.remove('active');
                        }
                    });
                    applyTemplateFilter('all');
                });
            }
        }
    }

    const evolutionForm = settings.querySelector('[data-evolution-form]');
    const evolutionSaveBar = settings.querySelector('[data-evolution-save-bar]');
    if (evolutionForm && evolutionSaveBar) {
        const snapshot = new FormData(evolutionForm);
        const isEvolutionDirty = () => [...snapshot.entries()].some(([key, value]) => evolutionForm.elements[key]?.value !== value);
        evolutionForm.addEventListener('input', () => { evolutionSaveBar.hidden = !isEvolutionDirty(); });
        evolutionForm.addEventListener('reset', () => window.setTimeout(() => { evolutionSaveBar.hidden = true; }));
        evolutionForm.addEventListener('submit', (event) => { event.submitter.disabled = true; });
    }

    const metaAdsForm = settings.querySelector('[data-meta-ads-form]');
    const metaAdsSaveBar = settings.querySelector('[data-meta-ads-save-bar]');
    if (metaAdsForm && metaAdsSaveBar) {
        const snapshot = new FormData(metaAdsForm);
        const isMetaAdsDirty = () => {
            const current = new FormData(metaAdsForm);
            for (const [key, value] of current.entries()) {
                if (key === 'csrf_test_name') continue;
                if (key === 'access_token' && value.trim() !== '') return true;
                if (key !== 'access_token' && value !== snapshot.get(key)) return true;
            }
            return false;
        };
        metaAdsForm.addEventListener('input', () => { metaAdsSaveBar.hidden = !isMetaAdsDirty(); });
        metaAdsForm.addEventListener('reset', () => window.setTimeout(() => { metaAdsSaveBar.hidden = true; }));
        metaAdsForm.addEventListener('submit', (event) => { if (event.submitter) event.submitter.disabled = true; });
    }

    const instanceStatusUrl = settings.dataset.instanceStatusUrl;
    const instanceCards = settings.querySelectorAll('[data-instance-card]');
    const evolutionPanel = settings.querySelector('[data-settings-panel="evolution"]');
    let instanceStatusTimer = null;
    let instanceStatusRequest = null;
    let instanceStatusFailures = 0;
    if (instanceStatusUrl && instanceCards.length > 0) {
        const stateLabels = {
            open: 'Conectada',
            connected: 'Conectada',
            close: 'Desconectada',
            closed: 'Desconectada',
            disconnected: 'Desconectada',
            connecting: 'Conectando',
            unknown: 'Desconhecido',
        };
        const updateInstanceCard = (instance) => {
            if (!instance || typeof instance.name !== 'string') return;
            const card = [...instanceCards].find((item) => item.dataset.instanceCard === instance.name);
            if (!card) return;
            const status = card.querySelector('[data-instance-status]');
            const connectForm = card.querySelector('[data-qr-connect-form]');
            const disconnectForm = card.querySelector('[data-instance-disconnect]');
            const sendTestButton = card.querySelector('[data-instance-send-test]');
            const connected = instance.connected === true;
            const state = typeof instance.state === 'string' ? instance.state.toLowerCase() : 'unknown';
            if (status) {
                status.classList.toggle('connected', connected);
                status.classList.toggle('disconnected', !connected);
                status.querySelector('i').className = `ti ${connected ? 'ti-circle-check-filled' : 'ti-circle-x-filled'}`;
                status.querySelector('span').textContent = connected ? 'Conectada' : (stateLabels[state] || state);
            }
            if (connectForm) connectForm.hidden = connected;
            if (disconnectForm) disconnectForm.hidden = !connected;
            if (sendTestButton) sendTestButton.hidden = !connected;
        };
        const isEvolutionVisible = () => !document.hidden && (!evolutionPanel || !evolutionPanel.hidden);
        const scheduleInstanceStatusUpdate = (delay = 5000) => {
            window.clearTimeout(instanceStatusTimer);
            if (isEvolutionVisible()) instanceStatusTimer = window.setTimeout(refreshInstanceStatuses, delay);
        };
        const refreshInstanceStatuses = async () => {
            if (!isEvolutionVisible() || instanceStatusRequest) return;
            instanceStatusRequest = new AbortController();
            let nextDelay = 5000;
            try {
                const response = await fetch(instanceStatusUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                    signal: instanceStatusRequest.signal,
                });
                const payload = await response.json().catch(() => ({}));
                if (response.ok && Array.isArray(payload.instances)) {
                    instanceStatusFailures = 0;
                    payload.instances.forEach(updateInstanceCard);
                } else {
                    instanceStatusFailures += 1;
                    nextDelay = Math.min(60000, 5000 * Math.pow(2, Math.min(instanceStatusFailures, 4)));
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    instanceStatusFailures += 1;
                    nextDelay = Math.min(60000, 5000 * Math.pow(2, Math.min(instanceStatusFailures, 4)));
                }
            } finally {
                instanceStatusRequest = null;
                scheduleInstanceStatusUpdate(nextDelay);
            }
        };
        document.addEventListener('visibilitychange', () => {
            window.clearTimeout(instanceStatusTimer);
            if (!isEvolutionVisible()) {
                instanceStatusRequest?.abort();
            } else {
                refreshInstanceStatuses();
            }
        });
        if (isEvolutionVisible()) scheduleInstanceStatusUpdate();
    }

    const sendTestDialog = settings.querySelector('[data-send-test-dialog]');
    const sendTestForm = sendTestDialog?.querySelector('[data-send-test-form]');
    const sendTestPhone = sendTestDialog?.querySelector('[data-send-test-phone]');
    const sendTestInstanceName = sendTestDialog?.querySelector('[data-send-test-instance-name]');
    const sendTestInstance = sendTestDialog?.querySelector('[data-send-test-instance]');
    const closeSendTestButton = sendTestDialog?.querySelector('[data-close-send-test]');
    const cancelSendTestButton = sendTestDialog?.querySelector('[data-cancel-send-test]');
    let sendTestTrigger = null;
    if (sendTestDialog && sendTestForm && sendTestPhone && sendTestInstanceName && sendTestInstance && closeSendTestButton && cancelSendTestButton) {
        const backgroundElements = [...settings.children].filter((element) => element !== sendTestDialog);
        const closeSendTestDialog = () => {
            sendTestDialog.classList.remove('open');
            sendTestDialog.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            window.setTimeout(() => { sendTestDialog.hidden = true; }, 200);
            sendTestForm.reset();
            sendTestTrigger?.focus();
        };
        settings.querySelectorAll('[data-instance-send-test]').forEach((button) => button.addEventListener('click', () => {
            sendTestTrigger = button;
            sendTestInstanceName.value = button.dataset.instanceName || '';
            sendTestInstance.textContent = button.dataset.instanceLabel || button.dataset.instanceName || 'esta instância';
            sendTestPhone.value = '';
            sendTestDialog.hidden = false;
            sendTestDialog.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            window.requestAnimationFrame(() => sendTestDialog.classList.add('open'));
            sendTestPhone.focus();
        }));
        sendTestPhone.addEventListener('input', () => { sendTestPhone.value = formatPhone(sendTestPhone.value); });
        closeSendTestButton.addEventListener('click', closeSendTestDialog);
        cancelSendTestButton.addEventListener('click', closeSendTestDialog);
        sendTestDialog.addEventListener('click', (event) => event.target === sendTestDialog && closeSendTestDialog());
        document.addEventListener('keydown', (event) => {
            if (!sendTestDialog.classList.contains('open')) return;
            if (event.key === 'Escape') {
                closeSendTestDialog();
                return;
            }
            if (event.key !== 'Tab') return;
            const focusable = [...sendTestDialog.querySelectorAll('button:not([disabled]), input:not([disabled])')];
            const first = focusable[0];
            const last = focusable.at(-1);
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last?.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first?.focus();
            }
        });
    }

    const qrDialog = settings.querySelector('[data-qr-dialog]');
    const closeQrButton = qrDialog?.querySelector('[data-close-qr]');
    const retryQrButton = qrDialog?.querySelector('[data-retry-qr]');
    const qrImage = qrDialog?.querySelector('[data-qr-image]');
    const qrLoading = qrDialog?.querySelector('[data-qr-loading]');
    const qrError = qrDialog?.querySelector('[data-qr-error]');
    const qrErrorMessage = qrDialog?.querySelector('[data-qr-error-message]');
    const qrInstance = qrDialog?.querySelector('[data-qr-instance]');
    const qrCountdown = qrDialog?.querySelector('[data-qr-countdown]');
    let activeQrForm = null;
    let qrRefreshTimer = null;
    let qrCountdownTimer = null;
    let qrRequest = null;
    let qrLastFocused = null;
    if (qrDialog && closeQrButton && retryQrButton && qrImage && qrLoading && qrError && qrErrorMessage && qrInstance && qrCountdown) {
        const stopQrUpdates = () => {
            window.clearTimeout(qrRefreshTimer);
            window.clearInterval(qrCountdownTimer);
            qrRequest?.abort();
            qrRequest = null;
        };
        const scheduleQrUpdate = () => {
            let remaining = 20;
            qrCountdown.textContent = `Atualização automática em ${remaining}s`;
            qrCountdownTimer = window.setInterval(() => {
                remaining -= 1;
                qrCountdown.textContent = `Atualização automática em ${remaining}s`;
            }, 1000);
            qrRefreshTimer = window.setTimeout(() => {
                window.clearInterval(qrCountdownTimer);
                requestQrCode(false, false);
            }, 20000);
        };
        const showQrError = (message) => {
            qrLoading.hidden = true;
            qrImage.hidden = true;
            qrError.hidden = false;
            qrErrorMessage.textContent = message;
            qrCountdown.textContent = 'Atualização automática pausada';
        };
        const requestQrCode = async (showLoading = true, forceDisconnect = false) => {
            if (!activeQrForm || qrRequest) return;
            window.clearTimeout(qrRefreshTimer);
            window.clearInterval(qrCountdownTimer);
            if (showLoading) {
                qrLoading.hidden = false;
                qrImage.hidden = true;
            }
            qrError.hidden = true;
            qrRequest = new AbortController();
            const formData = new FormData(activeQrForm);
            if (forceDisconnect) formData.set('force_disconnect', '1');
            try {
                const response = await fetch(activeQrForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    signal: qrRequest.signal,
                });
                const payload = await response.json().catch(() => ({}));
                const csrfInput = activeQrForm.querySelector('input[type="hidden"][name]');
                if (csrfInput && typeof payload.csrfHash === 'string') csrfInput.value = payload.csrfHash;
                if (!response.ok) throw new Error(payload.error || 'Não foi possível comunicar com o servidor.');
                if (payload.connected === true) {
                    qrCountdown.textContent = 'WhatsApp conectado. Atualizando a tela...';
                    qrLoading.hidden = false;
                    qrImage.hidden = true;
                    window.location.reload();
                    return;
                }
                if (typeof payload.qrCode !== 'string' || !payload.qrCode.startsWith('data:image/')) {
                    throw new Error('A Evolution API ainda não disponibilizou um QR Code.');
                }
                qrImage.src = payload.qrCode;
                qrImage.hidden = false;
                qrLoading.hidden = true;
                scheduleQrUpdate();
            } catch (error) {
                if (error.name !== 'AbortError') showQrError(error.message || 'Falha inesperada ao gerar o QR Code.');
            } finally {
                qrRequest = null;
            }
        };
        const closeQrDialog = () => {
            stopQrUpdates();
            qrDialog.classList.remove('open');
            qrDialog.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            window.setTimeout(() => { qrDialog.hidden = true; }, 200);
            qrLastFocused?.focus();
            activeQrForm = null;
        };
        settings.querySelectorAll('[data-qr-connect-form]').forEach((form) => form.addEventListener('submit', (event) => {
            event.preventDefault();
            activeQrForm = form;
            qrLastFocused = event.submitter;
            qrInstance.textContent = form.dataset.instanceLabel || 'esta instância';
            qrDialog.hidden = false;
            qrDialog.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            window.requestAnimationFrame(() => qrDialog.classList.add('open'));
            closeQrButton.focus();
            requestQrCode(true, true);
        }));
        retryQrButton.addEventListener('click', () => requestQrCode(true, false));
        closeQrButton.addEventListener('click', closeQrDialog);
        qrDialog.addEventListener('click', (event) => event.target === qrDialog && closeQrDialog());
        document.addEventListener('keydown', (event) => event.key === 'Escape' && qrDialog.classList.contains('open') && closeQrDialog());
    }

    const actionDialog = settings.querySelector('[data-action-dialog]');
    const cancelAction = settings.querySelector('[data-cancel-action]');
    const confirmAction = settings.querySelector('[data-confirm-action-button]');
    const actionTitle = settings.querySelector('[data-action-title]');
    const actionMessage = settings.querySelector('[data-action-message]');
    const landingForm = settings.querySelector('[data-landing-form]');
    const landingSaveBar = settings.querySelector('[data-landing-save-bar]');
    const cancelLandingButton = settings.querySelector('[data-cancel-landing]');

    if (landingForm && landingSaveBar) {
        const landingInputs = landingForm.querySelectorAll('[data-lp-input]');
        const initialLandingData = new FormData(landingForm);

        // Switcher de telas do preview (Página vs Modal)
        const previewModeButtons = settings.querySelectorAll('[data-preview-mode]');
        const previewScreens = settings.querySelectorAll('[data-preview-screen]');

        previewModeButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const mode = btn.dataset.previewMode;
                previewModeButtons.forEach((b) => b.classList.toggle('active', b === btn));
                previewScreens.forEach((scr) => {
                    const match = scr.dataset.previewScreen === mode;
                    scr.hidden = !match;
                });
            });
        });

        const updateLandingLivePreview = () => {
            landingInputs.forEach((input) => {
                const target = input.dataset.lpInput;
                const value = input.value;

                if (target === 'badge') {
                    const badge = settings.querySelector('[data-preview="badge"] span:last-child');
                    if (badge) badge.textContent = value || 'GRUPO VIP';
                } else if (target === 'headline') {
                    const headline = settings.querySelector('[data-preview="headline"]');
                    if (headline) headline.textContent = value || 'Título da Landing';
                } else if (target === 'subheadline') {
                    const subheadline = settings.querySelector('[data-preview="subheadline"]');
                    if (subheadline) subheadline.textContent = value || 'Descrição';
                } else if (target === 'button-text') {
                    const btn = settings.querySelector('[data-preview="button-text"]');
                    if (btn) btn.textContent = value || 'QUERO MEU ACESSO';
                } else if (target === 'button-subtext') {
                    const btnSub = settings.querySelector('[data-preview="button-subtext"]');
                    if (btnSub) btnSub.textContent = value || '';
                } else if (target === 'seo-title') {
                    const shareTitle = settings.querySelector('[data-share-preview-title]');
                    if (shareTitle) shareTitle.textContent = value || settings.querySelector('[data-lp-input="headline"]')?.value || 'Grupo VIP Dias Imports';
                } else if (target === 'seo-desc') {
                    const shareDesc = settings.querySelector('[data-share-preview-desc]');
                    if (shareDesc) shareDesc.textContent = value || settings.querySelector('[data-lp-input="subheadline"]')?.value || 'Receba oportunidades imperdíveis de importados em primeira mão.';
                } else if (target === 'modal-title') {
                    const modalTitle = settings.querySelector('[data-preview="modal-title"]');
                    if (modalTitle) modalTitle.textContent = value || 'Acesso Liberado';
                } else if (target === 'modal-desc') {
                    const modalDesc = settings.querySelector('[data-preview="modal-desc"]');
                    if (modalDesc) modalDesc.textContent = value || '';
                } else if (target === 'modal-button-text') {
                    const modalBtn = settings.querySelector('[data-preview="modal-button-text"]');
                    if (modalBtn) modalBtn.textContent = value || 'ENTRAR NO WHATSAPP';
                } else if (target.startsWith('card') && target.endsWith('-title')) {
                    const cardTitle = settings.querySelector(`[data-preview="${target}"]`);
                    if (cardTitle) cardTitle.textContent = value || 'Benefício';
                } else if (target.startsWith('card') && target.endsWith('-desc')) {
                    const cardDesc = settings.querySelector(`[data-preview="${target}"]`);
                    if (cardDesc) cardDesc.textContent = value || 'Descrição do benefício';
                } else if (target.startsWith('card') && target.endsWith('-icon')) {
                    const cardIcon = settings.querySelector(`[data-preview="${target}"]`);
                    if (cardIcon) cardIcon.className = `ti ${value || 'ti-star'}`;
                    const num = target.replace('card', '').replace('-icon', '');
                    const cardHeaderIcon = settings.querySelector(`[data-benefit-icon-preview="${num}"]`);
                    if (cardHeaderIcon) cardHeaderIcon.className = `ti ${value || 'ti-star'}`;
                    const inputFieldIcon = settings.querySelector(`[data-input-icon-preview="${num}"]`);
                    if (inputFieldIcon) inputFieldIcon.className = `ti ${value || 'ti-star'}`;
                }
            });
        };

        const isLandingDirty = () => {
            const currentData = new FormData(landingForm);
            for (const [key, val] of initialLandingData.entries()) {
                if (key === 'csrf_test_name') continue;
                if (currentData.get(key) !== val) return true;
            }
            return false;
        };

        const mockupScreen = settings.querySelector('[data-mockup-screen]');
        const modelRadios = landingForm.querySelectorAll('[data-lp-model-radio]');
        const paletteRadios = landingForm.querySelectorAll('[data-lp-palette-radio]');
        const bgAnimRadios = landingForm.querySelectorAll('[data-lp-bganim-radio]');
        const btnAnimRadios = landingForm.querySelectorAll('[data-lp-btnanim-radio]');

        const updateModelAndPalettePreview = () => {
            const selectedModel = landingForm.querySelector('[data-lp-model-radio]:checked')?.value || 'model-1';
            const selectedPalette = landingForm.querySelector('[data-lp-palette-radio]:checked')?.value || 'palette-aurora';
            const selectedBgAnim = landingForm.querySelector('[data-lp-bganim-radio]:checked')?.value || 'bg-particles';
            const selectedBtnAnim = landingForm.querySelector('[data-lp-btnanim-radio]:checked')?.value || 'btn-pulse';

            if (mockupScreen) {
                mockupScreen.setAttribute('data-mockup-model', selectedModel);
                mockupScreen.setAttribute('data-mockup-palette', selectedPalette);
                mockupScreen.setAttribute('data-mockup-bganim', selectedBgAnim);
                mockupScreen.setAttribute('data-mockup-btnanim', selectedBtnAnim);
            }

            modelRadios.forEach((r) => {
                r.closest('.template-model-card')?.classList.toggle('active', r.checked);
            });

            paletteRadios.forEach((p) => {
                p.closest('.color-palette-card')?.classList.toggle('active', p.checked);
            });

            bgAnimRadios.forEach((b) => {
                b.closest('.template-model-card')?.classList.toggle('active', b.checked);
            });

            btnAnimRadios.forEach((btn) => {
                btn.closest('.template-model-card')?.classList.toggle('active', btn.checked);
            });
        };

        modelRadios.forEach((r) => {
            r.addEventListener('change', () => {
                updateModelAndPalettePreview();
                landingSaveBar.hidden = !isLandingDirty();
            });
        });

        paletteRadios.forEach((p) => {
            p.addEventListener('change', () => {
                updateModelAndPalettePreview();
                landingSaveBar.hidden = !isLandingDirty();
            });
        });

        bgAnimRadios.forEach((b) => {
            b.addEventListener('change', () => {
                updateModelAndPalettePreview();
                landingSaveBar.hidden = !isLandingDirty();
            });
        });

        btnAnimRadios.forEach((btn) => {
            btn.addEventListener('change', () => {
                updateModelAndPalettePreview();
                landingSaveBar.hidden = !isLandingDirty();
            });
        });

        landingForm.addEventListener('input', () => {
            updateLandingLivePreview();
            updateModelAndPalettePreview();
            landingSaveBar.hidden = !isLandingDirty();
        });

        cancelLandingButton?.addEventListener('click', () => {
            landingForm.reset();
            updateLandingLivePreview();
            updateModelAndPalettePreview();
            landingSaveBar.hidden = true;
        });

        landingForm.addEventListener('submit', () => {
            const submitBtn = landingSaveBar.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
        });

        // Uploader e Cropper de Imagem SEO / WhatsApp
        const btnUploadSeo = settings.querySelector('#btn_upload_seo_image');
        const fileInputSeo = settings.querySelector('#seo_image_input');
        const btnRemoveSeo = settings.querySelector('#btn_remove_seo_image');
        const seoBase64Input = settings.querySelector('#seo_image_base64');
        const seoActionInput = settings.querySelector('#seo_image_action');
        const seoPreviewImg = settings.querySelector('#seo_preview_img');
        const ogThumbLabel = settings.querySelector('#og_thumb_label');
        const whatsappPreviewImg = settings.querySelector('#whatsapp_preview_img');

        const cropperModal = document.getElementById('cropper_modal');
        const cropperImage = document.getElementById('cropper_image');
        const btnCloseCropper = document.getElementById('btn_close_cropper');
        const btnCancelCropper = document.getElementById('btn_cancel_cropper');
        const btnApplyCropper = document.getElementById('btn_apply_cropper');

        let cropperInstance = null;
        let flipX = 1;

        if (btnUploadSeo && fileInputSeo) {
            btnUploadSeo.addEventListener('click', () => fileInputSeo.click());

            fileInputSeo.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) return;

                if (!file.type.match(/^image\/(png|jpeg|jpg|webp)$/i)) {
                    alert('Por favor, selecione uma imagem válida (PNG, JPG ou WEBP).');
                    fileInputSeo.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('A imagem selecionada é muito pesada (máximo 5MB).');
                    fileInputSeo.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (ev) => {
                    cropperImage.src = ev.target.result;
                    cropperModal.hidden = false;
                    cropperModal.setAttribute('aria-hidden', 'false');
                    window.requestAnimationFrame(() => cropperModal.classList.add('open'));
                    document.body.style.overflow = 'hidden';

                    if (cropperInstance) {
                        cropperInstance.destroy();
                        cropperInstance = null;
                    }

                    if (typeof Cropper !== 'undefined') {
                        cropperInstance = new Cropper(cropperImage, {
                            aspectRatio: 1, // Quadrado 1:1 perfeito para WhatsApp e Redes Sociais
                            viewMode: 1,
                            autoCropArea: 1, // Ocupa 100% da dimensão máxima mantendo a proporção 1:1
                            responsive: true,
                            background: false,
                            movable: true,
                            zoomable: true,
                            rotatable: true,
                            scalable: true,
                            zoomOnTouch: true,
                            zoomOnWheel: true,
                        });
                        flipX = 1;
                    }
                };
                reader.readAsDataURL(file);
            });

            const closeCropperModal = () => {
                cropperModal.classList.remove('open');
                cropperModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                window.setTimeout(() => {
                    cropperModal.hidden = true;
                    if (cropperInstance) {
                        cropperInstance.destroy();
                        cropperInstance = null;
                    }
                    fileInputSeo.value = '';
                }, 200);
            };

            btnCloseCropper?.addEventListener('click', closeCropperModal);
            btnCancelCropper?.addEventListener('click', closeCropperModal);

            btnApplyCropper?.addEventListener('click', () => {
                if (!cropperInstance) return;

                const canvas = cropperInstance.getCroppedCanvas({
                    width: 600,
                    height: 600,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                const croppedBase64 = canvas.toDataURL('image/png', 0.92);
                seoBase64Input.value = croppedBase64;
                seoActionInput.value = 'upload';

                seoPreviewImg.src = croppedBase64;
                if (whatsappPreviewImg) whatsappPreviewImg.src = croppedBase64;
                if (ogThumbLabel) ogThumbLabel.textContent = 'Personalizada';
                if (btnRemoveSeo) btnRemoveSeo.style.display = 'inline-flex';

                landingSaveBar.hidden = false;
                closeCropperModal();
            });

            // Controles da Toolbar do Cropper
            const btnCropZoomIn = document.getElementById('btn_crop_zoom_in');
            const btnCropZoomOut = document.getElementById('btn_crop_zoom_out');
            const btnCropRotateLeft = document.getElementById('btn_crop_rotate_left');
            const btnCropRotateRight = document.getElementById('btn_crop_rotate_right');
            const btnCropFlipX = document.getElementById('btn_crop_flip_x');
            const btnCropReset = document.getElementById('btn_crop_reset');

            btnCropZoomIn?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                cropperInstance?.zoom(0.1);
            });

            btnCropZoomOut?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                cropperInstance?.zoom(-0.1);
            });

            btnCropRotateLeft?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                cropperInstance?.rotate(-90);
            });

            btnCropRotateRight?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                cropperInstance?.rotate(90);
            });

            btnCropFlipX?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (!cropperInstance) return;
                flipX = flipX === 1 ? -1 : 1;
                cropperInstance.scaleX(flipX);
            });

            btnCropReset?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (!cropperInstance) return;
                cropperInstance.reset();
                flipX = 1;
            });

            btnRemoveSeo?.addEventListener('click', () => {
                const defaultSrc = seoPreviewImg.dataset.defaultSrc;
                seoBase64Input.value = '';
                seoActionInput.value = 'remove';
                seoPreviewImg.src = defaultSrc;
                if (whatsappPreviewImg) whatsappPreviewImg.src = defaultSrc;
                if (ogThumbLabel) ogThumbLabel.textContent = 'Padrão (DI)';
                btnRemoveSeo.style.display = 'none';
                landingSaveBar.hidden = false;
            });
        }
    }

    // Modal Picker de Ícones para Benefícios da Landing Page
    const iconPickerDialog = settings.querySelector('[data-icon-picker-dialog]');
    const closeIconPicker = iconPickerDialog?.querySelector('[data-close-icon-picker]');
    const iconGrid = iconPickerDialog?.querySelector('[data-icon-grid]');
    const iconSearchInput = iconPickerDialog?.querySelector('[data-icon-search-input]');

    if (iconPickerDialog && closeIconPicker && iconGrid && iconSearchInput) {
        let currentBenefitTarget = null;
        let iconTriggerBtn = null;

        const popularIcons = [
            { icon: 'ti-discount-check', label: 'Desconto Selo', cat: 'ofertas desconto preco' },
            { icon: 'ti-flame', label: 'Fogo / Destaque', cat: 'fogo novidade quente alta lancamento' },
            { icon: 'ti-shield-lock', label: 'Garantia / Seguro', cat: 'seguro seguranca garantia protecao oficial' },
            { icon: 'ti-shield-check', label: 'Segurança Verificada', cat: 'seguro seguranca verificado ok' },
            { icon: 'ti-star', label: 'Estrela VIP', cat: 'vip estrela destaque premio favorito' },
            { icon: 'ti-brand-whatsapp', label: 'WhatsApp', cat: 'whatsapp grupo mensagem chat zap' },
            { icon: 'ti-percentage', label: 'Porcentagem %', cat: 'porcentagem desconto promocao preco' },
            { icon: 'ti-tag', label: 'Etiqueta Oferta', cat: 'tag etiqueta cupom promocao' },
            { icon: 'ti-truck-delivery', label: 'Entrega Expressa', cat: 'frete entrega envio rapido caminhao' },
            { icon: 'ti-package', label: 'Pacote / Produto', cat: 'pacote caixa produto encomenda' },
            { icon: 'ti-gift', label: 'Presente / Bônus', cat: 'presente bonus brinde gratis oferta' },
            { icon: 'ti-trophy', label: 'Troféu Campeão', cat: 'trofeu premio top numero1 qualidade' },
            { icon: 'ti-award', label: 'Medalha / Selo', cat: 'medalha certificado selo oficial' },
            { icon: 'ti-bolt', label: 'Raio / Rápido', cat: 'raio relampago velocidade instantaneo' },
            { icon: 'ti-clock', label: 'Tempo / Imediato', cat: 'tempo relogio hora prazo limited' },
            { icon: 'ti-credit-card', label: 'Cartão / Parcelamento', cat: 'cartao credito parcelado pagamento' },
            { icon: 'ti-wallet', label: 'Economia / Carteira', cat: 'carteira dinheiro economia poupar' },
            { icon: 'ti-currency-dollar', label: 'Cifrão / Dinheiro', cat: 'dinheiro preco dolar valor real' },
            { icon: 'ti-receipt-2', label: 'Nota Fiscal', cat: 'nota fiscal comprovante recibo procedencia' },
            { icon: 'ti-crown', label: 'Coroa VIP', cat: 'coroa vip exclusivo luxo elite' },
            { icon: 'ti-sparkles', label: 'Brilho / Especial', cat: 'brilho novo especial magico lancamento' },
            { icon: 'ti-diamond', label: 'Diamante Premium', cat: 'diamante premium nobre raro' },
            { icon: 'ti-heart-handshake', label: 'Atendimento Humanizado', cat: 'atendimento suporte humano confianca' },
            { icon: 'ti-headset', label: 'Suporte Dedicado', cat: 'suporte pos venda fone ajuda' },
            { icon: 'ti-lock-check', label: 'Compra Protegida', cat: 'cadeado seguro protecao criptografia' },
            { icon: 'ti-thumb-up', label: 'Aprovado / Positivo', cat: 'aprovado joinha satisfacao recomendado' },
            { icon: 'ti-device-mobile', label: 'Celular / Tech', cat: 'celular smartphone iphone tecnologia' },
            { icon: 'ti-device-laptop', label: 'Eletrônicos', cat: 'notebook computador tech' },
            { icon: 'ti-box-seam', label: 'Caixa Lacrada', cat: 'caixa lacrada nova original estoque' },
            { icon: 'ti-bell-ringing', label: 'Avisos / Alertas', cat: 'sino notificacao aviso alerta novidade' },
            { icon: 'ti-users', label: 'Comunidade VIP', cat: 'grupo membros comunidade pessoas clientes' },
            { icon: 'ti-world', label: 'Importação Global', cat: 'mundo importado eua original importacao' },
        ];

        const renderIconGrid = (filter = '') => {
            const query = filter.toLowerCase().trim();
            const filtered = query === '' 
                ? popularIcons 
                : popularIcons.filter(item => item.icon.toLowerCase().includes(query) || item.label.toLowerCase().includes(query) || item.cat.includes(query));

            if (filtered.length === 0) {
                iconGrid.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 24px; color: var(--text-muted);"><i class="ti ti-search-off" style="font-size: 28px; display:block; margin-bottom: 8px;"></i>Nenhum ícone encontrado para essa busca.</div>';
                return;
            }

            iconGrid.innerHTML = filtered.map(item => `
                <button type="button" class="icon-picker-item" data-icon-value="${item.icon}" title="${item.label}">
                    <i class="ti ${item.icon}" aria-hidden="true"></i>
                    <span>${item.label}</span>
                </button>
            `).join('');
        };

        const openIconPickerModal = (benefitNum, triggerBtn) => {
            currentBenefitTarget = benefitNum;
            iconTriggerBtn = triggerBtn;
            iconSearchInput.value = '';
            renderIconGrid();
            iconPickerDialog.hidden = false;
            iconPickerDialog.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            window.requestAnimationFrame(() => {
                iconPickerDialog.classList.add('open');
                iconSearchInput.focus();
            });
        };

        const closeIconPickerModal = () => {
            iconPickerDialog.classList.remove('open');
            iconPickerDialog.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            window.setTimeout(() => {
                iconPickerDialog.hidden = true;
            }, 200);
            iconTriggerBtn?.focus();
        };

        // Escuta os botões que abrem o modal
        settings.querySelectorAll('[data-open-icon-picker]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const benefitNum = btn.dataset.openIconPicker;
                openIconPickerModal(benefitNum, btn);
            });
        });

        // Fechar modal
        closeIconPicker.addEventListener('click', (e) => {
            e.preventDefault();
            closeIconPickerModal();
        });

        iconPickerDialog.addEventListener('click', (e) => {
            if (e.target === iconPickerDialog) closeIconPickerModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && iconPickerDialog.classList.contains('open')) {
                closeIconPickerModal();
            }
        });

        // Pesquisa dinâmica
        iconSearchInput.addEventListener('input', () => {
            renderIconGrid(iconSearchInput.value);
        });

        // Seleção de ícone por delegação de evento
        iconGrid.addEventListener('click', (e) => {
            const itemBtn = e.target.closest('[data-icon-value]');
            if (!itemBtn || !currentBenefitTarget) return;

            const iconClass = itemBtn.dataset.iconValue;
            
            // 1. Atualizar o input correspondente
            const input = settings.querySelector(`input[name="card${currentBenefitTarget}_icon"]`);
            if (input) {
                input.value = iconClass;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // 2. Atualizar o preview do card header
            const headerIcon = settings.querySelector(`[data-benefit-icon-preview="${currentBenefitTarget}"]`);
            if (headerIcon) {
                headerIcon.className = `ti ${iconClass}`;
            }

            // 3. Atualizar o preview do input
            const inputIcon = settings.querySelector(`[data-input-icon-preview="${currentBenefitTarget}"]`);
            if (inputIcon) {
                inputIcon.className = `ti ${iconClass}`;
            }

            // 4. Fechar o modal
            closeIconPickerModal();
        });
    }
}

    // =========================================================================
    // MÓDULO DE USUÁRIOS
    // =========================================================================
    const usersModule = document.querySelector('[data-users-module]');
    if (usersModule) {
        const searchInput = usersModule.querySelector('[data-users-filter-input]');
        const filterPills = usersModule.querySelectorAll('.filter-pill');
        const userCards = usersModule.querySelectorAll('[data-user-card]');
        const emptyState = usersModule.querySelector('[data-users-empty-search]');

        let currentStatusFilter = 'all';
        let currentSearchQuery = '';

        const applyFilters = () => {
            let visibleCount = 0;
            userCards.forEach(card => {
                const name = (card.dataset.name || '').toLowerCase();
                const email = (card.dataset.email || '').toLowerCase();
                const status = card.dataset.status || '';
                const role = card.dataset.role || '';

                const matchesSearch = !currentSearchQuery || name.includes(currentSearchQuery) || email.includes(currentSearchQuery);
                
                let matchesStatus = true;
                if (currentStatusFilter === 'active') {
                    matchesStatus = (status === 'active');
                } else if (currentStatusFilter === 'inactive') {
                    matchesStatus = (status === 'inactive');
                } else if (currentStatusFilter === 'admin') {
                    matchesStatus = (role === 'admin');
                }

                const visible = matchesSearch && matchesStatus;
                card.style.display = visible ? 'flex' : 'none';
                if (visible) visibleCount++;
            });

            if (emptyState) {
                emptyState.style.display = (visibleCount === 0) ? 'flex' : 'none';
                const clearBtn = emptyState.querySelector('[data-users-clear-filters]');
                if (clearBtn) {
                    const hasFilter = Boolean(currentSearchQuery || currentStatusFilter !== 'all');
                    clearBtn.style.display = hasFilter ? 'inline-flex' : 'none';
                }
            }
        };

        // Executar filtro inicial para garantir que o estado vazio seja ocultado se houver usuários
        applyFilters();

        const resetFilters = () => {
            if (searchInput) searchInput.value = '';
            currentSearchQuery = '';
            currentStatusFilter = 'all';
            filterPills.forEach(p => {
                if (p.dataset.filter === 'all') {
                    p.classList.add('active');
                    p.setAttribute('aria-selected', 'true');
                } else {
                    p.classList.remove('active');
                    p.setAttribute('aria-selected', 'false');
                }
            });
            applyFilters();
        };

        const clearBtn = emptyState?.querySelector('[data-users-clear-filters]');
        if (clearBtn) {
            clearBtn.addEventListener('click', resetFilters);
        }

        searchInput?.addEventListener('input', (e) => {
            currentSearchQuery = e.target.value.toLowerCase().trim();
            applyFilters();
        });

        filterPills.forEach(pill => {
            pill.addEventListener('click', () => {
                filterPills.forEach(p => {
                    p.classList.remove('active');
                    p.setAttribute('aria-selected', 'false');
                });
                pill.classList.add('active');
                pill.setAttribute('aria-selected', 'true');
                currentStatusFilter = pill.dataset.filter;
                applyFilters();
            });
        });

        // Modal de Redefinição de Senha
        const resetPwdDialog = document.querySelector('[data-reset-pwd-dialog]');
        const closePwdBtn = resetPwdDialog?.querySelector('[data-close-reset-pwd]');
        const pwdUserName = resetPwdDialog?.querySelector('[data-pwd-user-name]');
        const pwdForm = resetPwdDialog?.querySelector('[data-pwd-form]');

        const closePwdDialog = () => {
            if (!resetPwdDialog) return;
            resetPwdDialog.close();
            resetPwdDialog.hidden = true;
            document.body.style.overflow = '';
        };

        usersModule.querySelectorAll('[data-open-reset-pwd]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!resetPwdDialog || !pwdForm) return;
                const userId = btn.dataset.userId;
                const userName = btn.dataset.userName;
                pwdForm.action = `${window.location.origin}/usuarios/${userId}/redefinir-senha`;
                if (pwdUserName) pwdUserName.textContent = userName;
                const input = pwdForm.querySelector('input[name="new_password"]');
                if (input) input.value = '';
                resetPwdDialog.hidden = false;
                resetPwdDialog.showModal();
                document.body.style.overflow = 'hidden';
                input?.focus();
            });
        });

        closePwdBtn?.addEventListener('click', closePwdDialog);
        resetPwdDialog?.addEventListener('click', (e) => {
            if (e.target === resetPwdDialog) closePwdDialog();
        });
        resetPwdDialog?.addEventListener('cancel', () => {
            document.body.style.overflow = '';
            resetPwdDialog.hidden = true;
        });
    }

    // =========================================================================
    // FORMULÁRIO DE USUÁRIOS
    // =========================================================================
    const userFormRoot = document.querySelector('[data-user-form-root]');
    if (userFormRoot) {
        const dirtyUserForm = userFormRoot.querySelector('[data-dirty-user-form]');
        const userSaveBar = userFormRoot.querySelector('[data-form-save-bar]');
        const roleSelect = userFormRoot.querySelector('[data-role-select]');
        const permissionsContainer = userFormRoot.querySelector('[data-permissions-container]');

        // Alternância da visibilidade do grupo Permissões conforme o perfil selecionado
        if (roleSelect && permissionsContainer) {
            const updatePermissionsVisibility = () => {
                const isUser = (roleSelect.value === 'user');
                permissionsContainer.style.display = isUser ? '' : 'none';
            };

            roleSelect.addEventListener('change', updatePermissionsVisibility);
            updatePermissionsVisibility();
        }

        // Toggle de permissões por módulo
        userFormRoot.querySelectorAll('[data-toggle-module-all]').forEach(btn => {
            btn.addEventListener('click', () => {
                const moduleName = btn.dataset.toggleModuleAll;
                const inputs = userFormRoot.querySelectorAll(`input[data-module="${moduleName}"]`);
                const allChecked = [...inputs].every(i => i.checked);
                inputs.forEach(i => {
                    i.checked = !allChecked;
                });
                btn.textContent = allChecked ? 'Marcar tudo' : 'Desmarcar tudo';
                dirtyUserForm?.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });

        // Detecção de alterações para exibição da Barra Flutuante Salvar/Cancelar
        if (dirtyUserForm && userSaveBar) {
            const getFormState = () => {
                const data = new FormData(dirtyUserForm);
                const obj = {};
                for (let [k, v] of data.entries()) {
                    obj[k] = v;
                }
                return JSON.stringify(obj);
            };

            const initialState = getFormState();

            const checkDirty = () => {
                const currentState = getFormState();
                userSaveBar.hidden = (currentState === initialState);
            };

            dirtyUserForm.addEventListener('input', checkDirty);
            dirtyUserForm.addEventListener('change', checkDirty);
        }
    }

    // Toggle de visualização de senha
    document.querySelectorAll('[data-toggle-pwd]').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-with-toggle')?.querySelector('input');
            if (!input) return;
            const isPassword = (input.type === 'password');
            input.type = isPassword ? 'text' : 'password';
            btn.querySelector('i').className = `ti ${isPassword ? 'ti-eye-off' : 'ti-eye'}`;
        });
    });

    // =========================================================================
    // MONITORAMENTO DE STATUS DO SISTEMA (BANCO DE DADOS & EVOLUTION API - 5s)
    // =========================================================================
    const statusPill = document.getElementById('system-status-pill');
    const statusDot = document.getElementById('system-status-dot');
    const statusText = document.getElementById('system-status-text');

    if (statusPill && statusDot && statusText) {
        const healthUrl = statusPill.dataset.healthUrl || `${window.location.origin}/health/status`;
        let isChecking = false;

        const checkSystemHealth = async () => {
            if (isChecking) return;
            isChecking = true;

            try {
                const response = await fetch(healthUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });

                if (!response.ok) {
                    throw new Error('Falha HTTP ao consultar status');
                }

                const data = await response.json();
                const dbOnline = data.database?.online === true;
                const evoConfigured = data.evolution?.configured === true;
                const evoOnline = data.evolution?.online === true;
                const defaultInstanceConnected = data.evolution?.default_instance_connected === true;

                // Remover classes de alerta anteriores
                statusDot.classList.remove('status-warning', 'status-danger');
                statusPill.classList.remove('status-warning', 'status-danger');

                if (dbOnline && (!evoConfigured || (evoOnline && defaultInstanceConnected))) {
                    // Ambos online e instância padrão conectada (ou Evolution ainda não configurada, banco ok)
                    statusText.textContent = 'Sistema online';
                    statusPill.title = `Banco de Dados: Online${evoConfigured ? ' | WhatsApp: Conectado' : ' | Evolution API: Não configurada'}`;
                } else if (!dbOnline && (!evoConfigured || !evoOnline)) {
                    // Banco e Evolution offline
                    statusDot.classList.add('status-danger');
                    statusPill.classList.add('status-danger');
                    statusText.textContent = 'Sistema instável';
                    statusPill.title = `Banco de Dados: Offline | Evolution API: ${evoConfigured ? 'Offline' : 'Não configurada'}`;
                } else if (!dbOnline) {
                    // Somente banco offline
                    statusDot.classList.add('status-danger');
                    statusPill.classList.add('status-danger');
                    statusText.textContent = 'BD Offline';
                    statusPill.title = `Banco de Dados: Offline | Evolution API: ${evoConfigured ? (evoOnline ? 'Online' : 'Offline') : 'Não configurada'}`;
                } else if (evoConfigured && !evoOnline) {
                    // Banco ok, mas Evolution offline
                    statusDot.classList.add('status-warning');
                    statusPill.classList.add('status-warning');
                    statusText.textContent = 'Evolution offline';
                    statusPill.title = 'Banco de Dados: Online | Evolution API: Offline (verifique a conexão nas configurações)';
                } else if (evoConfigured && evoOnline && !defaultInstanceConnected) {
                    // Banco ok, Evolution online, mas WhatsApp/Instância padrão desconectada
                    statusDot.classList.add('status-warning');
                    statusPill.classList.add('status-warning');
                    statusText.textContent = 'WhatsApp desconectado';
                    statusPill.title = 'Banco de Dados: Online | Instância WhatsApp padrão desconectada';
                } else {
                    statusDot.classList.add('status-warning');
                    statusPill.classList.add('status-warning');
                    statusText.textContent = 'Sistema instável';
                    statusPill.title = 'Instabilidade detectada nos serviços do sistema.';
                }
            } catch (err) {
                // Erro de rede ou indisponibilidade total da aplicação
                statusDot.classList.remove('status-warning');
                statusDot.classList.add('status-danger');
                statusPill.classList.remove('status-warning');
                statusPill.classList.add('status-danger');
                statusText.textContent = 'Sistema offline';
                statusPill.title = 'Falha na conexão com o servidor local.';
            } finally {
                isChecking = false;
            }
        };

        // Primeira checagem logo ao carregar
        checkSystemHealth();

        // Checagem a cada 30 segundos
        window.setInterval(checkSystemHealth, 30000);
    }
})();
