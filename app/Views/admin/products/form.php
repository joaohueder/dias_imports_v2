<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$isEdit = !empty($product);
$formAction = $isEdit ? site_url('produtos/' . $product->id . '/editar') : site_url('produtos/novo');
$isActive = (bool) old('active', $product->active ?? 1);
$metaAdsActive = (bool) old('meta_ads_active', $product->meta_ads_active ?? 1);
?>

<div class="user-form-container" data-product-form-root>
    <div class="user-form-topbar">
        <a href="<?= site_url('produtos') ?>" class="back-link-btn">
            <i class="ti ti-chevron-left"></i>
            <span>Produtos</span>
        </a>
    </div>

    <form action="<?= $formAction ?>" method="post" class="user-main-form" data-dirty-form id="product-editor-form" data-processing-title="<?= $isEdit ? 'Atualizando produto' : 'Cadastrando produto' ?>" data-processing-message="Salvando as informações com segurança.">
        <?= csrf_field() ?>
        <input type="hidden" name="active_tab" id="active_tab" value="<?= esc(service('request')->getGet('tab') ?? 'tab-info') ?>">

        <!-- Tabs Navigation -->
        <div class="settings-tabs" style="flex-direction: row; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none; border-bottom: 1px solid rgb(var(--border)); margin-bottom: 16px;">
            <button type="button" class="settings-tab active" data-tab-target="tab-info">Informações do Produto</button>
            <button type="button" class="settings-tab" data-tab-target="tab-images">Imagens</button>
            <button type="button" class="settings-tab" data-tab-target="tab-destination">Destino</button>
            <button type="button" class="settings-tab" data-tab-target="tab-landing">Landing Page</button>
            <?php if ($isEdit): ?>
            <button type="button" class="settings-tab" data-tab-target="tab-stats">
                <i class="ti ti-chart-bar" style="margin-right: 4px;"></i> Estatísticas
            </button>
            <?php endif; ?>
        </div>

        <!-- Tab Content: Informações do Produto -->
        <div class="settings-tab-panel" id="tab-info">
            <section class="form-card-section" aria-labelledby="dados-produto-title">
                <h2 id="dados-produto-title" class="section-card-title">Informações Principais</h2>

                <div class="form-grid-account">
                    <!-- Nome -->
                    <div class="form-group col-full">
                        <label for="name">Nome do Produto *</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= esc(old('name', $product->name ?? '')) ?>" required maxlength="255" placeholder="Ex: Perfume Silver Scent 100ml">
                    </div>

                    <!-- Slug -->
                    <div class="form-group col-full">
                        <label for="slug">Slug da URL (Identificador) *</label>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: rgb(var(--muted)); font-size: 13px;"><?= site_url('p/') ?></span>
                            <input type="text" id="slug" name="slug" class="form-control" value="<?= esc(old('slug', $product->slug ?? '')) ?>" required maxlength="255" placeholder="silver-scent-100ml" readonly style="background-color: rgba(255, 255, 255, 0.03); cursor: not-allowed; color: #94a3b8;">
                        </div>
                        <small class="form-help-text">Utilizado para formar o link direto da Landing Page. Gerado automaticamente.</small>
                    </div>

                    <!-- Preços -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="price">Preço Original (R$) *</label>
                            <input type="text" id="price" name="price" class="form-control money-mask" value="<?= esc(old('price', isset($product->price) ? number_format($product->price, 2, ',', '.') : '')) ?>" required placeholder="0,00">
                        </div>

                        <div class="form-group">
                            <label for="promotional_price">Preço Promocional (R$)</label>
                            <input type="text" id="promotional_price" name="promotional_price" class="form-control money-mask" value="<?= esc(old('promotional_price', isset($product->promotional_price) ? number_format($product->promotional_price, 2, ',', '.') : '')) ?>" placeholder="0,00">
                            <small class="form-help-text">Se preenchido, o preço original aparecerá riscado.</small>
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div class="form-group col-full">
                        <label for="description">Descrição Resumida</label>
                        <textarea id="description" name="description" class="form-control" rows="6" placeholder="Breve resumo ou especificações do produto"><?= esc(old('description', $product->description ?? '')) ?></textarea>
                    </div>
                </div>
            </section>
        </div>

        <!-- Tab Content: Imagens -->
        <div class="settings-tab-panel" id="tab-images" hidden>
            <section class="form-card-section" aria-labelledby="imagens-title">
                <h2 id="imagens-title" class="section-card-title">Galeria de Imagens</h2>
                
                <div class="form-grid-account">
                    <div class="form-group col-full">
                        <label>Fotos do Produto</label>
                        <div class="upload-area" id="product-images-upload" style="border: 2px dashed rgba(255,255,255,0.1); border-radius: 12px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.2s ease;">
                            <i class="ti ti-cloud-upload" style="font-size: 48px; color: #7c69ff; margin-bottom: 16px; display: block;"></i>
                            <h3 style="margin: 0 0 8px 0; font-size: 16px; color: #f1f5f9;">Clique ou arraste imagens aqui</h3>
                            <p style="margin: 0; font-size: 13px; color: #64748b;">Formatos suportados: JPG, PNG, WEBP. Máximo 5MB por imagem.</p>
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" style="display: none;" id="images-input">
                        </div>
                        <div id="images-preview-container" style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px;">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img): ?>
                                    <div class="image-preview-item" data-id="<?= $img->id ?>" style="position: relative; width: 120px; height: 120px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                                        <img src="<?= base_url('uploads/products/' . $img->image_path) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        
                                        <div style="position: absolute; top: 4px; right: 4px; display: flex; gap: 4px;">
                                            <?php if (!$img->is_cover): ?>
                                                <button type="button" onclick="setCoverImage(<?= $img->id ?>, this)" title="Definir como Capa" style="background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 4px; padding: 4px; cursor: pointer;">
                                                    <i class="ti ti-star"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" onclick="deleteImage(<?= $img->id ?>, this)" title="Excluir Imagem" style="background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 4px; padding: 4px; cursor: pointer;">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <?php if ($img->is_cover): ?>
                                            <span class="cover-badge" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(124, 105, 255, 0.9); color: white; font-size: 11px; text-align: center; padding: 2px 0;">Capa</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Tab Content: Destino -->
        <div class="settings-tab-panel" id="tab-destination" hidden>
            <section class="form-card-section" aria-labelledby="destino-title">
                <h2 id="destino-title" class="section-card-title">Configurações de Destino e Rastreamento</h2>

                <div class="form-grid-account" style="grid-template-columns: 1fr 1fr; gap: 24px;">
                    <!-- WhatsApp Number -->
                    <div class="form-group">
                        <label for="whatsapp_number">WhatsApp de Atendimento</label>
                        <select id="whatsapp_number" name="whatsapp_number" class="form-select">
                            <option value="">Usar número padrão da empresa</option>
                            <?php if (!empty($whatsapps)): ?>
                                <?php foreach ($whatsapps as $wa): ?>
                                    <option value="<?= esc($wa['phone']) ?>" <?= old('whatsapp_number', $product->whatsapp_number ?? '') === $wa['phone'] ? 'selected' : '' ?>>
                                        <?= esc($wa['name']) ?> (<?= esc($wa['phone']) ?>) <?= $wa['is_default'] ? '- Padrão' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-help-text">Número que receberá os contatos deste produto.</small>
                    </div>

                    <!-- Meta Ads Catalog -->
                    <div class="form-group">
                        <label for="meta_catalog">Catálogo da Meta Ads</label>
                        <select id="meta_catalog" name="meta_catalog" class="form-select" disabled>
                            <option value="">Selecione um catálogo (Em implantação)</option>
                        </select>
                        <small class="form-help-text">Integração com catálogo do Facebook/Instagram em desenvolvimento.</small>
                    </div>

                    <!-- Meta Ads Tracking -->
                    <div class="form-group form-group-row col-full" style="display: flex; gap: 24px; flex-wrap: wrap; margin-top: 12px;">
                        <div class="field-status-checkbox">
                            <label class="custom-checkbox-label">
                                <input type="checkbox" name="meta_ads_active" value="1" <?= $metaAdsActive ? 'checked' : '' ?>>
                                <span class="custom-checkbox-box"></span>
                                <span class="checkbox-text">Ativar Rastreamento Meta Ads (Pixel/API)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Tab Content: Landing Page -->
        <div class="settings-tab-panel" id="tab-landing" hidden>
            <div style="display: grid; grid-template-columns: 1fr 380px; gap: 32px; align-items: start; padding: 12px 0;">
                <!-- Formulário de Configurações da Landing Page -->
                <div class="landing-config-col">

                    <!-- 01 - MODELO VISUAL -->
                    <div class="settings-card-block" style="margin-bottom: 24px;">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(99, 91, 255, 0.15); color: #635bff;"><i class="ti ti-template"></i></div>
                            <div>
                                <h3 class="settings-section-title">01 - Modelos da Landing Page (6 Opções)</h3>
                                <p class="settings-section-subtitle">Selecione a estrutura e experiência de conversão do produto. O mockup ao lado atualiza em tempo real.</p>
                            </div>
                        </div>

                        <div class="template-models-grid">
                            <?php
                            $modelsList = [
                                ['id' => 'model-1', 'name' => 'Oferta Direta & Hero', 'desc' => 'Estrutura clássica de alta conversão focada no produto e checkout imediato.', 'icon' => 'ti-layout-topbar', 'available' => true],
                                ['id' => 'model-2', 'name' => 'Benefits & Prova', 'desc' => 'Foco ampliado em benefícios, diferenciais e confiança antes da compra.', 'icon' => 'ti-layout-list', 'available' => true],
                                ['id' => 'model-3', 'name' => 'Minimal Compact', 'desc' => 'Visual limpo e direto, foto no topo e checkout com botão pill rápido.', 'icon' => 'ti-pill', 'available' => true],
                                ['id' => 'model-4', 'name' => 'Bento Box Modern', 'desc' => 'Blocos visuais modulares e modernos com hierarquia de informações clara.', 'icon' => 'ti-layout-grid', 'available' => true],
                                ['id' => 'model-5', 'name' => 'Cyber Tech Glow', 'desc' => 'Estilo vibrante de alto impacto visual com contrastes marcantes.', 'icon' => 'ti-bolt', 'available' => true],
                                ['id' => 'model-6', 'name' => 'Editorial Luxury', 'desc' => 'Estilo premium e sofisticado perfeito para perfumes e itens de luxo.', 'icon' => 'ti-crown', 'available' => true],
                            ];
                            $currentModel = old('layout', $product->layout ?? 'model-1');
                            ?>
                            <?php foreach ($modelsList as $m): ?>
                                <label class="template-model-card <?= $currentModel === $m['id'] ? 'active' : '' ?> <?= !$m['available'] ? 'disabled' : '' ?>" <?= !$m['available'] ? 'style="opacity: 0.5; cursor: not-allowed;" title="Em breve"' : '' ?>>
                                    <input type="radio" name="layout" value="<?= esc($m['id']) ?>" <?= $currentModel === $m['id'] ? 'checked' : '' ?> <?= !$m['available'] ? 'disabled' : '' ?> data-lp-model-radio>
                                    <div class="template-card-inner">
                                        <div class="template-card-icon"><i class="ti <?= esc($m['icon']) ?>"></i></div>
                                        <div class="template-card-info">
                                            <strong><?= esc($m['name']) ?> <?= !$m['available'] ? '<span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #64748b; font-size: 9px; padding: 2px 4px; margin-left: 4px;">Em breve</span>' : '' ?></strong>
                                            <p><?= esc($m['desc']) ?></p>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 02 - PALETAS DE CORES -->
                    <div class="settings-card-block" style="margin-bottom: 24px;">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(236, 72, 153, 0.15); color: #ec4899;"><i class="ti ti-palette"></i></div>
                            <div>
                                <h3 class="settings-section-title">02 - Paletas de Cores (6 Opções)</h3>
                                <p class="settings-section-subtitle">Alterne as tonalidades, contraste dos botões e identidade visual.</p>
                            </div>
                        </div>

                        <div class="color-palettes-grid">
                            <?php
                            $palettesList = [
                                ['id' => 'palette-aurora', 'name' => 'Rubi & Rose (Padrão)', 'colors' => ['#9d174d', '#be185d'], 'desc' => 'Elegância & Perfumaria'],
                                ['id' => 'palette-emerald', 'name' => 'Emerald Tech', 'colors' => ['#10b981', '#059669'], 'desc' => 'Verde WhatsApp VIP'],
                                ['id' => 'palette-amber', 'name' => 'Amber Gold', 'colors' => ['#f59e0b', '#d97706'], 'desc' => 'Ouro & Luxo'],
                                ['id' => 'palette-ocean', 'name' => 'Ocean Royal', 'colors' => ['#2563eb', '#1d4ed8'], 'desc' => 'Azul Confiança'],
                                ['id' => 'palette-crimson', 'name' => 'Crimson Urgência', 'colors' => ['#dc2626', '#b91c1c'], 'desc' => 'Vermelho Alta Pressão'],
                                ['id' => 'palette-obsidian', 'name' => 'Obsidian Dark', 'colors' => ['#0f172a', '#334155'], 'desc' => 'Minimalista Preto'],
                            ];
                            $currentPalette = old('color_palette', $product->color_palette ?? 'palette-aurora');
                            if ($currentPalette === 'brasa') $currentPalette = 'palette-aurora';
                            ?>
                            <?php foreach ($palettesList as $p): ?>
                                <label class="color-palette-card <?= $currentPalette === $p['id'] ? 'active' : '' ?>">
                                    <input type="radio" name="color_palette" value="<?= esc($p['id']) ?>" <?= $currentPalette === $p['id'] ? 'checked' : '' ?> data-lp-palette-radio>
                                    <div class="palette-card-inner">
                                        <div class="palette-preview-dots">
                                            <span style="background: <?= esc($p['colors'][0]) ?>;"></span>
                                            <span style="background: <?= esc($p['colors'][1]) ?>;"></span>
                                        </div>
                                        <div class="palette-info">
                                            <strong><?= esc($p['name']) ?></strong>
                                            <small><?= esc($p['desc']) ?></small>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 03 - CONFIGURAÇÃO DA CTA (ÍCONE, TEXTO E 6 MODELOS DE ANIMAÇÃO) -->
                    <div class="settings-card-block" style="margin-bottom: 24px;">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;"><i class="ti ti-click"></i></div>
                            <div>
                                <h3 class="settings-section-title">03 - Configuração da CTA (Botão de Ação)</h3>
                                <p class="settings-section-subtitle">Defina o ícone, texto do botão e a animação de alta conversão.</p>
                            </div>
                        </div>

                        <div class="form-grid-account">
                            <!-- Texto do Botão CTA Principal -->
                            <div class="form-group">
                                <label for="button_text">Texto do Botão CTA Principal</label>
                                <input type="text" id="button_text" name="button_text" class="form-control" data-lp-input="button-text" value="<?= esc(old('button_text', $product->button_text ?? 'Quero aproveitar agora')) ?>" placeholder="Ex: Quero aproveitar agora">
                            </div>

                            <!-- Ícone do Botão CTA -->
                            <div class="form-group">
                                <label for="cta_icon">Ícone do Botão CTA</label>
                                <?php
                                $currentCtaIcon = old('cta_icon', $product->cta_icon ?? 'ti-arrow-narrow-right');
                                $ctaIcons = [
                                    ['id' => 'ti-arrow-narrow-right', 'name' => 'Seta para Direita (Clássica)', 'icon' => 'ti-arrow-narrow-right'],
                                    ['id' => 'ti-brand-whatsapp', 'name' => 'WhatsApp Oficial', 'icon' => 'ti-brand-whatsapp'],
                                    ['id' => 'ti-shopping-bag', 'name' => 'Sacola de Compras', 'icon' => 'ti-shopping-bag'],
                                    ['id' => 'ti-sparkles', 'name' => 'Brilho / Especial', 'icon' => 'ti-sparkles'],
                                    ['id' => 'ti-check', 'name' => 'Check / Concluído', 'icon' => 'ti-check'],
                                    ['id' => 'ti-bolt', 'name' => 'Raio / Imediato', 'icon' => 'ti-bolt'],
                                ];
                                ?>
                                <select id="cta_icon" name="cta_icon" class="form-select" data-lp-input="cta-icon">
                                    <?php foreach ($ctaIcons as $ci): ?>
                                        <option value="<?= esc($ci['id']) ?>" <?= $currentCtaIcon === $ci['id'] ? 'selected' : '' ?>>
                                            <?= esc($ci['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Texto de Segurança / Microcopy -->
                            <div class="form-group col-full">
                                <label for="urgency_text">Microcopy de Apoio abaixo do Botão</label>
                                <input type="text" id="urgency_text" name="urgency_text" class="form-control" data-lp-input="urgency-text" value="<?= esc(old('urgency_text', $product->urgency_text ?? 'Compra sem cadastro. Você fala direto com a loja.')) ?>" placeholder="Ex: Compra sem cadastro. Você fala direto com a loja.">
                            </div>

                            <!-- 6 Modelos de Animação do Botão CTA -->
                            <div class="form-group col-full" style="margin-top: 6px;">
                                <label style="margin-bottom: 8px; display: block; font-weight: 700; color: rgb(var(--foreground));">Animação do Botão CTA (6 Opções de Impacto)</label>
                                <div class="template-models-grid">
                                    <?php
                                    $btnAnimations = [
                                        ['id' => 'btn-pulse', 'name' => '1. Pulso / Batimento VIP', 'desc' => 'Escala suave periódica atraindo atenção sutil.', 'icon' => 'ti-heartbeat'],
                                        ['id' => 'btn-shimmer', 'name' => '2. Brilho Shimmer', 'desc' => 'Feixe de luz varrendo o botão ciclicamente.', 'icon' => 'ti-sun'],
                                        ['id' => 'btn-shake', 'name' => '3. Vibração Shake', 'desc' => 'Tremidinha sutil de alta conversão a cada 4s.', 'icon' => 'ti-arrows-shuffle'],
                                        ['id' => 'btn-bounce', 'name' => '4. Salto Bounce', 'desc' => 'Leve salto vertical enfatizando a chamada.', 'icon' => 'ti-arrow-up-circle'],
                                        ['id' => 'btn-glow', 'name' => '5. Glow Neon Fluido', 'desc' => 'Aura de sombra colorida pulsando ao redor.', 'icon' => 'ti-ripple'],
                                        ['id' => 'btn-static', 'name' => '6. Estático Sofisticado', 'desc' => 'Sem animação contínua, elegante e sóbrio.', 'icon' => 'ti-player-pause'],
                                    ];
                                    $currentBtnAnim = old('btn_animation', $product->btn_animation ?? 'btn-pulse');
                                    ?>
                                    <?php foreach ($btnAnimations as $ba): ?>
                                        <label class="template-model-card <?= $currentBtnAnim === $ba['id'] ? 'active' : '' ?>">
                                            <input type="radio" name="btn_animation" value="<?= esc($ba['id']) ?>" <?= $currentBtnAnim === $ba['id'] ? 'checked' : '' ?> data-lp-btnanim-radio>
                                            <div class="template-card-inner">
                                                <div class="template-card-icon"><i class="ti <?= esc($ba['icon']) ?>"></i></div>
                                                <div class="template-card-info">
                                                    <strong><?= esc($ba['name']) ?></strong>
                                                    <p><?= esc($ba['desc']) ?></p>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 04 - CABEÇALHO E OFERTA PRINCIPAL -->
                    <div class="settings-card-block" style="margin-bottom: 24px;">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;"><i class="ti ti-layout-navbar"></i></div>
                            <div>
                                <h3 class="settings-section-title">04 - Cabeçalho e Oferta Principal</h3>
                                <p class="settings-section-subtitle">Defina o destaque superior, título e subtítulo de impacto da oferta.</p>
                            </div>
                        </div>

                        <div class="form-grid-account">
                            <!-- Badge Superior -->
                            <div class="form-group col-full">
                                <label for="badge_text">Texto da Badge / Destaque Superior</label>
                                <input type="text" id="badge_text" name="badge_text" class="form-control" data-lp-input="badge" value="<?= esc(old('badge_text', $product->badge_text ?? '🔥 OFERTA EXCLUSIVA • PRONTA ENTREGA')) ?>" placeholder="Ex: 🔥 OFERTA EXCLUSIVA • PRONTA ENTREGA">
                                <small class="form-help-text">Destaque chamativo que aparece no topo da página acima do título.</small>
                            </div>

                            <!-- Headline Principal -->
                            <div class="form-group col-full">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                    <label for="headline" style="margin-bottom: 0;">Título Principal (Headline)</label>
                                    <button type="button" id="btn-sync-headline" class="button secondary small" style="padding: 2px 8px; font-size: 11px; height: 26px; min-height: 26px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" title="Copiar nome do produto para a headline">
                                        <i class="ti ti-copy"></i>
                                        <span>Copiar do Nome</span>
                                    </button>
                                </div>
                                <input type="text" id="headline" name="headline" class="form-control" data-lp-input="headline" value="<?= esc(old('headline', $product->headline ?? '')) ?>" placeholder="Deixe em branco para usar o nome do produto">
                                <small class="form-help-text">Título de impacto da oferta. Se vazio, utiliza o nome do produto.</small>
                            </div>

                            <!-- Subheadline -->
                            <div class="form-group col-full">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                    <label for="subheadline" style="margin-bottom: 0;">Subtítulo / Linha de Apoio</label>
                                    <button type="button" id="btn-sync-subheadline" class="button secondary small" style="padding: 2px 8px; font-size: 11px; height: 26px; min-height: 26px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" title="Copiar descrição do produto para o subtítulo">
                                        <i class="ti ti-copy"></i>
                                        <span>Copiar da Descrição</span>
                                    </button>
                                </div>
                                <textarea id="subheadline" name="subheadline" class="form-control" data-lp-input="subheadline" rows="4" placeholder="Deixe em branco para usar a descrição resumida"><?= esc(old('subheadline', $product->subheadline ?? '')) ?></textarea>
                                <small class="form-help-text">Aparece logo abaixo do título explicando a proposta de valor.</small>
                            </div>
                        </div>
                    </div>

                    <!-- 05 - GARANTIAS E VANTAGENS RÁPIDAS -->
                    <div class="settings-card-block" style="margin-bottom: 24px;">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;"><i class="ti ti-shield-check"></i></div>
                            <div>
                                <h3 class="settings-section-title">05 - Garantias e Vantagens Rápidas (3 Pontos)</h3>
                                <p class="settings-section-subtitle">Destaque os 3 principais pilares de confiança para o cliente.</p>
                            </div>
                        </div>

                        <div class="form-grid-account">
                            <!-- Entrega / Envio -->
                            <div class="form-group col-full">
                                <label for="shipping_info">Ponto 1 (Entrega / Envio)</label>
                                <input type="text" id="shipping_info" name="shipping_info" class="form-control" data-lp-input="shipping-info" value="<?= esc(old('shipping_info', $product->shipping_info ?? 'Entrega rápida em Barretos e região')) ?>" placeholder="Ex: Entrega rápida em Barretos e região">
                            </div>

                            <!-- Pagamento -->
                            <div class="form-group col-full">
                                <label for="payment_info">Ponto 2 (Pagamento)</label>
                                <input type="text" id="payment_info" name="payment_info" class="form-control" data-lp-input="payment-info" value="<?= esc(old('payment_info', $product->payment_info ?? 'Pagamento no PIX ou cartão')) ?>" placeholder="Ex: Pagamento no PIX ou cartão">
                            </div>

                            <!-- Garantia / Procedência -->
                            <div class="form-group col-full">
                                <label for="guarantee_info">Ponto 3 (Garantia / Procedência)</label>
                                <input type="text" id="guarantee_info" name="guarantee_info" class="form-control" data-lp-input="guarantee-info" value="<?= esc(old('guarantee_info', $product->guarantee_info ?? 'Produto conferido antes do envio')) ?>" placeholder="Ex: Produto conferido antes do envio">
                            </div>
                        </div>
                    </div>

                    <!-- 06 - BLOCO INFORMATIVO / URGÊNCIA & CONVERSÃO -->
                    <div class="settings-card-block" style="margin-bottom: 24px;">
                        <div class="settings-card-header" style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="display: flex; gap: 12px;">
                                <div class="card-header-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;"><i class="ti ti-flame"></i></div>
                                <div>
                                    <h3 class="settings-section-title">06 - Bloco de Urgência & Conversão</h3>
                                    <p class="settings-section-subtitle">Gatilhos mentais de escassez e urgência para acelerar a decisão de compra.</p>
                                </div>
                            </div>
                            <span class="badge" style="background: rgba(157, 23, 77, 0.08); color: #9d174d; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 6px;">Alta Conversão (JH7 Marketing Master)</span>
                        </div>

                        <div class="form-grid-account">
                            <!-- Selecionar Frase de Alta Conversão / Urgência -->
                            <div class="form-group col-full">
                                <label for="urgency_phrase_selector">
                                    <i class="ti ti-bolt text-warning" style="margin-right: 4px;"></i> Sugestões de Alta Conversão (Copywriting & Urgência)
                                </label>
                                <select id="urgency_phrase_selector" class="form-control">
                                    <option value="" style="color: #475569;">-- Selecione uma das 20 frases de alta conversão para preencher --</option>
                                    <optgroup label="🔥 Escassez de Lote & Estoque Limitado" style="color: #0f172a;">
                                        <option value="ÚLTIMAS UNIDADES EM ESTOQUE|Devido à alta demanda desta edição, restam pouquíssimos frascos disponíveis. Garanta o seu antes que o lote esgote definitivamente." style="color: #475569;">1. Últimas unidades em estoque (Esgotamento iminente)</option>
                                        <option value="LOTE PROMOCIONAL EXCLUSIVO|Este valor com desconto especial é válido estritamente para as últimas unidades deste lote. Envio imediato para todo o Brasil." style="color: #475569;">2. Lote promocional exclusivo (Validade limitada)</option>
                                        <option value="RESTAM APENAS 3 FRASCOS DESTE LOTE|Nosso estoque desta fragrância está quase no fim. Não perca a oportunidade de adquirir com valor de importação direta." style="color: #475569;">3. Restam pouquíssimos frascos (Escassez numérica)</option>
                                        <option value="EDIÇÃO LIMITADA COM ALTA PROCURA|Fragrância disputada com matéria-prima importada. O próximo lote pode sofrer reajuste de tabela." style="color: #475569;">4. Edição limitada e disputada (Risco de reajuste)</option>
                                    </optgroup>
                                    <optgroup label="⚡ Urgência Temporal & Oferta Relâmpago" style="color: #0f172a;">
                                        <option value="CONDIÇÃO ESPECIAL POR TEMPO LIMITADO|Aproveite o preço exclusivo de lançamento direto no WhatsApp antes do encerramento da campanha promocional." style="color: #475569;">5. Condição especial por tempo limitado</option>
                                        <option value="GARANTA O SEU PEDIDO HOJE|Pedidos confirmados hoje ganham prioridade máxima na separação e despacho com código de rastreio expresso." style="color: #475569;">6. Prioridade de despacho imediato para hoje</option>
                                        <option value="OFERTA RELÂMPAGO DIRETO DA IMPORTADORA|Economia inteligente sem abrir mão de alta fixação e projeção marcante. Clique e reserve o seu agora." style="color: #475569;">7. Oferta relâmpago direto da importadora</option>
                                        <option value="PREÇO DE TABELA ANTIGA ATÉ O FIM DO ESTOQUE|Garantimos o valor anunciado apenas enquanto durarem os frascos deste lote atual." style="color: #475569;">8. Preço de tabela antiga (Proteção contra reajuste)</option>
                                    </optgroup>
                                    <optgroup label="💎 Proposta de Valor & Custo-Benefício Inteligente" style="color: #0f172a;">
                                        <option value="POR QUE PAGAR CARO NA GRIFE?|Fixação marcante de 8h a 12h, alta projeção e mesma identidade olfativa pagando uma fração do valor." style="color: #475569;">9. Por que pagar caro na grife? (Ancoragem racional)</option>
                                        <option value="O MESMO CHEIRO DE GRIFE, NO SEU BOLSO|Experimente a sofisticação das melhores fragrâncias internacionais com o melhor custo-benefício do Brasil." style="color: #475569;">10. O mesmo cheiro de grife, no seu bolso</option>
                                        <option value="PERFUME DE PRESENÇA E ALTA FIXAÇÃO|Fragrância marcante e elogiada por onde você passar. Testado, conferido e aprovado antes do envio." style="color: #475569;">11. Perfume de presença e fixação marcante</option>
                                        <option value="EXPERIÊNCIA OLFATIVA PREMIUM|Matérias-primas nobres com alta concentração de essência pura. Você perfumado o dia inteiro." style="color: #475569;">12. Experiência olfativa premium com alta essência</option>
                                    </optgroup>
                                    <optgroup label="🛡️ Risco Zero & Garantia de Satisfação" style="color: #0f172a;">
                                        <option value="COMPRA 100% SEGURA E VERIFICADA|Atendimento personalizado e humanizado no WhatsApp. Você só fecha após tirar todas as suas dúvidas." style="color: #475569;">13. Compra 100% segura e humanizada</option>
                                        <option value="SATISFAÇÃO E PROCEDÊNCIA GARANTIDA|Produto selecionado a dedo, conferido minuciosamente e embalado com proteção reforçada." style="color: #475569;">14. Satisfação e procedência garantida</option>
                                        <option value="ATENDIMENTO VIP E RÁPIDO NO WHATSAPP|Fale direto com nossa equipe, veja fotos reais do produto e tire dúvidas antes de confirmar." style="color: #475569;">15. Atendimento VIP com fotos reais no WhatsApp</option>
                                        <option value="TRANSPARÊNCIA E CONFIANÇA TOTAL|Milhares de clientes satisfeitos em todo o Brasil. Enviamos seu código de rastreamento com seguro." style="color: #475569;">16. Transparência, seguro e código de rastreio</option>
                                    </optgroup>
                                    <optgroup label="🚀 Ação Imediata & Chamada Forte (CTA)" style="color: #0f172a;">
                                        <option value="CLIQUE E RECEBA O ATENDIMENTO EM 1 MINUTO|Não deixe para depois: clique no botão abaixo e garanta seu frasco com a melhor condição do dia." style="color: #475569;">17. Atendimento imediato em 1 minuto</option>
                                        <option value="RESERVE O SEU ANTES QUE ACABE|Clique no WhatsApp para consultar a disponibilidade do seu frasco e fechar seu pedido sem burocracia." style="color: #475569;">18. Reserve seu frasco sem burocracia</option>
                                        <option value="PEÇA AGORA E RECEBA COM RAPIDEZ|Enviamos para todo o Brasil com embalagem discreta e protegida. Aproveite a condição de hoje." style="color: #475569;">19. Peça agora e receba com rapidez</option>
                                        <option value="SUA MARCA PESSOAL COMEÇA AQUI|Um perfume marcante transforma sua presença. Clique abaixo e garanta o seu com desconto exclusivo." style="color: #475569;">20. Sua marca pessoal começa aqui (Transformação)</option>
                                    </optgroup>
                                </select>
                                <small class="form-help-text">Selecione uma copy persuasiva acima para preencher automaticamente o título e o texto com foco em conversão imediata.</small>
                            </div>

                            <!-- Título da Seção Sobre / Urgência -->
                            <div class="form-group col-full">
                                <label for="about_title">Título da Seção de Urgência</label>
                                <input type="text" id="about_title" name="about_title" class="form-control" data-lp-input="about-title" value="<?= esc(old('about_title', $product->about_title ?? 'Últimas unidades em estoque')) ?>" placeholder="Ex: Últimas unidades em estoque">
                            </div>

                            <!-- Conteúdo Detalhado / Urgência -->
                            <div class="form-group col-full">
                                <label for="about_content">Texto de Urgência & Conversão</label>
                                <textarea id="about_content" name="about_content" class="form-control" data-lp-input="about-content" rows="4" placeholder="Descreva a urgência, escassez ou benefício marcante da oferta"><?= esc(old('about_content', $product->about_content ?? '')) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 07 - BLOCO DE FECHAMENTO (RODAPÉ DA LP) -->
                    <div class="settings-card-block">
                        <div class="settings-card-header">
                            <div class="card-header-icon" style="background: rgba(100, 116, 139, 0.15); color: #64748b;"><i class="ti ti-layout-bottombar"></i></div>
                            <div>
                                <h3 class="settings-section-title">07 - Bloco de Fechamento (Rodapé da LP)</h3>
                                <p class="settings-section-subtitle">Chamada final para tirar dúvidas ou finalizar compra no WhatsApp.</p>
                            </div>
                        </div>

                        <div class="form-grid-account">
                            <!-- Título do Checkout -->
                            <div class="form-group col-full">
                                <label for="checkout_title">Título do Bloco Final</label>
                                <input type="text" id="checkout_title" name="checkout_title" class="form-control" data-lp-input="checkout-title" value="<?= esc(old('checkout_title', $product->checkout_title ?? 'Fechar pedido pelo WhatsApp')) ?>" placeholder="Ex: Fechar pedido pelo WhatsApp">
                            </div>

                            <!-- Subtítulo / Instrução Final -->
                            <div class="form-group col-full">
                                <label for="checkout_subtitle">Texto de Instrução Final</label>
                                <input type="text" id="checkout_subtitle" name="checkout_subtitle" class="form-control" data-lp-input="checkout-subtitle" value="<?= esc(old('checkout_subtitle', $product->checkout_subtitle ?? 'A conversa abre com o produto e o preço já escritos. Você só confirma.')) ?>" placeholder="Ex: A conversa abre com o produto e o preço já escritos. Você só confirma.">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mockup do Smartphone em Tempo Real Fiel -->
                <?php
                $lpPrice = isset($product->price) ? (float) $product->price : 0;
                $lpPromoPrice = !empty($product->promotional_price) ? (float) $product->promotional_price : null;
                $lpHasPromo = ($lpPromoPrice !== null && $lpPromoPrice > 0 && $lpPromoPrice < $lpPrice);
                $lpCurrentPriceFormatted = number_format($lpHasPromo ? $lpPromoPrice : $lpPrice, 2, ',', '.');
                $lpOldPriceFormatted = number_format($lpPrice, 2, ',', '.');
                $lpSavings = $lpHasPromo ? ($lpPrice - $lpPromoPrice) : 0;
                $lpSavingsFormatted = number_format($lpSavings, 2, ',', '.');
                $lpDiscount = ($lpHasPromo && $lpPrice > 0) ? round((($lpPrice - $lpPromoPrice) / $lpPrice) * 100) : 0;

                $lpBadge = old('badge_text', $product->badge_text ?? '🔥 OFERTA EXCLUSIVA • PRONTA ENTREGA');
                $lpHeadline = old('headline', !empty($product->headline) ? $product->headline : ($product->name ?? 'NOME DO PRODUTO'));
                $lpSubheadline = old('subheadline', !empty($product->subheadline) ? $product->subheadline : ($product->description ?? 'Descrição resumida do produto para o cliente.'));
                $lpBtnText = old('button_text', $product->button_text ?? 'Quero aproveitar agora');
                $lpUrgency = old('urgency_text', $product->urgency_text ?? 'Compra sem cadastro. Você fala direto com a loja.');
                $lpShipping = old('shipping_info', $product->shipping_info ?? 'Entrega rápida em Barretos e região');
                $lpPayment = old('payment_info', $product->payment_info ?? 'Pagamento no PIX ou cartão');
                $lpGuarantee = old('guarantee_info', $product->guarantee_info ?? 'Produto conferido antes do envio');
                $lpAboutTitle = old('about_title', $product->about_title ?? 'Últimas unidades em estoque');
                $lpAboutContent = old('about_content', !empty($product->about_content) ? $product->about_content : 'Devido à alta demanda desta edição, restam pouquíssimos frascos disponíveis. Garanta o seu antes que o lote esgote definitivamente.');
                $lpCheckoutTitle = old('checkout_title', $product->checkout_title ?? 'Fechar pedido pelo WhatsApp');
                $lpCheckoutSubtitle = old('checkout_subtitle', $product->checkout_subtitle ?? 'A conversa abre com o produto e o preço já escritos. Você só confirma.');

                $lpImages = $images ?? [];
                $lpTotalImages = count($lpImages);
                $lpMainImg = !empty($lpImages) ? base_url('uploads/products/' . $lpImages[0]->image_path) : '';
                ?>
                <div class="landing-preview-col" style="position: sticky; top: 12px; z-index: 10;">
                    <div class="preview-sticky-wrap">
                        <div class="preview-device-header" style="background: #0f172a; color: white; padding: 10px 16px; border-radius: 14px 14px 0 0; display: flex; align-items: center; justify-content: space-between;">
                            <div class="preview-device-title" style="display: flex; align-items: center; gap: 8px;">
                                <span class="preview-live-indicator" style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                <strong style="font-size: 12px; letter-spacing: 0.3px;">Visualização Fiel do Modelo</strong>
                            </div>
                            <?php if (!empty($product->slug)): ?>
                                <a href="<?= base_url('p/' . $product->slug) ?>" target="_blank" class="button secondary small" style="background: rgba(255, 255, 255, 0.12); color: #fff; border: 1px solid rgba(255, 255, 255, 0.25); font-size: 11px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; border-radius: 6px; font-weight: 600;" title="Abrir Landing Page em nova aba">
                                    <i class="ti ti-external-link"></i>
                                    <span>Abrir Página</span>
                                </a>
                            <?php else: ?>
                                <span style="font-size: 11px; opacity: 0.7;">Oferta Produto</span>
                            <?php endif; ?>
                        </div>

                        <div class="mobile-mockup-frame" id="lp_mockup_frame" data-palette="<?= esc($product->color_palette ?? 'palette-aurora') ?>" data-model="<?= esc($product->template_model ?? 'model-1') ?>" data-btn-animation="<?= esc($product->btn_animation ?? 'btn-pulse') ?>" style="border: 10px solid #0f172a; border-top: none; border-radius: 0 0 36px 36px; background: #f8fafc; overflow: hidden; height: calc(100vh - 220px); min-height: 440px; max-height: 560px; position: relative; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.35);">
                            
                            <!-- Conteúdo da Tela do Mockup -->
                            <div class="mobile-screen-content" id="lp_live_preview_container" data-palette="<?= esc($product->color_palette ?? 'palette-aurora') ?>" data-model="<?= esc($product->template_model ?? 'model-1') ?>" data-btn-animation="<?= esc($product->btn_animation ?? 'btn-pulse') ?>" style="height: 100%; overflow-y: auto; padding: 18px 14px 80px 14px; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; color: #1e293b; background-color: #f8fafc;">
                                <!-- Badge Superior -->
                                <div class="mockup-badge-wrap" style="display: <?= !empty($lpBadge) ? 'inline-flex' : 'none' ?>; align-items: center; gap: 6px; background: var(--lp-accent-badge, #fce7f3); color: var(--lp-accent-badge-text, #be185d); padding: 4px 12px; border-radius: 100px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;" id="mockup_badge_wrap">
                                    <span id="mockup_badge"><?= esc($lpBadge) ?></span>
                                </div>

                                <!-- Título e Subtítulo -->
                                <h1 id="mockup_title" class="mockup-title" style="font-size: 20px; font-weight: 900; line-height: 1.2; text-transform: uppercase; letter-spacing: -0.4px; color: #0f172a; margin-bottom: 8px;"><?= esc($lpHeadline) ?></h1>
                                <p id="mockup_subheadline" class="mockup-subheadline" style="font-size: 11.5px; color: #475569; line-height: 1.55; margin-bottom: 16px;"><?= esc($lpSubheadline) ?></p>

                                <!-- Galeria / Foto Principal -->
                                <div class="mockup-gallery-wrapper" style="position: relative; width: 100%; aspect-ratio: 1; border-radius: 16px; overflow: hidden; background: #ffffff; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 10px;">
                                    <div id="mockup_discount_tag" style="position: absolute; top: 12px; left: 12px; background: var(--lp-primary, #9d174d); color: #ffffff; font-size: 10px; font-weight: 900; padding: 5px 9px; border-radius: 6px; text-transform: uppercase; z-index: 2; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25); display: <?= $lpHasPromo ? 'block' : 'none' ?>;">
                                        -<?= $lpDiscount ?>% OFF
                                    </div>
                                    <img id="mockup_main_image" src="<?= esc($lpMainImg) ?>" alt="<?= esc($lpHeadline) ?>" style="width: 100%; height: 100%; object-fit: cover; display: <?= !empty($lpMainImg) ? 'block' : 'none' ?>;">
                                    <div id="mockup_image_placeholder" style="width: 100%; height: 100%; display: <?= empty($lpMainImg) ? 'flex' : 'none' ?>; flex-direction: column; align-items: center; justify-content: center; background: #f1f5f9; color: #94a3b8; gap: 6px;">
                                        <i class="ti ti-photo" style="font-size: 36px;"></i>
                                        <span style="font-size: 10px; font-weight: 600;">Sem foto cadastrada</span>
                                    </div>
                                    <div id="mockup_gallery_counter" style="position: absolute; bottom: 10px; right: 10px; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); color: #ffffff; font-size: 9.5px; font-weight: 700; padding: 3px 9px; border-radius: 100px; display: <?= $lpTotalImages > 0 ? 'block' : 'none' ?>;">
                                        <span id="mockup_current_img_index">1</span>/<span id="mockup_total_img_count"><?= $lpTotalImages ?></span>
                                    </div>
                                </div>

                                <!-- Miniaturas do Mockup -->
                                <div id="mockup_gallery_thumbs" class="mockup-gallery-thumbs" style="display: <?= $lpTotalImages > 1 ? 'flex' : 'none' ?>; gap: 6px; overflow-x: auto; padding-bottom: 4px; margin-bottom: 16px; scrollbar-width: none;">
                                    <?php if ($lpTotalImages > 1): ?>
                                        <?php foreach ($lpImages as $idx => $img): ?>
                                            <button type="button" class="mockup-thumb-btn" data-thumb-src="<?= base_url('uploads/products/' . $img->image_path) ?>" data-thumb-idx="<?= $idx + 1 ?>" style="width: 46px; height: 46px; border-radius: 10px; overflow: hidden; border: <?= $idx === 0 ? '2px solid var(--lp-primary, #9d174d)' : '1px solid #e2e8f0' ?>; padding: 0; background: #fff; cursor: pointer; flex-shrink: 0; transition: all 0.2s ease; opacity: <?= $idx === 0 ? '1' : '0.65' ?>;">
                                                <img src="<?= base_url('uploads/products/' . $img->image_path) ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="Thumb">
                                            </button>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Card de Preço e CTA -->
                                <div class="mockup-price-card" style="background: #ffffff; border-radius: 16px; padding: 16px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 14px; border: 1px solid #f1f5f9;">
                                    <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px;">
                                        <span id="mockup_old_price" style="font-size: 12px; color: #94a3b8; text-decoration: line-through; font-weight: 600; display: <?= $lpHasPromo ? 'inline' : 'none' ?>;">R$ <?= $lpOldPriceFormatted ?></span>
                                        <span id="mockup_current_price" style="font-size: 24px; font-weight: 900; color: var(--lp-primary, #9d174d); letter-spacing: -0.5px;">R$ <?= $lpCurrentPriceFormatted ?></span>
                                        <span id="mockup_savings_tag" style="background: var(--lp-accent-badge, #fdf2f8); color: var(--lp-accent-badge-text, #9d174d); font-size: 10px; font-weight: 800; padding: 3px 7px; border-radius: 6px; margin-left: auto; display: <?= $lpHasPromo ? 'inline-block' : 'none' ?>;">economize R$ <?= $lpSavingsFormatted ?></span>
                                    </div>

                                    <p id="mockup_savings_text" style="font-size: 10.5px; color: #475569; margin-bottom: 14px; display: <?= $lpHasPromo ? 'flex' : 'none' ?>; align-items: center; gap: 5px;">
                                        <span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: var(--lp-primary, #9d174d);"></span>
                                        Você economiza R$ <?= $lpSavingsFormatted ?> neste preço
                                    </p>

                                    <div class="mockup-main-cta-btn" style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: var(--lp-primary, #9d174d); color: #ffffff; font-size: 13px; font-weight: 800; padding: 13px 16px; border-radius: 12px; text-decoration: none; box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);">
                                        <span id="mockup_button_text"><?= esc($lpBtnText) ?></span>
                                        <i class="ti ti-arrow-narrow-right" style="font-size: 16px;"></i>
                                    </div>

                                    <div style="font-size: 10px; color: #64748b; text-align: center; margin-top: 9px; display: flex; align-items: center; justify-content: center; gap: 4px;">
                                        <i class="ti ti-lock" style="font-size: 12px;"></i>
                                        <span id="mockup_urgency_text"><?= esc($lpUrgency) ?></span>
                                    </div>
                                </div>

                                <!-- 3 Pilares -->
                                <div class="mockup-features-wrapper" style="background: #ffffff; border-radius: 16px; padding: 12px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 14px; border: 1px solid #f1f5f9;">
                                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; text-align: center;">
                                        <div>
                                            <i class="ti ti-message-circle" style="color: var(--lp-primary, #9d174d); font-size: 14px; margin-bottom: 2px; display: inline-block;"></i>
                                            <strong style="display: block; font-size: 9.5px; font-weight: 800; color: #1e293b; margin-bottom: 1px;">Atendimento humano</strong>
                                            <small style="font-size: 8.5px; color: #64748b; line-height: 1.2; display: block;">Você fala com a loja</small>
                                        </div>
                                        <div>
                                            <i class="ti ti-notes" style="color: var(--lp-primary, #9d174d); font-size: 14px; margin-bottom: 2px; display: inline-block;"></i>
                                            <strong style="display: block; font-size: 9.5px; font-weight: 800; color: #1e293b; margin-bottom: 1px;">Sem cadastro</strong>
                                            <small style="font-size: 8.5px; color: #64748b; line-height: 1.2; display: block;">Nenhum formulário</small>
                                        </div>
                                        <div>
                                            <i class="ti ti-currency-dollar" style="color: var(--lp-primary, #9d174d); font-size: 14px; margin-bottom: 2px; display: inline-block;"></i>
                                            <strong style="display: block; font-size: 9.5px; font-weight: 800; color: #1e293b; margin-bottom: 1px;">Preço fechado</strong>
                                            <small style="font-size: 8.5px; color: #64748b; line-height: 1.2; display: block;">Confirmado antes</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3 Garantias -->
                                <div class="mockup-guarantees-card" style="background: #ffffff; border-radius: 16px; padding: 14px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 18px; border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 9px;">
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; color: #334155;">
                                        <i class="ti ti-check" style="color: var(--lp-primary, #9d174d); font-size: 15px; flex-shrink: 0;"></i>
                                        <span id="mockup_shipping_info"><?= esc($lpShipping) ?></span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; color: #334155;">
                                        <i class="ti ti-check" style="color: var(--lp-primary, #9d174d); font-size: 15px; flex-shrink: 0;"></i>
                                        <span id="mockup_payment_info"><?= esc($lpPayment) ?></span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; color: #334155;">
                                        <i class="ti ti-check" style="color: var(--lp-primary, #9d174d); font-size: 15px; flex-shrink: 0;"></i>
                                        <span id="mockup_guarantee_info"><?= esc($lpGuarantee) ?></span>
                                    </div>
                                    <div class="mockup-guarantee-cta-btn mockup-main-cta-btn" style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: var(--lp-primary, #9d174d); color: #ffffff; font-size: 12px; font-weight: 800; padding: 11px 14px; border-radius: 11px; text-decoration: none; margin-top: 4px; box-shadow: 0 6px 16px -3px rgba(0, 0, 0, 0.22);">
                                        <span id="mockup_button_text_3"><?= esc($lpBtnText) ?></span>
                                        <i class="ti <?= esc(!empty($product->cta_icon) ? $product->cta_icon : 'ti-arrow-narrow-right') ?>" style="font-size: 15px;"></i>
                                    </div>
                                </div>

                                <!-- Bloco de Urgência / Escassez e Ação com Botão CTA -->
                                <div class="mockup-urgency-card" style="background: #ffffff; border-radius: 16px; padding: 14px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 20px; border: 1px solid #f1f5f9;">
                                    <h2 id="mockup_about_title" style="font-size: 13.5px; font-weight: 900; color: #0f172a; margin-bottom: 8px;"><?= esc($lpAboutTitle) ?></h2>
                                    <div id="mockup_about_content" style="font-size: 11px; color: #475569; line-height: 1.6; margin-bottom: 0; white-space: pre-line;">
                                        <?= nl2br(esc($lpAboutContent)) ?>
                                    </div>
                                </div>

                                <!-- FAQ (Antes de fechar) Accordion Interativo -->
                                <h2 class="mockup-faq-heading" style="font-size: 14px; font-weight: 900; color: #0f172a; margin-bottom: 10px;">Antes de fechar</h2>
                                <div class="mockup-faq-accordion" style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
                                    <div class="mockup-faq-card" style="background: #ffffff; border-radius: 12px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.04);">
                                        <button type="button" class="mockup-faq-btn" style="width: 100%; background: none; border: none; padding: 12px 14px; font-size: 11px; font-weight: 800; color: #0f172a; text-align: left; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                                            <span>Como eu compro?</span>
                                            <i class="ti ti-plus" style="color: var(--lp-primary, #9d174d); font-size: 13px; transition: transform 0.2s ease;"></i>
                                        </button>
                                        <div class="mockup-faq-ans" style="padding: 0 14px 12px 14px; font-size: 10.5px; color: #64748b; line-height: 1.5; display: none;">
                                            É só clicar no botão. Você vai direto para o WhatsApp com o produto selecionado. Nós confirmamos seu endereço, calculamos o envio e combinamos o pagamento na hora.
                                        </div>
                                    </div>
                                    <div class="mockup-faq-card" style="background: #ffffff; border-radius: 12px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.04);">
                                        <button type="button" class="mockup-faq-btn" style="width: 100%; background: none; border: none; padding: 12px 14px; font-size: 11px; font-weight: 800; color: #0f172a; text-align: left; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                                            <span>O preço desta página é o que eu pago?</span>
                                            <i class="ti ti-plus" style="color: var(--lp-primary, #9d174d); font-size: 13px; transition: transform 0.2s ease;"></i>
                                        </button>
                                        <div class="mockup-faq-ans" style="padding: 0 14px 12px 14px; font-size: 10.5px; color: #64748b; line-height: 1.5; display: none;">
                                            Sim. O valor mostrado nesta página é garantido no atendimento pelo WhatsApp. Sem surpresas ou taxas escondidas.
                                        </div>
                                    </div>
                                    <div class="mockup-faq-card" style="background: #ffffff; border-radius: 12px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.04);">
                                        <button type="button" class="mockup-faq-btn" style="width: 100%; background: none; border: none; padding: 12px 14px; font-size: 11px; font-weight: 800; color: #0f172a; text-align: left; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                                            <span>Com quem eu estou falando?</span>
                                            <i class="ti ti-plus" style="color: var(--lp-primary, #9d174d); font-size: 13px; transition: transform 0.2s ease;"></i>
                                        </button>
                                        <div class="mockup-faq-ans" style="padding: 0 14px 12px 14px; font-size: 10.5px; color: #64748b; line-height: 1.5; display: none;">
                                            Você fala diretamente com a equipe da Dias Imports. Atendimento humano, rápido e transparente.
                                        </div>
                                    </div>
                                    <div class="mockup-faq-card" style="background: #ffffff; border-radius: 12px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.04);">
                                        <button type="button" class="mockup-faq-btn" style="width: 100%; background: none; border: none; padding: 12px 14px; font-size: 11px; font-weight: 800; color: #0f172a; text-align: left; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                                            <span>E se eu ficar com dúvida antes de decidir?</span>
                                            <i class="ti ti-plus" style="color: var(--lp-primary, #9d174d); font-size: 13px; transition: transform 0.2s ease;"></i>
                                        </button>
                                        <div class="mockup-faq-ans" style="padding: 0 14px 12px 14px; font-size: 10.5px; color: #64748b; line-height: 1.5; display: none;">
                                            Chame pelo WhatsApp mesmo assim. Tiramos fotos reais, respondemos sobre tamanho, aplicação e prazo antes de você fechar.
                                        </div>
                                    </div>
                                </div>

                                <!-- Card de Fechamento -->
                                <div class="mockup-closing-card" style="background: #ffffff; border-radius: 16px; padding: 20px 14px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); text-align: center; border: 1px solid #f1f5f9; margin-bottom: 24px;">
                                    <h2 id="mockup_checkout_title" style="font-size: 14px; font-weight: 900; color: #0f172a; margin-bottom: 12px;"><?= esc($lpCheckoutTitle) ?></h2>
                                    
                                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                                        <span id="mockup_closing_old_price" style="font-size: 12px; color: #94a3b8; text-decoration: line-through; font-weight: 600; display: <?= $lpHasPromo ? 'inline' : 'none' ?>;">R$ <?= $lpOldPriceFormatted ?></span>
                                        <span id="mockup_closing_current_price" style="font-size: 24px; font-weight: 900; color: var(--lp-primary, #9d174d); letter-spacing: -0.5px;">R$ <?= $lpCurrentPriceFormatted ?></span>
                                        <span id="mockup_closing_savings_tag" style="background: var(--lp-accent-badge, #fdf2f8); color: var(--lp-accent-badge-text, #9d174d); font-size: 10px; font-weight: 800; padding: 3px 7px; border-radius: 6px; display: <?= $lpHasPromo ? 'inline-block' : 'none' ?>;">economize R$ <?= $lpSavingsFormatted ?></span>
                                    </div>

                                    <div class="mockup-closing-cta-btn" style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: var(--lp-primary, #9d174d); color: #ffffff; font-size: 13px; font-weight: 800; padding: 13px 16px; border-radius: 12px; box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);">
                                        <span id="mockup_button_text_2"><?= esc($lpBtnText) ?></span>
                                        <i class="ti ti-arrow-narrow-right" style="font-size: 16px;"></i>
                                    </div>
                                    <p id="mockup_checkout_subtitle" style="font-size: 10px; color: #64748b; margin-top: 10px; line-height: 1.45;"><?= esc($lpCheckoutSubtitle) ?></p>
                                </div>

                                <!-- Rodapé Mockup -->
                                <div class="mockup-footer-wrap" style="text-align: center; padding: 16px 0 10px 0; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 9.5px; line-height: 1.6;">
                                    <strong style="color: #64748b; font-weight: 800; display: block; font-size: 10.5px; margin-bottom: 2px;">Dias Imports</strong>
                                    <span>Barretos - SP</span><br>
                                    <span>Atendimento e pedidos pelo WhatsApp. Imagens meramente ilustrativas. Preço promocional válido para a compra feita por esta página.</span>
                                </div>
                            </div>

                            <!-- Barra Fixa Inferior Fake no Preview -->
                            <div class="mockup-sticky-bar" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.96); backdrop-filter: blur(12px); border-top: 1px solid #e2e8f0; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; gap: 10px; z-index: 10; box-shadow: 0 -4px 16px rgba(0,0,0,0.06);">
                                <div style="display: flex; flex-direction: column; min-width: 65px;">
                                    <small id="mockup_sticky_old_price" style="font-size: 9px; color: #94a3b8; text-decoration: line-through; line-height: 1; font-weight: 600; display: <?= $lpHasPromo ? 'block' : 'none' ?>;">R$ <?= $lpOldPriceFormatted ?></small>
                                    <strong id="mockup_sticky_price" style="font-size: 16px; font-weight: 900; color: var(--lp-primary, #9d174d); display: block; line-height: 1.1; letter-spacing: -0.3px;">R$ <?= $lpCurrentPriceFormatted ?></strong>
                                </div>
                                <div class="mockup-sticky-cta-btn" style="background: var(--lp-primary, #9d174d); color: #ffffff; font-size: 11px; font-weight: 800; padding: 9px 14px; border-radius: 9px; display: flex; align-items: center; justify-content: center; gap: 4px; flex: 1; box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.25); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <span id="mockup_sticky_btn_text" style="overflow: hidden; text-overflow: ellipsis;"><?= esc($lpBtnText) ?></span>
                                    <i class="ti ti-arrow-narrow-right" style="flex-shrink: 0; font-size: 14px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Estatísticas -->
        <?php if ($isEdit && isset($stats)): ?>
        <div class="settings-tab-panel" id="tab-stats" hidden>
            <section class="form-card-section" aria-labelledby="stats-title" style="margin-bottom: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h2 id="stats-title" class="section-card-title" style="margin-bottom: 4px;">Estatísticas de Desempenho</h2>
                        <p class="section-card-subtitle" style="margin: 0; font-size: 13px; color: rgb(var(--muted));">Métricas de acessos, cliques no WhatsApp e conversão da Landing Page deste produto.</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" id="btn_refresh_stats" class="button secondary small" style="height: 34px; padding: 0 12px; display: inline-flex; align-items: center; gap: 6px; font-size: 12px; cursor: pointer;">
                            <i class="ti ti-refresh" id="refresh_stats_icon"></i> <span>Atualizar Dados</span>
                        </button>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="ti ti-activity" style="font-size: 14px;"></i> Monitoramento Ativo
                        </span>
                        <a href="<?= !empty($product->slug) ? site_url('p/' . $product->slug) : '#' ?>" target="_blank" class="button secondary small" style="height: 34px; padding: 0 12px; display: inline-flex; align-items: center; gap: 6px; font-size: 12px;" <?= empty($product->slug) ? 'onclick="return false;" style="opacity: 0.5; pointer-events: none;"' : '' ?>>
                            <i class="ti ti-external-link"></i> Abrir Landing Page
                        </a>
                    </div>
                </div>

                <!-- Grid de KPIs Principais -->
                <div class="leads-dash-grid" style="margin-bottom: 24px;">
                    <!-- Card 1: Total de Visualizações -->
                    <div class="lead-kpi-card total-leads">
                        <div class="kpi-header-row">
                            <span class="kpi-label">Acessos Totais</span>
                            <div class="kpi-icon-pill" aria-hidden="true">
                                <i class="ti ti-eye"></i>
                            </div>
                        </div>
                        <div>
                            <div class="kpi-big-number" id="kpi_total_pageviews"><?= number_format($stats['totalPageviews'], 0, ',', '.') ?></div>
                            <div class="kpi-subtext" style="margin-top: 6px;">Visitas na página do produto</div>
                        </div>
                        <div class="kpi-subtext" id="kpi_trend_pageviews">
                            <?php
                            $diffPv = $stats['todayPageviews'] - $stats['yesterdayPageviews'];
                            if ($diffPv > 0): ?>
                                <span class="kpi-trend-pill positive" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #10b981;">
                                    <i class="ti ti-trending-up"></i> +<?= $diffPv ?> hoje
                                </span>
                            <?php elseif ($diffPv < 0): ?>
                                <span class="kpi-trend-pill negative" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #ef4444;">
                                    <i class="ti ti-trending-down"></i> <?= $diffPv ?> hoje
                                </span>
                            <?php else: ?>
                                <span class="kpi-trend-pill neutral" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #64748b;">
                                    <i class="ti ti-minus"></i> Igual a ontem
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card 2: Visitantes Únicos -->
                    <div class="lead-kpi-card daily-comparison">
                        <div class="kpi-header-row">
                            <span class="kpi-label">Visitantes Únicos</span>
                            <div class="kpi-icon-pill success" aria-hidden="true">
                                <i class="ti ti-users"></i>
                            </div>
                        </div>
                        <div>
                            <div class="kpi-big-number"><?= number_format($stats['uniqueVisitors'], 0, ',', '.') ?></div>
                            <div class="kpi-subtext" style="margin-top: 6px;">Pessoas distintas alcançadas</div>
                        </div>
                        <div class="kpi-subtext">
                            <i class="ti ti-device-mobile" style="color: #635bff;"></i> <?= $stats['mobilePct'] ?>% tráfego mobile
                        </div>
                    </div>

                    <!-- Card 3: Cliques no WhatsApp / Conversão -->
                    <div class="lead-kpi-card" style="grid-column: span 3; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #10b981, #3b82f6);"></div>
                        <div class="kpi-header-row">
                            <span class="kpi-label">Cliques no WhatsApp</span>
                            <div class="kpi-icon-pill" style="color: #10b981; background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2);" aria-hidden="true">
                                <i class="ti ti-brand-whatsapp"></i>
                            </div>
                        </div>
                        <div>
                            <div class="kpi-big-number" id="kpi_total_cta_clicks" style="color: #10b981;"><?= number_format($stats['totalCtaClicks'], 0, ',', '.') ?></div>
                            <div class="kpi-subtext" style="margin-top: 6px;">Cliques no botão de compra</div>
                        </div>
                        <div class="kpi-subtext">
                            <span class="kpi-trend-pill positive" id="kpi_conversion_rate" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #10b981;">
                                <i class="ti ti-bolt"></i> Taxa de conversão: <?= $stats['conversionRate'] ?>%
                            </span>
                        </div>
                    </div>

                    <!-- Card 4: Eventos Rastreados -->
                    <div class="lead-kpi-card" style="grid-column: span 3; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #f59e0b, #ec4899);"></div>
                        <div class="kpi-header-row">
                            <span class="kpi-label">Meta Ads & Conversões</span>
                            <div class="kpi-icon-pill" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);" aria-hidden="true">
                                <i class="ti ti-activity"></i>
                            </div>
                        </div>
                        <div>
                            <div class="kpi-big-number" id="kpi_meta_ads_status"><?= !empty($product->meta_ads_active) ? 'Ativo' : 'Inativo' ?></div>
                            <div class="kpi-subtext" style="margin-top: 6px;">Pixel & API de Conversões</div>
                        </div>
                        <div class="kpi-subtext">
                            <i class="ti ti-shield-check" style="color: #10b981;"></i> ViewContent & Purchase
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Evolução e Dispositivos -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <!-- Gráfico Misto: Visitas (Linha) x Cliques (Barra) -->
                    <div class="lead-kpi-card" style="padding: 20px; overflow: visible; position: relative;">
                        <div class="kpi-header-row" style="margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <span class="kpi-label">Evolução de Acessos & Cliques</span>
                                <div class="kpi-subtext" id="stats_period_subtitle" style="margin-top: 2px;">Desempenho diário nos últimos <?= $stats['period'] ?> dias</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <!-- Legenda -->
                                <div style="display: flex; align-items: center; gap: 12px; font-size: 11px;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: #a855f7; font-weight: 600;">
                                        <span style="width: 14px; height: 3px; background: #a855f7; border-radius: 2px; display: inline-block;"></span> Visitas
                                    </span>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-weight: 600;">
                                        <span style="width: 10px; height: 10px; background: #10b981; border-radius: 3px; display: inline-block;"></span> Cliques
                                    </span>
                                </div>
                                <div class="evolution-filter-pills" role="group">
                                    <?php foreach ([7, 14, 21, 30] as $p): ?>
                                        <button type="button" class="period-pill-btn js-stats-period-btn <?= ($stats['period'] == $p) ? 'active' : '' ?>" data-period="<?= $p ?>" style="padding: 3px 8px; font-size: 11px; border-radius: 6px; cursor: pointer; border: 1px solid <?= ($stats['period'] == $p) ? '#635bff' : 'rgb(var(--border))' ?>; background: <?= ($stats['period'] == $p) ? 'rgba(99, 91, 255, 0.15)' : 'rgb(var(--surface))' ?>; color: <?= ($stats['period'] == $p) ? '#635bff' : 'rgb(var(--muted))' ?>; font-weight: <?= ($stats['period'] == $p) ? '700' : 'normal' ?>;"><?= $p ?>d</button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div style="position: relative; width: 100%; min-height: 180px;">
                            <div id="stats_mixed_chart_container" style="width: 100%; position: relative;">
                                <!-- Renderizado via renderMixedChart() -->
                            </div>
                        </div>
                    </div>

                    <!-- Origem do Tráfego Real -->
                    <div class="lead-kpi-card" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="kpi-header-row" style="margin-bottom: 14px;">
                                <span class="kpi-label">Origem do Tráfego</span>
                                <div class="kpi-icon-pill" aria-hidden="true"><i class="ti ti-compass"></i></div>
                            </div>
                            
                            <div id="stats_sources_container" style="display: flex; flex-direction: column; gap: 12px;">
                                <?php if (!empty($stats['sources'])): ?>
                                    <?php
                                    $sourceColors = [
                                        'Instagram' => '#ec4899',
                                        'Facebook'  => '#2563eb',
                                        'WhatsApp'  => '#10b981',
                                        'Google'    => '#ea4335',
                                        'Direto'    => '#f59e0b',
                                        'Referral'  => '#8b5cf6',
                                    ];
                                    ?>
                                    <?php foreach ($stats['sources'] as $src): ?>
                                        <?php $color = $sourceColors[$src['name']] ?? '#64748b'; ?>
                                        <div>
                                            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 4px;">
                                                <span style="display: flex; align-items: center; gap: 6px; color: rgb(var(--foreground));">
                                                    <i class="ti ti-point-filled" style="color: <?= $color ?>;"></i> <?= esc($src['name']) ?>
                                                </span>
                                                <span style="color: rgb(var(--muted));"><?= $src['percentage'] ?>% (<?= $src['total'] ?>)</span>
                                            </div>
                                            <div style="height: 6px; border-radius: 3px; background: rgba(255,255,255,0.06); overflow: hidden;">
                                                <div style="width: <?= $src['percentage'] ?>%; height: 100%; background: <?= $color ?>; border-radius: 3px;"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="color: rgb(var(--muted)); font-size: 12px; text-align: center; padding: 20px 0;">Aguardando primeiros acessos para mapear origens.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="kpi-subtext" id="kpi_mobile_pct_2" style="margin-top: 14px; padding-top: 10px; border-top: 1px solid rgb(var(--border)); font-size: 11px;">
                            <i class="ti ti-device-mobile" style="color: #635bff;"></i> <?= $stats['mobilePct'] ?>% tráfego mobile
                        </div>
                    </div>
                </div>

                <!-- Tabela de Últimas Interações Reais -->
                <div style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm);">
                    <div style="padding: 16px 20px; border-bottom: 1px solid rgb(var(--border)); display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 14px; font-weight: 750; color: rgb(var(--foreground)); margin: 0 0 2px 0;">Últimos Eventos & Interações</h3>
                            <p style="font-size: 12px; color: rgb(var(--muted)); margin: 0;">Logs reais gravados na Landing Page deste produto.</p>
                        </div>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600;">Banco de Dados</span>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                            <thead>
                                <tr style="background: rgb(var(--surface-secondary)); color: rgb(var(--muted)); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid rgb(var(--border));">
                                    <th style="padding: 10px 16px;">Evento</th>
                                    <th style="padding: 10px 16px;">Origem / Canal</th>
                                    <th style="padding: 10px 16px;">Dispositivo</th>
                                    <th style="padding: 10px 16px;">IP / Visitante</th>
                                    <th style="padding: 10px 16px; text-align: right;">Data / Hora</th>
                                </tr>
                            </thead>
                            <tbody id="stats_recent_logs_tbody">
                                <?php if (!empty($stats['recentLogs'])): ?>
                                    <?php foreach ($stats['recentLogs'] as $log): ?>
                                        <?php
                                        $isClick = in_array($log['event_type'], ['cta_click', 'sticky_cta_click', 'whatsapp_click'], true);
                                        $eventName = $isClick ? 'Clique de Compra (WhatsApp)' : 'Visualização de Página (PageView)';
                                        $eventColor = $isClick ? '#10b981' : '#635bff';
                                        $eventIcon = $isClick ? 'ti-brand-whatsapp' : 'ti-eye';
                                        ?>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                            <td style="padding: 12px 16px; font-weight: 600; color: <?= $eventColor ?>; display: flex; align-items: center; gap: 8px;">
                                                <i class="ti <?= $eventIcon ?>" style="font-size: 16px;"></i> <?= $eventName ?>
                                            </td>
                                            <td style="padding: 12px 16px; color: rgb(var(--foreground));">
                                                <?= esc(!empty($log['utm_source']) ? $log['utm_source'] : 'Direto') ?>
                                                <?php if (!empty($log['utm_campaign'])): ?>
                                                    <small style="color: rgb(var(--muted)); font-size: 11px;">(<?= esc($log['utm_campaign']) ?>)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 12px 16px; color: rgb(var(--muted));">
                                                <i class="ti ti-device-<?= $log['device_type'] === 'mobile' ? 'mobile' : 'laptop' ?>"></i>
                                                <?= ucfirst(esc($log['device_type'])) ?>
                                            </td>
                                            <td style="padding: 12px 16px; color: rgb(var(--muted)); font-family: monospace; font-size: 11px;">
                                                <?= esc($log['ip_address'] ?: substr($log['visitor_id'], 0, 12) . '...') ?>
                                            </td>
                                            <td style="padding: 12px 16px; text-align: right; color: rgb(var(--muted)); font-size: 12px;">
                                                <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="padding: 30px 16px; text-align: center; color: rgb(var(--muted)); font-size: 13px;">
                                            Nenhum evento registrado ainda. Quando os clientes acessarem a Landing Page do produto, os dados aparecerão aqui em tempo real.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <?php endif; ?>

        <!-- Floating save bar -->
        <div class="save-bar" data-form-save-bar hidden>
            <p>
                <strong>Existem alterações não salvas</strong>
                <span>Clique em salvar para aplicar as modificações no produto.</span>
            </p>
            <div class="save-actions">
                <button type="button" class="button secondary" data-cancel-form>Cancelar</button>
                <button type="submit" class="button primary">
                    <i class="ti ti-device-floppy"></i>
                    <span>Salvar Produto</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de Recorte de Imagem (Cropper) -->
<div class="template-dialog" id="cropper_modal" hidden aria-hidden="true">
    <section class="template-dialog-card cropper-dialog-card" role="dialog" aria-modal="true">
        <button class="template-dialog-close" type="button" id="btn_close_cropper" aria-label="Fechar modal"><i class="ti ti-x" aria-hidden="true"></i></button>
        
        <div class="template-dialog-header">
            <div class="card-header-icon" style="background: rgba(124, 105, 255, 0.15); color: #8b7cff;"><i class="ti ti-crop"></i></div>
            <div>
                <h2 class="template-dialog-title">Recortar e Enquadrar Imagem</h2>
                <p class="template-dialog-desc">Arraste para mover, use o scroll ou botões para zoom e enquadre a imagem no formato quadrado (1:1).</p>
            </div>
        </div>

        <!-- Área de Visualização do Cropper -->
        <div class="cropper-viewport-wrap">
            <img id="cropper_image" src="" alt="Imagem para recorte">
        </div>

        <!-- Barra de Ferramentas / Controles de Zoom, Rotação e Reset -->
        <div class="cropper-toolbar">
            <div class="cropper-toolbar-group">
                <button type="button" class="cropper-tool-btn" id="btn_crop_zoom_in" title="Aumentar Zoom">
                    <i class="ti ti-zoom-in"></i>
                </button>
                <button type="button" class="cropper-tool-btn" id="btn_crop_zoom_out" title="Diminuir Zoom">
                    <i class="ti ti-zoom-out"></i>
                </button>
                <button type="button" class="cropper-tool-btn" id="btn_crop_rotate_left" title="Girar 90° para a esquerda">
                    <i class="ti ti-rotate-2"></i>
                </button>
                <button type="button" class="cropper-tool-btn" id="btn_crop_rotate_right" title="Girar 90° para a direita">
                    <i class="ti ti-rotate-clockwise-2"></i>
                </button>
                <button type="button" class="cropper-tool-btn" id="btn_crop_reset" title="Restaurar Posição Original">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>

            <div class="cropper-info-badge">
                <i class="ti ti-aspect-ratio"></i>
                <span>Proporção 1:1</span>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="cropper-dialog-actions">
            <button type="button" class="button secondary" id="btn_cancel_cropper">Cancelar</button>
            <button type="button" class="button primary" id="btn_apply_cropper"><i class="ti ti-check"></i> Concluir Recorte</button>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('[data-dirty-form]');
    const saveBar = document.querySelector('[data-form-save-bar]');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    // Tab Navigation Logic
    const tabBtns = document.querySelectorAll('.settings-tab');
    const tabContents = document.querySelectorAll('.settings-tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.dataset.tabTarget;
            document.getElementById('active_tab').value = target;
            
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => {
                c.hidden = true;
            });
            this.classList.add('active');
            const targetEl = document.getElementById(target);
            if (targetEl) {
                targetEl.hidden = false;
            }

            if (target === 'tab-landing') {
                updateLpPreview();
            }
        });
    });

    // Restore active tab from URL or hidden input
    const initialTab = document.getElementById('active_tab').value;
    if (initialTab) {
        const tabBtn = document.querySelector(`[data-tab-target="${initialTab}"]`);
        if (tabBtn) {
            tabBtn.click();
        }
    }

    // Landing Page Live Preview Logic
    const mockupFrame = document.getElementById('lp_mockup_frame');
    const mockupScreen = document.getElementById('lp_live_preview_container');
    const landingInputs = document.querySelectorAll('[data-lp-input]');
    const modelRadios = document.querySelectorAll('[data-lp-model-radio]');
    const paletteRadios = document.querySelectorAll('[data-lp-palette-radio]');
    const btnAnimRadios = document.querySelectorAll('[data-lp-btnanim-radio]');

    function updateLpPreview() {
        // Atualizar classes visuais dos cards de seleção e data-model no mockup
        modelRadios.forEach(r => {
            const card = r.closest('.template-model-card');
            if (card) {
                if (r.checked) {
                    card.classList.add('active');
                    if (mockupScreen) {
                        mockupScreen.setAttribute('data-model', r.value);
                    }
                    if (mockupFrame) {
                        mockupFrame.setAttribute('data-model', r.value);
                    }
                } else {
                    card.classList.remove('active');
                }
                // Remove inline styles to let CSS handle the dark mode properly
                card.style.borderColor = '';
                card.style.background = '';
            }
        });

        paletteRadios.forEach(p => {
            const card = p.closest('.color-palette-card');
            if (card) {
                if (p.checked) {
                    card.classList.add('active');
                    if (mockupScreen) {
                        mockupScreen.setAttribute('data-palette', p.value);
                    }
                    if (mockupFrame) {
                        mockupFrame.setAttribute('data-palette', p.value);
                    }
                } else {
                    card.classList.remove('active');
                }
                // Remove inline styles to let CSS handle the dark mode properly
                card.style.borderColor = '';
                card.style.background = '';
            }
        });

        btnAnimRadios.forEach(ba => {
            const card = ba.closest('.template-model-card') || ba.closest('.btn-anim-card');
            if (card) {
                if (ba.checked) {
                    card.classList.add('active');
                    if (mockupScreen) {
                        mockupScreen.setAttribute('data-btn-animation', ba.value);
                    }
                    if (mockupFrame) {
                        mockupFrame.setAttribute('data-btn-animation', ba.value);
                    }
                } else {
                    card.classList.remove('active');
                }
                // Remove inline styles to let CSS handle the dark mode properly
                card.style.borderColor = '';
                card.style.background = '';
            }
        });

        // Atualizar textos e valores no preview
        landingInputs.forEach((input) => {
            const target = input.dataset.lpInput;
            const value = input.value;

            if (target === 'badge') {
                const badge = mockupScreen?.querySelector('#mockup_badge');
                const badgeWrap = mockupScreen?.querySelector('#mockup_badge_wrap');
                if (badge) badge.textContent = value || '🔥 OFERTA EXCLUSIVA • PRONTA ENTREGA';
                if (badgeWrap) badgeWrap.style.display = value ? 'inline-flex' : 'none';
            } else if (target === 'headline') {
                const headline = mockupScreen?.querySelector('#mockup_title');
                if (headline) headline.textContent = value || document.getElementById('name')?.value || 'NOME DO PRODUTO';
            } else if (target === 'subheadline') {
                const subheadline = mockupScreen?.querySelector('#mockup_subheadline');
                if (subheadline) subheadline.textContent = value || document.getElementById('description')?.value || 'Descrição resumida do produto para o cliente.';
            } else if (target === 'button-text') {
                const btn = mockupScreen?.querySelector('#mockup_button_text');
                const btn2 = mockupScreen?.querySelector('#mockup_button_text_2');
                const btn3 = mockupScreen?.querySelector('#mockup_button_text_3');
                const btnSticky = document.getElementById('mockup_sticky_btn_text') || mockupFrame?.querySelector('#mockup_sticky_btn_text');
                if (btn) btn.textContent = value || 'Quero aproveitar agora';
                if (btn2) btn2.textContent = value || 'Quero aproveitar agora';
                if (btn3) btn3.textContent = value || 'Quero aproveitar agora';
                if (btnSticky) btnSticky.textContent = value || 'Quero aproveitar agora';
            } else if (target === 'urgency-text') {
                const u = mockupScreen?.querySelector('#mockup_urgency_text');
                if (u) u.textContent = value || 'Compra sem cadastro. Você fala direto com a loja.';
            } else if (target === 'cta-icon') {
                const iconClass = value || 'ti-arrow-narrow-right';
                const mainBtnIcon = mockupScreen?.querySelector('#mockup_button_text')?.nextElementSibling;
                const closeBtnIcon = mockupScreen?.querySelector('#mockup_button_text_2')?.nextElementSibling;
                const guaranteeBtnIcon = mockupScreen?.querySelector('#mockup_button_text_3')?.nextElementSibling;
                const stickyBtnIcon = document.getElementById('mockup_sticky_btn_text')?.nextElementSibling || mockupFrame?.querySelector('#mockup_sticky_btn_text')?.nextElementSibling;
                if (mainBtnIcon) mainBtnIcon.className = 'ti ' + iconClass;
                if (closeBtnIcon) closeBtnIcon.className = 'ti ' + iconClass;
                if (guaranteeBtnIcon) guaranteeBtnIcon.className = 'ti ' + iconClass;
                if (stickyBtnIcon) stickyBtnIcon.className = 'ti ' + iconClass;
            } else if (target === 'shipping-info') {
                const s = mockupScreen?.querySelector('#mockup_shipping_info');
                if (s) s.textContent = value || 'Entrega rápida em Barretos e região';
            } else if (target === 'payment-info') {
                const p = mockupScreen?.querySelector('#mockup_payment_info');
                if (p) p.textContent = value || 'Pagamento no PIX ou cartão';
            } else if (target === 'guarantee-info') {
                const g = mockupScreen?.querySelector('#mockup_guarantee_info');
                if (g) g.textContent = value || 'Produto conferido antes do envio';
            } else if (target === 'about-title') {
                const at = mockupScreen?.querySelector('#mockup_about_title');
                if (at) at.textContent = value || 'Últimas unidades em estoque';
            } else if (target === 'about-content') {
                const ac = mockupScreen?.querySelector('#mockup_about_content');
                if (ac) ac.textContent = value || 'Devido à alta demanda desta edição, restam pouquíssimos frascos disponíveis. Garanta o seu antes que o lote esgote definitivamente.';
            } else if (target === 'checkout-title') {
                const ct = mockupScreen?.querySelector('#mockup_checkout_title');
                if (ct) ct.textContent = value || 'Fechar pedido pelo WhatsApp';
            } else if (target === 'checkout-subtitle') {
                const cs = mockupScreen?.querySelector('#mockup_checkout_subtitle');
                if (cs) cs.textContent = value || 'A conversa abre com o produto e o preço já escritos. Você só confirma.';
            }
        });

        // Atualizar preço e imagem no preview
        const priceInput = document.getElementById('price');
        const promoInput = document.getElementById('promotional_price');
        const priceVal = (priceInput ? priceInput.value : '').trim();
        const promoVal = (promoInput ? promoInput.value : '').trim();
        
        const currentPriceDisplay = mockupScreen?.querySelector('#mockup_current_price');
        const oldPriceDisplay = mockupScreen?.querySelector('#mockup_old_price');
        const savingsTag = mockupScreen?.querySelector('#mockup_savings_tag');
        const savingsText = mockupScreen?.querySelector('#mockup_savings_text');
        const discountTag = mockupScreen?.querySelector('#mockup_discount_tag');
        const stickyPrice = mockupScreen?.querySelector('#mockup_sticky_price');
        const stickyOldPrice = mockupScreen?.querySelector('#mockup_sticky_old_price');
        const closingPrice = mockupScreen?.querySelector('#mockup_closing_current_price');
        const closingOldPrice = mockupScreen?.querySelector('#mockup_closing_old_price');
        const closingSavingsTag = mockupScreen?.querySelector('#mockup_closing_savings_tag');

        // Valores numéricos reais
        let pNum = 0;
        let prNum = 0;
        if (priceVal) {
            pNum = parseFloat(priceVal.replace(/\./g, '').replace(',', '.'));
        }
        if (promoVal) {
            prNum = parseFloat(promoVal.replace(/\./g, '').replace(',', '.'));
        }

        const hasPromo = !isNaN(prNum) && prNum > 0 && !isNaN(pNum) && pNum > prNum;
        const mainDisplayPrice = hasPromo ? promoVal : (priceVal || '0,00');

        if (currentPriceDisplay) {
            currentPriceDisplay.textContent = 'R$ ' + mainDisplayPrice;
        }
        if (closingPrice) {
            closingPrice.textContent = 'R$ ' + mainDisplayPrice;
        }
        if (stickyPrice) {
            stickyPrice.textContent = 'R$ ' + mainDisplayPrice;
        }

        if (hasPromo) {
            if (oldPriceDisplay) {
                oldPriceDisplay.textContent = 'R$ ' + priceVal;
                oldPriceDisplay.style.display = 'inline';
            }
            if (closingOldPrice) {
                closingOldPrice.textContent = 'R$ ' + priceVal;
                closingOldPrice.style.display = 'inline';
            }
            if (stickyOldPrice) {
                stickyOldPrice.textContent = 'R$ ' + priceVal;
                stickyOldPrice.style.display = 'block';
            }
            
            // Calcular economia
            const savings = pNum - prNum;
            const savingsStr = savings.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const discount = Math.round(((pNum - prNum) / pNum) * 100);
            
            if (savingsTag) {
                savingsTag.textContent = 'economize R$ ' + savingsStr;
                savingsTag.style.display = 'inline-block';
            }
            if (closingSavingsTag) {
                closingSavingsTag.textContent = 'economize R$ ' + savingsStr;
                closingSavingsTag.style.display = 'inline-block';
            }
            if (savingsText) {
                savingsText.innerHTML = `<span style="display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: var(--lp-primary, #9d174d);"></span> Você economiza R$ ${savingsStr} neste preço`;
                savingsText.style.display = 'flex';
            }
            if (discountTag) {
                discountTag.textContent = '-' + discount + '% OFF';
                discountTag.style.display = 'block';
            }
        } else {
            if (oldPriceDisplay) oldPriceDisplay.style.display = 'none';
            if (closingOldPrice) closingOldPrice.style.display = 'none';
            if (stickyOldPrice) stickyOldPrice.style.display = 'none';
            if (savingsTag) savingsTag.style.display = 'none';
            if (closingSavingsTag) closingSavingsTag.style.display = 'none';
            if (savingsText) savingsText.style.display = 'none';
            if (discountTag) discountTag.style.display = 'none';
        }

        const allImgEls = Array.from(document.querySelectorAll('.image-preview-item img'));
        const previewImg = mockupScreen?.querySelector('#mockup_main_image');
        const placeholderImg = mockupScreen?.querySelector('#mockup_image_placeholder');
        const galleryCounter = mockupScreen?.querySelector('#mockup_gallery_counter');
        const currentIndexEl = mockupScreen?.querySelector('#mockup_current_img_index');
        const totalCountEl = mockupScreen?.querySelector('#mockup_total_img_count');
        const thumbsContainer = mockupScreen?.querySelector('#mockup_gallery_thumbs');

        if (allImgEls.length > 0) {
            const coverItem = document.querySelector('.image-preview-item .cover-badge')?.closest('.image-preview-item');
            const coverImgEl = coverItem?.querySelector('img') || allImgEls[0];
            
            if (previewImg && placeholderImg) {
                previewImg.src = previewImg.dataset.activeSrc || coverImgEl.src;
                previewImg.style.display = 'block';
                placeholderImg.style.display = 'none';
            }

            if (galleryCounter && currentIndexEl && totalCountEl) {
                if (allImgEls.length > 1) {
                    galleryCounter.style.display = 'block';
                    totalCountEl.textContent = allImgEls.length;
                    
                    // Descobrir index atual
                    const currentSrc = previewImg?.src || '';
                    const foundIndex = allImgEls.findIndex(img => img.src === currentSrc);
                    currentIndexEl.textContent = foundIndex >= 0 ? (foundIndex + 1) : 1;
                } else {
                    galleryCounter.style.display = 'none';
                }
            }

            // Renderizar miniaturas no mockup se houver mais de 1 imagem
            if (thumbsContainer) {
                if (allImgEls.length > 1) {
                    thumbsContainer.style.display = 'flex';
                    thumbsContainer.innerHTML = '';
                    
                    allImgEls.forEach((img, idx) => {
                        const thumb = document.createElement('button');
                        thumb.type = 'button';
                        const isActive = (previewImg?.src === img.src) || (!previewImg?.dataset.activeSrc && idx === 0);
                        thumb.style.cssText = `width: 46px; height: 46px; border-radius: 10px; overflow: hidden; border: ${isActive ? '2px solid var(--lp-primary, #9d174d)' : '1px solid #e2e8f0'}; padding: 0; background: #fff; cursor: pointer; flex-shrink: 0; transition: all 0.2s ease; opacity: ${isActive ? '1' : '0.65'};`;
                        
                        const thumbImg = document.createElement('img');
                        thumbImg.src = img.src;
                        thumbImg.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
                        thumb.appendChild(thumbImg);

                        thumb.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            if (previewImg) {
                                previewImg.src = img.src;
                                previewImg.dataset.activeSrc = img.src;
                            }
                            if (currentIndexEl) currentIndexEl.textContent = idx + 1;
                            
                            // Atualizar borda ativa dos thumbs
                            thumbsContainer.querySelectorAll('button').forEach((b, bIdx) => {
                                if (bIdx === idx) {
                                    b.style.border = '2px solid var(--lp-primary, #9d174d)';
                                    b.style.opacity = '1';
                                } else {
                                    b.style.border = '1px solid #e2e8f0';
                                    b.style.opacity = '0.65';
                                }
                            });
                        });

                        thumbsContainer.appendChild(thumb);
                    });
                } else {
                    thumbsContainer.style.display = 'none';
                    thumbsContainer.innerHTML = '';
                }
            }
        } else {
            if (previewImg && placeholderImg) {
                previewImg.removeAttribute('data-active-src');
                previewImg.style.display = 'none';
                placeholderImg.style.display = 'flex';
            }
            if (galleryCounter) galleryCounter.style.display = 'none';
            if (thumbsContainer) {
                thumbsContainer.style.display = 'none';
                thumbsContainer.innerHTML = '';
            }
        }
    }

    landingInputs.forEach(input => {
        input.addEventListener('input', updateLpPreview);
        input.addEventListener('change', updateLpPreview);
    });
    modelRadios.forEach(r => r.addEventListener('change', updateLpPreview));
    paletteRadios.forEach(p => p.addEventListener('change', updateLpPreview));
    btnAnimRadios.forEach(ba => ba.addEventListener('change', updateLpPreview));

    document.getElementById('price')?.addEventListener('input', updateLpPreview);
    document.getElementById('promotional_price')?.addEventListener('input', updateLpPreview);
    document.getElementById('name')?.addEventListener('input', updateLpPreview);
    document.getElementById('description')?.addEventListener('input', updateLpPreview);

    // Inicializar preview no carregamento da página e em intervalos curtos para máscaras
    updateLpPreview();
    setTimeout(updateLpPreview, 50);
    setTimeout(updateLpPreview, 250);

    // FAQ Accordion Interativo no Mockup
    document.querySelectorAll('.mockup-faq-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const card = this.closest('.mockup-faq-card');
            const ans = card?.querySelector('.mockup-faq-ans');
            const icon = this.querySelector('i');
            const isOpen = ans && ans.style.display === 'block';

            // Fechar outros
            document.querySelectorAll('.mockup-faq-card').forEach(c => {
                const a = c.querySelector('.mockup-faq-ans');
                const ic = c.querySelector('.mockup-faq-btn i');
                if (a) a.style.display = 'none';
                if (ic) ic.style.transform = 'rotate(0deg)';
            });

            if (!isOpen && ans) {
                ans.style.display = 'block';
                if (icon) icon.style.transform = 'rotate(45deg)';
            }
        });
    });

    // Thumbs existentes no PHP
    document.querySelectorAll('.mockup-thumb-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const src = this.dataset.thumbSrc;
            const idx = this.dataset.thumbIdx;
            const previewImg = document.getElementById('mockup_main_image');
            const currentIndexEl = document.getElementById('mockup_current_img_index');

            if (previewImg && src) {
                previewImg.src = src;
                previewImg.dataset.activeSrc = src;
            }
            if (currentIndexEl && idx) {
                currentIndexEl.textContent = idx;
            }

            document.querySelectorAll('.mockup-thumb-btn').forEach(b => {
                b.style.border = '1px solid #e2e8f0';
                b.style.opacity = '0.65';
            });
            this.style.border = '2px solid var(--lp-primary, #9d174d)';
            this.style.opacity = '1';
        });
    });

    // Format money helper
    function formatMoney(value) {
        let clean = value.replace(/\D/g, '');
        if (!clean) return '';
        let num = (parseInt(clean, 10) / 100).toFixed(2);
        let parts = num.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return parts.join(',');
    }

    document.querySelectorAll('.money-mask').forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = formatMoney(this.value);
        });
    });

    // Auto slug & Auto Headline/Subheadline logic
    const isNewProduct = <?= $isEdit ? 'false' : 'true' ?>;
    const headlineInput = document.getElementById('headline');
    const subheadlineInput = document.getElementById('subheadline');
    const descInput = document.getElementById('description');
    const btnSyncHeadline = document.getElementById('btn-sync-headline');
    const btnSyncSubheadline = document.getElementById('btn-sync-subheadline');
    const urgencySelector = document.getElementById('urgency_phrase_selector');
    const aboutTitleInput = document.getElementById('about_title');
    const aboutContentInput = document.getElementById('about_content');

    urgencySelector?.addEventListener('change', function() {
        if (!this.value) return;
        const parts = this.value.split('|');
        if (parts.length === 2) {
            if (aboutTitleInput) {
                aboutTitleInput.value = parts[0];
                aboutTitleInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (aboutContentInput) {
                aboutContentInput.value = parts[1];
                aboutContentInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            updateLpPreview();
        }
    });

    let headlineCustomized = !isNewProduct && !!(headlineInput && headlineInput.value);
    let subheadlineCustomized = !isNewProduct && !!(subheadlineInput && subheadlineInput.value);

    headlineInput?.addEventListener('input', function() {
        headlineCustomized = true;
    });

    subheadlineInput?.addEventListener('input', function() {
        subheadlineCustomized = true;
    });

    btnSyncHeadline?.addEventListener('click', function() {
        if (nameInput && headlineInput) {
            headlineInput.value = nameInput.value;
            headlineInput.dispatchEvent(new Event('input', { bubbles: true }));
            updateLpPreview();
        }
    });

    btnSyncSubheadline?.addEventListener('click', function() {
        if (descInput && subheadlineInput) {
            subheadlineInput.value = descInput.value;
            subheadlineInput.dispatchEvent(new Event('input', { bubbles: true }));
            updateLpPreview();
        }
    });

    if (nameInput && slugInput) {
        // Extract existing 6-digit random code if available, or generate a new one
        let existingRandom = '';
        const match = slugInput.value.match(/-(\d{6})$/);
        if (match) {
            existingRandom = match[1];
        } else {
            existingRandom = Math.floor(100000 + Math.random() * 900000).toString();
        }

        nameInput.addEventListener('input', function() {
            const baseSlug = nameInput.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            
            if (baseSlug) {
                slugInput.value = `${baseSlug}-${existingRandom}`;
            } else {
                slugInput.value = '';
            }

            if (isNewProduct && !headlineCustomized && headlineInput) {
                headlineInput.value = nameInput.value;
                updateLpPreview();
            }
        });
    }

    if (descInput && subheadlineInput) {
        descInput.addEventListener('input', function() {
            if (isNewProduct && !subheadlineCustomized) {
                subheadlineInput.value = descInput.value;
                updateLpPreview();
            }
        });
    }

    // Dirty state management
    if (form && saveBar) {
        const getFormSnapshot = () => {
            const data = {};
            Array.from(form.elements).forEach(el => {
                if (!el.name || el.type === 'file' || el.type === 'button' || el.type === 'submit') return;
                if (el.type === 'checkbox') {
                    data[el.name] = el.checked ? (el.value || '1') : '';
                } else if (el.type === 'radio') {
                    if (el.checked) data[el.name] = el.value;
                } else {
                    data[el.name] = el.value;
                }
            });
            return JSON.stringify(data);
        };

        const initialState = getFormSnapshot();

        function checkDirty() {
            const currentState = getFormSnapshot();
            const isDirty = (currentState !== initialState);
            saveBar.hidden = !isDirty;
        }

        form.addEventListener('input', checkDirty);
        form.addEventListener('change', checkDirty);

        const cancelBtn = form.querySelector('[data-cancel-form]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                window.location.reload();
            });
        }
    }

    // Image Upload Logic
    const uploadArea = document.getElementById('product-images-upload');
    const fileInput = document.getElementById('images-input');
    const previewContainer = document.getElementById('images-preview-container');
    const productId = <?= isset($product) ? $product->id : 'null' ?>;

    // Cropper Elements
    const cropperModal = document.getElementById('cropper_modal');
    const cropperImage = document.getElementById('cropper_image');
    const btnCloseCropper = document.getElementById('btn_close_cropper');
    const btnCancelCropper = document.getElementById('btn_cancel_cropper');
    const btnApplyCropper = document.getElementById('btn_apply_cropper');
    let cropperInstance = null;
    let pendingFiles = [];

    if (uploadArea && fileInput && productId) {
        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#7c69ff';
            uploadArea.style.backgroundColor = 'rgba(124, 105, 255, 0.05)';
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'rgba(255,255,255,0.1)';
            uploadArea.style.backgroundColor = 'transparent';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'rgba(255,255,255,0.1)';
            uploadArea.style.backgroundColor = 'transparent';
            if (e.dataTransfer.files.length > 0) {
                handleFiles(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFiles(this.files);
            }
        });

        function handleFiles(files) {
            const validFiles = Array.from(files).filter(f => f.type.match('image.*'));
            if (validFiles.length === 0) return;
            
            pendingFiles = pendingFiles.concat(validFiles);
            if (!cropperModal.classList.contains('open')) {
                processNextFile();
            }
            fileInput.value = ''; // Reset
        }

        function processNextFile() {
            if (pendingFiles.length === 0) return;
            
            const file = pendingFiles.shift();
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
                        aspectRatio: 1, // Quadrado 1:1
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                        movable: true,
                        zoomable: true,
                        rotatable: true,
                        scalable: true,
                        zoomOnTouch: true,
                        zoomOnWheel: true,
                    });
                }
            };
            reader.readAsDataURL(file);
        }

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
                
                // Se houver mais arquivos na fila, abre o próximo
                if (pendingFiles.length > 0) {
                    processNextFile();
                }
            }, 200);
        };

        btnCloseCropper?.addEventListener('click', () => {
            pendingFiles = []; // Limpa a fila se fechar no X
            closeCropperModal();
        });
        
        btnCancelCropper?.addEventListener('click', () => {
            // Pula a imagem atual e vai para a próxima (se houver)
            closeCropperModal();
        });

        btnApplyCropper?.addEventListener('click', () => {
            if (!cropperInstance) return;

            const canvas = cropperInstance.getCroppedCanvas({
                width: 800,
                height: 800,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            const croppedBase64 = canvas.toDataURL('image/webp', 0.90);
            uploadImageBase64(croppedBase64);
            closeCropperModal();
        });

        // Controles da Toolbar do Cropper
        document.getElementById('btn_crop_zoom_in')?.addEventListener('click', (e) => { e.preventDefault(); cropperInstance?.zoom(0.1); });
        document.getElementById('btn_crop_zoom_out')?.addEventListener('click', (e) => { e.preventDefault(); cropperInstance?.zoom(-0.1); });
        document.getElementById('btn_crop_rotate_left')?.addEventListener('click', (e) => { e.preventDefault(); cropperInstance?.rotate(-90); });
        document.getElementById('btn_crop_rotate_right')?.addEventListener('click', (e) => { e.preventDefault(); cropperInstance?.rotate(90); });
        document.getElementById('btn_crop_reset')?.addEventListener('click', (e) => { e.preventDefault(); cropperInstance?.reset(); });

        function uploadImageBase64(base64Data) {
            const formData = new FormData();
            formData.append('image_base64', base64Data);
            formData.append('product_id', productId);
            
            const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
            const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.getAttribute('content') || '<?= csrf_hash() ?>';
            const csrfHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';
            
            formData.append(csrfTokenName, csrfHash);

            // Create a temporary preview element with loading state
            const tempId = 'upload-' + Date.now();
            const tempHtml = `
                <div id="${tempId}" style="width: 120px; height: 120px; border-radius: 8px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.1);">
                    <i class="ti ti-loader" style="animation: spin 1s linear infinite; font-size: 24px; color: #7c69ff;"></i>
                </div>
            `;
            previewContainer.insertAdjacentHTML('beforeend', tempHtml);

            fetch('<?= site_url('produtos/upload-imagem') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfHash
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const tempEl = document.getElementById(tempId);
                if (data.success) {
                    // Update meta tags if new CSRF token was returned
                    if (data.csrfHash) {
                        const hashMeta = document.querySelector('meta[name="csrf-hash"]');
                        if (hashMeta) hashMeta.setAttribute('content', data.csrfHash);
                    }

                    // Replace temp with actual image
                    const imgHtml = `
                        <div class="image-preview-item" data-id="${data.image.id}" style="position: relative; width: 120px; height: 120px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                            <img src="${data.image.url}" style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="position: absolute; top: 4px; right: 4px; display: flex; gap: 4px;">
                                ${!data.image.is_cover ? `<button type="button" onclick="setCoverImage(${data.image.id}, this)" title="Definir como Capa" style="background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 4px; padding: 4px; cursor: pointer;"><i class="ti ti-star"></i></button>` : ''}
                                <button type="button" onclick="deleteImage(${data.image.id}, this)" title="Excluir Imagem" style="background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 4px; padding: 4px; cursor: pointer;">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            ${data.image.is_cover ? '<span class="cover-badge" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(124, 105, 255, 0.9); color: white; font-size: 11px; text-align: center; padding: 2px 0;">Capa</span>' : ''}
                        </div>
                    `;
                    if (tempEl) tempEl.outerHTML = imgHtml;
                } else {
                    if (tempEl) tempEl.remove();
                    alert(data.message || 'Erro ao fazer upload da imagem.');
                }
            })
            .catch(error => {
                const tempEl = document.getElementById(tempId);
                if (tempEl) tempEl.remove();
                alert('Erro de conexão ao fazer upload.');
            });
        }
    } else if (uploadArea && !productId) {
        // Se for um novo produto (ainda não salvo)
        uploadArea.addEventListener('click', () => {
            alert('Por favor, salve as informações principais do produto primeiro antes de adicionar imagens.');
        });
    }
});

