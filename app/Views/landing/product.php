<!DOCTYPE html>
<html lang="pt-BR" data-model="<?= esc($product->layout ?? 'model-1') ?>" data-palette="<?= esc($product->color_palette ?? 'palette-aurora') ?>" data-btn-animation="<?= esc($product->btn_animation ?? 'btn-pulse') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($product->name) ?> | Dias Imports</title>
    <meta name="description" content="<?= esc($product->subheadline ?: $product->description) ?>">
    
    <!-- Open Graph / WhatsApp Preview -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="<?= esc($product->name) ?>">
    <meta property="og:description" content="<?= esc($product->subheadline ?: $product->description) ?>">
    <?php if (!empty($images)): ?>
        <meta property="og:image" content="<?= base_url('uploads/products/' . $images[0]->image_path) ?>">
    <?php endif; ?>

    <!-- Google Fonts: Inter & Cormorant Garamond / Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <?php if (! empty($product->meta_ads_active) && ! empty($metaAds['pixel_id'])): ?>
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
    fbq('track', 'ViewContent', {
        content_name: '<?= esc($product->name, 'js') ?>',
        content_type: 'product',
        content_ids: ['<?= esc((string)$product->id, 'js') ?>'],
        value: <?= (float)($product->promotional_price ?: $product->price) ?>,
        currency: 'BRL'
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?= esc($metaAds['pixel_id'], 'url') ?>&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    <?php endif; ?>

    <style>
        /* =========================================
           PALETAS DE CORES (Variáveis CSS)
           ========================================= */
        /* 1. Aurora Neon (Rubi & Rose - Padrão) */
        :root, html[data-palette="palette-aurora"] {
            --lp-primary: #9d174d;
            --lp-primary-hover: #831843;
            --lp-accent-badge: #fce7f3;
            --lp-accent-badge-text: #be185d;
        }

        /* 2. Emerald Tech (Verde WhatsApp VIP) */
        html[data-palette="palette-emerald"] {
            --lp-primary: #10b981;
            --lp-primary-hover: #059669;
            --lp-accent-badge: #d1fae5;
            --lp-accent-badge-text: #047857;
        }

        /* 3. Amber Gold (Ouro & Luxo) */
        html[data-palette="palette-amber"] {
            --lp-primary: #f59e0b;
            --lp-primary-hover: #d97706;
            --lp-accent-badge: #fef3c7;
            --lp-accent-badge-text: #b45309;
        }

        /* 4. Ocean Royal (Azul Confiança) */
        html[data-palette="palette-ocean"] {
            --lp-primary: #2563eb;
            --lp-primary-hover: #1d4ed8;
            --lp-accent-badge: #dbeafe;
            --lp-accent-badge-text: #1e40af;
        }

        /* 5. Crimson Urgência (Vermelho Alta Pressão) */
        html[data-palette="palette-crimson"] {
            --lp-primary: #dc2626;
            --lp-primary-hover: #b91c1c;
            --lp-accent-badge: #fee2e2;
            --lp-accent-badge-text: #991b1b;
        }

        /* 6. Obsidian Dark (Minimalista Preto) */
        html[data-palette="palette-obsidian"] {
            --lp-primary: #0f172a;
            --lp-primary-hover: #334155;
            --lp-accent-badge: #f1f5f9;
            --lp-accent-badge-text: #0f172a;
        }

        :root {
            --lp-bg: #f8fafc;
            --lp-card-bg: #ffffff;
            --lp-text-main: #1e293b;
            --lp-text-muted: #64748b;
            --lp-border: #f1f5f9;
            --lp-card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--lp-bg);
            color: var(--lp-text-main);
            line-height: 1.5;
            padding-bottom: 90px;
        }

        .container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        /* BADGE TOPO */
        .promo-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--lp-accent-badge);
            color: var(--lp-accent-badge-text);
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        /* HEADER DO PRODUTO */
        .product-title {
            font-size: 26px;
            font-weight: 900;
            line-height: 1.15;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .product-subheadline {
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* CARROSSEL / IMAGEM PRINCIPAL */
        .gallery-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            border-radius: 20px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: var(--lp-card-shadow);
            margin-bottom: 12px;
        }

        .gallery-main-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.2s ease;
        }

        .gallery-discount-tag {
            position: absolute;
            top: 14px;
            left: 14px;
            background: #9d174d;
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            padding: 6px 10px;
            border-radius: 8px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(157, 23, 77, 0.3);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .gallery-counter {
            position: absolute;
            bottom: 14px;
            right: 14px;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 100px;
        }

        /* MINIATURAS */
        .gallery-thumbs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            margin-bottom: 20px;
            scrollbar-width: none;
        }
        .gallery-thumbs::-webkit-scrollbar {
            display: none;
        }

        .thumb-item {
            flex: 0 0 54px;
            height: 54px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            background: #ffffff;
            opacity: 0.6;
            transition: all 0.2s ease;
        }

        .thumb-item.active {
            border-color: var(--lp-primary);
            opacity: 1;
            transform: scale(1.04);
        }

        .thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* CARD DE PREÇO E CTA */
        .price-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--lp-card-shadow);
            margin-bottom: 16px;
            border: 1px solid #f1f5f9;
        }

        .price-row {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 6px;
        }

        .old-price {
            font-size: 14px;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 600;
        }

        .current-price {
            font-size: 32px;
            font-weight: 900;
            color: var(--lp-primary);
            letter-spacing: -0.5px;
        }

        .savings-tag {
            background: #fdf2f8;
            color: #9d174d;
            font-size: 11px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            margin-left: auto;
        }

        .price-subtext {
            font-size: 12px;
            color: #475569;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .price-subtext::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #9d174d;
        }

        .btn-cta {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: var(--lp-primary);
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            padding: 16px 20px;
            border-radius: 14px;
            text-decoration: none;
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
            transition: all 0.2s ease;
        }

        .btn-cta:hover, .btn-cta:active {
            background: var(--lp-primary-hover);
            transform: translateY(-1px);
        }

        .btn-whatsapp-urgency {
            position: relative;
            overflow: hidden;
        }

        .btn-sticky-cta {
            position: relative;
            overflow: hidden;
            background: var(--lp-primary);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            padding: 12px 18px;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.3);
            white-space: nowrap;
        }

        /* ==========================================================================
           AS 6 ANIMAÇÕES DO BOTÃO DE CTA (Landing Page Pública)
           ========================================================================== */

        /* 1. btn-pulse: Pulso / Batimento VIP */
        html[data-btn-animation="btn-pulse"] .btn-cta,
        html[data-btn-animation="btn-pulse"] .btn-sticky-cta {
            animation: lpBtnPulseGlow 2.2s infinite ease-in-out;
        }
        @keyframes lpBtnPulseGlow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
            }
            50% {
                transform: scale(1.03);
                box-shadow: 0 12px 28px -2px var(--lp-primary), 0 0 16px var(--lp-primary);
            }
        }

        /* 2. btn-shimmer: Brilho Shimmer Metálico Contínuo */
        html[data-btn-animation="btn-shimmer"] .btn-cta::after,
        html[data-btn-animation="btn-shimmer"] .btn-sticky-cta::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -100%;
            width: 50%;
            height: 200%;
            background: linear-gradient(
                60deg,
                rgba(255, 255, 255, 0) 20%,
                rgba(255, 255, 255, 0.5) 50%,
                rgba(255, 255, 255, 0) 80%
            );
            transform: rotate(25deg);
            animation: lpBtnShimmerSweep 3s infinite ease-in-out;
            pointer-events: none;
        }
        @keyframes lpBtnShimmerSweep {
            0% { left: -100%; }
            40%, 100% { left: 200%; }
        }

        /* 3. btn-shake: Vibração Shake */
        html[data-btn-animation="btn-shake"] .btn-cta,
        html[data-btn-animation="btn-shake"] .btn-sticky-cta {
            animation: lpBtnShakeInterval 4s infinite ease-in-out;
        }
        @keyframes lpBtnShakeInterval {
            0%, 80%, 100% { transform: translateX(0) scale(1); }
            82% { transform: translateX(-4px) rotate(-1deg); }
            84% { transform: translateX(4px) rotate(1deg); }
            86% { transform: translateX(-4px) rotate(-1deg); }
            88% { transform: translateX(4px) rotate(1deg); }
            90% { transform: translateX(0) scale(1.02); }
        }

        /* 4. btn-bounce: Salto Bounce */
        html[data-btn-animation="btn-bounce"] .btn-cta,
        html[data-btn-animation="btn-bounce"] .btn-sticky-cta {
            animation: lpBtnBounceJump 2.6s infinite ease-in-out;
        }
        @keyframes lpBtnBounceJump {
            0%, 75%, 100% { transform: translateY(0); }
            80% { transform: translateY(-7px); }
            85% { transform: translateY(0); }
            90% { transform: translateY(-3px); }
            95% { transform: translateY(0); }
        }

        /* 5. btn-glow: Glow Neon Fluido */
        html[data-btn-animation="btn-glow"] .btn-cta,
        html[data-btn-animation="btn-glow"] .btn-sticky-cta {
            animation: lpBtnNeonGlow 2s infinite alternate ease-in-out;
        }
        @keyframes lpBtnNeonGlow {
            0% {
                box-shadow: 0 0 5px var(--lp-primary), 0 4px 12px rgba(0, 0, 0, 0.2);
            }
            100% {
                box-shadow: 0 0 22px var(--lp-primary), 0 0 10px var(--lp-primary-hover);
            }
        }

        /* 6. btn-static: Estático Sofisticado */
        html[data-btn-animation="btn-static"] .btn-cta,
        html[data-btn-animation="btn-static"] .btn-sticky-cta {
            animation: none !important;
        }

        .micro-security {
            font-size: 11px;
            color: #64748b;
            text-align: center;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        /* 3 PILARES */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        .feature-box {
            text-align: center;
        }

        .feature-box i {
            font-size: 16px;
            color: var(--lp-primary);
            margin-bottom: 4px;
            display: inline-block;
        }

        .feature-box strong {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .feature-box small {
            display: block;
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.3;
        }

        /* LISTA DE GARANTIAS (3 CHECKS) */
        .guarantees-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 16px;
            box-shadow: var(--lp-card-shadow);
            margin-bottom: 24px;
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .guarantee-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .guarantee-item i {
            color: #9d174d;
            font-size: 18px;
            flex-shrink: 0;
        }

        /* SEÇÃO SOBRE O PRODUTO */
        .section-title {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .about-text {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.7;
            white-space: pre-line;
            margin-bottom: 28px;
        }

        /* ACCORDION FAQ (ANTES DE FECHAR) */
        .faq-accordion {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 28px;
        }

        .faq-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            box-shadow: var(--lp-card-shadow);
        }

        .faq-question {
            width: 100%;
            background: none;
            border: none;
            padding: 16px 18px;
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }

        .faq-question i {
            color: #9d174d;
            font-size: 16px;
            transition: transform 0.2s ease;
        }

        .faq-card.open .faq-question i {
            transform: rotate(45deg);
        }

        .faq-answer {
            padding: 0 18px 16px 18px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            display: none;
        }

        .faq-card.open .faq-answer {
            display: block;
        }

        /* CARD DE FECHAMENTO */
        .closing-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px 20px;
            box-shadow: var(--lp-card-shadow);
            border: 1px solid #f1f5f9;
            text-align: center;
            margin-bottom: 28px;
        }

        .closing-card .section-title {
            margin-bottom: 12px;
            text-align: center;
        }

        .closing-price-wrap {
            margin-bottom: 16px;
        }

        .closing-subtext {
            font-size: 11px;
            color: #64748b;
            margin-top: 10px;
        }

        /* RODAPÉ */
        .footer-wrap {
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.6;
        }

        /* BARRA FIXA INFERIOR */
        .bottom-sticky-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-top: 1px solid #e2e8f0;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            z-index: 100;
            max-width: 480px;
            margin: 0 auto;
        }

        .sticky-price-col {
            display: flex;
            flex-direction: column;
        }

        .sticky-price-col small {
            font-size: 10px;
            color: #94a3b8;
            text-decoration: line-through;
            line-height: 1;
        }

        .sticky-price-col strong {
            font-size: 18px;
            font-weight: 900;
            color: var(--lp-primary);
            line-height: 1.1;
        }

        .btn-sticky-cta {
            position: relative;
            overflow: hidden;
            background: var(--lp-primary);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            padding: 12px 20px;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.25);
            transition: all 0.2s ease;
        }

        .btn-sticky-cta:hover, .btn-sticky-cta:active {
            background: var(--lp-primary-hover);
            color: #ffffff;
        }

        html[data-model="model-6"] .btn-sticky-cta {
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            box-shadow: none;
        }

        /* =========================================
           MODEL 2: BENEFITS & PROVA FIRST
           Foco ampliado em benefícios, prova de qualidade e diferenciais antes do checkout.
           ========================================= */
        html[data-model="model-2"] body {
            background-color: #f1f5f9;
        }

        html[data-model="model-2"] .container {
            display: flex;
            flex-direction: column;
        }
        
        /* Reordenação estrutural total para Benefits First */
        html[data-model="model-2"] .promo-badge { 
            order: 1; 
            background: linear-gradient(135deg, var(--lp-primary) 0%, var(--lp-primary-hover) 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        html[data-model="model-2"] .product-title { 
            order: 2; 
            font-size: 26px;
            letter-spacing: -0.5px;
        }
        html[data-model="model-2"] .product-subheadline { 
            order: 3; 
            background: #ffffff;
            padding: 12px 14px;
            border-radius: 12px;
            border-left: 3.5px solid var(--lp-primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 16px;
        }
        html[data-model="model-2"] .gallery-wrapper { 
            order: 4; 
            width: 100%;
            aspect-ratio: 1;
            flex-shrink: 0;
        }
        html[data-model="model-2"] .gallery-thumbs { 
            order: 5; 
            margin-bottom: 18px; 
            flex-shrink: 0;
        }
        
        /* 1º BLOCO DE VALOR: 3 Garantias & Procedência com visual de Cartão de Confiança */
        html[data-model="model-2"] .guarantees-card { 
            order: 6; 
            margin-top: 0;
            margin-bottom: 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 18px 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.06);
            position: relative;
        }

        html[data-model="model-2"] .guarantees-card::before {
            content: "🛡️ GARANTIAS & PROCEDÊNCIA TESTADA";
            display: block;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.6px;
            color: var(--lp-primary);
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        html[data-model="model-2"] .guarantees-card .guarantee-item {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        html[data-model="model-2"] .guarantees-card .guarantee-item i {
            color: #ffffff;
            background: #10b981;
            padding: 2px;
            font-size: 13px;
            border-radius: 50%;
            font-weight: 900;
        }
        
        /* 2º BLOCO DE VALOR: 3 Pilares de Atendimento Exclusivo */
        html[data-model="model-2"] .features-grid-wrapper { 
            order: 7; 
            margin-bottom: 20px;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 18px !important;
            padding: 18px 14px !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05) !important;
        }

        html[data-model="model-2"] .features-grid .feature-box {
            background: #f8fafc;
            padding: 12px 6px;
            border-radius: 14px;
            border: 1px solid #edf2f7;
        }

        html[data-model="model-2"] .features-grid .feature-box i {
            font-size: 20px;
            color: var(--lp-primary);
        }
        
        /* 3º BLOCO DE VALOR: Card de Preço em Destaque Absoluto */
        html[data-model="model-2"] .price-card { 
            order: 8; 
            margin-bottom: 24px;
            border: 2px solid var(--lp-primary);
            border-radius: 22px;
            padding: 24px 20px 20px 20px;
            background: linear-gradient(180deg, #ffffff 0%, #fffbfd 100%);
            box-shadow: 0 10px 30px -4px rgba(157, 23, 77, 0.2);
            position: relative;
        }

        html[data-model="model-2"] .price-card::before {
            content: "⚡ OFERTA DIRETA COM CONDIÇÃO ESPECIAL";
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--lp-primary);
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            padding: 3px 14px;
            border-radius: 100px;
            letter-spacing: 0.6px;
            white-space: nowrap;
            box-shadow: 0 4px 10px rgba(157, 23, 77, 0.35);
        }

        html[data-model="model-2"] .price-card .current-price {
            font-size: 36px;
        }
        
        /* O restante do fluxo no Modelo 2 */
        html[data-model="model-2"] .urgency-block { order: 9; }
        html[data-model="model-2"] .faq-title { order: 10; }
        html[data-model="model-2"] .faq-accordion { order: 11; }
        html[data-model="model-2"] .closing-card { order: 12; }
        html[data-model="model-2"] .footer-wrap { order: 13; }

        /* =========================================
           MODEL 3: MINIMAL COMPACT
           Visual limpo, direto, com a imagem em destaque total no topo,
           título e preço unificados e botões estilo pill com máxima conversão móvel.
           ========================================= */
        html[data-model="model-3"] body {
            background-color: #fafafa;
        }

        html[data-model="model-3"] .container {
            display: flex;
            flex-direction: column;
            padding: 12px 14px 100px 14px;
        }

        /* Reordenação do Modelo 3: Imagem no topo imediato (Top Visual) */
        html[data-model="model-3"] .promo-badge { 
            order: 1; 
            align-self: center;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 100px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        html[data-model="model-3"] .gallery-wrapper { 
            order: 2; 
            width: 100%;
            aspect-ratio: 1;
            flex-shrink: 0;
            border-radius: 24px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.04);
            margin-bottom: 8px;
        }

        html[data-model="model-3"] .gallery-thumbs { 
            order: 3; 
            margin-bottom: 16px; 
            flex-shrink: 0;
            justify-content: center;
        }

        html[data-model="model-3"] .gallery-thumbs .thumb-item {
            border-radius: 12px;
            width: 52px;
            height: 52px;
        }

        /* Bloco de Título e Preço Integrados */
        html[data-model="model-3"] .product-title { 
            order: 4; 
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.5px;
            line-height: 1.25;
            text-align: center;
            margin-bottom: 6px;
        }

        html[data-model="model-3"] .product-subheadline { 
            order: 5; 
            text-align: center;
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 16px;
            padding: 0 8px;
        }

        html[data-model="model-3"] .price-card { 
            order: 6; 
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 24px;
            padding: 20px 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
            text-align: center;
        }

        html[data-model="model-3"] .price-card .price-row {
            justify-content: center;
            align-items: baseline;
            gap: 10px;
        }

        html[data-model="model-3"] .price-card .current-price {
            font-size: 32px;
            font-weight: 950;
            color: #0f172a;
            letter-spacing: -1px;
        }

        html[data-model="model-3"] .price-card .btn-cta {
            border-radius: 100px;
            padding: 15px 24px;
            font-size: 15px;
            box-shadow: 0 8px 25px -4px rgba(157, 23, 77, 0.4);
            letter-spacing: 0.2px;
        }

        /* 3 Pilares e Garantias Minimalistas */
        html[data-model="model-3"] .features-grid-wrapper {
            order: 7;
            margin-bottom: 14px;
            background: #ffffff;
            border-radius: 20px;
            padding: 12px 10px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        html[data-model="model-3"] .features-grid .feature-box {
            background: transparent;
            padding: 6px 2px;
            border: none;
        }

        html[data-model="model-3"] .features-grid .feature-box i {
            font-size: 18px;
        }

        html[data-model="model-3"] .features-grid .feature-box strong {
            font-size: 10.5px;
        }

        html[data-model="model-3"] .features-grid .feature-box small {
            font-size: 9.5px;
        }

        html[data-model="model-3"] .guarantees-card {
            order: 8;
            margin-top: 0;
            margin-bottom: 16px;
            background: #ffffff;
            border-radius: 20px;
            padding: 14px 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        html[data-model="model-3"] .guarantees-card .guarantee-item {
            font-size: 12.5px;
        }

        html[data-model="model-3"] .urgency-block { 
            order: 9; 
            border-radius: 20px;
            border: 1px dashed #cbd5e1;
            background: #ffffff;
        }

        html[data-model="model-3"] .urgency-block .btn-whatsapp-urgency {
            border-radius: 100px;
        }

        html[data-model="model-3"] .faq-title { order: 10; }
        html[data-model="model-3"] .faq-accordion { order: 11; }
        html[data-model="model-3"] .faq-card { border-radius: 16px; }
        html[data-model="model-3"] .closing-card { 
            order: 12; 
            border-radius: 24px;
        }
        html[data-model="model-3"] .closing-card .btn-cta {
            border-radius: 100px;
        }
        html[data-model="model-3"] .footer-wrap { order: 13; }

        /* =========================================
           MODEL 4: BENTO BOX MODERN
           Blocos visuais modulares e modernos com hierarquia de informações clara.
           Design inspirado em Bento Boxes, com cantos arredondados e sombras suaves.
           ========================================= */
        html[data-model="model-4"] body {
            background-color: #f3f4f6;
        }

        html[data-model="model-4"] .container {
            display: flex;
            flex-direction: column;
            padding: 16px 16px 100px 16px;
            gap: 12px;
        }

        /* Reordenação do Modelo 4 */
        html[data-model="model-4"] .promo-badge { 
            order: 1; 
            align-self: flex-start;
            background: #ffffff;
            color: var(--lp-primary);
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 0;
        }

        html[data-model="model-4"] .product-title { 
            order: 2; 
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 0;
            background: #ffffff;
            padding: 16px;
            border-radius: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
        }

        html[data-model="model-4"] .product-subheadline { 
            order: 3; 
            font-size: 13px;
            color: #4b5563;
            background: #ffffff;
            padding: 12px 16px;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            margin-bottom: 0;
        }

        html[data-model="model-4"] .gallery-wrapper { 
            order: 4; 
            width: 100%;
            aspect-ratio: 1;
            flex-shrink: 0;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 0;
        }

        html[data-model="model-4"] .gallery-thumbs { 
            order: 5; 
            margin-bottom: 0; 
            flex-shrink: 0;
            background: #ffffff;
            padding: 12px;
            border-radius: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
        }

        html[data-model="model-4"] .price-card { 
            order: 6; 
            background: #ffffff;
            border: none;
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
        }

        html[data-model="model-4"] .price-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--lp-primary);
        }

        html[data-model="model-4"] .price-card .current-price {
            font-size: 34px;
            font-weight: 900;
            letter-spacing: -1px;
        }

        html[data-model="model-4"] .features-grid-wrapper {
            order: 7;
            margin-bottom: 0;
            background: transparent;
            border: none;
            padding: 0;
            box-shadow: none;
        }

        html[data-model="model-4"] .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            border-top: none;
            padding-top: 0;
            margin-top: 0;
        }

        html[data-model="model-4"] .features-grid .feature-box {
            background: #ffffff;
            padding: 16px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        html[data-model="model-4"] .features-grid .feature-box:last-child {
            grid-column: span 2;
            flex-direction: row;
            gap: 12px;
            text-align: left;
        }

        html[data-model="model-4"] .features-grid .feature-box:last-child i {
            margin-bottom: 0;
            font-size: 24px;
        }

        html[data-model="model-4"] .guarantees-card {
            order: 8;
            margin-top: 0;
            margin-bottom: 0;
            background: #ffffff;
            border-radius: 20px;
            padding: 16px;
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
        }

        html[data-model="model-4"] .urgency-block { 
            order: 9; 
            border-radius: 20px;
            border: none;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            margin-bottom: 0;
        }

        html[data-model="model-4"] .faq-title { 
            order: 10; 
            background: #ffffff;
            padding: 16px;
            border-radius: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            margin-bottom: 0;
            text-align: center;
        }
        
        html[data-model="model-4"] .faq-accordion { 
            order: 11; 
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        html[data-model="model-4"] .faq-card { 
            border-radius: 16px; 
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            margin-bottom: 0;
        }

        html[data-model="model-4"] .closing-card { 
            order: 12; 
            border-radius: 24px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 0;
        }

        html[data-model="model-4"] .footer-wrap { 
            order: 13; 
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            margin-top: 12px;
        }

        /* =========================================
           MODELO 05: CYBER TECH GLOW (DARK THEME)
           ========================================= */
        html[data-model="model-5"] body {
            background-color: #0b0f19;
            color: #f8fafc;
        }

        html[data-model="model-5"] .container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 16px;
            max-width: 520px;
        }

        html[data-model="model-5"] .promo-badge { 
            order: 1; 
            align-self: center;
            background: var(--lp-primary);
            color: #ffffff;
            border: none;
            border-radius: 100px;
            padding: 6px 16px;
            margin-bottom: 0;
            box-shadow: 0 0 15px var(--lp-primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 900;
        }

        html[data-model="model-5"] .product-title { 
            order: 2; 
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.5px;
            background: transparent;
            color: #f8fafc;
            padding: 0 4px;
            margin-bottom: 0;
            text-align: center;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }

        html[data-model="model-5"] .product-subheadline { 
            order: 3; 
            font-size: 13px;
            color: #94a3b8;
            background: transparent;
            padding: 0 8px;
            margin-bottom: 0;
            text-align: center;
        }

        html[data-model="model-5"] .gallery-wrapper { 
            order: 4; 
            width: 100%;
            height: 380px;
            border-radius: 24px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.8);
            margin-bottom: 0;
            border: 1px solid #1e293b;
        }

        html[data-model="model-5"] .gallery-thumbs { 
            order: 5; 
            margin-bottom: 0;
            background: #111827;
            padding: 10px 14px;
            border-radius: 20px;
            border: 1px solid #1f2937;
        }

        html[data-model="model-5"] .price-card { 
            order: 6; 
            background: linear-gradient(145deg, #111827, #0b0f19);
            border: 1px solid var(--lp-primary);
            border-radius: 24px;
            padding: 22px 18px;
            box-shadow: 0 0 25px var(--lp-primary);
            margin-bottom: 0;
            position: relative;
        }

        html[data-model="model-5"] .price-card .old-price {
            color: #64748b;
        }

        html[data-model="model-5"] .price-card .current-price { 
            color: var(--lp-primary); 
            text-shadow: 0 0 12px var(--lp-primary);
            font-size: 32px;
        }

        html[data-model="model-5"] .price-card .savings-tag {
            background: var(--lp-primary);
            color: #ffffff;
            border: 1px solid var(--lp-primary);
        }

        html[data-model="model-5"] .price-card .savings-text {
            color: #94a3b8;
        }

        html[data-model="model-5"] .price-card .savings-text span {
            background: var(--lp-primary);
            box-shadow: 0 0 8px var(--lp-primary);
        }

        html[data-model="model-5"] .price-card .btn-whatsapp {
            background: linear-gradient(135deg, var(--lp-primary) 0%, var(--lp-primary-hover) 100%);
            box-shadow: 0 4px 20px var(--lp-primary);
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-radius: 14px;
        }

        html[data-model="model-5"] .price-card .urgency-text {
            color: #94a3b8;
        }

        html[data-model="model-5"] .features-grid-wrapper { 
            order: 7; 
            background: transparent;
            border-radius: 0;
            padding: 0;
            border: none;
            margin-bottom: 0;
            box-shadow: none;
        }

        html[data-model="model-5"] .features-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            border: none;
            padding-top: 0;
            margin-top: 0;
        }

        html[data-model="model-5"] .features-grid .feature-box {
            background: #111827;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #1f2937;
            display: flex;
            align-items: center;
            text-align: left;
            gap: 14px;
        }

        html[data-model="model-5"] .features-grid .feature-box i {
            color: var(--lp-primary);
            font-size: 22px;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 0;
        }

        html[data-model="model-5"] .features-grid .feature-box strong {
            color: #f8fafc;
            font-size: 13px;
        }

        html[data-model="model-5"] .features-grid .feature-box small {
            color: #94a3b8;
            font-size: 11px;
        }

        html[data-model="model-5"] .guarantees-card { 
            order: 8; 
            background: #111827;
            border-radius: 20px;
            padding: 18px;
            border: 1px solid #1f2937;
            margin-bottom: 0;
        }

        html[data-model="model-5"] .guarantees-card h2 {
            color: #f8fafc;
        }

        html[data-model="model-5"] .guarantees-card p {
            color: #cbd5e1;
        }

        html[data-model="model-5"] .urgency-block { 
            order: 9; 
            border-radius: 20px;
            border: 1px solid var(--lp-primary);
            background: rgba(255, 255, 255, 0.02);
            margin-bottom: 0;
        }

        html[data-model="model-5"] .urgency-block h3 {
            color: var(--lp-primary);
        }

        html[data-model="model-5"] .urgency-block p {
            color: #f8fafc;
        }

        html[data-model="model-5"] .urgency-block .timer-badge {
            background: var(--lp-primary);
            color: #ffffff;
            box-shadow: 0 0 15px var(--lp-primary);
        }

        html[data-model="model-5"] .faq-title { 
            order: 10; 
            background: transparent;
            padding: 12px 0;
            margin-bottom: 0;
            text-align: center;
            color: #f8fafc;
            text-shadow: 0 2px 6px rgba(0,0,0,0.5);
        }

        html[data-model="model-5"] .faq-accordion { 
            order: 11; 
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        html[data-model="model-5"] .faq-card { 
            border-radius: 16px; 
            border: 1px solid #1f2937;
            background: #111827;
            margin-bottom: 0;
        }

        html[data-model="model-5"] .faq-question {
            color: #f1f5f9;
        }

        html[data-model="model-5"] .faq-question i {
            color: var(--lp-primary);
        }

        html[data-model="model-5"] .faq-answer {
            color: #94a3b8;
        }

        html[data-model="model-5"] .closing-card { 
            order: 12; 
            border-radius: 24px;
            border: 1px solid var(--lp-primary);
            background: linear-gradient(180deg, #111827, #0b0f19);
            box-shadow: 0 0 25px var(--lp-primary);
            margin-bottom: 0;
        }

        html[data-model="model-5"] .closing-card h2 {
            color: #f8fafc;
        }

        html[data-model="model-5"] .closing-card p {
            color: #cbd5e1;
        }

        html[data-model="model-5"] .closing-card .btn-whatsapp {
            background: linear-gradient(135deg, var(--lp-primary) 0%, var(--lp-primary-hover) 100%);
            box-shadow: 0 4px 20px var(--lp-primary);
        }

        html[data-model="model-5"] .footer-wrap { 
            order: 13; 
            background: transparent;
            border-radius: 0;
            padding: 20px 0;
            margin-top: 8px;
            color: #64748b;
        }

        /* =========================================
           MODELO 6: EDITORIAL LUXURY
           ========================================= */
        html[data-model="model-6"] body {
            background-color: #faf9f6; /* Off-white luxuoso */
            color: #2c2c2c;
        }

        html[data-model="model-6"] .container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 24px 20px 100px 20px;
        }

        html[data-model="model-6"] .promo-badge {
            order: 1;
            background: transparent;
            border: 1px solid var(--lp-primary);
            color: var(--lp-primary);
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 10px;
            font-weight: 600;
            padding: 6px 16px;
            align-self: center;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .product-title {
            order: 2;
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 600;
            line-height: 1.1;
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .product-subheadline {
            order: 3;
            font-size: 14px;
            font-weight: 400;
            text-align: center;
            color: #555;
            line-height: 1.6;
            margin-bottom: 0;
            padding: 0 10px;
        }

        html[data-model="model-6"] .gallery-wrapper {
            order: 4;
            border-radius: 0;
            box-shadow: none;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .gallery-thumbs {
            order: 5;
            gap: 12px;
            padding: 12px 0;
            justify-content: center;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .gallery-thumbs .thumb-item {
            border-radius: 0;
            border: 1px solid #e5e5e5;
            width: 48px;
            height: 48px;
        }

        html[data-model="model-6"] .gallery-thumbs .thumb-item.active {
            border-color: var(--lp-primary);
            opacity: 1;
        }

        html[data-model="model-6"] .price-card {
            order: 6;
            background: transparent;
            box-shadow: none;
            border: none;
            border-top: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
            border-radius: 0;
            padding: 24px 0;
            margin-bottom: 0;
            text-align: center;
            align-items: center;
        }

        html[data-model="model-6"] .price-card .old-price {
            font-size: 16px;
            color: #888;
        }

        html[data-model="model-6"] .price-card .current-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 500;
            color: #1a1a1a;
            margin: 4px 0;
        }

        html[data-model="model-6"] .price-card .savings-tag {
            background: #f4f4f4;
            color: #333;
            border-radius: 0;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 10px;
        }

        html[data-model="model-6"] .price-card .savings-text {
            display: none;
        }

        html[data-model="model-6"] .price-card .btn-whatsapp {
            border-radius: 0;
            background: var(--lp-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            font-size: 14px;
            padding: 18px 24px;
            margin-top: 16px;
            box-shadow: none;
        }

        html[data-model="model-6"] .price-card .urgency-text {
            font-size: 12px;
            color: #666;
            font-style: italic;
            margin-top: 12px;
        }

        html[data-model="model-6"] .features-grid-wrapper {
            order: 6;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .features-grid-wrapper > h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 24px;
        }

        html[data-model="model-6"] .features-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        html[data-model="model-6"] .features-grid .feature-box {
            background: transparent;
            border: 1px solid #e5e5e5;
            border-radius: 0;
            padding: 20px;
            flex-direction: row;
            align-items: center;
            text-align: left;
            gap: 16px;
        }

        html[data-model="model-6"] .features-grid .feature-box i {
            font-size: 24px;
            color: var(--lp-primary);
            margin-bottom: 0;
        }

        html[data-model="model-6"] .features-grid .feature-box strong {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            color: #1a1a1a;
            display: block;
            margin-bottom: 4px;
        }

        html[data-model="model-6"] .features-grid .feature-box small {
            font-size: 13px;
            color: #666;
        }

        html[data-model="model-6"] .guarantees-card {
            order: 7;
            background: transparent;
            color: #2c2c2c;
            border: 1px solid #e5e5e5;
            border-radius: 0;
            box-shadow: none;
            padding: 20px;
            text-align: left;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .guarantees-card .guarantee-item {
            color: #2c2c2c;
        }

        html[data-model="model-6"] .guarantees-card .btn-cta {
            border-radius: 0;
            background: var(--lp-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            box-shadow: none;
        }

        html[data-model="model-6"] .urgency-block {
            order: 8;
            background: transparent;
            border: 1px solid var(--lp-primary);
            border-radius: 0;
            padding: 24px;
            text-align: center;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .urgency-block h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
            color: #1a1a1a;
        }

        html[data-model="model-6"] .urgency-block p {
            color: #555;
            font-size: 14px;
        }

        html[data-model="model-6"] .urgency-block .timer-badge {
            background: #f4f4f4;
            color: #1a1a1a;
            border-radius: 0;
            font-weight: 600;
            letter-spacing: 1px;
        }

        html[data-model="model-6"] .faq-title {
            order: 9;
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .faq-accordion {
            order: 10;
            gap: 0;
        }

        html[data-model="model-6"] .faq-card {
            background: transparent;
            border: none;
            border-bottom: 1px solid #e5e5e5;
            border-radius: 0;
            box-shadow: none;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .faq-question {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            padding: 20px 0;
        }

        html[data-model="model-6"] .faq-question i {
            color: #1a1a1a;
        }

        html[data-model="model-6"] .faq-answer {
            padding: 0 0 20px 0;
            color: #555;
            font-size: 14px;
        }

        html[data-model="model-6"] .closing-card {
            order: 11;
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 32px 0;
            text-align: center;
            margin-bottom: 0;
        }

        html[data-model="model-6"] .closing-card h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            color: #1a1a1a;
            margin-bottom: 16px;
        }

        html[data-model="model-6"] .closing-card p {
            color: #555;
            font-size: 15px;
            margin-bottom: 24px;
        }

        html[data-model="model-6"] .closing-card .btn-whatsapp {
            border-radius: 0;
            background: var(--lp-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            box-shadow: none;
        }

        html[data-model="model-6"] .footer-wrap {
            order: 12;
            background: transparent;
            border-top: 1px solid #e5e5e5;
            border-radius: 0;
            padding: 24px 0;
            margin-top: 16px;
            color: #888;
        }

    </style>
</head>
<body>

    <main class="container">
        <!-- Badge Superior -->
        <?php if (!empty($product->badge_text)): ?>
            <div class="promo-badge">
                <span><?= esc($product->badge_text) ?></span>
            </div>
        <?php endif; ?>

        <!-- Título e Subtítulo -->
        <h1 class="product-title"><?= esc(!empty($product->headline) ? $product->headline : $product->name) ?></h1>
        <?php 
        $subheadlineText = !empty($product->subheadline) ? $product->subheadline : $product->description;
        if (!empty($subheadlineText)): 
        ?>
            <p class="product-subheadline"><?= esc($subheadlineText) ?></p>
        <?php endif; ?>

        <!-- Galeria / Imagem Principal -->
        <?php
        $mainImgSrc = !empty($images) ? base_url('uploads/products/' . $images[0]->image_path) : '';
        $totalImages = count($images);
        ?>
        <div class="gallery-wrapper">
            <?php if (!empty($product->promotional_price) && $product->promotional_price < $product->price): ?>
                <?php 
                $discount = round((($product->price - $product->promotional_price) / $product->price) * 100);
                ?>
                <div class="gallery-discount-tag">
                    -<?= $discount ?>% OFF
                </div>
            <?php endif; ?>

            <img id="main-product-image" src="<?= esc($mainImgSrc) ?>" alt="<?= esc($product->name) ?>" class="gallery-main-img">

            <?php if ($totalImages > 0): ?>
                <div class="gallery-counter">
                    <span id="current-img-index">1</span>/<?= $totalImages ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Miniaturas de Imagens -->
        <?php if ($totalImages > 1): ?>
            <div class="gallery-thumbs">
                <?php foreach ($images as $index => $img): ?>
                    <div class="thumb-item <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index + 1 ?>" data-src="<?= base_url('uploads/products/' . $img->image_path) ?>">
                        <img src="<?= base_url('uploads/products/' . $img->image_path) ?>" alt="Miniatura">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- 3 Pilares de Atendimento -->
        <div class="features-grid-wrapper" style="background: #ffffff; border-radius: 18px; padding: 16px; box-shadow: var(--lp-card-shadow); margin-bottom: 20px; border: 1px solid #f1f5f9;">
            <div class="features-grid" style="margin-top: 0; padding-top: 0; border-top: none;">
                <div class="feature-box">
                    <i class="ti ti-message-circle"></i>
                    <strong>Atendimento humano</strong>
                    <small>Você fala com <?= esc($company['company_name'] ?? 'Dias Imports') ?>, não com robô</small>
                </div>
                <div class="feature-box">
                    <i class="ti ti-notes"></i>
                    <strong>Sem cadastro</strong>
                    <small>Nenhum formulário, nenhuma senha</small>
                </div>
                <div class="feature-box">
                    <i class="ti ti-currency-dollar"></i>
                    <strong>Preço fechado</strong>
                    <small>Confirmado na conversa antes de pagar</small>
                </div>
            </div>
        </div>

        <!-- Card de Preço e CTA -->
        <?php
        $price = (float) $product->price;
        $promoPrice = !empty($product->promotional_price) ? (float) $product->promotional_price : null;
        $currentPriceFormatted = number_format($promoPrice ?: $price, 2, ',', '.');
        $oldPriceFormatted = number_format($price, 2, ',', '.');
        $savings = $promoPrice ? ($price - $promoPrice) : 0;
        $savingsFormatted = number_format($savings, 2, ',', '.');
        ?>
        <div class="price-card">
            <div class="price-row">
                <?php if ($promoPrice): ?>
                    <span class="old-price">R$ <?= $oldPriceFormatted ?></span>
                <?php endif; ?>
                <span class="current-price">R$ <?= $currentPriceFormatted ?></span>
                <?php if ($savings > 0): ?>
                    <span class="savings-tag">economize R$ <?= $savingsFormatted ?></span>
                <?php endif; ?>
            </div>

            <?php if ($savings > 0): ?>
                <p class="price-subtext">Você economiza R$ <?= $savingsFormatted ?> neste preço</p>
            <?php endif; ?>

            <a href="<?= esc($whatsappUrl) ?>" class="btn-cta" target="_blank">
                <span><?= esc(!empty($product->button_text) ? $product->button_text : 'Quero aproveitar agora') ?></span>
                <i class="ti <?= esc(!empty($product->cta_icon) ? $product->cta_icon : 'ti-arrow-narrow-right') ?>"></i>
            </a>

            <div class="micro-security">
                <i class="ti ti-lock"></i>
                <span><?= esc(!empty($product->urgency_text) ? $product->urgency_text : 'Compra sem cadastro. Você fala direto com a loja.') ?></span>
            </div>
        </div>

        <!-- 3 Garantias Rápidas com Check e CTA -->
        <div class="guarantees-card">
            <div class="guarantee-item">
                <i class="ti ti-check"></i>
                <span><?= esc(!empty($product->shipping_info) ? $product->shipping_info : 'Entrega rápida em ' . ($company['city'] ?? 'Barretos') . ' e região') ?></span>
            </div>
            <div class="guarantee-item">
                <i class="ti ti-check"></i>
                <span><?= esc(!empty($product->payment_info) ? $product->payment_info : 'Pagamento no PIX ou cartão') ?></span>
            </div>
            <div class="guarantee-item">
                <i class="ti ti-check"></i>
                <span><?= esc(!empty($product->guarantee_info) ? $product->guarantee_info : 'Produto conferido antes do envio') ?></span>
            </div>
            <a href="<?= esc($whatsappUrl) ?>" class="btn-cta" target="_blank" style="margin-top: 6px;">
                <span><?= esc(!empty($product->button_text) ? $product->button_text : 'Quero aproveitar agora') ?></span>
                <i class="ti <?= esc(!empty($product->cta_icon) ? $product->cta_icon : 'ti-arrow-narrow-right') ?>"></i>
            </a>
        </div>

        <!-- Bloco de Urgência / Escassez -->
        <div class="urgency-block" style="background: #ffffff; border-radius: 18px; padding: 20px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 28px; border: 1px solid #f1f5f9;">
            <h2 class="section-title" style="margin-bottom: 8px;"><?= esc(!empty($product->about_title) ? $product->about_title : 'Últimas unidades em estoque') ?></h2>
            <div class="about-text" style="margin-bottom: 0;">
                <?= nl2br(esc(!empty($product->about_content) ? $product->about_content : 'Devido à alta demanda desta edição, restam pouquíssimos frascos disponíveis. Garanta o seu antes que o lote esgote definitivamente.')) ?>
            </div>
        </div>

        <!-- Accordion FAQ: Antes de Fechar -->
        <h2 class="section-title faq-title">Antes de fechar</h2>
        <div class="faq-accordion">
            <?php foreach ($faqList as $faq): ?>
                <div class="faq-card">
                    <button type="button" class="faq-question">
                        <span><?= esc($faq['q'] ?? '') ?></span>
                        <i class="ti ti-plus"></i>
                    </button>
                    <div class="faq-answer">
                        <?= esc($faq['a'] ?? '') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Card de Fechamento pelo WhatsApp -->
        <div class="closing-card">
            <h2 class="section-title"><?= esc(!empty($product->checkout_title) ? $product->checkout_title : 'Fechar pedido pelo WhatsApp') ?></h2>
            <div class="closing-price-wrap">
                <div class="price-row" style="justify-content: center;">
                    <?php if ($promoPrice): ?>
                        <span class="old-price">R$ <?= $oldPriceFormatted ?></span>
                    <?php endif; ?>
                    <span class="current-price">R$ <?= $currentPriceFormatted ?></span>
                    <?php if ($savings > 0): ?>
                        <span class="savings-tag">economize R$ <?= $savingsFormatted ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?= esc($whatsappUrl) ?>" class="btn-cta" target="_blank">
                <span><?= esc(!empty($product->button_text) ? $product->button_text : 'Quero aproveitar agora') ?></span>
                <i class="ti <?= esc(!empty($product->cta_icon) ? $product->cta_icon : 'ti-arrow-narrow-right') ?>"></i>
            </a>

            <p class="closing-subtext"><?= esc(!empty($product->checkout_subtitle) ? $product->checkout_subtitle : 'A conversa abre com o produto e o preço já escritos. Você só confirma.') ?></p>
        </div>

        <!-- Rodapé Institucional -->
        <footer class="footer-wrap">
            <strong><?= esc($company['company_name'] ?? 'Dias Imports') ?></strong><br>
            <span><?= esc($company['city'] ?? 'Barretos') ?> - <?= esc($company['state'] ?? 'SP') ?></span><br>
            <span style="font-size: 10px; margin-top: 4px; display: block;">Atendimento e pedidos pelo WhatsApp. Imagens meramente ilustrativas. Preço promocional válido para a compra feita por esta página.</span>
        </footer>
    </main>

    <!-- Barra Fixa Inferior de Compra Rápida -->
    <div class="bottom-sticky-bar">
        <div class="sticky-price-col">
            <?php if ($promoPrice): ?>
                <small>R$ <?= $oldPriceFormatted ?></small>
            <?php endif; ?>
            <strong>R$ <?= $currentPriceFormatted ?></strong>
        </div>
        <a href="<?= esc($whatsappUrl) ?>" class="btn-sticky-cta" target="_blank">
            <span><?= esc(!empty($product->button_text) ? $product->button_text : 'Quero aproveitar agora') ?></span>
            <i class="ti <?= esc(!empty($product->cta_icon) ? $product->cta_icon : 'ti-arrow-narrow-right') ?>"></i>
        </a>
    </div>

    <script>
        // Galeria de Fotos - Troca ao clicar na miniatura
        document.querySelectorAll('.thumb-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const mainImg = document.getElementById('main-product-image');
                const counter = document.getElementById('current-img-index');
                if (mainImg) {
                    mainImg.src = this.dataset.src;
                }
                if (counter) {
                    counter.textContent = this.dataset.index;
                }
            });
        });

        // Accordion de Dúvidas
        document.querySelectorAll('.faq-question').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.faq-card');
                card.classList.toggle('open');
            });
        });

        // Rastreamento local interno de cliques de compra/WhatsApp (Estatísticas do Produto)
        document.querySelectorAll('a.btn-cta, a.btn-sticky-cta').forEach(link => {
            link.addEventListener('click', function() {
                const isSticky = this.classList.contains('btn-sticky-cta');
                const eventType = isSticky ? 'sticky_cta_click' : 'cta_click';
                
                // Envio em beacon/fetch assíncrono para não travar o redirecionamento
                try {
                    const trackUrl = '<?= site_url('p/' . $product->id . '/track') ?>';
                    const formData = new FormData();
                    formData.append('event_type', eventType);
                    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                    
                    if (navigator.sendBeacon) {
                        navigator.sendBeacon(trackUrl, formData);
                    } else {
                        fetch(trackUrl, { method: 'POST', body: formData, keepalive: true });
                    }
                } catch (e) {
                    console.debug('Tracking click error', e);
                }

                <?php if (! empty($product->meta_ads_active) && ! empty($metaAds['pixel_id'])): ?>
                if (typeof fbq === 'function') {
                    try {
                        fbq('track', 'Purchase', {
                            content_name: '<?= esc($product->name, 'js') ?>',
                            content_type: 'product',
                            content_ids: ['<?= esc((string)$product->id, 'js') ?>'],
                            value: <?= (float)($product->promotional_price ?: $product->price) ?>,
                            currency: 'BRL'
                        });
                    } catch (pixelErr) {
                        console.warn('Meta Pixel Purchase tracking error:', pixelErr);
                    }
                }
                <?php endif; ?>
            });
        });
    </script>
</body>
</html>
