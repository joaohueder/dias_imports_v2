<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?= esc($landing['headline'] ?? 'Grupo VIP Dias Imports') ?> | Dias Imports</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <?php if (! empty($metaAds['pixel_id'])): ?>
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?= esc($metaAds['pixel_id'], 'js') ?>');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?= esc($metaAds['pixel_id'], 'url') ?>&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    <?php endif; ?>
    
    <style>
        :root {
            --bg-base: #090a0f;
            --bg-surface: #11131d;
            --bg-surface-elevated: #171a27;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(99, 91, 255, 0.35);
            --primary: #635bff;
            --primary-hover: #7b74ff;
            --primary-gradient: linear-gradient(135deg, #635bff 0%, #a855f7 50%, #ec4899 100%);
            --cta-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --cta-glow: rgba(16, 185, 129, 0.35);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-amber: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(99, 91, 255, 0.15), transparent 45%),
                radial-gradient(circle at 10% 80%, rgba(236, 72, 153, 0.08), transparent 35%);
            background-repeat: no-repeat;
        }

        .lp-container {
            width: 100%;
            max-width: 480px;
            min-height: 100vh;
            padding: 24px 20px 48px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Top Brand Bar */
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .brand-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--primary-gradient);
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 900;
            font-size: 16px;
            box-shadow: 0 4px 16px rgba(99, 91, 255, 0.4);
        }

        .brand-name {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Urgency / Context Badge */
        .urgency-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            padding: 6px 14px;
            border-radius: 999px;
            margin: 0 auto 16px;
            text-align: center;
            animation: pulse-border 2.5s infinite ease-in-out;
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #fbbf24;
            box-shadow: 0 0 8px #fbbf24;
            animation: blink 1.2s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        @keyframes pulse-border {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        /* Hero Section */
        .hero-title {
            font-size: clamp(24px, 6.5vw, 30px);
            font-weight: 900;
            line-height: 1.2;
            text-align: center;
            letter-spacing: -0.03em;
            margin-bottom: 12px;
            background: linear-gradient(180deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.55;
            margin-bottom: 24px;
            padding: 0 4px;
        }

        /* High Conversion Form Card */
        .lead-form-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 24px 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative;
            margin-bottom: 28px;
        }

        .lead-form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .form-header h3 {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }

        .form-header p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #cbd5e1;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: #64748b;
            font-size: 18px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control {
            width: 100%;
            height: 50px;
            background: var(--bg-base);
            border: 1.5px solid var(--border-subtle);
            border-radius: 12px;
            padding: 0 14px 0 42px;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.2);
        }

        .form-control:focus + i,
        .input-wrapper:focus-within i {
            color: var(--primary-hover);
        }

        /* CTA Button */
        .btn-cta {
            width: 100%;
            min-height: 54px;
            padding: 12px 20px;
            background: var(--cta-gradient);
            border: none;
            border-radius: 14px;
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.02em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 24px var(--cta-glow);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            margin-top: 6px;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px var(--cta-glow);
            filter: brightness(1.08);
        }

        .btn-cta:active {
            transform: scale(0.98);
        }

        .btn-subtext {
            display: block;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Benefits / Cards List */
        .benefits-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 32px;
        }

        .benefit-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: transform 0.2s ease;
        }

        .benefit-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(99, 91, 255, 0.12);
            border: 1px solid rgba(99, 91, 255, 0.24);
            color: #818cf8;
            display: grid;
            place-items: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .benefit-info h4 {
            font-size: 14px;
            font-weight: 800;
            color: #f1f5f9;
            margin-bottom: 3px;
        }

        .benefit-info p {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.45;
        }

        /* Social Proof / Security Footer */
        .trust-footer {
            margin-top: auto;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid var(--border-subtle);
        }

        .trust-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .trust-badges span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .trust-copy {
            font-size: 11px;
            color: #475569;
        }

        /* Modal de Confirmação VIP */
        .vip-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .vip-modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .vip-modal-card {
            background: var(--bg-surface-elevated);
            border: 1px solid var(--border-glow);
            border-radius: 24px;
            padding: 32px 24px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8);
            transform: translateY(20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .vip-modal-backdrop.active .vip-modal-card {
            transform: translateY(0) scale(1);
        }

        .modal-celebration-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid rgba(16, 185, 129, 0.35);
            color: #10b981;
            font-size: 36px;
            display: grid;
            place-items: center;
            margin: 0 auto 18px;
            animation: bounce-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes bounce-in {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .modal-title {
            font-size: 20px;
            font-weight: 900;
            color: #fff;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .modal-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .btn-whatsapp-group {
            width: 100%;
            min-height: 52px;
            background: #25d366;
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(37, 211, 102, 0.35);
            transition: all 0.2s ease;
        }

        .btn-whatsapp-group:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        .btn-whatsapp-group i {
            font-size: 22px;
        }

        .spinner-icon {
            display: none;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .is-loading .btn-text { display: none; }
        .is-loading .spinner-icon { display: inline-block; }
    </style>
</head>
<body>
    <main class="lp-container">
        <header class="brand-header">
            <div class="brand-logo-icon">DI</div>
            <span class="brand-name"><?= esc($company['name'] ?? 'Dias Imports') ?></span>
        </header>

        <div class="urgency-badge" data-badge-text>
            <span class="pulse-dot"></span>
            <span><?= esc($landing['badge_text'] ?? 'GRUPO VIP EXCLUSIVO • VAGAS LIMITADAS') ?></span>
        </div>

        <h1 class="hero-title" data-headline><?= esc($landing['headline'] ?? 'Garanta Descontos Secretos e Ofertas VIP no WhatsApp') ?></h1>
        <p class="hero-subtitle" data-subheadline><?= esc($landing['subheadline'] ?? 'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.') ?></p>

        <section class="lead-form-card">
            <div class="form-header">
                <h3>Cadastre-se para Liberar o Acesso</h3>
                <p>Preencha abaixo para receber o link do grupo</p>
            </div>

            <form id="leadForm" action="<?= site_url('leads/capture') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="lead_name">Seu Nome Completo</label>
                    <div class="input-wrapper">
                        <input type="text" id="lead_name" name="name" class="form-control" placeholder="Ex.: Lucas Silva" required autocomplete="name">
                        <i class="ti ti-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="lead_phone">Seu WhatsApp com DDD</label>
                    <div class="input-wrapper">
                        <input type="tel" id="lead_phone" name="phone" class="form-control" placeholder="(00) 00000-0000" required inputmode="numeric" autocomplete="tel">
                        <i class="ti ti-brand-whatsapp"></i>
                    </div>
                </div>

                <button type="submit" class="btn-cta" id="btnSubmit">
                    <span class="btn-text">
                        <i class="ti ti-lock-open" style="margin-right: 4px;"></i>
                        <span data-button-text><?= esc($landing['button_text'] ?? 'QUERO MEU ACESSO VIP AGORA') ?></span>
                    </span>
                    <i class="ti ti-loader-2 spinner-icon"></i>
                </button>
                <small class="btn-subtext" data-button-subtext><?= esc($landing['button_subtext'] ?? '🔒 Acesso 100% gratuito e sem spam') ?></small>
            </form>
        </section>

        <section class="benefits-section">
            <article class="benefit-card">
                <div class="benefit-icon-box" data-card1-icon-box><i class="ti <?= esc($landing['card1_icon'] ?? 'ti-discount-check') ?>" data-card1-icon></i></div>
                <div class="benefit-info">
                    <h4 data-card1-title><?= esc($landing['card1_title'] ?? 'Até 50% de Desconto Real') ?></h4>
                    <p data-card1-desc><?= esc($landing['card1_desc'] ?? 'Preços exclusivos de atacado e varejo direto para membros do grupo.') ?></p>
                </div>
            </article>

            <article class="benefit-card">
                <div class="benefit-icon-box" data-card2-icon-box><i class="ti <?= esc($landing['card2_icon'] ?? 'ti-flame') ?>" data-card2-icon></i></div>
                <div class="benefit-info">
                    <h4 data-card2-title><?= esc($landing['card2_title'] ?? 'Ofertas Relâmpago e Primeira Mão') ?></h4>
                    <p data-card2-desc><?= esc($landing['card2_desc'] ?? 'Novidades e lançamentos liberados no grupo antes de todo mundo.') ?></p>
                </div>
            </article>

            <article class="benefit-card">
                <div class="benefit-icon-box" data-card3-icon-box><i class="ti <?= esc($landing['card3_icon'] ?? 'ti-shield-lock') ?>" data-card3-icon></i></div>
                <div class="benefit-info">
                    <h4 data-card3-title><?= esc($landing['card3_title'] ?? '100% Original e com Garantia') ?></h4>
                    <p data-card3-desc><?= esc($landing['card3_desc'] ?? 'Importados com nota fiscal, procedência garantida e suporte humanizado.') ?></p>
                </div>
            </article>
        </section>

        <footer class="trust-footer">
            <div class="trust-badges">
                <span><i class="ti ti-shield-check" style="color:#10b981;"></i> Compra Segura</span>
                <span><i class="ti ti-truck" style="color:#635bff;"></i> Envio Rápido</span>
                <span><i class="ti ti-star" style="color:#f59e0b;"></i> Satisfação</span>
            </div>
            <p class="trust-copy">© <?= date('Y') ?> <?= esc($company['name'] ?? 'Dias Imports') ?>. Todos os direitos reservados.</p>
        </footer>
    </main>

    <!-- Modal de Sucesso / Grupo VIP -->
    <div class="vip-modal-backdrop" id="vipModal" role="dialog" aria-modal="true">
        <div class="vip-modal-card">
            <div class="modal-celebration-icon">
                <i class="ti ti-brand-whatsapp"></i>
            </div>
            <h2 class="modal-title" id="modalTitle"><?= esc($landing['modal_title'] ?? '🎉 Parabéns! Seu Acesso VIP Está Liberado') ?></h2>
            <p class="modal-desc" id="modalDesc"><?= esc($landing['modal_desc'] ?? 'Seu cadastro foi realizado com sucesso. Clique no botão abaixo para entrar agora no Grupo VIP Oficial no WhatsApp e não perder nenhuma oportunidade.') ?></p>
            <a href="<?= esc($landing['whatsapp_group_link'] ?? 'https://chat.whatsapp.com/') ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-group" id="btnGroupLink">
                <i class="ti ti-brand-whatsapp"></i>
                <span id="modalBtnText"><?= esc($landing['modal_button_text'] ?? 'ENTRAR NO GRUPO VIP DO WHATSAPP') ?></span>
            </a>
        </div>
    </div>

    <script>
        // Máscara de telefone
        const phoneInput = document.getElementById('lead_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 11) v = v.slice(0, 11);
                if (v.length > 6) {
                    v = `(${v.slice(0, 2)}) ${v.slice(2, 7)}-${v.slice(7)}`;
                } else if (v.length > 2) {
                    v = `(${v.slice(0, 2)}) ${v.slice(2)}`;
                } else if (v.length > 0) {
                    v = `(${v}`;
                }
                e.target.value = v;
            });
        }

        // Submissão do formulário
        const form = document.getElementById('leadForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const vipModal = document.getElementById('vipModal');

        if (form && btnSubmit && vipModal) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btnSubmit.classList.add('is-loading');
                btnSubmit.disabled = true;

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Dispara evento Lead no Meta Pixel do navegador
                        if (typeof fbq === 'function') {
                            try {
                                fbq('track', 'Lead', {
                                    content_name: 'Landing Page VIP Lead',
                                    status: true
                                });
                            } catch (pixelErr) {
                                console.warn('Meta Pixel Lead tracking warning:', pixelErr);
                            }
                        }

                        if (result.group_link) {
                            document.getElementById('btnGroupLink').href = result.group_link;
                        }
                        if (result.modal_title) {
                            document.getElementById('modalTitle').textContent = result.modal_title;
                        }
                        if (result.modal_desc) {
                            document.getElementById('modalDesc').textContent = result.modal_desc;
                        }
                        if (result.modal_button_text) {
                            document.getElementById('modalBtnText').textContent = result.modal_button_text;
                        }

                        vipModal.classList.add('active');
                    } else {
                        alert(result.message || 'Ocorreu um erro ao cadastrar. Tente novamente.');
                    }
                } catch (err) {
                    alert('Erro na conexão. Verifique sua internet e tente novamente.');
                } finally {
                    btnSubmit.classList.remove('is-loading');
                    btnSubmit.disabled = false;
                }
            });
        }
    </script>
</body>
</html>