function deleteImage(id, btn) {
    if (typeof window.triggerActionConfirm === 'function') {
        window.triggerActionConfirm('image-delete', '', function() {
            executeDeleteImage(id, btn);
        });
    } else {
        if (confirm('Tem certeza que deseja excluir esta imagem?')) {
            executeDeleteImage(id, btn);
        }
    }
}

function executeDeleteImage(id, btn) {
    const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
    const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.getAttribute('content') || '<?= csrf_hash() ?>';
    const csrfHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';
    
    const formData = new FormData();
    formData.append('image_id', id);
    formData.append(csrfTokenName, csrfHash);

    fetch('<?= site_url('produtos/excluir-imagem') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            [csrfHeader]: csrfHash
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Se nova hash de csrf veio
            if (data.csrfHash) {
                const hashMeta = document.querySelector('meta[name="csrf-hash"]');
                if (hashMeta) hashMeta.setAttribute('content', data.csrfHash);
            }
            const item = btn.closest('.image-preview-item');
            const wasCover = item.querySelector('.cover-badge') !== null;
            item.remove();

            // Se apagou a capa, atualiza a primeira imagem restante para exibir a badge
            if (wasCover) {
                const firstRemaining = document.querySelector('.image-preview-item');
                if (firstRemaining && !firstRemaining.querySelector('.cover-badge')) {
                    firstRemaining.insertAdjacentHTML('beforeend', '<span class="cover-badge" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(124, 105, 255, 0.9); color: white; font-size: 11px; text-align: center; padding: 2px 0;">Capa</span>');
                    const starBtn = firstRemaining.querySelector('button[title="Definir como Capa"]');
                    if (starBtn) starBtn.remove();
                }
            }
        } else {
            alert(data.message || 'Erro ao excluir imagem.');
        }
    })
    .catch(error => {
        alert('Erro ao excluir imagem.');
    });
}

