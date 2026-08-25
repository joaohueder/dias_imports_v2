<!doctype html>
<html lang="pt-BR" data-model="<?= esc($landing['template_model'] ?? 'model-1') ?>" data-palette="<?= esc($landing['color_palette'] ?? 'palette-aurora') ?>" data-bg-animation="<?= esc($landing['bg_animation'] ?? 'bg-particles') ?>" data-btn-animation="<?= esc($landing['btn_animation'] ?? 'btn-pulse') ?>">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <?php
    $pageSeoTitle = !empty($landing['seo_title']) ? $landing['seo_title'] : ($landing['headline'] ?? 'Grupo VIP Dias Imports');
    $pageSeoDesc = !empty($landing['seo_description']) ? $landing['seo_description'] : ($landing['subheadline'] ?? 'Receba oportunidades imperdíveis de importados em primeira mão.');
    $shareImageUrl = !empty($landing['seo_image']) ? base_url($landing['seo_image']) : base_url('og-image.png');
    $imgExtension = pathinfo(parse_url($shareImageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
    $imgMime = ($imgExtension === 'jpg' || $imgExtension === 'jpeg') ? 'image/jpeg' : (($imgExtension === 'webp') ? 'image/webp' : 'image/png');
    ?>
    <title><?= esc($pageSeoTitle) ?> | Dias Imports</title>
    <meta name="description" content="<?= esc($pageSeoDesc) ?>"/>
    <meta itemprop="name" content="<?= esc($pageSeoTitle) ?> | Dias Imports"/>
    <meta itemprop="description" content="<?= esc($pageSeoDesc) ?>"/>
    <meta itemprop="image" content="<?= esc($shareImageUrl) ?>"/>

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?= current_url() ?>"/>
    <meta property="og:title" content="<?= esc($pageSeoTitle) ?> | Dias Imports"/>
    <meta property="og:description" content="<?= esc($pageSeoDesc) ?>"/>
    <meta property="og:image" content="<?= esc($shareImageUrl) ?>"/>
    <meta property="og:image:secure_url" content="<?= esc($shareImageUrl) ?>"/>
    <meta property="og:image:type" content="<?= esc($imgMime) ?>"/>
    <meta property="og:image:width" content="600"/>
    <meta property="og:image:height" content="600"/>
    <meta property="og:image:alt" content="<?= esc($pageSeoTitle) ?>"/>
    <meta property="og:site_name" content="Dias Imports"/>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:url" content="<?= current_url() ?>"/>
    <meta name="twitter:title" content="<?= esc($pageSeoTitle) ?> | Dias Imports"/>
    <meta name="twitter:description" content="<?= esc($pageSeoDesc) ?>"/>
    <meta name="twitter:image" content="<?= esc($shareImageUrl) ?>"/>

    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

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
    
    <script>
        // Registra o pageview interno na primeira página que abre /leads
        fetch('<?= base_url('leads/pageview') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(e => console.error('Erro ao registrar pageview:', e));
    </script>

    <style>
        /* ==========================================================================
           1. PALETAS DE CORES (6 PALETAS PREMIUM E HARMONIOSAS)
           ========================================================================== */
        
        /* 1. Aurora Neon (Violeta / Magenta - Padrão) */
        :root, html[data-palette="palette-aurora"] {
            --bg-base: #090a12;
            --bg-surface: #111322;
            --bg-surface-elevated: #181b30;
            --bg-surface-card: rgba(17, 19, 34, 0.75);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(99, 91, 255, 0.35);
            --primary: #635bff;
            --primary-hover: #7b74ff;
            --primary-light: rgba(99, 91, 255, 0.14);
            --primary-gradient: linear-gradient(135deg, #635bff 0%, #a855f7 50%, #ec4899 100%);
            --cta-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --cta-glow: rgba(16, 185, 129, 0.38);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --badge-bg: rgba(245, 158, 11, 0.12);
            --badge-border: rgba(245, 158, 11, 0.32);
            --badge-text: #fbbf24;
            --bg-glow-1: rgba(99, 91, 255, 0.16);
            --bg-glow-2: rgba(236, 72, 153, 0.1);
        }

        /* 2. Emerald Tech (Verde Esmeralda / WhatsApp VIP) */
        html[data-palette="palette-emerald"] {
            --bg-base: #06110d;
            --bg-surface: #0a1f18;
            --bg-surface-elevated: #0f2e24;
            --bg-surface-card: rgba(10, 31, 24, 0.8);
            --border-subtle: rgba(16, 185, 129, 0.15);
            --border-glow: rgba(16, 185, 129, 0.4);
            --primary: #10b981;
            --primary-hover: #34d399;
            --primary-light: rgba(16, 185, 129, 0.15);
            --primary-gradient: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            --cta-gradient: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --cta-glow: rgba(34, 197, 94, 0.4);
            --text-main: #f0fdf4;
            --text-muted: #86efac;
            --badge-bg: rgba(34, 197, 94, 0.15);
            --badge-border: rgba(34, 197, 94, 0.35);
            --badge-text: #4ade80;
            --bg-glow-1: rgba(16, 185, 129, 0.2);
            --bg-glow-2: rgba(5, 150, 105, 0.12);
        }

        /* 3. Amber Gold (Ouro / Luxo Importados) */
        html[data-palette="palette-amber"] {
            --bg-base: #0f0c05;
            --bg-surface: #1c170d;
            --bg-surface-elevated: #2b2315;
            --bg-surface-card: rgba(28, 23, 13, 0.8);
            --border-subtle: rgba(245, 158, 11, 0.18);
            --border-glow: rgba(245, 158, 11, 0.45);
            --primary: #f59e0b;
            --primary-hover: #fbbf24;
            --primary-light: rgba(245, 158, 11, 0.15);
            --primary-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
            --cta-gradient: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            --cta-glow: rgba(245, 158, 11, 0.4);
            --text-main: #fffbeb;
            --text-muted: #fde68a;
            --badge-bg: rgba(245, 158, 11, 0.18);
            --badge-border: rgba(245, 158, 11, 0.4);
            --badge-text: #fbbf24;
            --bg-glow-1: rgba(245, 158, 11, 0.18);
            --bg-glow-2: rgba(234, 88, 12, 0.12);
        }

        /* 4. Ocean Cyan (Azul Profundo / Ciano Futurista) */
        html[data-palette="palette-ocean"] {
            --bg-base: #050b14;
            --bg-surface: #0a1628;
            --bg-surface-elevated: #0f223d;
            --bg-surface-card: rgba(10, 22, 40, 0.8);
            --border-subtle: rgba(14, 165, 233, 0.15);
            --border-glow: rgba(14, 165, 233, 0.4);
            --primary: #0ea5e9;
            --primary-hover: #38bdf8;
            --primary-light: rgba(14, 165, 233, 0.15);
            --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 50%, #4f46e5 100%);
            --cta-gradient: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
            --cta-glow: rgba(6, 182, 212, 0.4);
            --text-main: #f0f9ff;
            --text-muted: #7dd3fc;
            --badge-bg: rgba(14, 165, 233, 0.15);
            --badge-border: rgba(14, 165, 233, 0.35);
            --badge-text: #38bdf8;
            --bg-glow-1: rgba(14, 165, 233, 0.2);
            --bg-glow-2: rgba(37, 99, 235, 0.12);
        }

        /* 5. Crimson Ruby (Vermelho / Ofertas Quentes) */
        html[data-palette="palette-crimson"] {
            --bg-base: #110507;
            --bg-surface: #200a0e;
            --bg-surface-elevated: #311016;
            --bg-surface-card: rgba(32, 10, 14, 0.8);
            --border-subtle: rgba(244, 63, 94, 0.16);
            --border-glow: rgba(244, 63, 94, 0.45);
            --primary: #f43f5e;
            --primary-hover: #fb7185;
            --primary-light: rgba(244, 63, 94, 0.15);
            --primary-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 50%, #9f1239 100%);
            --cta-gradient: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);
            --cta-glow: rgba(244, 63, 94, 0.4);
            --text-main: #fff1f2;
            --text-muted: #fda4af;
            --badge-bg: rgba(244, 63, 94, 0.15);
            --badge-border: rgba(244, 63, 94, 0.35);
            --badge-text: #fb7185;
            --bg-glow-1: rgba(244, 63, 94, 0.2);
            --bg-glow-2: rgba(225, 29, 72, 0.12);
        }

        /* 6. Obsidian Minimal (Preto Puro & Titânio) */
        html[data-palette="palette-obsidian"] {
            --bg-base: #030303;
            --bg-surface: #0f0f11;
            --bg-surface-elevated: #18181b;
            --bg-surface-card: rgba(15, 15, 17, 0.85);
            --border-subtle: rgba(255, 255, 255, 0.12);
            --border-glow: rgba(255, 255, 255, 0.35);
            --primary: #ffffff;
            --primary-hover: #e4e4e7;
            --primary-light: rgba(255, 255, 255, 0.1);
            --primary-gradient: linear-gradient(135deg, #ffffff 0%, #a1a1aa 100%);
            --cta-gradient: linear-gradient(135deg, #ffffff 0%, #d4d4d8 100%);
            --cta-glow: rgba(255, 255, 255, 0.25);
            --text-main: #ffffff;
            --text-muted: #a1a1aa;
            --badge-bg: rgba(255, 255, 255, 0.08);
            --badge-border: rgba(255, 255, 255, 0.2);
            --badge-text: #f4f4f5;
            --bg-glow-1: rgba(255, 255, 255, 0.08);
            --bg-glow-2: rgba(161, 161, 170, 0.05);
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
            position: relative;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 50% 0%, var(--bg-glow-1), transparent 50%),
                radial-gradient(circle at 15% 85%, var(--bg-glow-2), transparent 40%);
            background-repeat: no-repeat;
            transition: background-color 0.3s ease;
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
            box-shadow: 0 4px 16px var(--border-glow);
        }

        .brand-name {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(180deg, #ffffff 0%, var(--text-muted) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Urgency / Context Badge */
        .urgency-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--badge-bg);
            border: 1px solid var(--badge-border);
            color: var(--badge-text);
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
            background: var(--badge-text);
            box-shadow: 0 0 8px var(--badge-text);
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
            font-size: clamp(23px, 6.2vw, 29px);
            font-weight: 900;
            line-height: 1.22;
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
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
            position: relative;
            margin-bottom: 28px;
            backdrop-filter: blur(10px);
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
            box-shadow: 0 0 0 3px var(--primary-light);
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

        html[data-palette="palette-obsidian"] .btn-cta {
            color: #000000;
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
            color: var(--text-muted);
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
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .benefit-card:hover {
            border-color: var(--border-glow);
        }

        .benefit-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary-light);
            border: 1px solid var(--border-glow);
            color: var(--primary);
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

        /* ==========================================================================
           5. ESPECIFICIDADES DOS 6 MODELOS DE LANDING PAGE
           ========================================================================== */

        /* MODELO 1: Hero Direct & Glassmorphism (Padrão) */
        html[data-model="model-1"] .lead-form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        /* MODELO 2: Benefits First / Invertido (Benefícios antes do Form para Alto Engajamento) */
        html[data-model="model-2"] .lp-content-wrapper {
            display: flex;
            flex-direction: column;
        }
        html[data-model="model-2"] .benefits-section {
            order: 1;
            margin-bottom: 24px;
        }
        html[data-model="model-2"] .lead-form-card {
            order: 2;
            border-color: var(--primary);
            box-shadow: 0 0 35px var(--primary-light);
        }
        html[data-model="model-2"] .benefit-card {
            background: var(--bg-surface-elevated);
            border-left: 4px solid var(--primary);
        }

        /* MODELO 3: Minimal Compact / Floating Pill (Direto e Focado em Mobile 1-Thumb) */
        html[data-model="model-3"] .lp-container {
            padding: 18px 16px 36px;
        }
        html[data-model="model-3"] .hero-title {
            font-size: clamp(21px, 5.8vw, 26px);
            margin-bottom: 8px;
        }
        html[data-model="model-3"] .hero-subtitle {
            font-size: 13px;
            margin-bottom: 18px;
        }
        html[data-model="model-3"] .lead-form-card {
            background: transparent;
            border: 1px solid var(--border-subtle);
            padding: 18px 14px;
            box-shadow: none;
            backdrop-filter: none;
            margin-bottom: 20px;
        }
        html[data-model="model-3"] .form-header {
            display: none;
        }
        html[data-model="model-3"] .form-control {
            height: 46px;
            border-radius: 999px;
            padding-left: 44px;
        }
        html[data-model="model-3"] .btn-cta {
            border-radius: 999px;
            min-height: 48px;
        }
        html[data-model="model-3"] .benefits-section {
            gap: 8px;
            margin-bottom: 20px;
        }
        html[data-model="model-3"] .benefit-card {
            padding: 12px;
            border-radius: 12px;
        }

        /* MODELO 4: Grid Cards / Bento Box Style (Layout Moderno em Grade) */
        html[data-model="model-4"] .benefits-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        html[data-model="model-4"] .benefit-card {
            flex-direction: column;
            gap: 10px;
            padding: 14px;
        }
        html[data-model="model-4"] .benefit-card:first-child {
            grid-column: span 2;
            flex-direction: row;
            align-items: center;
        }
        html[data-model="model-4"] .benefit-icon-box {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }

        /* MODELO 5: Cyber Neon / Glowing Border & Dark Tech */
        html[data-model="model-5"] {
            font-family: 'Space Grotesk', 'Plus Jakarta Sans', sans-serif;
        }
        html[data-model="model-5"] .lead-form-card {
            background: #000000;
            border: 1.5px solid var(--primary);
            box-shadow: 0 0 25px var(--primary-light), inset 0 0 15px rgba(255, 255, 255, 0.02);
            border-radius: 14px;
        }
        html[data-model="model-5"] .urgency-badge {
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        html[data-model="model-5"] .form-control {
            border-radius: 8px;
            border-color: rgba(255, 255, 255, 0.15);
            background: #080808;
        }
        html[data-model="model-5"] .btn-cta {
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        html[data-model="model-5"] .benefit-card {
            border-radius: 8px;
            background: #08080c;
            border: 1px dashed var(--border-glow);
        }

        /* MODELO 6: Premium Editorial / Luxury Gold & Clean Typography */
        html[data-model="model-6"] {
            font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        }
        html[data-model="model-6"] .brand-name {
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-size: 14px;
        }
        html[data-model="model-6"] .hero-title {
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.28;
        }
        html[data-model="model-6"] .lead-form-card {
            border: 1px solid var(--border-subtle);
            border-radius: 28px;
            padding: 30px 22px;
            background: linear-gradient(180deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%);
        }
        html[data-model="model-6"] .btn-cta {
            border-radius: 24px;
            box-shadow: 0 12px 30px var(--cta-glow);
        }
        html[data-model="model-6"] .benefit-card {
            border-radius: 20px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-surface-card);
        }

        /* ==========================================================================
           6. OS 6 MODELOS DE ANIMAÇÃO DO BACKGROUND
           ========================================================================== */

        /* Canvas de Fundo Animado */
        .bg-fx-layer {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        /* 1. bg-particles: Partículas e Orbes Flutuantes */
        .bg-particles-wrap {
            display: none;
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        html[data-bg-animation="bg-particles"] .bg-particles-wrap {
            display: block;
        }
        .particle-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            opacity: 0.5;
            animation: floatOrb 12s infinite alternate ease-in-out;
        }
        .particle-orb:nth-child(1) {
            top: 10%;
            left: 15%;
            width: 300px;
            height: 300px;
            background: var(--primary);
            animation-duration: 14s;
        }
        .particle-orb:nth-child(2) {
            bottom: 15%;
            right: 10%;
            width: 350px;
            height: 350px;
            background: var(--bg-glow-2);
            animation-duration: 18s;
            animation-delay: -5s;
        }
        .particle-orb:nth-child(3) {
            top: 45%;
            left: 55%;
            width: 220px;
            height: 220px;
            background: var(--primary-hover);
            animation-duration: 10s;
            animation-delay: -3s;
        }
        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(45px, -55px) scale(1.2); }
            100% { transform: translate(-40px, 40px) scale(0.85); }
        }

        /* 2. bg-mesh-gradient: Gradiente Líquido Fluido em Movimento */
        .bg-mesh-wrap {
            display: none;
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 20%, var(--primary) 0%, transparent 45%),
                        radial-gradient(circle at 80% 30%, var(--bg-glow-2) 0%, transparent 45%),
                        radial-gradient(circle at 30% 80%, var(--bg-glow-1) 0%, transparent 50%),
                        radial-gradient(circle at 85% 85%, var(--primary-light) 0%, transparent 45%);
            background-size: 150% 150%;
            opacity: 0.7;
            animation: liquidMesh 14s ease infinite alternate;
        }
        html[data-bg-animation="bg-mesh-gradient"] .bg-mesh-wrap {
            display: block;
        }
        @keyframes liquidMesh {
            0% { background-position: 0% 0%; filter: hue-rotate(0deg); }
            50% { background-position: 100% 100%; filter: hue-rotate(30deg); }
            100% { background-position: 0% 100%; filter: hue-rotate(-20deg); }
        }

        /* 3. bg-cyber-grid: Grid Tech Futurista em Perspectiva */
        .bg-grid-wrap {
            display: none;
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            perspective: 500px;
            overflow: hidden;
        }
        .bg-grid-plane {
            position: absolute;
            inset: -60%;
            background-size: 50px 50px;
            background-image: linear-gradient(to right, var(--border-glow) 1px, transparent 1px),
                              linear-gradient(to bottom, var(--border-glow) 1px, transparent 1px);
            transform: rotateX(60deg);
            transform-origin: center 40%;
            animation: moveGrid 6s linear infinite;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,0.95), rgba(0,0,0,0));
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,0.95), rgba(0,0,0,0));
            opacity: 0.6;
        }
        html[data-bg-animation="bg-cyber-grid"] .bg-grid-wrap {
            display: block;
        }
        @keyframes moveGrid {
            0% { transform: rotateX(60deg) translateY(0); }
            100% { transform: rotateX(60deg) translateY(50px); }
        }

        /* 4. bg-radial-pulse: Pulso Radial Luminoso Concêntrico */
        .bg-pulse-wrap {
            display: none;
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .pulse-ring {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid var(--border-glow);
            box-shadow: 0 0 30px var(--primary);
            animation: expandRing 5s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        }
        .pulse-ring:nth-child(2) { animation-delay: 1.6s; }
        .pulse-ring:nth-child(3) { animation-delay: 3.2s; }
        html[data-bg-animation="bg-radial-pulse"] .bg-pulse-wrap {
            display: flex;
        }
        @keyframes expandRing {
            0% { transform: scale(0.3); opacity: 0.9; }
            70% { opacity: 0.35; }
            100% { transform: scale(8); opacity: 0; }
        }

        /* 5. bg-floating-shapes: Geometrias Translúcidas Flutuantes */
        .bg-shapes-wrap {
            display: none;
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .shape-item {
            position: absolute;
            border: 1.5px solid var(--border-glow);
            background: var(--primary-light);
            backdrop-filter: blur(6px);
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--cta-glow);
            animation: rotateShape 18s linear infinite;
        }
        .shape-item:nth-child(1) {
            width: 140px;
            height: 140px;
            top: 12%;
            right: 10%;
            animation-duration: 20s;
        }
        .shape-item:nth-child(2) {
            width: 90px;
            height: 90px;
            bottom: 20%;
            left: 8%;
            border-radius: 50%;
            animation-duration: 15s;
            animation-direction: reverse;
        }
        .shape-item:nth-child(3) {
            width: 70px;
            height: 70px;
            top: 60%;
            right: 15%;
            transform: rotate(45deg);
            animation-duration: 24s;
        }
        html[data-bg-animation="bg-floating-shapes"] .bg-shapes-wrap {
            display: block;
        }
        @keyframes rotateShape {
            0% { transform: rotate(0deg) translateY(0); }
            50% { transform: rotate(180deg) translateY(-30px) scale(1.1); }
            100% { transform: rotate(360deg) translateY(0); }
        }

        /* 6. bg-minimal-static: Fundo Estático Limpo */
        /* Usa apenas o background-image base padrão sem animações de canvas */

        /* ==========================================================================
           7. AS 6 ANIMAÇÕES DO BOTÃO DE CTA
           ========================================================================== */

        /* 1. btn-pulse: Pulso Rítmico de Escala e Brilho Halo */
        html[data-btn-animation="btn-pulse"] .btn-cta,
        .btn-cta[data-anim="btn-pulse"] {
            animation: btnPulseGlow 2.2s infinite ease-in-out;
        }
        @keyframes btnPulseGlow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 24px var(--cta-glow);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 14px 34px var(--cta-glow), 0 0 20px var(--cta-glow);
            }
        }

        /* 2. btn-shimmer: Feixe de Luz Metálica Reflexiva Contínuo */
        html[data-btn-animation="btn-shimmer"] .btn-cta::after,
        .btn-cta[data-anim="btn-shimmer"]::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -100%;
            width: 50%;
            height: 200%;
            background: linear-gradient(
                60deg,
                rgba(255, 255, 255, 0) 20%,
                rgba(255, 255, 255, 0.45) 50%,
                rgba(255, 255, 255, 0) 80%
            );
            transform: rotate(25deg);
            animation: btnShimmerSweep 3s infinite ease-in-out;
        }
        @keyframes btnShimmerSweep {
            0% { left: -100%; }
            40%, 100% { left: 200%; }
        }

        /* 3. btn-shake: Microvibração Atrativa Periódica */
        html[data-btn-animation="btn-shake"] .btn-cta,
        .btn-cta[data-anim="btn-shake"] {
            animation: btnVibrateInterval 4s infinite ease-in-out;
        }
        @keyframes btnVibrateInterval {
            0%, 80%, 100% { transform: translateX(0) scale(1); }
            82% { transform: translateX(-3px) rotate(-1deg); }
            84% { transform: translateX(3px) rotate(1deg); }
            86% { transform: translateX(-3px) rotate(-1deg); }
            88% { transform: translateX(3px) rotate(1deg); }
            90% { transform: translateX(0) scale(1.02); }
        }

        /* 4. btn-bounce: Salto Suave em Ritmo */
        html[data-btn-animation="btn-bounce"] .btn-cta,
        .btn-cta[data-anim="btn-bounce"] {
            animation: btnBounceJump 2.6s infinite ease-in-out;
        }
        @keyframes btnBounceJump {
            0%, 75%, 100% { transform: translateY(0); }
            80% { transform: translateY(-8px); }
            85% { transform: translateY(0); }
            90% { transform: translateY(-4px); }
            95% { transform: translateY(0); }
        }

        /* 5. btn-glow-expand: Onda Luminosa Expansiva Circular */
        html[data-btn-animation="btn-glow-expand"] .btn-cta::before,
        .btn-cta[data-anim="btn-glow-expand"]::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            box-shadow: 0 0 0 0 var(--cta-glow);
            animation: btnExpandRipple 2s infinite ease-out;
        }
        @keyframes btnExpandRipple {
            0% { box-shadow: 0 0 0 0 var(--cta-glow); opacity: 0.9; }
            70% { box-shadow: 0 0 0 16px transparent; opacity: 0; }
            100% { box-shadow: 0 0 0 0 transparent; opacity: 0; }
        }

        /* 6. btn-none: Estático */
        html[data-btn-animation="btn-none"] .btn-cta,
        .btn-cta[data-anim="btn-none"] {
            animation: none !important;
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
    <!-- Camada de Efeitos Dinâmicos de Fundo -->
    <div class="bg-fx-layer" aria-hidden="true">
        <!-- 1. Partículas e Orbes Flutuantes -->
        <div class="bg-particles-wrap">
            <div class="particle-orb"></div>
            <div class="particle-orb"></div>
            <div class="particle-orb"></div>
        </div>

        <!-- 2. Gradiente Líquido Mesh -->
        <div class="bg-mesh-wrap"></div>

        <!-- 3. Grid Tech Futurista -->
        <div class="bg-grid-wrap">
            <div class="bg-grid-plane"></div>
        </div>

        <!-- 4. Pulso Radial Concêntrico -->
        <div class="bg-pulse-wrap">
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
        </div>

        <!-- 5. Geometrias Translúcidas Flutuantes -->
        <div class="bg-shapes-wrap">
            <div class="shape-item"></div>
            <div class="shape-item"></div>
            <div class="shape-item"></div>
        </div>
    </div>

    <main class="lp-container" style="position: relative; z-index: 1;">
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

        <div class="lp-content-wrapper">
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
        </div>

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
