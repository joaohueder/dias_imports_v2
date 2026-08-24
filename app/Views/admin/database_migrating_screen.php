<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0c12">
    <title><?= esc($pageTitle ?? 'Atualizando Banco de Dados') ?> | Dias Imports</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.1/dist/tabler-icons.min.css">
    <style>
        :root {
            --bg-body: #090b10;
            --card-bg: rgba(18, 22, 33, 0.92);
            --card-border: rgba(255, 255, 255, 0.08);
            --accent-primary: #3b82f6;
            --accent-gradient: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
            --accent-glow: rgba(59, 130, 246, 0.25);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
        }

        /* Fundo com efeito de luz sutil */
        .bg-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            filter: blur(80px);
            z-index: 0;
            animation: pulseGlow 4s ease-in-out infinite alternate;
        }

        @keyframes pulseGlow {
            0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.5; }
            100% { transform: translate(-50%, -50%) scale(1.15); opacity: 0.8; }
        }

        .migrator-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 580px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.05);
            text-align: center;
        }

        .icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-primary);
            font-size: 2.5rem;
            box-shadow: 0 0 25px rgba(59, 130, 246, 0.15);
            position: relative;
        }

        .icon-spinning {
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .migrator-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
            background: linear-gradient(180deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .migrator-subtitle {
            font-size: 0.925rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 2rem;
        }

        /* Barra de Progresso */
        .progress-wrapper {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .progress-bar-inner {
            height: 10px;
            border-radius: 8px;
            background: var(--accent-gradient);
            background-size: 200% 200%;
            width: 15%;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: gradientShift 2s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Lista de passos / status */
        .status-steps {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1rem;
            text-align: left;
            margin-bottom: 1.75rem;
            max-height: 140px;
            overflow-y: auto;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.825rem;
        }

        .status-steps::-webkit-scrollbar {
            width: 4px;
        }
        .status-steps::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            color: #cbd5e1;
        }
        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-item.active {
            color: #60a5fa;
            font-weight: 600;
        }

        .step-item.success {
            color: var(--success);
        }

        .step-item.error {
            color: var(--danger);
        }

        .step-icon {
            font-size: 1rem;
            flex-shrink: 0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            background: var(--accent-gradient);
            color: #fff;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            display: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .info-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            font-size: 0.78rem;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="bg-glow"></div>

    <div class="migrator-card">
        <div class="icon-container" id="migratorIcon">
            <i class="ti ti-database-cog icon-spinning"></i>
        </div>

        <div class="info-pill" id="infoPill">
            <i class="ti ti-shield-check"></i>
            <span>Sincronização Automática de Integridade</span>
        </div>

        <h1 class="migrator-title" id="migratorTitle">Atualizando Estrutura do Banco</h1>
        <p class="migrator-subtitle" id="migratorSubtitle">
            Detectamos novas atualizações ou tabelas/colunas pendentes. O sistema está aplicando as migrações automaticamente para garantir total estabilidade.
        </p>

        <div class="progress-wrapper">
            <div class="progress-bar-inner" id="progressBar" style="width: 15%;"></div>
        </div>

        <div class="status-steps" id="statusSteps">
            <div class="step-item active" id="step1">
                <i class="ti ti-loader-2 icon-spinning step-icon"></i>
                <span>Verificando migrações pendentes no sistema...</span>
            </div>
        </div>

        <div>
            <a href="<?= esc($returnUrl) ?>" class="btn-action" id="btnContinue">
                <i class="ti ti-arrow-right"></i>
                <span>Continuar para o Sistema</span>
            </a>
            <button type="button" class="btn-action" id="btnRetry" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; box-shadow: none;">
                <i class="ti ti-refresh"></i>
                <span>Tentar Novamente</span>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const progressBar = document.getElementById('progressBar');
            const statusSteps = document.getElementById('statusSteps');
            const migratorIcon = document.getElementById('migratorIcon');
            const migratorTitle = document.getElementById('migratorTitle');
            const migratorSubtitle = document.getElementById('migratorSubtitle');
            const btnContinue = document.getElementById('btnContinue');
            const btnRetry = document.getElementById('btnRetry');
            const returnUrl = <?= json_encode($returnUrl) ?>;

            function appendStep(html, type = 'info') {
                const div = document.createElement('div');
                div.className = `step-item ${type}`;
                div.innerHTML = html;
                statusSteps.appendChild(div);
                statusSteps.scrollTop = statusSteps.scrollHeight;
            }

            function setProgress(percent) {
                progressBar.style.width = percent + '%';
            }

            async function runMigration() {
                setProgress(30);
                appendStep('<i class="ti ti-database-import step-icon"></i><span>Conectando ao banco de dados e preparando runner...</span>', 'active');

                try {
                    const response = await fetch('<?= site_url('database/auto-migrate/execute') ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    setProgress(75);

                    const data = await response.json();

                    if (data.success) {
                        setProgress(100);
                        
                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                appendStep(`<i class="ti ti-check step-icon"></i><span>${msg}</span>`, 'success');
                            });
                        } else {
                            appendStep('<i class="ti ti-check step-icon"></i><span>Estrutura e tabelas validadas com sucesso!</span>', 'success');
                        }

                        migratorIcon.innerHTML = '<i class="ti ti-circle-check" style="color: var(--success);"></i>';
                        migratorTitle.textContent = 'Banco de Dados Atualizado!';
                        migratorSubtitle.textContent = 'Todas as migrações foram executadas com sucesso. Você será redirecionado em instantes.';
                        
                        btnContinue.style.display = 'inline-flex';

                        // Redireciona automaticamente após 2.5 segundos
                        setTimeout(() => {
                            window.location.href = returnUrl;
                        }, 2200);

                    } else {
                        throw new Error(data.message || data.error || 'Erro desconhecido durante a migração.');
                    }
                } catch (err) {
                    setProgress(100);
                    progressBar.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                    migratorIcon.innerHTML = '<i class="ti ti-alert-triangle" style="color: var(--danger);"></i>';
                    migratorTitle.textContent = 'Falha na Atualização';
                    migratorSubtitle.textContent = 'Houve um problema ao tentar aplicar as migrações estruturais.';
                    appendStep(`<i class="ti ti-x step-icon"></i><span>${err.message}</span>`, 'error');
                    
                    btnRetry.style.display = 'inline-flex';
                }
            }

            btnRetry.addEventListener('click', () => {
                btnRetry.style.display = 'none';
                progressBar.style.background = 'var(--accent-gradient)';
                migratorIcon.innerHTML = '<i class="ti ti-database-cog icon-spinning"></i>';
                migratorTitle.textContent = 'Atualizando Estrutura do Banco';
                migratorSubtitle.textContent = 'Tentando novamente aplicar as migrações...';
                runMigration();
            });

            // Dispara após um curto intervalo para animação suave
            setTimeout(runMigration, 500);
        });
    </script>
</body>
</html>