function setCoverImage(id, btn) {
    const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || '<?= csrf_token() ?>';
    const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.getAttribute('content') || '<?= csrf_hash() ?>';
    const csrfHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || 'X-CSRF-TOKEN';
    
    const formData = new FormData();
    formData.append('image_id', id);
    formData.append('product_id', <?= isset($product) ? $product->id : 'null' ?>);
    formData.append(csrfTokenName, csrfHash);

    fetch('<?= site_url('produtos/capa-imagem') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            [csrfHeader]: csrfHash
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.csrfHash) {
                const hashMeta = document.querySelector('meta[name="csrf-hash"]');
                if (hashMeta) hashMeta.setAttribute('content', data.csrfHash);
            }

            // Remove badges anteriores e readiciona botões de estrela
            document.querySelectorAll('.image-preview-item').forEach(item => {
                const badge = item.querySelector('.cover-badge');
                if (badge) badge.remove();

                const actionsContainer = item.querySelector('div[style*="position: absolute"]');
                if (actionsContainer && !actionsContainer.querySelector('button[title="Definir como Capa"]')) {
                    const itemId = item.dataset.id;
                    const starBtn = document.createElement('button');
                    starBtn.type = 'button';
                    starBtn.title = 'Definir como Capa';
                    starBtn.style.cssText = 'background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 4px; padding: 4px; cursor: pointer;';
                    starBtn.innerHTML = '<i class="ti ti-star"></i>';
                    starBtn.onclick = function() { setCoverImage(itemId, this); };
                    actionsContainer.insertBefore(starBtn, actionsContainer.firstChild);
                }
            });

            // Adiciona badge de capa no item selecionado e remove seu botão estrela
            const currentItem = btn.closest('.image-preview-item');
            currentItem.insertAdjacentHTML('beforeend', '<span class="cover-badge" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(124, 105, 255, 0.9); color: white; font-size: 11px; text-align: center; padding: 2px 0;">Capa</span>');
            btn.remove();
        } else {
            alert(data.message || 'Erro ao definir imagem de capa.');
        }
    });
}

