<?php $loginError = session()->getFlashdata('error'); ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Login | JH7 Marketing</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <!-- Tabler Core CSS & Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark-hero: #0c0d14;
            --bg-dark-panel: #0f111a;
            --card-glass: rgba(23, 26, 38, 0.7);
            --card-border: rgba(255, 255, 255, 0.07);
            --input-bg: #1c2233;
            --input-border: #283046;
            --primary-purple: #635bff;
            --primary-gradient: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            --accent-pink: #ec4899;
            --accent-purple: #8b5cf6;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-dark-hero);
            color: #f1f5f9;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* Layout Split */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* Left Showcase Column */
        .showcase-side {
            background-color: var(--bg-dark-hero);
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.022) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.022) 1px, transparent 1px);
            background-size: 48px 48px;
            padding: clamp(2.5rem, 4vw, 4.5rem);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .showcase-side::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(9, 10, 16, 0.06), rgba(9, 10, 16, 0.42));
            pointer-events: none;
            z-index: -1;
        }

        .background-orb {
            position: absolute;
            width: 32rem;
            aspect-ratio: 1;
            border-radius: 50%;
            filter: blur(95px);
            opacity: 0.24;
            pointer-events: none;
            z-index: -2;
            will-change: transform;
        }

        .background-orb--purple {
            top: -12rem;
            left: -10rem;
            background: #6d5dfc;
            animation: drift-purple 16s ease-in-out infinite alternate;
        }

        .background-orb--pink {
            right: -14rem;
            bottom: -10rem;
            background: #db2777;
            animation: drift-pink 19s ease-in-out infinite alternate;
        }

        @keyframes drift-purple {
            to { transform: translate(9rem, 7rem) scale(1.15); }
        }

        @keyframes drift-pink {
            to { transform: translate(-8rem, -6rem) scale(1.12); }
        }

        .showcase-content {
            width: min(100%, 680px);
            position: relative;
            z-index: 1;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(99, 91, 255, 0.16);
            border: 1px solid rgba(99, 91, 255, 0.35);
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #a5b4fc;
            width: fit-content;
        }

        .showcase-headline {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-top: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .headline-gradient {
            background: linear-gradient(90deg, #818cf8 0%, #ec4899 70%, #f472b6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .showcase-description {
            color: #94a3b8;
            font-size: 1.05rem;
            line-height: 1.6;
            max-width: 580px;
            margin-bottom: 2.25rem;
        }

        .feature-list {
            display: grid;
            gap: 0.9rem;
        }

        /* Feature Cards */
        .feature-card {
            background: var(--card-glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.15rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
            transition: border-color 0.25s ease, background 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
        }

        .feature-card:hover {
            border-color: rgba(99, 91, 255, 0.38);
            background: rgba(28, 32, 48, 0.88);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
            transform: translateX(5px);
        }

        .feature-icon-wrapper {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 12px;
            background: rgba(99, 91, 255, 0.15);
            color: #818cf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .feature-title {
            font-size: 1rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.25rem;
        }

        .feature-text {
            font-size: 0.86rem;
            color: #94a3b8;
            line-height: 1.45;
            margin: 0;
        }

        .feature-step-num {
            position: absolute;
            right: 1.25rem;
            top: 1.25rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #475569;
            font-weight: 600;
        }

        .showcase-footer {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.85rem;
            color: #94a3b8;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .showcase-footer-item {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .showcase-footer-item i {
            color: #34d399;
            font-size: 1.1rem;
        }

        /* Right Auth Column */
        .auth-side {
            background-color: var(--bg-dark-panel);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3.5rem 2.5rem;
            position: relative;
        }

        .auth-card {
            width: 100%;
            max-width: 430px;
        }

        .auth-header {
            margin-bottom: 2rem;
        }

        .auth-title {
            font-size: 1.9rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: #64748b;
            font-size: 0.95rem;
        }

        .login-alert {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1.25rem;
            padding: 0.85rem 1rem;
            border: 1px solid rgba(248, 113, 113, 0.28);
            border-radius: 10px;
            background: rgba(127, 29, 29, 0.18);
            color: #fecaca;
            font-size: 0.85rem;
        }

        .login-alert i {
            color: #f87171;
            font-size: 1.2rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }

        .input-group-custom {
            position: relative;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .input-group-custom:focus-within {
            border-color: #635bff;
            box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.2);
        }

        .input-icon-prefix {
            padding-left: 1rem;
            color: #64748b;
            display: flex;
            align-items: center;
            font-size: 1.2rem;
        }

        .form-control-custom {
            background: transparent;
            border: none;
            color: #ffffff;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            width: 100%;
            outline: none;
        }

        .form-control-custom::placeholder {
            color: #475569;
        }

        .btn-toggle-password {
            background: transparent;
            border: none;
            color: #64748b;
            padding-right: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 1.15rem;
        }

        .btn-toggle-password:hover {
            color: #94a3b8;
        }

        .form-check-custom {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            margin-top: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .form-check-custom input[type="checkbox"] {
            width: 17px;
            height: 17px;
            margin-top: 2px;
            accent-color: #635bff;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-custom label {
            font-size: 0.875rem;
            color: #cbd5e1;
            font-weight: 500;
            cursor: pointer;
            line-height: 1.3;
        }

        .form-check-custom .hint {
            display: block;
            font-size: 0.775rem;
            color: #64748b;
            margin-top: 0.15rem;
        }

        .btn-submit {
            background: #635bff;
            border: none;
            border-radius: 10px;
            padding: 0.9rem 1.5rem;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(99, 91, 255, 0.35);
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #5348e8;
            box-shadow: 0 6px 20px rgba(99, 91, 255, 0.45);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .auth-footer {
            margin-top: 3.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
        }

        .login-processing, .login-error-modal { position: fixed; inset: 0; z-index: 3000; display: grid; place-items: center; padding: max(20px, env(safe-area-inset-top)) max(20px, env(safe-area-inset-right)) max(20px, env(safe-area-inset-bottom)) max(20px, env(safe-area-inset-left)); background: rgba(9, 10, 16, .94); backdrop-filter: blur(18px); }
        .login-processing[hidden], .login-error-modal[hidden] { display: none; }
        .login-processing-card, .login-error-card { width: min(100%, 440px); padding: 34px; border: 1px solid rgba(139, 92, 246, .32); border-radius: 24px; background: rgba(23, 26, 38, .96); box-shadow: 0 30px 90px rgba(0, 0, 0, .55); text-align: center; }
        .login-processing-icon { position: relative; display: grid; width: 86px; height: 86px; margin: 0 auto 22px; place-items: center; border-radius: 24px; color: #fff; background: linear-gradient(135deg, #635bff, #ec4899); font-size: 32px; box-shadow: 0 14px 38px rgba(99, 91, 255, .4); animation: login-pulse 1.5s ease-in-out infinite; }
        .login-processing-icon::before { content: ""; position: absolute; inset: -10px; border: 2px solid transparent; border-top-color: #a5b4fc; border-radius: 50%; animation: login-spin 1.2s linear infinite; }
        .login-processing-card h2, .login-error-card h2 { margin: 0; color: #f8fafc; font-size: 1.45rem; letter-spacing: -.03em; }
        .login-processing-card > p { margin: 10px 0 20px; color: #94a3b8; font-size: .86rem; line-height: 1.6; }
        .login-progress { height: 5px; overflow: hidden; border-radius: 999px; background: #0c0d14; }
        .login-progress span { display: block; width: 42%; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #635bff, #ec4899); animation: login-progress 1.4s ease-in-out infinite; }
        .login-joke { min-height: 48px; padding: 12px; border: 1px solid rgba(255,255,255,.08); border-radius: 12px; background: rgba(12,13,20,.55); }
        .login-error-modal { z-index: 3100; background: rgba(0,0,0,.72); }
        .login-error-card { border-color: rgba(248, 113, 113, .32); }
        .login-error-icon { display: grid; width: 64px; height: 64px; margin: 0 auto 18px; place-items: center; border-radius: 20px; color: #f87171; background: rgba(248,113,113,.12); font-size: 30px; }
        .login-error-card p { margin: 10px 0 22px; color: #cbd5e1; font-size: .86rem; line-height: 1.6; overflow-wrap: anywhere; }
        .login-error-card button { min-height: 44px; width: 100%; border: 0; border-radius: 11px; color: #fff; background: #dc2626; font: inherit; font-weight: 700; cursor: pointer; }
        @keyframes login-spin { to { transform: rotate(360deg); } }
        @keyframes login-pulse { 50% { transform: scale(1.07) rotate(3deg); } }
        @keyframes login-progress { from { transform: translateX(-110%); } to { transform: translateX(340%); } }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }

        @media (max-width: 991.98px) {
            .showcase-side {
                display: none;
            }
            .auth-side {
                padding: 2rem 1.25rem;
            }
            .login-processing-card, .login-error-card { padding: 28px 20px; }
            .login-error-modal { align-items: end; }
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0 login-wrapper">
        <!-- Showcase Coluna Esquerda -->
        <div class="col-lg-7 col-xl-7 showcase-side">
            <div class="background-orb background-orb--purple" aria-hidden="true"></div>
            <div class="background-orb background-orb--pink" aria-hidden="true"></div>

            <div class="showcase-content">
                <div class="brand-badge">
                    DIAS IMPORTS
                </div>

                <h1 class="showcase-headline">
                    Um produto.<br>
                    <span class="headline-gradient">Três canais</span> de venda
                </h1>

                <p class="showcase-description">
                    Cadastre o produto uma única vez. Ele sai divulgado nos grupos de WhatsApp, publicado em landing page própria e listado no catálogo da Meta — com registro de tudo que foi enviado.
                </p>

                <!-- Cards de Recursos -->
                <div class="feature-list">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="ti ti-message-circle"></i>
                    </div>
                    <div>
                        <div class="feature-title">Divulgação em grupos</div>
                        <p class="feature-text">
                            Escolha o modelo de mensagem, marque os grupos e dispare. Imagem, preço e desconto entram prontos, com intervalo seguro entre cada envio.
                        </p>
                    </div>
                    <span class="feature-step-num">01</span>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="ti ti-browser"></i>
                    </div>
                    <div>
                        <div class="feature-title">Landing page por produto</div>
                        <p class="feature-text">
                            Cada produto ganha uma página de venda com link próprio, contagem de visitas e cliques, e um botão que abre a conversa no WhatsApp já com o produto e o preço escritos.
                        </p>
                    </div>
                    <span class="feature-step-num">02</span>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="ti ti-brand-meta"></i>
                    </div>
                    <div>
                        <div class="feature-title">Catálogo da Meta</div>
                        <p class="feature-text">
                            O feed é publicado e atualizado sozinho. A Meta busca os produtos no horário dela, sem planilha e sem upload manual.
                        </p>
                    </div>
                    <span class="feature-step-num">03</span>
                </div>
                </div>
            </div>

            <!-- Footer Esquerda -->
            <div class="showcase-footer">
                <div class="showcase-footer-item">
                    <i class="ti ti-check"></i>
                    <span>Alcance medido por grupo</span>
                </div>
                <div class="showcase-footer-item">
                    <i class="ti ti-check"></i>
                    <span>Histórico de cada envio</span>
                </div>
                <div class="showcase-footer-item">
                    <i class="ti ti-check"></i>
                    <span>Acesso por permissão</span>
                </div>
            </div>
        </div>

        <!-- Auth Coluna Direita -->
        <div class="col-lg-5 col-xl-5 auth-side">
            <div class="auth-card">
                <div class="auth-header">
                    <h2 class="auth-title">Acesse sua conta</h2>
                    <p class="auth-subtitle">Entre com suas credenciais para abrir o painel</p>
                </div>

                <form id="loginForm" method="POST" action="<?= base_url('login') ?>">
                    <?= csrf_field() ?>

                    <!-- Campo Email -->
                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail corporativo</label>
                        <div class="input-group-custom">
                            <span class="input-icon-prefix">
                                <i class="ti ti-mail"></i>
                            </span>
                            <input type="email" class="form-control-custom" id="email" name="email" value="<?= esc(old('email')) ?>" placeholder="nome@diasimports.com.br" maxlength="190" required autocomplete="email">
                        </div>
                    </div>

                    <!-- Campo Senha -->
                    <div class="mb-2">
                        <label class="form-label" for="password">Senha</label>
                        <div class="input-group-custom">
                            <span class="input-icon-prefix">
                                <i class="ti ti-lock"></i>
                            </span>
                            <input type="password" class="form-control-custom" id="password" name="password" placeholder="••••••••••••" required autocomplete="current-password">
                            <button type="button" class="btn-toggle-password" id="togglePassword" aria-label="Mostrar ou ocultar senha">
                                <i class="ti ti-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Lembrar-me -->
                    <div class="form-check-custom">
                        <input type="checkbox" id="rememberMe" name="remember" value="1" <?= old('remember') ? 'checked' : '' ?>>
                        <div>
                            <label for="rememberMe">Manter conectado por 30 dias</label>
                            <span class="hint">Evite em computadores compartilhados.</span>
                        </div>
                    </div>

                    <!-- Botão Entrar -->
                    <button type="submit" class="btn-submit" id="btnSubmit">
                        <span>Entrar no painel</span>
                        <i class="ti ti-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    O acesso é liberado por um administrador do sistema.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="login-processing" id="loginProcessing" hidden aria-hidden="true">
    <section class="login-processing-card" role="status" aria-live="polite">
        <div class="login-processing-icon" aria-hidden="true"><i class="ti ti-shield-lock"></i></div>
        <h2>Validando seu acesso</h2>
        <p>Verificando suas credenciais e preparando o painel com segurança.</p>
        <div class="login-progress" aria-hidden="true"><span></span></div>
        <p class="login-joke" id="loginJoke">Batendo na porta do servidor. Prometemos tirar os sapatos.</p>
    </section>
</div>

<?php if (is_string($loginError) && $loginError !== ''): ?>
<div class="login-error-modal" id="loginErrorModal" aria-hidden="false">
    <section class="login-error-card" role="alertdialog" aria-modal="true" aria-labelledby="loginErrorTitle" aria-describedby="loginErrorMessage">
        <div class="login-error-icon" aria-hidden="true"><i class="ti ti-alert-triangle"></i></div>
        <h2 id="loginErrorTitle">Não foi possível entrar</h2>
        <p id="loginErrorMessage"><?= esc($loginError) ?></p>
        <button type="button" id="closeLoginError">Entendi</button>
    </section>
</div>
<?php endif; ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle exibição de senha
        $('#togglePassword').on('click', function() {
            const passwordInput = $('#password');
            const icon = $('#toggleIcon');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('ti-eye').addClass('ti-eye-off');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('ti-eye-off').addClass('ti-eye');
            }
        });

        $('#loginForm').on('submit', function() {
            const jokes = [
                'Batendo na porta do servidor. Prometemos tirar os sapatos.',
                'Conferindo a lista VIP dos humanos autorizados.',
                'Alinhando os bits para uma entrada triunfal.',
                'O servidor está procurando a chave certa no chaveiro digital.',
            ];
            $('#btnSubmit').prop('disabled', true).find('span').text('Entrando...');
            $('#loginJoke').text(jokes[Math.floor(Math.random() * jokes.length)]);
            $('#loginProcessing').prop('hidden', false).attr('aria-hidden', 'false');
            $('body').css('overflow', 'hidden');
        });

        const loginErrorModal = $('#loginErrorModal');
        if (loginErrorModal.length) {
            $('.login-wrapper').attr('inert', '');
            $('body').css('overflow', 'hidden');
            $('#closeLoginError').trigger('focus').on('click', function() {
                loginErrorModal.prop('hidden', true).attr('aria-hidden', 'true');
                $('.login-wrapper').removeAttr('inert');
                $('body').css('overflow', '');
            });
            loginErrorModal.on('click', function(event) {
                if (event.target === this) $('#closeLoginError').trigger('click');
            });
            $(document).on('keydown', function(event) {
                if (event.key === 'Escape') $('#closeLoginError').trigger('click');
            });
        }
    });
</script>
</body>
</html>
