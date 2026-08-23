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

        <!-- Tabs Navigation -->
        <div class="settings-tabs">
            <button type="button" class="settings-tab active" data-tab-target="tab-info">Informações do Produto</button>
            <button type="button" class="settings-tab" data-tab-target="tab-images">Imagens</button>
            <button type="button" class="settings-tab" data-tab-target="tab-destination">Destino</button>
            <button type="button" class="settings-tab" data-tab-target="tab-landing">Landing Page</button>
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
            <div style="display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start;">
                
                <!-- Configurações da LP -->
                <section class="form-card-section" aria-labelledby="landing-page-title" style="margin: 0;">
                    <h2 id="landing-page-title" class="section-card-title">Estilo e Conteúdo da Landing Page</h2>

                    <div class="form-grid-account">
                        <!-- Layout -->
                        <div class="form-group col-full">
                            <label>Modelo de Layout</label>
                            <div class="micro-cards-grid">
                                <?php
                                $layouts = [
                                    'oferta_direta' => ['icon' => 'ti-layout-bottombar', 'label' => 'Oferta Direta']
                                ];
                                $currentLayout = old('layout', $product->layout ?? 'oferta_direta');
                                foreach ($layouts as $val => $info):
                                ?>
                                <label class="micro-card <?= $currentLayout === $val ? 'active' : '' ?>">
                                    <input type="radio" name="layout" value="<?= $val ?>" <?= $currentLayout === $val ? 'checked' : '' ?> data-lp-preview-trigger style="display: none;">
                                    <i class="ti <?= $info['icon'] ?>"></i>
                                    <span><?= $info['label'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Paleta -->
                        <div class="form-group col-full">
                            <label>Paleta de Cores</label>
                            <div class="micro-cards-grid">
                                <?php
                                $palettes = [
                                    'brasa' => ['color' => '#e11d48', 'label' => 'Brasa'],
                                    'menta' => ['color' => '#10b981', 'label' => 'Menta'],
                                    'noturno' => ['color' => '#6366f1', 'label' => 'Noturno'],
                                    'aurora' => ['color' => '#a855f7', 'label' => 'Aurora'],
                                    'areia' => ['color' => '#f59e0b', 'label' => 'Areia'],
                                    'jade' => ['color' => '#14b8a6', 'label' => 'Jade']
                                ];
                                $currentPalette = old('color_palette', $product->color_palette ?? 'brasa');
                                foreach ($palettes as $val => $info):
                                ?>
                                <label class="micro-card <?= $currentPalette === $val ? 'active' : '' ?>">
                                    <input type="radio" name="color_palette" value="<?= $val ?>" <?= $currentPalette === $val ? 'checked' : '' ?> data-lp-preview-trigger style="display: none;">
                                    <span class="color-swatch" style="background-color: <?= $info['color'] ?>;"></span>
                                    <span><?= $info['label'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Animação Background -->
                        <div class="form-group col-full">
                            <label>Animação do Background</label>
                            <div class="micro-cards-grid">
                                <?php
                                $bgAnims = [
                                    'none' => ['icon' => 'ti-ban', 'label' => 'Sem animação'],
                                    'particles' => ['icon' => 'ti-sparkles', 'label' => 'Partículas'],
                                    'waves' => ['icon' => 'ti-ripple', 'label' => 'Ondas'],
                                    'gradient' => ['icon' => 'ti-palette', 'label' => 'Gradiente'],
                                    'grid' => ['icon' => 'ti-grid-dots', 'label' => 'Grid'],
                                    'stars' => ['icon' => 'ti-stars', 'label' => 'Estrelas']
                                ];
                                $currentBgAnim = old('bg_animation', $product->bg_animation ?? 'none');
                                foreach ($bgAnims as $val => $info):
                                ?>
                                <label class="micro-card <?= $currentBgAnim === $val ? 'active' : '' ?>">
                                    <input type="radio" name="bg_animation" value="<?= $val ?>" <?= $currentBgAnim === $val ? 'checked' : '' ?> data-lp-preview-trigger style="display: none;">
                                    <i class="ti <?= $info['icon'] ?>"></i>
                                    <span><?= $info['label'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Animação Botões -->
                        <div class="form-group col-full">
                            <label>Animação dos Botões</label>
                            <div class="micro-cards-grid">
                                <?php
                                $btnAnims = [
                                    'pulse' => ['icon' => 'ti-activity', 'label' => 'Pulsar'],
                                    'shake' => ['icon' => 'ti-bell-ringing', 'label' => 'Tremer'],
                                    'glow' => ['icon' => 'ti-bulb', 'label' => 'Brilho'],
                                    'bounce' => ['icon' => 'ti-arrow-bounce', 'label' => 'Pular'],
                                    'slide' => ['icon' => 'ti-chevrons-right', 'label' => 'Deslizar'],
                                    'none' => ['icon' => 'ti-ban', 'label' => 'Estático']
                                ];
                                $currentBtnAnim = old('btn_animation', $product->btn_animation ?? 'pulse');
                                foreach ($btnAnims as $val => $info):
                                ?>
                                <label class="micro-card <?= $currentBtnAnim === $val ? 'active' : '' ?>">
                                    <input type="radio" name="btn_animation" value="<?= $val ?>" <?= $currentBtnAnim === $val ? 'checked' : '' ?> data-lp-preview-trigger style="display: none;">
                                    <i class="ti <?= $info['icon'] ?>"></i>
                                    <span><?= $info['label'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Headline -->
                        <div class="form-group col-full">
                            <label for="headline">Headline Principal (Título de Impacto)</label>
                            <input type="text" id="headline" name="headline" class="form-control" value="<?= esc(old('headline', $product->headline ?? '')) ?>" placeholder="Ex: Sinta o poder de uma fragrância inesquecível" data-lp-preview-trigger>
                        </div>

                        <!-- Subheadline -->
                        <div class="form-group col-full">
                            <label for="subheadline">Subheadline (Subtítulo explicativo)</label>
                            <input type="text" id="subheadline" name="subheadline" class="form-control" value="<?= esc(old('subheadline', $product->subheadline ?? '')) ?>" placeholder="Ex: Perfume 100% original importado com fixação de até 12 horas." data-lp-preview-trigger>
                        </div>

                        <!-- Botão CTA -->
                        <div class="form-group col-full">
                            <label for="button_text">Texto do Botão (CTA)</label>
                            <input type="text" id="button_text" name="button_text" class="form-control" value="<?= esc(old('button_text', $product->button_text ?? 'Garantir meu exemplar no WhatsApp')) ?>" data-lp-preview-trigger>
                        </div>
                    </div>
                </section>

                <!-- Preview Mobile -->
                <div class="lp-mobile-preview-container" style="position: sticky; top: 24px;">
                    <div class="mobile-device-frame" style="width: 320px; height: 640px; background: #000; border-radius: 36px; border: 8px solid #1e2235; overflow: hidden; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
                        <!-- Notch -->
                        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 120px; height: 24px; background: #1e2235; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; z-index: 10;"></div>
                        
                        <!-- Iframe for preview -->
                        <iframe id="lp-preview-frame" src="about:blank" style="width: 100%; height: 100%; border: none; background: #fff;"></iframe>
                        
                        <!-- Loading overlay -->
                        <div id="lp-preview-loader" style="position: absolute; inset: 0; background: rgba(15, 18, 29, 0.8); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; z-index: 5; backdrop-filter: blur(4px);">
                            <i class="ti ti-loader" style="font-size: 32px; animation: spin 1s linear infinite; margin-bottom: 12px;"></i>
                            <span style="font-size: 13px; font-weight: 600;">Atualizando preview...</span>
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 12px; color: #64748b; font-size: 12px; font-weight: 600;">
                        <i class="ti ti-device-mobile"></i> Preview em tempo real
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating save bar -->
        <div class="save-bar" data-floating-save-bar hidden>
            <p>
                <strong>Existem alterações não salvas</strong>
                <span>Clique em salvar para aplicar as modificações no produto.</span>
            </p>
            <div class="save-actions">
                <a href="<?= site_url('produtos') ?>" class="button secondary">Cancelar</a>
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
    const saveBar = document.querySelector('[data-floating-save-bar]');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    // Tab Navigation Logic
    const tabBtns = document.querySelectorAll('.settings-tab');
    const tabContents = document.querySelectorAll('.settings-tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.dataset.tabTarget;
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

    // Landing Page Live Preview Logic
    const previewTriggers = document.querySelectorAll('[data-lp-preview-trigger], #name, #price, #promotional_price, #description');
    const previewFrame = document.getElementById('lp-preview-frame');
    const previewLoader = document.getElementById('lp-preview-loader');

    function updateLpPreview() {
        if (!previewFrame) return;
        if (previewLoader) previewLoader.style.display = 'flex';

        const name = document.getElementById('name')?.value || 'Nome do Produto';
        const price = document.getElementById('price')?.value || '0.00';
        const promoPrice = document.getElementById('promotional_price')?.value || '';
        const headline = document.getElementById('headline')?.value || 'Sinta a exclusividade';
        const subheadline = document.getElementById('subheadline')?.value || 'Fragrância marcante e duradoura.';
        const btnText = document.getElementById('button_text')?.value || 'Comprar no WhatsApp';
        const layout = document.querySelector('input[name="layout"]:checked')?.value || 'oferta_direta';
        const palette = document.querySelector('input[name="color_palette"]:checked')?.value || 'brasa';
        const bgAnim = document.querySelector('input[name="bg_animation"]:checked')?.value || 'none';
        const btnAnim = document.querySelector('input[name="btn_animation"]:checked')?.value || 'pulse';

        // Pega a imagem de capa (se houver)
        const coverImgEl = document.querySelector('.image-preview-item .cover-badge')?.closest('.image-preview-item')?.querySelector('img');
        const firstImgEl = document.querySelector('.image-preview-item img');
        const imgUrl = coverImgEl ? coverImgEl.src : (firstImgEl ? firstImgEl.src : null);
        const imgHtml = imgUrl ? `<img src="${imgUrl}" style="width: 100%; height: 100%; object-fit: cover;">` : `<i class="ti ti-photo" style="font-size: 32px;"></i>`;

        // Cores de acordo com paleta
        const palettes = {
            brasa: { bg: '#0f0c0c', primary: '#e11d48', card: '#1c1516', text: '#fff' },
            menta: { bg: '#0c1612', primary: '#10b981', card: '#13231d', text: '#fff' },
            noturno: { bg: '#0b0f19', primary: '#6366f1', card: '#111827', text: '#fff' },
            aurora: { bg: '#130c1e', primary: '#a855f7', card: '#1e1430', text: '#fff' },
            areia: { bg: '#171411', primary: '#f59e0b', card: '#241f1a', text: '#fff' },
            jade: { bg: '#091514', primary: '#14b8a6', card: '#0f2422', text: '#fff' }
        };

        const currentPal = palettes[palette] || palettes.brasa;

        // Layout specific styles
        let layoutStyles = '';
        let layoutHtml = '';

        if (layout === 'oferta_direta') {
            layoutStyles = `
                body {
                    background: #000;
                    color: #fff;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    padding: 20px 16px 32px 16px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
                .hero {
                    width: 100%;
                    max-width: 400px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                }
                .top-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    background: transparent;
                    border: 1px solid rgba(255, 255, 255, 0.25);
                    border-radius: 100px;
                    padding: 6px 16px;
                    font-size: 13px;
                    font-weight: 600;
                    color: #fff;
                    margin-bottom: 20px;
                }
                .product-title {
                    font-size: 32px;
                    font-weight: 800;
                    line-height: 1.15;
                    letter-spacing: -0.5px;
                    margin-bottom: 12px;
                    color: #fff;
                }
                .product-title span {
                    color: \${currentPal.primary};
                }
                .product-desc {
                    font-size: 15px;
                    line-height: 1.5;
                    color: #94a3b8;
                    margin-bottom: 24px;
                    padding: 0 8px;
                }
                .product-image-container {
                    width: 100%;
                    max-width: 320px;
                    aspect-ratio: 1;
                    margin-bottom: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .product-image-container img {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    filter: drop-shadow(0 20px 30px rgba(0,0,0,0.8));
                }
                .features-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 8px;
                    width: 100%;
                    margin-bottom: 20px;
                }
                .feature-card {
                    background: rgba(255, 255, 255, 0.03);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 16px;
                    padding: 12px 6px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 6px;
                }
                .feature-card i {
                    font-size: 22px;
                    color: \${currentPal.primary};
                }
                .feature-card span {
                    font-size: 11px;
                    font-weight: 500;
                    color: #e2e8f0;
                    line-height: 1.2;
                }
                .price-card {
                    background: #0a0e17;
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    border-radius: 20px;
                    padding: 24px 16px;
                    width: 100%;
                    margin-bottom: 20px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
                .installment-text {
                    font-size: 14px;
                    font-weight: 700;
                    color: \${currentPal.primary};
                    margin-bottom: 4px;
                }
                .installment-value {
                    display: flex;
                    align-items: baseline;
                    gap: 4px;
                    margin-bottom: 8px;
                }
                .currency {
                    font-size: 24px;
                    font-weight: 800;
                    color: \${currentPal.primary};
                }
                .amount {
                    font-size: 52px;
                    font-weight: 900;
                    color: #fff;
                    letter-spacing: -2px;
                    line-height: 1;
                }
                .cash-price {
                    font-size: 14px;
                    color: #94a3b8;
                }
                .cash-price strong {
                    color: \${currentPal.primary};
                }
                .cta-button {
                    background: \${currentPal.primary};
                    color: #000;
                    font-weight: 800;
                    font-size: 16px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    border-radius: 14px;
                    padding: 18px 24px;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    text-decoration: none;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
                    transition: transform 0.2s ease;
                }
                .cta-left {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin: 0 auto;
                }
                .cta-left i {
                    font-size: 20px;
                }
                .trust-badges {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 16px;
                    margin-top: 24px;
                    font-size: 12px;
                    color: #94a3b8;
                    font-weight: 500;
                }
                .trust-item {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                .trust-dot {
                    width: 4px;
                    height: 4px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.2);
                }
            `;

            let displayPrice = promoPrice || price;
            let numPrice = parseFloat(displayPrice.replace(/\./g, '').replace(',', '.')) || 0;
            let installmentVal = (numPrice / 12).toFixed(2).replace('.', ',');

            layoutHtml = `
                <div class="hero">
                    <div class="top-badge">
                        <i class="ti ti-truck-delivery"></i>
                        <span>Frete grátis hoje</span>
                    </div>

                    <h1 class="product-title">${headline}</h1>
                    <p class="product-desc">${subheadline}</p>

                    <div class="product-image-container">
                        ${imgHtml}
                    </div>

                    <div class="features-grid">
                        <div class="feature-card">
                            <i class="ti ti-heart-rate-monitor"></i>
                            <span>Monitor<br>cardíaco</span>
                        </div>
                        <div class="feature-card">
                            <i class="ti ti-battery-4"></i>
                            <span>Bateria<br>longa</span>
                        </div>
                        <div class="feature-card">
                            <i class="ti ti-droplet"></i>
                            <span>Resistente<br>à água</span>
                        </div>
                    </div>

                    <div class="price-card">
                        <div class="installment-text">12x de</div>
                        <div class="installment-value">
                            <span class="currency">R$</span>
                            <span class="amount">${installmentVal}</span>
                        </div>
                        <div class="cash-price">
                            ou <strong>R$ ${displayPrice}</strong> à vista
                        </div>
                    </div>

                    <a href="#" class="cta-button anim-${btnAnim}">
                        <div class="cta-left">
                            <i class="ti ti-shopping-bag"></i>
                            <span>${btnText}</span>
                        </div>
                        <i class="ti ti-chevron-right"></i>
                    </a>

                    <div class="trust-badges">
                        <div class="trust-item">
                            <i class="ti ti-lock"></i>
                            <span>Pagamento seguro</span>
                        </div>
                        <div class="trust-dot"></div>
                        <div class="trust-item">
                            <i class="ti ti-truck"></i>
                            <span>Envio para todo Brasil</span>
                        </div>
                    </div>
                </div>
            `;
        }

        const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
                <style>
                    * { box-sizing: border-box; margin: 0; padding: 0; }
                    body { 
                        font-family: system-ui, -apple-system, sans-serif; 
                        background: \${currentPal.bg}; 
                        color: \${currentPal.text};
                        min-height: 100vh;
                        display: flex;
                        flex-direction: column;
                        padding: 16px;
                    }
                    .hero {
                        display: flex;
                        flex-direction: column;
                        width: 100%;
                        max-width: 400px;
                        margin: 0 auto;
                    }
                    .badge {
                        display: inline-block;
                        padding: 4px 12px;
                        background: \${currentPal.primary}20;
                        color: \${currentPal.primary};
                        border-radius: 100px;
                        font-size: 12px;
                        font-weight: 600;
                        margin-bottom: 16px;
                        align-self: flex-start;
                    }
                    .product-img {
                        background: \${currentPal.card};
                        border-radius: 16px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: \${currentPal.text}40;
                        border: 1px solid rgba(255,255,255,0.05);
                    }
                    h1 {
                        font-size: 24px;
                        font-weight: 700;
                        line-height: 1.2;
                        margin-bottom: 8px;
                    }
                    .sub {
                        font-size: 15px;
                        color: \${currentPal.text}99;
                        line-height: 1.5;
                    }
                    .price-box {
                        display: flex;
                        align-items: baseline;
                        gap: 8px;
                        margin: 24px 0;
                    }
                    .old-price {
                        font-size: 14px;
                        color: \${currentPal.text}60;
                        text-decoration: line-through;
                    }
                    .cur-price {
                        font-size: 24px;
                        font-weight: 700;
                        color: \${currentPal.primary};
                    }
                    .btn-cta {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        background: \${currentPal.primary};
                        color: #fff;
                        text-decoration: none;
                        padding: 16px 24px;
                        border-radius: 12px;
                        font-weight: 600;
                        font-size: 16px;
                        width: 100%;
                        transition: all 0.3s ease;
                    }
                    
                    /* Animações do botão */
                    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
                    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
                    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
                    
                    .anim-pulse { animation: pulse 2s infinite; }
                    .anim-shake { animation: shake 3s infinite; }
                    .anim-bounce { animation: bounce 2s infinite; }
                    
                    ${layoutStyles}
                </style>
            </head>
            <body>
                ${layoutHtml}
            </body>
            </html>
        `;
        previewFrame.srcdoc = html;
        setTimeout(() => {
            if (previewLoader) previewLoader.style.display = 'none';
        }, 200);
    }

    let previewTimer;
    previewTriggers.forEach(el => {
        el.addEventListener('input', function() {
            clearTimeout(previewTimer);
            previewTimer = setTimeout(updateLpPreview, 300);
        });
        el.addEventListener('change', function() {
            if (this.type === 'radio') {
                // Atualiza a classe active no micro-card
                const group = this.closest('.micro-cards-grid');
                if (group) {
                    group.querySelectorAll('.micro-card').forEach(card => card.classList.remove('active'));
                    this.closest('.micro-card').classList.add('active');
                }
            }
            updateLpPreview();
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

    // Auto slug generator if typing name (both create and edit)
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
        });
    }

    // Dirty state management
    if (form && saveBar) {
        let isDirty = false;
        const initialData = new FormData(form);
        const initialEntries = Array.from(initialData.entries());

        function checkDirty() {
            const currentData = new FormData(form);
            const currentEntries = Array.from(currentData.entries());

            let dirty = false;
            if (currentEntries.length !== initialEntries.length) {
                dirty = true;
            } else {
                for (let i = 0; i < currentEntries.length; i++) {
                    if (currentEntries[i][0] !== initialEntries[i][0] || currentEntries[i][1] !== initialEntries[i][1]) {
                        dirty = true;
                        break;
                    }
                }
            }

            if (dirty !== isDirty) {
                isDirty = dirty;
                saveBar.hidden = !isDirty;
            }
        }

        form.addEventListener('input', checkDirty);
        form.addEventListener('change', checkDirty);
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
</script>
<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
</style>
<?= $this->endSection() ?>