// ----------------------------------------------------
// Atualização Assíncrona de Estatísticas (AJAX / Sem Refresh)
// ----------------------------------------------------
<?php if ($isEdit && isset($product->id)): ?>
let currentStatsPeriod = <?= (int) ($stats['period'] ?? 7) ?>;
let lastStatsEvolutionData = <?= !empty($stats['evolution']) ? json_encode($stats['evolution']) : '[]' ?>;

function renderMixedChart(evolution) {
    const container = document.getElementById('stats_mixed_chart_container');
    if (!container) return;

    if (!evolution || evolution.length === 0) {
        container.innerHTML = '<div style="width: 100%; text-align: center; color: rgb(var(--muted)); padding: 50px 0; font-size: 13px;">Nenhum acesso registrado no período.</div>';
        return;
    }

    lastStatsEvolutionData = evolution;
    const count = evolution.length;

    // Calcular máximo comum para usar a mesma escala no eixo Y
    let maxVal = 0;
    evolution.forEach(d => {
        if ((d.pageviews || 0) > maxVal) maxVal = d.pageviews;
        if ((d.clicks || 0) > maxVal) maxVal = d.clicks;
    });

    const scaleMax = Math.max(maxVal, 1);

    const svgWidth = 1000;
    const svgHeight = 150;
    const topPadding = 18;
    const bottomPadding = 26;
    const chartHeight = svgHeight - topPadding - bottomPadding;

    const todayStr = new Date().toISOString().split('T')[0];

    // Montar pontos da linha (Visitas) na mesma escala
    const points = [];
    const step = svgWidth / count;

    evolution.forEach((d, i) => {
        const x = (i * step) + (step / 2);
        const y = topPadding + chartHeight - (((d.pageviews || 0) / scaleMax) * chartHeight);
        points.push({ x, y, data: d, index: i });
    });

    // Caminho SVG da linha e do gradiente da área
    let linePath = '';
    let areaPath = '';
    if (points.length > 0) {
        linePath = `M ${points[0].x} ${points[0].y}`;
        points.forEach((p, i) => {
            if (i > 0) {
                // Curva Bezier suave
                const prev = points[i - 1];
                const cx1 = prev.x + (p.x - prev.x) / 2;
                const cy1 = prev.y;
                const cx2 = prev.x + (p.x - prev.x) / 2;
                const cy2 = p.y;
                linePath += ` C ${cx1} ${cy1}, ${cx2} ${cy2}, ${p.x} ${p.y}`;
            }
        });

        const firstX = points[0].x;
        const lastX = points[points.length - 1].x;
        const baseY = topPadding + chartHeight;
        areaPath = `${linePath} L ${lastX} ${baseY} L ${firstX} ${baseY} Z`;
    }

    // Gerar Barras de Cliques (SVG) na mesma escala
    let barsSvg = '';
    const barWidth = Math.min(Math.max(step * 0.38, 10), 28);

    points.forEach((p) => {
        const clicks = p.data.clicks || 0;
        const barH = (clicks / scaleMax) * chartHeight;
        const barY = topPadding + chartHeight - barH;
        const barX = p.x - (barWidth / 2);
        const isToday = (p.data.date === todayStr);

        barsSvg += `
            <rect x="${barX}" y="${barY}" width="${barWidth}" height="${barH}" rx="4" ry="4" fill="${isToday ? '#10b981' : 'rgba(16, 185, 129, 0.65)'}" class="chart-click-bar">
                <title>${p.data.dayLabel}: ${clicks} clique(s)</title>
            </rect>
        `;
    });

    // Gerar Pontos interativos da Linha (Visitas)
    let dotsSvg = '';
    points.forEach((p) => {
        const isToday = (p.data.date === todayStr);
        dotsSvg += `
            <circle cx="${p.x}" cy="${p.y}" r="4.5" fill="${isToday ? '#10b981' : '#a855f7'}" stroke="#1e222d" stroke-width="2" class="chart-pv-dot" style="transition: r 0.15s ease;">
            </circle>
        `;
    });

    // Labels do eixo X
    let labelsHtml = '<div style="display: flex; justify-content: space-between; width: 100%; margin-top: 4px;">';
    points.forEach((p) => {
        labelsHtml += `
            <div style="flex: 1; text-align: center; font-size: 10px; color: rgb(var(--muted)); font-weight: 500;">
                ${p.data.dayLabel}
            </div>
        `;
    });
    labelsHtml += '</div>';

    // Colunas de Hover interativo com Tooltip fixo e sem corte
    let overlayColumns = '<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 24px; display: flex;">';
    points.forEach((p) => {
        overlayColumns += `
            <div class="mixed-chart-col" data-idx="${p.index}" style="flex: 1; height: 100%; position: relative; cursor: pointer; display: flex; justify-content: center;">
                <div class="mixed-chart-tooltip" style="position: absolute; top: 2px; left: 50%; transform: translateX(-50%); background: #0f172a; color: #f8fafc; border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 8px 24px rgba(0,0,0,0.6); padding: 5px 10px; border-radius: 6px; font-size: 11px; white-space: nowrap; pointer-events: none; opacity: 0; transition: opacity 0.15s ease; z-index: 100; display: flex; flex-direction: column; gap: 3px; font-weight: 600;">
                    <div style="font-size: 11px; color: #94a3b8; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 2px; text-align: center;">${p.data.dayLabel}</div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <span style="color: #c084fc; display: flex; align-items: center; gap: 4px;"><i class="ti ti-eye"></i> Visitas:</span>
                        <span style="color: #fff;">${p.data.pageviews || 0}</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <span style="color: #34d399; display: flex; align-items: center; gap: 4px;"><i class="ti ti-brand-whatsapp"></i> Cliques:</span>
                        <span style="color: #fff;">${p.data.clicks || 0}</span>
                    </div>
                </div>
            </div>
        `;
    });
    overlayColumns += '</div>';

    container.innerHTML = `
        <div style="position: relative; width: 100%; height: 160px;">
            <svg viewBox="0 0 ${svgWidth} ${svgHeight}" preserveAspectRatio="none" style="width: 100%; height: 100%; overflow: visible; display: block;">
                <defs>
                    <linearGradient id="pvAreaGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#a855f7" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#a855f7" stop-opacity="0.0"/>
                    </linearGradient>
                    <linearGradient id="gridLineGradient" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="rgba(255,255,255,0.02)"/>
                        <stop offset="50%" stop-color="rgba(255,255,255,0.08)"/>
                        <stop offset="100%" stop-color="rgba(255,255,255,0.02)"/>
                    </linearGradient>
                </defs>
                <!-- Linhas guia de fundo -->
                <line x1="0" y1="${topPadding}" x2="${svgWidth}" y2="${topPadding}" stroke="url(#gridLineGradient)" stroke-dasharray="4,4" />
                <line x1="0" y1="${topPadding + (chartHeight / 2)}" x2="${svgWidth}" y2="${topPadding + (chartHeight / 2)}" stroke="url(#gridLineGradient)" stroke-dasharray="4,4" />
                <line x1="0" y1="${topPadding + chartHeight}" x2="${svgWidth}" y2="${topPadding + chartHeight}" stroke="rgba(255,255,255,0.1)" />

                <!-- Barras (Cliques WhatsApp) -->
                ${barsSvg}

                <!-- Área sombreada (Visitas) -->
                <path d="${areaPath}" fill="url(#pvAreaGradient)" />

                <!-- Linha (Visitas) -->
                <path d="${linePath}" fill="none" stroke="#a855f7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                <!-- Pontos da Linha -->
                ${dotsSvg}
            </svg>
            ${overlayColumns}
        </div>
        ${labelsHtml}
    `;

    // Eventos de Hover para exibir o tooltip ajustado
    container.querySelectorAll('.mixed-chart-col').forEach(col => {
        col.addEventListener('mouseenter', function() {
            const tip = this.querySelector('.mixed-chart-tooltip');
            if (tip) tip.style.opacity = '1';
            const idx = parseInt(this.dataset.idx, 10);
            const dot = container.querySelectorAll('.chart-pv-dot')[idx];
            if (dot) dot.setAttribute('r', '7');
        });
        col.addEventListener('mouseleave', function() {
            const tip = this.querySelector('.mixed-chart-tooltip');
            if (tip) tip.style.opacity = '0';
            const idx = parseInt(this.dataset.idx, 10);
            const dot = container.querySelectorAll('.chart-pv-dot')[idx];
            if (dot) dot.setAttribute('r', '4.5');
        });
    });
}

function fetchProductStats(period) {
    if (period) currentStatsPeriod = period;
    const btnRefresh = document.getElementById('btn_refresh_stats');
    const refreshIcon = document.getElementById('refresh_stats_icon');

    if (btnRefresh) btnRefresh.disabled = true;
    if (refreshIcon) refreshIcon.classList.add('ti-spin');

    const url = '<?= site_url('produtos/' . $product->id . '/stats-data') ?>?stats_period=' + encodeURIComponent(currentStatsPeriod);

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        if (!response.ok) throw new Error('Falha ao carregar estatísticas');
        return response.json();
    })
    .then(data => {
        if (!data) return;

        // 1. Atualizar KPIs principais
        const elTotalPv = document.getElementById('kpi_total_pageviews');
        if (elTotalPv) elTotalPv.textContent = Number(data.totalPageviews || 0).toLocaleString('pt-BR');

        const elUniqueVis = document.getElementById('kpi_unique_visitors');
        if (elUniqueVis) elUniqueVis.textContent = Number(data.uniqueVisitors || 0).toLocaleString('pt-BR');

        const elCtaClicks = document.getElementById('kpi_total_cta_clicks');
        if (elCtaClicks) elCtaClicks.textContent = Number(data.totalCtaClicks || 0).toLocaleString('pt-BR');

        const elConvRate = document.getElementById('kpi_conversion_rate');
        if (elConvRate) elConvRate.innerHTML = `<i class="ti ti-bolt"></i> Taxa de conversão: ${data.conversionRate || 0}%`;

        // 2. Tendência hoje vs ontem
        const elTrendPv = document.getElementById('kpi_trend_pageviews');
        if (elTrendPv) {
            const diffPv = (data.todayPageviews || 0) - (data.yesterdayPageviews || 0);
            if (diffPv > 0) {
                elTrendPv.innerHTML = `<span class="kpi-trend-pill positive" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #10b981;"><i class="ti ti-trending-up"></i> +${diffPv} hoje</span>`;
            } else if (diffPv < 0) {
                elTrendPv.innerHTML = `<span class="kpi-trend-pill negative" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #ef4444;"><i class="ti ti-trending-down"></i> ${diffPv} hoje</span>`;
            } else {
                elTrendPv.innerHTML = `<span class="kpi-trend-pill neutral" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #64748b;"><i class="ti ti-minus"></i> Igual a ontem</span>`;
            }
        }

        // 3. Mobile %
        const elMob1 = document.getElementById('kpi_mobile_pct_1');
        if (elMob1) elMob1.innerHTML = `<i class="ti ti-device-mobile"></i> ${data.mobilePct || 0}% tráfego mobile`;

        const elMob2 = document.getElementById('kpi_mobile_pct_2');
        if (elMob2) elMob2.innerHTML = `<i class="ti ti-device-mobile" style="color: #635bff;"></i> ${data.mobilePct || 0}% tráfego mobile`;

        // 4. Subtítulo do período e botões de filtro
        const elSubtitle = document.getElementById('stats_period_subtitle');
        if (elSubtitle) elSubtitle.textContent = `Visualizações da Landing Page nos últimos ${data.period || currentStatsPeriod} dias`;

        document.querySelectorAll('.js-stats-period-btn').forEach(btn => {
            const p = parseInt(btn.getAttribute('data-period'), 10);
            const isActive = (p === parseInt(data.period, 10));
            btn.classList.toggle('active', isActive);
            btn.style.border = isActive ? '1px solid #635bff' : '1px solid rgb(var(--border))';
            btn.style.background = isActive ? 'rgba(99, 91, 255, 0.15)' : 'rgb(var(--surface))';
            btn.style.color = isActive ? '#635bff' : 'rgb(var(--muted))';
            btn.style.fontWeight = isActive ? '700' : 'normal';
        });

        // 5. Gráfico de Evolução (Visitas = Linha SVG, Cliques = Barra)
        renderMixedChart(data.evolution || []);

        // 6. Fontes de Tráfego
        const elSources = document.getElementById('stats_sources_container');
        if (elSources) {
            const sourceColors = {
                'Instagram': '#ec4899',
                'Facebook': '#2563eb',
                'WhatsApp': '#10b981',
                'Google': '#ea4335',
                'Direto': '#f59e0b',
                'Referral': '#8b5cf6'
            };
            if (data.sources && data.sources.length > 0) {
                let htmlSources = '';
                data.sources.forEach(src => {
                    const col = sourceColors[src.name] || '#64748b';
                    htmlSources += `
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 4px;">
                                <span style="display: flex; align-items: center; gap: 6px; color: rgb(var(--foreground));">
                                    <i class="ti ti-point-filled" style="color: ${col};"></i> ${src.name}
                                </span>
                                <span style="color: rgb(var(--muted));">${src.percentage}% (${src.total})</span>
                            </div>
                            <div style="height: 6px; border-radius: 3px; background: rgba(255,255,255,0.06); overflow: hidden;">
                                <div style="width: ${src.percentage}%; height: 100%; background: ${col}; border-radius: 3px;"></div>
                            </div>
                        </div>
                    `;
                });
                elSources.innerHTML = htmlSources;
            } else {
                elSources.innerHTML = '<div style="color: rgb(var(--muted)); font-size: 12px; text-align: center; padding: 20px 0;">Aguardando primeiros acessos para mapear origens.</div>';
            }
        }

        // 7. Logs Recentes
        const elTbody = document.getElementById('stats_recent_logs_tbody');
        if (elTbody) {
            if (data.recentLogs && data.recentLogs.length > 0) {
                let htmlLogs = '';
                data.recentLogs.forEach(log => {
                    const isClick = ['cta_click', 'sticky_cta_click', 'whatsapp_click'].includes(log.event_type);
                    const eventName = isClick ? 'Clique de Compra (WhatsApp)' : 'Visualização de Página (PageView)';
                    const eventColor = isClick ? '#10b981' : '#635bff';
                    const eventIcon = isClick ? 'ti-brand-whatsapp' : 'ti-eye';
                    const devIcon = (log.device_type === 'mobile') ? 'ti-device-mobile' : 'ti-device-laptop';
                    const devName = (log.device_type ? log.device_type.charAt(0).toUpperCase() + log.device_type.slice(1) : 'Desktop');
                    const visitorDisplay = log.ip_address || (log.visitor_id ? log.visitor_id.substring(0, 12) + '...' : '-');
                    
                    let dateFormatted = '-';
                    if (log.created_at) {
                        const d = new Date(log.created_at.replace(' ', 'T'));
                        if (!isNaN(d)) {
                            const day = String(d.getDate()).padStart(2, '0');
                            const month = String(d.getMonth() + 1).padStart(2, '0');
                            const yr = d.getFullYear();
                            const hrs = String(d.getHours()).padStart(2, '0');
                            const mins = String(d.getMinutes()).padStart(2, '0');
                            dateFormatted = `${day}/${month}/${yr} ${hrs}:${mins}`;
                        }
                    }

                    const utmSource = log.utm_source || 'Direto';
                    const utmCamp = log.utm_campaign ? `<small style="color: rgb(var(--muted)); font-size: 11px;">(${log.utm_campaign})</small>` : '';

                    htmlLogs += `
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td style="padding: 12px 16px; font-weight: 600; color: ${eventColor}; display: flex; align-items: center; gap: 8px;">
                                <i class="ti ${eventIcon}" style="font-size: 16px;"></i> ${eventName}
                            </td>
                            <td style="padding: 12px 16px; color: rgb(var(--foreground));">
                                ${utmSource} ${utmCamp}
                            </td>
                            <td style="padding: 12px 16px; color: rgb(var(--muted));">
                                <i class="ti ${devIcon}"></i> ${devName}
                            </td>
                            <td style="padding: 12px 16px; color: rgb(var(--muted)); font-family: monospace; font-size: 11px;">
                                ${visitorDisplay}
                            </td>
                            <td style="padding: 12px 16px; text-align: right; color: rgb(var(--muted)); font-size: 12px;">
                                ${dateFormatted}
                            </td>
                        </tr>
                    `;
                });
                elTbody.innerHTML = htmlLogs;
            } else {
                elTbody.innerHTML = '<tr><td colspan="5" style="padding: 30px 16px; text-align: center; color: rgb(var(--muted)); font-size: 13px;">Nenhum evento registrado ainda. Quando os clientes acessarem a Landing Page do produto, os dados aparecerão aqui em tempo real.</td></tr>';
            }
        }
    })
    .catch(err => {
        console.error(err);
    })
    .finally(() => {
        if (btnRefresh) btnRefresh.disabled = false;
        if (refreshIcon) refreshIcon.classList.remove('ti-spin');
    });
}

document.addEventListener('click', function(e) {
    const periodBtn = e.target.closest('.js-stats-period-btn');
    if (periodBtn) {
        e.preventDefault();
        const p = parseInt(periodBtn.getAttribute('data-period'), 10);
        if (p) fetchProductStats(p);
    }

    const refreshBtn = e.target.closest('#btn_refresh_stats');
    if (refreshBtn) {
        e.preventDefault();
        fetchProductStats(currentStatsPeriod);
    }
});

// Renderizar gráfico inicial de estatísticas
if (typeof renderMixedChart === 'function' && Array.isArray(lastStatsEvolutionData)) {
    renderMixedChart(lastStatsEvolutionData);
}
<?php endif; ?>
</script>
    <!-- Google Fonts: Inter & Cormorant Garamond -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Definições de Paletas no Mockup do Admin (Isolado do Dashboard Admin) */
#lp_live_preview_container,
#lp_mockup_frame,
#lp_live_preview_container[data-palette="palette-aurora"],
#lp_mockup_frame[data-palette="palette-aurora"] {
    --lp-primary: #9d174d;
    --lp-primary-hover: #831843;
    --lp-accent-badge: #fce7f3;
    --lp-accent-badge-text: #be185d;
}

#lp_live_preview_container[data-palette="palette-emerald"],
#lp_mockup_frame[data-palette="palette-emerald"] {
    --lp-primary: #10b981;
    --lp-primary-hover: #059669;
    --lp-accent-badge: #d1fae5;
    --lp-accent-badge-text: #047857;
}

#lp_live_preview_container[data-palette="palette-amber"],
#lp_mockup_frame[data-palette="palette-amber"] {
    --lp-primary: #f59e0b;
    --lp-primary-hover: #d97706;
    --lp-accent-badge: #fef3c7;
    --lp-accent-badge-text: #b45309;
}

#lp_live_preview_container[data-palette="palette-ocean"],
#lp_mockup_frame[data-palette="palette-ocean"] {
    --lp-primary: #2563eb;
    --lp-primary-hover: #1d4ed8;
    --lp-accent-badge: #dbeafe;
    --lp-accent-badge-text: #1e40af;
}

#lp_live_preview_container[data-palette="palette-crimson"],
#lp_mockup_frame[data-palette="palette-crimson"] {
    --lp-primary: #dc2626;
    --lp-primary-hover: #b91c1c;
    --lp-accent-badge: #fee2e2;
    --lp-accent-badge-text: #991b1b;
}

#lp_live_preview_container[data-palette="palette-obsidian"],
#lp_mockup_frame[data-palette="palette-obsidian"] {
    --lp-primary: #0f172a;
    --lp-primary-hover: #334155;
    --lp-accent-badge: #f1f5f9;
    --lp-accent-badge-text: #0f172a;
}

/* ==========================================================================
   AS 6 ANIMAÇÕES DO BOTÃO CTA NO MOCKUP DO ADMIN
   ========================================================================== */
#lp_live_preview_container .mockup-main-cta-btn,
#lp_live_preview_container .mockup-closing-cta-btn,
#lp_mockup_frame .mockup-sticky-cta-btn {
    position: relative;
    overflow: hidden;
}

/* 1. btn-pulse: Pulso / Batimento VIP */
#lp_live_preview_container[data-btn-animation="btn-pulse"] .mockup-main-cta-btn,
#lp_live_preview_container[data-btn-animation="btn-pulse"] .mockup-closing-cta-btn,
#lp_mockup_frame[data-btn-animation="btn-pulse"] .mockup-sticky-cta-btn {
    animation: lpMockupBtnPulse 2.2s infinite ease-in-out;
}
@keyframes lpMockupBtnPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.25);
    }
    50% {
        transform: scale(1.03);
        box-shadow: 0 12px 28px -2px var(--lp-primary, #9d174d), 0 0 16px var(--lp-primary, #9d174d);
    }
}

/* 2. btn-shimmer: Brilho Shimmer Metálico */
#lp_live_preview_container[data-btn-animation="btn-shimmer"] .mockup-main-cta-btn::after,
#lp_live_preview_container[data-btn-animation="btn-shimmer"] .mockup-closing-cta-btn::after,
#lp_mockup_frame[data-btn-animation="btn-shimmer"] .mockup-sticky-cta-btn::after {
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
    animation: lpMockupBtnShimmer 3s infinite ease-in-out;
    pointer-events: none;
}
@keyframes lpMockupBtnShimmer {
    0% { left: -100%; }
    40%, 100% { left: 200%; }
}

/* 3. btn-shake: Vibração Shake */
#lp_live_preview_container[data-btn-animation="btn-shake"] .mockup-main-cta-btn,
#lp_live_preview_container[data-btn-animation="btn-shake"] .mockup-closing-cta-btn,
#lp_mockup_frame[data-btn-animation="btn-shake"] .mockup-sticky-cta-btn {
    animation: lpMockupBtnShake 4s infinite ease-in-out;
}
@keyframes lpMockupBtnShake {
    0%, 80%, 100% { transform: translateX(0) scale(1); }
    82% { transform: translateX(-4px) rotate(-1deg); }
    84% { transform: translateX(4px) rotate(1deg); }
    86% { transform: translateX(-4px) rotate(-1deg); }
    88% { transform: translateX(4px) rotate(1deg); }
    90% { transform: translateX(0) scale(1.02); }
}

/* 4. btn-bounce: Salto Bounce */
#lp_live_preview_container[data-btn-animation="btn-bounce"] .mockup-main-cta-btn,
#lp_live_preview_container[data-btn-animation="btn-bounce"] .mockup-closing-cta-btn,
#lp_mockup_frame[data-btn-animation="btn-bounce"] .mockup-sticky-cta-btn {
    animation: lpMockupBtnBounce 2.6s infinite ease-in-out;
}
@keyframes lpMockupBtnBounce {
    0%, 75%, 100% { transform: translateY(0); }
    80% { transform: translateY(-7px); }
    85% { transform: translateY(0); }
    90% { transform: translateY(-3px); }
    95% { transform: translateY(0); }
}

/* 5. btn-glow: Glow Neon Fluido */
#lp_live_preview_container[data-btn-animation="btn-glow"] .mockup-main-cta-btn,
#lp_live_preview_container[data-btn-animation="btn-glow"] .mockup-closing-cta-btn,
#lp_mockup_frame[data-btn-animation="btn-glow"] .mockup-sticky-cta-btn {
    animation: lpMockupBtnGlow 2s infinite alternate ease-in-out;
}
@keyframes lpMockupBtnGlow {
    0% {
        box-shadow: 0 0 5px var(--lp-primary, #9d174d), 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    100% {
        box-shadow: 0 0 22px var(--lp-primary, #9d174d), 0 0 10px var(--lp-primary-hover, #831843);
    }
}

/* 6. btn-static: Estático Sofisticado */
#lp_live_preview_container[data-btn-animation="btn-static"] .mockup-main-cta-btn,
#lp_live_preview_container[data-btn-animation="btn-static"] .mockup-closing-cta-btn,
#lp_mockup_frame[data-btn-animation="btn-static"] .mockup-sticky-cta-btn {
    animation: none !important;
}

/* Reordenação do Mockup no Admin para o Modelo 2 (Benefits & Prova) */
#lp_live_preview_container[data-model="model-2"] {
    display: flex !important;
    flex-direction: column !important;
    background-color: #f1f5f9 !important;
}
#lp_live_preview_container[data-model="model-2"] .mockup-badge-wrap { 
    order: 1; 
    background: linear-gradient(135deg, var(--lp-primary, #9d174d) 0%, var(--lp-primary-hover, #be185d) 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
}
#lp_live_preview_container[data-model="model-2"] .mockup-title { order: 2; }
#lp_live_preview_container[data-model="model-2"] .mockup-subheadline { 
    order: 3; 
    background: #ffffff !important;
    padding: 10px 12px !important;
    border-radius: 10px !important;
    border-left: 3.5px solid var(--lp-primary, #9d174d) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
    margin-bottom: 14px !important;
}
#lp_live_preview_container .mockup-gallery-wrapper,
#lp_live_preview_container[data-model="model-2"] .mockup-gallery-wrapper { 
    order: 4; 
    width: 100% !important;
    height: 327px !important;
    min-height: 327px !important;
    flex-shrink: 0 !important;
}
#lp_live_preview_container[data-model="model-2"] .mockup-gallery-thumbs { 
    order: 5; 
    margin-bottom: 14px; 
    flex-shrink: 0 !important;
}

/* Trazendo Garantias e Features para cima do Preço no Mockup */
#lp_live_preview_container[data-model="model-2"] .mockup-guarantees-card { 
    order: 6; 
    margin-top: 0 !important;
    margin-bottom: 12px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 14px !important;
    padding: 14px 12px !important;
    box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.05) !important;
    position: relative;
}
#lp_live_preview_container[data-model="model-2"] .mockup-guarantees-card::before {
    content: "🛡️ GARANTIAS & PROCEDÊNCIA TESTADA";
    display: block;
    font-size: 8.5px;
    font-weight: 900;
    letter-spacing: 0.5px;
    color: var(--lp-primary, #9d174d);
    margin-bottom: 8px;
    text-transform: uppercase;
}
#lp_live_preview_container[data-model="model-2"] .mockup-guarantees-card i {
    color: #ffffff !important;
    background: #10b981 !important;
    padding: 2px !important;
    font-size: 11px !important;
    border-radius: 50% !important;
    font-weight: 900 !important;
}

#lp_live_preview_container[data-model="model-2"] .mockup-features-wrapper {
    order: 7;
    margin-bottom: 14px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 14px !important;
    padding: 12px 10px !important;
    box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.04) !important;
}
#lp_live_preview_container[data-model="model-2"] .mockup-features-wrapper > div > div {
    background: #f8fafc;
    padding: 8px 4px;
    border-radius: 10px;
    border: 1px solid #edf2f7;
}

/* Card de Preço no Mockup */
#lp_live_preview_container[data-model="model-2"] .mockup-price-card { 
    order: 8; 
    margin-bottom: 20px !important;
    border: 2px solid var(--lp-primary, #9d174d) !important;
    border-radius: 16px !important;
    padding: 18px 14px 14px 14px !important;
    background: linear-gradient(180deg, #ffffff 0%, #fffbfd 100%) !important;
    box-shadow: 0 8px 24px -3px rgba(0, 0, 0, 0.18) !important;
    position: relative !important;
}
#lp_live_preview_container[data-model="model-2"] .mockup-price-card::before {
    content: "⚡ OFERTA DIRETA COM CONDIÇÃO ESPECIAL";
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--lp-primary, #9d174d);
    color: #ffffff;
    font-size: 8.5px;
    font-weight: 900;
    padding: 2px 10px;
    border-radius: 100px;
    letter-spacing: 0.5px;
    white-space: nowrap;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

/* Restante do fluxo no Modelo 2 */
#lp_live_preview_container[data-model="model-2"] .mockup-urgency-card { order: 9; }
#lp_live_preview_container[data-model="model-2"] .mockup-faq-heading { order: 10; }
#lp_live_preview_container[data-model="model-2"] .mockup-faq-accordion { order: 11; }
#lp_live_preview_container[data-model="model-2"] .mockup-closing-card { order: 12; }
#lp_live_preview_container[data-model="model-2"] .mockup-footer-wrap { order: 13; }

/* =========================================
   MODEL 3: MINIMAL COMPACT NO MOCKUP ADMIN
   ========================================= */
#lp_live_preview_container[data-model="model-3"] {
    display: flex !important;
    flex-direction: column !important;
    background-color: #fafafa !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-badge-wrap { 
    order: 1; 
    align-self: center !important;
    background: #0f172a !important;
    color: #ffffff !important;
    border-radius: 100px !important;
    padding: 3px 10px !important;
    margin-bottom: 10px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-gallery-wrapper { 
    order: 2; 
    width: 100% !important;
    height: 327px !important;
    min-height: 327px !important;
    flex-shrink: 0 !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.08) !important;
    margin-bottom: 8px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-gallery-thumbs { 
    order: 3; 
    margin-bottom: 12px !important; 
    flex-shrink: 0 !important;
    justify-content: center !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-title { 
    order: 4; 
    text-align: center !important;
    font-size: 17px !important;
    font-weight: 900 !important;
    letter-spacing: -0.4px !important;
    margin-bottom: 4px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-subheadline { 
    order: 5; 
    text-align: center !important;
    font-size: 11px !important;
    color: #64748b !important;
    margin-bottom: 14px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-price-card { 
    order: 6; 
    background: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 16px 14px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
    margin-bottom: 14px !important;
    text-align: center !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-price-card > div:first-child {
    justify-content: center !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-price-card a,
#lp_live_preview_container[data-model="model-3"] .mockup-price-card div.mockup-main-cta-btn {
    border-radius: 100px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-features-wrapper {
    order: 7;
    background: #ffffff !important;
    border-radius: 16px !important;
    padding: 10px !important;
    border: 1px solid #f1f5f9 !important;
    margin-bottom: 12px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-guarantees-card {
    order: 8;
    background: #ffffff !important;
    border-radius: 16px !important;
    padding: 12px 14px !important;
    border: 1px solid #f1f5f9 !important;
    margin-bottom: 14px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-urgency-card { 
    order: 9; 
    border-radius: 16px !important;
    border: 1px dashed #cbd5e1 !important;
    background: #ffffff !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-urgency-card div[style*="background: #0f172a"] {
    border-radius: 100px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-faq-heading { order: 10; }
#lp_live_preview_container[data-model="model-3"] .mockup-faq-accordion { order: 11; }
#lp_live_preview_container[data-model="model-3"] .mockup-closing-card { 
    order: 12; 
    border-radius: 20px !important;
}
#lp_live_preview_container[data-model="model-3"] .mockup-footer-wrap { order: 13; }

/* =========================================
   MODEL 4: BENTO BOX MODERN NO MOCKUP ADMIN
   ========================================= */
#lp_live_preview_container[data-model="model-4"] > * {
    flex-shrink: 0 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-badge-wrap { 
    order: 1; 
    align-self: flex-start !important;
    background: #ffffff !important;
    color: var(--lp-primary, #9d174d) !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    padding: 3px 10px !important;
    margin-bottom: 0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03) !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-title { 
    order: 2; 
    font-size: 16px !important;
    font-weight: 800 !important;
    letter-spacing: -0.4px !important;
    background: #ffffff !important;
    padding: 12px 14px !important;
    border-radius: 16px !important;
    margin-bottom: 0 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-subheadline { 
    order: 3; 
    font-size: 11px !important;
    color: #4b5563 !important;
    background: #ffffff !important;
    padding: 10px 14px !important;
    border-radius: 14px !important;
    margin-bottom: 0 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-gallery-wrapper { 
    order: 4; 
    width: 100% !important;
    height: 327px !important;
    min-height: 327px !important;
    flex-shrink: 0 !important;
    border-radius: 18px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05) !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-gallery-thumbs { 
    order: 5; 
    margin-bottom: 0 !important; 
    flex-shrink: 0 !important;
    background: #ffffff !important;
    padding: 8px 10px !important;
    border-radius: 16px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-price-card { 
    order: 6; 
    background: #ffffff !important;
    border: none !important;
    border-radius: 18px !important;
    padding: 16px 14px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05) !important;
    margin-bottom: 0 !important;
    position: relative !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-price-card::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 4px !important;
    height: 100% !important;
    background: var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-features-wrapper {
    order: 7;
    background: transparent !important;
    border-radius: 0 !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 0 !important;
    box-shadow: none !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-features-wrapper > div {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 8px !important;
    border: none !important;
    padding-top: 0 !important;
    margin-top: 0 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-features-wrapper > div > div {
    background: #ffffff !important;
    padding: 10px 8px !important;
    border-radius: 14px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-features-wrapper > div > div:last-child {
    grid-column: span 2 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-guarantees-card {
    order: 8;
    background: #ffffff !important;
    border-radius: 16px !important;
    padding: 12px 14px !important;
    border: none !important;
    margin-bottom: 0 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-urgency-card { 
    order: 9; 
    border-radius: 16px !important;
    border: none !important;
    background: #ffffff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-faq-heading { 
    order: 10; 
    background: #ffffff !important;
    padding: 12px !important;
    border-radius: 14px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
    margin-bottom: 0 !important;
    text-align: center !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-faq-accordion { 
    order: 11; 
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-faq-accordion > div {
    border-radius: 14px !important;
    border: none !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-closing-card { 
    order: 12; 
    border-radius: 18px !important;
    border: none !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05) !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-4"] .mockup-footer-wrap { 
    order: 13; 
    background: #ffffff !important;
    border-radius: 16px !important;
    padding: 14px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
    margin-top: 4px !important;
}

/* =========================================
   MODEL 5: CYBER TECH GLOW NO MOCKUP ADMIN
   ========================================= */
#lp_live_preview_container[data-model="model-5"] {
    display: flex !important;
    flex-direction: column !important;
    background-color: #0f172a !important; /* Fundo escuro */
    gap: 12px !important;
    padding: 12px !important;
}
#lp_live_preview_container[data-model="model-5"] > * {
    flex-shrink: 0 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-badge-wrap { 
    order: 1; 
    align-self: center !important;
    background: transparent !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 100px !important;
    padding: 0 !important;
    margin-bottom: 0 !important;
    box-shadow: none !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-badge-wrap span {
    background: var(--lp-primary, #9d174d) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 100px !important;
    padding: 4px 14px !important;
    box-shadow: 0 0 14px var(--lp-primary, #9d174d) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    font-weight: 900 !important;
    display: inline-block !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-title { 
    order: 2; 
    font-size: 18px !important;
    font-weight: 900 !important;
    letter-spacing: -0.5px !important;
    background: transparent !important;
    color: #f8fafc !important;
    padding: 0 4px !important;
    margin-bottom: 0 !important;
    text-align: center !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-subheadline { 
    order: 3; 
    font-size: 11px !important;
    color: #94a3b8 !important;
    background: transparent !important;
    padding: 0 8px !important;
    margin-bottom: 0 !important;
    text-align: center !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-gallery-wrapper { 
    order: 4; 
    width: 100% !important;
    height: 327px !important;
    min-height: 327px !important;
    border-radius: 20px !important;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.8) !important;
    margin-bottom: 0 !important;
    border: 1px solid #334155 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-gallery-thumbs { 
    order: 5; 
    margin-bottom: 0 !important; 
    background: #1e293b !important;
    padding: 8px 10px !important;
    border-radius: 16px !important;
    border: 1px solid #334155 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card { 
    order: 6; 
    background: linear-gradient(145deg, #1e293b, #0f172a) !important;
    border: 1px solid var(--lp-primary, #9d174d) !important;
    border-radius: 20px !important;
    padding: 18px 14px !important;
    box-shadow: 0 0 20px var(--lp-primary, #9d174d) !important;
    margin-bottom: 0 !important;
    position: relative !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card span[id="mockup_old_price"] {
    color: #64748b !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card span[id="mockup_current_price"] {
    color: var(--lp-primary, #9d174d) !important;
    text-shadow: 0 0 10px var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card span[id="mockup_savings_tag"] {
    background: var(--lp-primary, #9d174d) !important;
    color: #ffffff !important;
    border: 1px solid var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card p[id="mockup_savings_text"] {
    color: #94a3b8 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card p[id="mockup_savings_text"] span {
    background: var(--lp-primary, #9d174d) !important;
    box-shadow: 0 0 5px var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card > div.mockup-main-cta-btn {
    background: var(--lp-primary, #9d174d) !important;
    box-shadow: 0 4px 15px var(--lp-primary, #9d174d) !important;
    border: none !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-price-card > div:last-child {
    color: #94a3b8 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-features-wrapper {
    order: 7;
    background: transparent !important;
    border-radius: 0 !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 0 !important;
    box-shadow: none !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-features-wrapper > div {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-features-wrapper > div > div {
    background: #111827 !important;
    padding: 12px !important;
    border-radius: 12px !important;
    border: 1px solid #1f2937 !important;
    display: flex !important;
    align-items: center !important;
    text-align: left !important;
    gap: 10px !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-features-wrapper > div > div i {
    color: var(--lp-primary, #9d174d) !important;
    font-size: 18px !important;
    background: rgba(255, 255, 255, 0.05) !important;
    padding: 6px !important;
    border-radius: 8px !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-features-wrapper > div > div strong {
    color: #f8fafc !important;
    font-size: 11px !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-features-wrapper > div > div small {
    color: #94a3b8 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-guarantees-card {
    order: 8;
    background: #1e293b !important;
    border-radius: 16px !important;
    padding: 14px !important;
    border: 1px solid #334155 !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-guarantees-card h2 {
    color: #f8fafc !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-guarantees-card p {
    color: #cbd5e1 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-urgency-card { 
    order: 9; 
    border-radius: 16px !important;
    border: 1px solid var(--lp-primary, #9d174d) !important;
    background: rgba(255, 255, 255, 0.02) !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-urgency-card h3 {
    color: var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-urgency-card p {
    color: #f8fafc !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-urgency-card div[style*="background: #0f172a"] {
    background: var(--lp-primary, #9d174d) !important;
    color: #ffffff !important;
    box-shadow: 0 0 10px var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-faq-heading { 
    order: 10; 
    background: transparent !important;
    padding: 12px 0 !important;
    margin-bottom: 0 !important;
    text-align: center !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-faq-heading h2 {
    color: #f8fafc !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-faq-accordion { 
    order: 11; 
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-faq-accordion > div {
    border-radius: 12px !important;
    border: 1px solid #334155 !important;
    background: #1e293b !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-faq-accordion > div strong {
    color: #f1f5f9 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-faq-accordion > div p,
#lp_live_preview_container[data-model="model-5"] .mockup-faq-accordion > div span {
    color: #94a3b8 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-faq-accordion > div i {
    color: var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-closing-card { 
    order: 12; 
    border-radius: 20px !important;
    border: 1px solid var(--lp-primary, #9d174d) !important;
    background: linear-gradient(180deg, #1e293b, #0f172a) !important;
    box-shadow: 0 0 20px var(--lp-primary, #9d174d) !important;
    margin-bottom: 0 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-closing-card h2 {
    color: #f8fafc !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-closing-card p {
    color: #cbd5e1 !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-closing-card .mockup-closing-cta-btn {
    background: var(--lp-primary, #9d174d) !important;
    box-shadow: 0 4px 15px var(--lp-primary, #9d174d) !important;
}
#lp_live_preview_container[data-model="model-5"] .mockup-footer-wrap { 
    order: 13; 
    background: transparent !important;
    border-radius: 0 !important;
    padding: 14px 0 !important;
    margin-top: 4px !important;
    color: #64748b !important;
}

/* =========================================
   MOCKUP MODELO 6: EDITORIAL LUXURY
   ========================================= */
#lp_live_preview_container[data-model="model-6"] {
    background-color: #faf9f6 !important;
    background: #faf9f6 !important;
    color: #2c2c2c !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 16px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-badge-wrap {
    order: 1 !important;
    text-align: center !important;
    margin-bottom: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-badge-wrap span {
    background: transparent !important;
    border: 1px solid var(--lp-primary, #9d174d) !important;
    color: var(--lp-primary, #9d174d) !important;
    border-radius: 0 !important;
    text-transform: uppercase !important;
    letter-spacing: 1.5px !important;
    font-size: 8.5px !important;
    font-weight: 700 !important;
    padding: 4px 10px !important;
    box-shadow: none !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-title {
    order: 2 !important;
    font-family: 'Cormorant Garamond', serif, Georgia, 'Times New Roman' !important;
    font-size: 24px !important;
    font-weight: 600 !important;
    line-height: 1.15 !important;
    text-align: center !important;
    color: #1a1a1a !important;
    margin-bottom: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-subheadline {
    order: 3 !important;
    font-size: 11px !important;
    font-weight: 400 !important;
    text-align: center !important;
    color: #555555 !important;
    line-height: 1.5 !important;
    margin-bottom: 0 !important;
    padding: 0 4px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-gallery-wrapper {
    order: 4 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    margin-bottom: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-gallery-wrapper img {
    border-radius: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-gallery-thumbs {
    order: 5 !important;
    padding: 6px 0 !important;
    gap: 8px !important;
    justify-content: center !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-gallery-thumbs img,
#lp_live_preview_container[data-model="model-6"] .mockup-gallery-thumbs button {
    border-radius: 0 !important;
    border: 1px solid #e5e5e5 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-gallery-thumbs button:hover {
    border-color: var(--lp-primary, #9d174d) !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-price-card {
    order: 6 !important;
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
    border-top: 1px solid #e5e5e5 !important;
    border-bottom: 1px solid #e5e5e5 !important;
    border-radius: 0 !important;
    padding: 16px 0 !important;
    margin-bottom: 0 !important;
    text-align: center !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-price-card span[id="mockup_old_price"] {
    font-size: 12px !important;
    color: #888888 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-price-card span[id="mockup_current_price"] {
    font-family: 'Cormorant Garamond', serif, Georgia !important;
    font-size: 28px !important;
    font-weight: 500 !important;
    color: #1a1a1a !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-price-card span[id="mockup_savings_tag"] {
    background: #f4f4f4 !important;
    color: #333333 !important;
    border-radius: 0 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    font-size: 8.5px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-price-card p[id="mockup_savings_text"] {
    display: none !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-price-card .mockup-main-cta-btn {
    border-radius: 0 !important;
    background: var(--lp-primary, #9d174d) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    font-weight: 700 !important;
    font-size: 11px !important;
    box-shadow: none !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-features-wrapper {
    order: 7 !important;
    margin-bottom: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-features-wrapper > div {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-features-wrapper > div > div {
    background: transparent !important;
    border: 1px solid #e5e5e5 !important;
    border-radius: 0 !important;
    padding: 10px !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    text-align: left !important;
    gap: 10px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-features-wrapper > div > div i {
    color: var(--lp-primary, #9d174d) !important;
    margin-bottom: 0 !important;
    font-size: 16px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-features-wrapper > div > div strong {
    font-family: 'Cormorant Garamond', serif, Georgia !important;
    font-size: 14px !important;
    color: #1a1a1a !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-features-wrapper > div > div small {
    color: #666666 !important;
    font-size: 10px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-guarantees-card {
    order: 8 !important;
    background: transparent !important;
    color: #2c2c2c !important;
    border: 1px solid #e5e5e5 !important;
    border-radius: 0 !important;
    padding: 16px 14px !important;
    text-align: left !important;
    box-shadow: none !important;
    margin-bottom: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-guarantees-card div[style*="color: #334155"] {
    color: #2c2c2c !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-guarantees-card .mockup-guarantee-cta-btn {
    border-radius: 0 !important;
    background: var(--lp-primary, #9d174d) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    font-weight: 700 !important;
    box-shadow: none !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-urgency-card {
    order: 9 !important;
    background: transparent !important;
    border: 1px solid var(--lp-primary, #9d174d) !important;
    border-radius: 0 !important;
    padding: 16px 12px !important;
    text-align: center !important;
    margin-bottom: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-urgency-card h3 {
    font-family: 'Cormorant Garamond', serif, Georgia !important;
    font-size: 16px !important;
    color: #1a1a1a !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-urgency-card p {
    color: #555555 !important;
    font-size: 10.5px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-urgency-card div[style*="background: #0f172a"] {
    background: #f4f4f4 !important;
    color: #1a1a1a !important;
    border-radius: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-faq-heading {
    order: 10 !important;
    margin-bottom: 0 !important;
    padding: 8px 0 !important;
    text-align: center !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-faq-heading h2 {
    font-family: 'Cormorant Garamond', serif, Georgia !important;
    font-size: 18px !important;
    color: #1a1a1a !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-faq-accordion {
    order: 11 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-faq-accordion > div {
    background: transparent !important;
    border: none !important;
    border-bottom: 1px solid #e5e5e5 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    padding: 10px 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-faq-accordion > div strong {
    font-family: 'Cormorant Garamond', serif, Georgia !important;
    font-size: 13px !important;
    color: #1a1a1a !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-closing-card {
    order: 12 !important;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 20px 0 !important;
    text-align: center !important;
    margin-bottom: 0 !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-closing-card h2 {
    font-family: 'Cormorant Garamond', serif, Georgia !important;
    font-size: 20px !important;
    color: #1a1a1a !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-closing-card p {
    color: #555555 !important;
    font-size: 10.5px !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-closing-card .mockup-closing-cta-btn {
    border-radius: 0 !important;
    background: var(--lp-primary, #9d174d) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    font-weight: 700 !important;
    box-shadow: none !important;
}

#lp_mockup_frame[data-model="model-6"] .mockup-sticky-cta-btn {
    border-radius: 0 !important;
    background: var(--lp-primary, #9d174d) !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    font-weight: 700 !important;
    box-shadow: none !important;
}

#lp_live_preview_container[data-model="model-6"] .mockup-footer-wrap {
    order: 13 !important;
    background: transparent !important;
    border-top: 1px solid #e5e5e5 !important;
    border-radius: 0 !important;
    padding: 16px 0 !important;
    color: #888888 !important;
}
</style>
<?= $this->endSection() ?>