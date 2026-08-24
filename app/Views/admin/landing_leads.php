<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="landing-page-settings-root" data-settings-root>
    <div class="setting-intro">
        <div class="setting-intro-header">
            <div>
                <span class="setting-intro-badge"><i class="ti ti-device-mobile"></i> Mobile First & Conversão</span>
                <h2>Landing Page de Leads</h2>
                <p>Personalize todos os textos, promessas, benefícios e links da página de captura de clientes para o Grupo VIP no WhatsApp. Acompanhe o resultado em tempo real no mockup ao lado.</p>
            </div>
            <div class="setting-intro-actions">
                <a href="<?= site_url('leads') ?>" target="_blank" class="button outline" style="text-decoration:none;"><i class="ti ti-external-link" aria-hidden="true"></i>Ver Página Pública</a>
            </div>
        </div>
    </div>

    <?php
    $lp = $landingLeadSetting ?? [
        'template_model' => 'model-1',
        'color_palette' => 'palette-aurora',
        'bg_animation' => 'bg-particles',
        'btn_animation' => 'btn-pulse',
        'seo_title' => 'Grupo VIP Dias Imports | Ofertas e Descontos Exclusivos',
        'seo_description' => 'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.',
        'headline' => 'Garanta Descontos Secretos e Ofertas VIP no WhatsApp',
        'subheadline' => 'Participe do nosso grupo restrito de clientes e receba oportunidades imperdíveis de importados em primeira mão, com preços que não publicamos abertamente.',
        'badge_text' => '🔥 GRUPO VIP EXCLUSIVO • VAGAS LIMITADAS',
        'button_text' => 'QUERO MEU ACESSO VIP AGORA',
        'button_subtext' => '🔒 Acesso 100% gratuito e sem spam',
        'whatsapp_group_link' => 'https://chat.whatsapp.com/',
        'card1_icon' => 'ti-discount-check',
        'card1_title' => 'Até 50% de Desconto Real',
        'card1_desc' => 'Preços exclusivos de atacado e varejo direto para membros do grupo.',
        'card2_icon' => 'ti-flame',
        'card2_title' => 'Ofertas Relâmpago e Primeira Mão',
        'card2_desc' => 'Novidades e lançamentos liberados no grupo antes de todo mundo.',
        'card3_icon' => 'ti-shield-lock',
        'card3_title' => '100% Original e com Garantia',
        'card3_desc' => 'Importados com nota fiscal, procedência garantida e suporte humanizado.',
        'modal_title' => '🎉 Parabéns! Seu Acesso VIP Está Liberado',
        'modal_desc' => 'Seu cadastro foi realizado com sucesso. Clique no botão abaixo para entrar agora no Grupo VIP Oficial no WhatsApp e não perder nenhuma oportunidade.',
        'modal_button_text' => 'ENTRAR NO GRUPO VIP DO WHATSAPP',
    ];
    ?>

    <form action="<?= site_url('landing-leads') ?>" method="post" data-landing-form>
        <?= csrf_field() ?>
        <div class="landing-split-layout">
            <!-- Coluna da Esquerda: Configurações -->
            <div class="landing-config-col">
                <!-- SELETOR DE MODELO VISUAL (6 MODELOS) -->
                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(99, 91, 255, 0.15); color: #635bff;"><i class="ti ti-template"></i></div>
                        <div>
                            <h3 class="settings-section-title">Modelo Visual da Landing Page (6 Opções)</h3>
                            <p class="settings-section-subtitle">Selecione a estrutura e experiência visual da página. O mockup ao lado atualiza em tempo real.</p>
                        </div>
                    </div>

                    <div class="template-models-grid">
                        <?php
                        $modelsList = [
                            ['id' => 'model-1', 'name' => 'Hero Direct & Glass', 'desc' => 'Form no topo com glassmorphism e foco total na conversão imediata.', 'icon' => 'ti-layout-topbar'],
                            ['id' => 'model-2', 'name' => 'Benefits First', 'desc' => 'Benefícios e prova de valor antes do form para gerar mais desejo.', 'icon' => 'ti-layout-list'],
                            ['id' => 'model-3', 'name' => 'Minimal Compact', 'desc' => 'Pílulas arredondadas, direto ao ponto e otimizado para 1 polegar.', 'icon' => 'ti-pill'],
                            ['id' => 'model-4', 'name' => 'Bento Grid', 'desc' => 'Layout moderno em blocos assimétricos estilo Bento Box.', 'icon' => 'ti-layout-grid'],
                            ['id' => 'model-5', 'name' => 'Cyber Tech Neon', 'desc' => 'Bordas tracejadas neon, visual escuro e tipografia futurista.', 'icon' => 'ti-bolt'],
                            ['id' => 'model-6', 'name' => 'Editorial Luxury', 'desc' => 'Estilo premium e sofisticado para produtos de alto padrão.', 'icon' => 'ti-crown'],
                        ];
                        $currentModel = $lp['template_model'] ?? 'model-1';
                        ?>
                        <?php foreach ($modelsList as $m): ?>
                            <label class="template-model-card <?= $currentModel === $m['id'] ? 'active' : '' ?>">
                                <input type="radio" name="template_model" value="<?= esc($m['id']) ?>" <?= $currentModel === $m['id'] ? 'checked' : '' ?> data-lp-model-radio>
                                <div class="template-card-inner">
                                    <div class="template-card-icon"><i class="ti <?= esc($m['icon']) ?>"></i></div>
                                    <div class="template-card-info">
                                        <strong><?= esc($m['name']) ?></strong>
                                        <p><?= esc($m['desc']) ?></p>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SELETOR DE PALETA DE CORES (6 PALETAS) -->
                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(236, 72, 153, 0.15); color: #ec4899;"><i class="ti ti-palette"></i></div>
                        <div>
                            <h3 class="settings-section-title">Paleta de Cores & Efeitos (6 Opções)</h3>
                            <p class="settings-section-subtitle">Alterne as cores e vibração de contraste da página de captura instantaneamente.</p>
                        </div>
                    </div>

                    <div class="color-palettes-grid">
                        <?php
                        $palettesList = [
                            ['id' => 'palette-aurora', 'name' => 'Aurora Neon', 'colors' => ['#635bff', '#ec4899'], 'desc' => 'Violeta & Rosa'],
                            ['id' => 'palette-emerald', 'name' => 'Emerald Tech', 'colors' => ['#10b981', '#059669'], 'desc' => 'Verde WhatsApp VIP'],
                            ['id' => 'palette-amber', 'name' => 'Amber Gold', 'colors' => ['#f59e0b', '#ea580c'], 'desc' => 'Ouro & Luxo'],
                            ['id' => 'palette-ocean', 'name' => 'Ocean Cyan', 'colors' => ['#0ea5e9', '#2563eb'], 'desc' => 'Azul & Ciano Tech'],
                            ['id' => 'palette-crimson', 'name' => 'Crimson Ruby', 'colors' => ['#f43f5e', '#be123c'], 'desc' => 'Vermelho Urgência'],
                            ['id' => 'palette-obsidian', 'name' => 'Obsidian Minimal', 'colors' => ['#ffffff', '#71717a'], 'desc' => 'Preto Puro & Titânio'],
                        ];
                        $currentPalette = $lp['color_palette'] ?? 'palette-aurora';
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

                <!-- SELETOR DE ANIMAÇÃO DO BACKGROUND (6 MODELOS) -->
                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;"><i class="ti ti-sparkles"></i></div>
                        <div>
                            <h3 class="settings-section-title">Animação de Fundo (6 Modelos de Background)</h3>
                            <p class="settings-section-subtitle">Escolha o efeito dinâmico de partículas, luzes e fluidos do background.</p>
                        </div>
                    </div>

                    <div class="template-models-grid">
                        <?php
                        $bgList = [
                            ['id' => 'bg-particles', 'name' => 'Partículas Dinâmicas', 'desc' => 'Pontos flutuantes interativos conectados com linhas de luz.', 'icon' => 'ti-dots'],
                            ['id' => 'bg-mesh-gradient', 'name' => 'Mesh Gradient Fluido', 'desc' => 'Gradientes líquidos animados com iluminação suave em ondas.', 'icon' => 'ti-blur'],
                            ['id' => 'bg-cyber-grid', 'name' => 'Grade Cibernética 3D', 'desc' => 'Perspectiva de grid futurista iluminado com pulso de neon.', 'icon' => 'ti-grid-dots'],
                            ['id' => 'bg-radial-pulse', 'name' => 'Pulso Radial / Glow', 'desc' => 'Círculos concêntricos pulsantes que atraem foco para o centro.', 'icon' => 'ti-antenna-bars-5'],
                            ['id' => 'bg-floating-shapes', 'name' => 'Formas Geométricas', 'desc' => 'Esferas e prismas tridimensionais com rotação contínua suave.', 'icon' => 'ti-shapes'],
                            ['id' => 'bg-minimal-static', 'name' => 'Minimalista Escuro', 'desc' => 'Fundo escuro profundo com sutil vinheta para máxima legibilidade.', 'icon' => 'ti-moon'],
                        ];
                        $currentBg = $lp['bg_animation'] ?? 'bg-particles';
                        ?>
                        <?php foreach ($bgList as $bg): ?>
                            <label class="template-model-card <?= $currentBg === $bg['id'] ? 'active' : '' ?>">
                                <input type="radio" name="bg_animation" value="<?= esc($bg['id']) ?>" <?= $currentBg === $bg['id'] ? 'checked' : '' ?> data-lp-bg-radio>
                                <div class="template-card-inner">
                                    <div class="template-card-icon"><i class="ti <?= esc($bg['icon']) ?>"></i></div>
                                    <div class="template-card-info">
                                        <strong><?= esc($bg['name']) ?></strong>
                                        <p><?= esc($bg['desc']) ?></p>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SELETOR DE ANIMAÇÃO DO BOTÃO CTA (6 MODELOS) -->
                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;"><i class="ti ti-pointer"></i></div>
                        <div>
                            <h3 class="settings-section-title">Animação do Botão de Ação CTA (6 Opções)</h3>
                            <p class="settings-section-subtitle">Efeito contínuo no botão principal para aumentar a taxa de cliques e conversão.</p>
                        </div>
                    </div>

                    <div class="template-models-grid">
                        <?php
                        $btnList = [
                            ['id' => 'btn-pulse', 'name' => 'Pulso / Batimento VIP', 'desc' => 'Escala suave periódica chamando atenção sutil.', 'icon' => 'ti-heartbeat'],
                            ['id' => 'btn-shimmer', 'name' => 'Brilho Metálico Shimmer', 'desc' => 'Feixe de luz que varre a superfície do botão ciclicamente.', 'icon' => 'ti-sun'],
                            ['id' => 'btn-shake', 'name' => 'Vibração / Shake de Alerta', 'desc' => 'Pequena oscilação de alta energia em intervalos regulares.', 'icon' => 'ti-arrows-shuffle'],
                            ['id' => 'btn-bounce', 'name' => 'Bounce / Salto Suave', 'desc' => 'Salto vertical discreto simulando gravidade.', 'icon' => 'ti-arrow-up-circle'],
                            ['id' => 'btn-glow-expand', 'name' => 'Glow Expand / Onda de Choque', 'desc' => 'Aura de luz neon que se expande e dissipa externamente.', 'icon' => 'ti-ripple'],
                            ['id' => 'btn-none', 'name' => 'Estático Premium', 'desc' => 'Sem animação contínua, mantendo apenas o efeito de hover.', 'icon' => 'ti-player-pause'],
                        ];
                        $currentBtn = $lp['btn_animation'] ?? 'btn-pulse';
                        ?>
                        <?php foreach ($btnList as $b): ?>
                            <label class="template-model-card <?= $currentBtn === $b['id'] ? 'active' : '' ?>">
                                <input type="radio" name="btn_animation" value="<?= esc($b['id']) ?>" <?= $currentBtn === $b['id'] ? 'checked' : '' ?> data-lp-btn-radio>
                                <div class="template-card-inner">
                                    <div class="template-card-icon"><i class="ti <?= esc($b['icon']) ?>"></i></div>
                                    <div class="template-card-info">
                                        <strong><?= esc($b['name']) ?></strong>
                                        <p><?= esc($b['desc']) ?></p>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- OTIMIZAÇÃO DE BUSCA E COMPARTILHAMENTO (SEO & OPEN GRAPH) -->
                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(34, 197, 94, 0.15); color: #22c55e;"><i class="ti ti-brand-google"></i></div>
                        <div>
                            <h3 class="settings-section-title">Otimização de Busca & Compartilhamento (SEO & Open Graph)</h3>
                            <p class="settings-section-subtitle">Títulos, descrições e imagem de pré-visualização que aparecem no Google, WhatsApp, Facebook e Instagram ao compartilhar o link.</p>
                        </div>
                    </div>

                    <div class="form-grid single">
                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Título da Página / Aba do Navegador (SEO Title)</span>
                                <small class="field-hint">Ideal até 60 caracteres</small>
                            </span>
                            <input type="text" name="seo_title" value="<?= esc($lp['seo_title'] ?? '') ?>" maxlength="100" placeholder="Ex: Grupo VIP Dias Imports | Ofertas e Descontos Exclusivos" data-lp-input="seo-title">
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Descrição para Google e Redes Sociais (Meta Description)</span>
                                <small class="field-hint">Ideal entre 120 e 160 caracteres</small>
                            </span>
                            <textarea name="seo_description" rows="3" maxlength="255" placeholder="Descreva de forma atraente para incentivar o clique no Google ou ao receber o link no WhatsApp..." data-lp-input="seo-desc"><?= esc($lp['seo_description'] ?? '') ?></textarea>
                        </label>

                        <!-- IMAGEM DE COMPARTILHAMENTO (OPEN GRAPH / SOCIAL SHARE) -->
                        <div class="form-field" style="margin-top: 10px;">
                            <span class="field-label-row">
                                <span>Imagem de Compartilhamento (Open Graph / Banner Social)</span>
                                <small class="field-hint">Exibida em cartões no WhatsApp, Facebook e Twitter</small>
                            </span>
                            
                            <div class="seo-image-upload-wrapper" style="display: flex; gap: 20px; align-items: flex-start; margin-top: 10px; flex-wrap: wrap;">
                                <div class="seo-image-preview-container" style="position: relative; width: 140px; height: 140px; border-radius: 12px; border: 2px dashed rgba(255, 255, 255, 0.15); background: rgba(0, 0, 0, 0.2); overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <?php $hasSeoImg = !empty($lp['seo_image']) && is_file(FCPATH . ltrim($lp['seo_image'], '/\\')); ?>
                                    <img id="seo_image_preview" src="<?= $hasSeoImg ? base_url($lp['seo_image']) : '' ?>" alt="Prévia da Imagem de Compartilhamento" style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; z-index: 2; <?= $hasSeoImg ? '' : 'display: none;' ?>">
                                    <div id="seo_image_placeholder" style="<?= $hasSeoImg ? 'display: none;' : '' ?> width: 100%; height: 100%; background: linear-gradient(135deg, #635bff 0%, #a855f7 50%, #ec4899 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 48px; font-weight: 900; letter-spacing: -2px; position: absolute; inset: 0; z-index: 1;">
                                        DI
                                    </div>
                                </div>
                                <div class="seo-image-controls" style="flex: 1; min-width: 200px;">
                                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Selecione uma imagem atraente para o banner de compartilhamento social. Ela será recortada no formato quadrado (1:1).</p>
                                    <input type="file" id="seo_image_input" accept="image/png, image/jpeg, image/webp" style="display: none;">
                                    <input type="hidden" name="seo_image_base64" id="seo_image_base64">
                                    <input type="hidden" name="seo_image_action" id="seo_image_action" value="keep">
                                    
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <button type="button" class="button outline compact" id="btn_upload_seo_image">
                                            <i class="ti ti-upload"></i> Escolher e Recortar Imagem
                                        </button>
                                        <button type="button" class="button secondary compact danger" id="btn_remove_seo_image" style="<?= $hasSeoImg ? '' : 'display: none;' ?>">
                                            <i class="ti ti-trash"></i> Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prévia de Compartilhamento no WhatsApp -->
                        <div class="share-preview-card">
                            <span class="share-preview-badge"><i class="ti ti-brand-whatsapp"></i> Prévia de como aparece no WhatsApp ao enviar o link:</span>
                            <div class="share-preview-box">
                                <div class="share-preview-img" style="position: relative;">
                                    <img id="og_card_preview_img" src="<?= $hasSeoImg ? base_url($lp['seo_image']) : '' ?>" alt="Card Preview" style="<?= $hasSeoImg ? '' : 'display: none;' ?> width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; z-index: 2;">
                                    <div id="og_card_preview_placeholder" style="<?= $hasSeoImg ? 'display: none;' : '' ?> width: 100%; height: 100%; background: linear-gradient(135deg, #635bff 0%, #a855f7 50%, #ec4899 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; font-weight: 900; letter-spacing: -1px; position: absolute; inset: 0; z-index: 1;">
                                        DI
                                    </div>
                                </div>
                                <div class="share-preview-content">
                                    <span class="share-preview-domain"><?= parse_url(base_url(), PHP_URL_HOST) ?: 'diasimports.com.br' ?></span>
                                    <strong class="share-preview-title" data-share-preview-title><?= esc(!empty($lp['seo_title']) ? $lp['seo_title'] : ($lp['headline'] ?? 'Grupo VIP Dias Imports')) ?></strong>
                                    <p class="share-preview-desc" data-share-preview-desc><?= esc(!empty($lp['seo_description']) ? $lp['seo_description'] : ($lp['subheadline'] ?? 'Receba oportunidades imperdíveis de importados em primeira mão.')) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon"><i class="ti ti-flame"></i></div>
                        <div>
                            <h3 class="settings-section-title">Dobra Principal & Promessa Irresistível</h3>
                            <p class="settings-section-subtitle">Textos que causam o primeiro impacto visual e prendem a atenção do visitante no celular.</p>
                        </div>
                    </div>
                    
                    <div class="form-grid single">
                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Badge de Urgência / Escassez (Topo)</span>
                                <small class="field-hint">Ex: 🔥 Vagas Limitadas</small>
                            </span>
                            <input type="text" name="badge_text" value="<?= esc($lp['badge_text']) ?>" required maxlength="100" data-lp-input="badge">
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Headline Principal (Promessa Forte de Ganho/Desconto)</span>
                                <small class="field-hint">Destaque o valor principal</small>
                            </span>
                            <input type="text" name="headline" value="<?= esc($lp['headline']) ?>" required maxlength="255" data-lp-input="headline">
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Subheadline (Texto de Apoio Persuasivo)</span>
                                <small class="field-hint">Explique como funciona o grupo exclusivo</small>
                            </span>
                            <textarea name="subheadline" rows="3" data-lp-input="subheadline"><?= esc($lp['subheadline']) ?></textarea>
                        </label>
                    </div>
                </div>

                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon" style="background: rgba(37, 211, 102, 0.15); color: #25d366;"><i class="ti ti-brand-whatsapp"></i></div>
                        <div>
                            <h3 class="settings-section-title">Chamada para Ação (CTA) e Grupo VIP</h3>
                            <p class="settings-section-subtitle">Configurações do botão de envio, animação atrativa e link oficial do grupo no WhatsApp.</p>
                        </div>
                    </div>
                    
                    <div class="form-grid single">
                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Link de Convite do Grupo VIP no WhatsApp</span>
                                <small class="field-hint">https://chat.whatsapp.com/...</small>
                            </span>
                            <input type="url" name="whatsapp_group_link" value="<?= esc($lp['whatsapp_group_link']) ?>" required placeholder="https://chat.whatsapp.com/ExemploDoGrupo" data-lp-input="whatsapp-link">
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Texto do Botão Principal</span>
                                <small class="field-hint">Ação em primeira pessoa</small>
                            </span>
                            <input type="text" name="button_text" value="<?= esc($lp['button_text']) ?>" required maxlength="80" data-lp-input="button-text">
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Subtexto / Garantia Abaixo do Botão</span>
                                <small class="field-hint">Elimine o medo de spam</small>
                            </span>
                            <input type="text" name="button_subtext" value="<?= esc($lp['button_subtext']) ?>" maxlength="120" data-lp-input="button-subtext">
                        </label>
                    </div>
                </div>

                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon"><i class="ti ti-star"></i></div>
                        <div>
                            <h3 class="settings-section-title">3 Grandes Benefícios e Vantagens Exclusivas</h3>
                            <p class="settings-section-subtitle">Itens que justificam por que vale a pena entrar no grupo VIP agora.</p>
                        </div>
                    </div>

                    <div class="benefits-config-list">
                        <!-- Benefício 1 -->
                        <div class="benefit-config-card">
                            <div class="benefit-card-header">
                                <span class="benefit-pill">Benefício 01</span>
                                <div class="benefit-card-actions">
                                    <input type="hidden" name="card1_icon" id="card1_icon" value="<?= esc($lp['card1_icon']) ?>" data-lp-input="card1-icon">
                                    <button type="button" class="icon-picker-trigger-btn" data-open-icon-picker="1" title="Clique para escolher o ícone">
                                        <span class="icon-trigger-preview"><i class="ti <?= esc($lp['card1_icon']) ?>" data-benefit-icon-preview="1"></i></span>
                                        <span>Escolher Ícone</span>
                                        <i class="ti ti-chevron-down icon-trigger-arrow" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-grid single">
                                <label class="form-field">
                                    <span>Título do Benefício</span>
                                    <input type="text" name="card1_title" value="<?= esc($lp['card1_title']) ?>" required maxlength="100" data-lp-input="card1-title">
                                </label>
                                <label class="form-field">
                                    <span>Descrição Detalhada</span>
                                    <input type="text" name="card1_desc" value="<?= esc($lp['card1_desc']) ?>" required maxlength="255" data-lp-input="card1-desc">
                                </label>
                            </div>
                        </div>

                        <!-- Benefício 2 -->
                        <div class="benefit-config-card" style="margin-top:16px;">
                            <div class="benefit-card-header">
                                <span class="benefit-pill">Benefício 02</span>
                                <div class="benefit-card-actions">
                                    <input type="hidden" name="card2_icon" id="card2_icon" value="<?= esc($lp['card2_icon']) ?>" data-lp-input="card2-icon">
                                    <button type="button" class="icon-picker-trigger-btn" data-open-icon-picker="2" title="Clique para escolher o ícone">
                                        <span class="icon-trigger-preview"><i class="ti <?= esc($lp['card2_icon']) ?>" data-benefit-icon-preview="2"></i></span>
                                        <span>Escolher Ícone</span>
                                        <i class="ti ti-chevron-down icon-trigger-arrow" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-grid single">
                                <label class="form-field">
                                    <span>Título do Benefício</span>
                                    <input type="text" name="card2_title" value="<?= esc($lp['card2_title']) ?>" required maxlength="100" data-lp-input="card2-title">
                                </label>
                                <label class="form-field">
                                    <span>Descrição Detalhada</span>
                                    <input type="text" name="card2_desc" value="<?= esc($lp['card2_desc']) ?>" required maxlength="255" data-lp-input="card2-desc">
                                </label>
                            </div>
                        </div>

                        <!-- Benefício 3 -->
                        <div class="benefit-config-card" style="margin-top:16px;">
                            <div class="benefit-card-header">
                                <span class="benefit-pill">Benefício 03</span>
                                <div class="benefit-card-actions">
                                    <input type="hidden" name="card3_icon" id="card3_icon" value="<?= esc($lp['card3_icon']) ?>" data-lp-input="card3-icon">
                                    <button type="button" class="icon-picker-trigger-btn" data-open-icon-picker="3" title="Clique para escolher o ícone">
                                        <span class="icon-trigger-preview"><i class="ti <?= esc($lp['card3_icon']) ?>" data-benefit-icon-preview="3"></i></span>
                                        <span>Escolher Ícone</span>
                                        <i class="ti ti-chevron-down icon-trigger-arrow" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-grid single">
                                <label class="form-field">
                                    <span>Título do Benefício</span>
                                    <input type="text" name="card3_title" value="<?= esc($lp['card3_title']) ?>" required maxlength="100" data-lp-input="card3-title">
                                </label>
                                <label class="form-field">
                                    <span>Descrição Detalhada</span>
                                    <input type="text" name="card3_desc" value="<?= esc($lp['card3_desc']) ?>" required maxlength="255" data-lp-input="card3-desc">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="settings-card-block">
                    <div class="settings-card-header">
                        <div class="card-header-icon"><i class="ti ti-layout-bottombar-collapse"></i></div>
                        <div>
                            <h3 class="settings-section-title">Modal de Sucesso / Redirecionamento</h3>
                            <p class="settings-section-subtitle">Exibido após o lead preencher o formulário antes de ir ao WhatsApp.</p>
                        </div>
                    </div>
                    
                    <div class="form-grid single">
                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Título do Modal de Sucesso</span>
                                <small class="field-hint">Comemoração positiva</small>
                            </span>
                            <input type="text" name="modal_title" value="<?= esc($lp['modal_title']) ?>" required maxlength="120" data-lp-input="modal-title">
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Mensagem Explicativa do Modal</span>
                                <small class="field-hint">Oriente o clique final</small>
                            </span>
                            <textarea name="modal_desc" rows="3" data-lp-input="modal-desc"><?= esc($lp['modal_desc']) ?></textarea>
                        </label>

                        <label class="form-field">
                            <span class="field-label-row">
                                <span>Texto do Botão no Modal</span>
                                <small class="field-hint">Acesso direto ao WhatsApp</small>
                            </span>
                            <input type="text" name="modal_button_text" value="<?= esc($lp['modal_button_text']) ?>" required maxlength="80" data-lp-input="modal-button-text">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Coluna da Direita: Mockup do Smartphone em Tempo Real -->
            <div class="landing-preview-col">
                <div class="preview-sticky-wrap">
                    <div class="preview-device-header">
                        <div class="preview-device-title">
                            <span class="preview-live-indicator"></span>
                            <strong>Prévia em Tempo Real</strong>
                        </div>
                        <div class="preview-mode-switch" role="tablist">
                            <button type="button" class="preview-mode-btn active" data-preview-mode="landing"><i class="ti ti-layout"></i> Página</button>
                            <button type="button" class="preview-mode-btn" data-preview-mode="modal"><i class="ti ti-app-window"></i> Modal VIP</button>
                        </div>
                    </div>

                    <div class="mobile-mockup-frame">
                        <!-- Dynamic Island do iPhone -->
                        <div class="mobile-dynamic-island">
                            <div class="island-camera"></div>
                            <div class="island-sensor"></div>
                        </div>

                        <!-- Barra de Status do iOS -->
                        <div class="mobile-mockup-status-bar">
                            <span class="status-time">9:41</span>
                            <div class="status-icons">
                                <i class="ti ti-antenna-bars-5"></i>
                                <i class="ti ti-wifi"></i>
                                <i class="ti ti-battery-4"></i>
                            </div>
                        </div>
                        
                        <!-- Conteúdo da Tela do iPhone -->
                        <div class="mobile-screen-content" id="lp_live_preview_container" data-mockup-model="<?= esc($lp['template_model'] ?? 'model-1') ?>" data-mockup-palette="<?= esc($lp['color_palette'] ?? 'palette-aurora') ?>" data-mockup-bganim="<?= esc($lp['bg_animation'] ?? 'bg-particles') ?>" data-mockup-btnanim="<?= esc($lp['btn_animation'] ?? 'btn-pulse') ?>">
                            <!-- Camada de Animações de Fundo -->
                            <div class="bg-fx-layer">
                                <div class="bg-particles-wrap">
                                    <div class="particle-orb"></div>
                                    <div class="particle-orb"></div>
                                    <div class="particle-orb"></div>
                                </div>
                                <div class="bg-mesh-wrap"></div>
                                <div class="bg-grid-wrap"><div class="bg-grid-plane"></div></div>
                                <div class="bg-pulse-wrap">
                                    <div class="pulse-ring"></div>
                                    <div class="pulse-ring"></div>
                                    <div class="pulse-ring"></div>
                                </div>
                                <div class="bg-shapes-wrap">
                                    <div class="shape-item"></div>
                                    <div class="shape-item"></div>
                                    <div class="shape-item"></div>
                                </div>
                            </div>

                            <!-- Modo 1: Preview da Landing Page -->
                            <div class="lp-preview-body" data-preview-screen="landing">
                                <div class="lp-preview-brand">
                                    <div class="lp-preview-logo">DI</div>
                                    <span class="lp-preview-brandname"><?= esc($companyProfile['name'] ?? 'Dias Imports') ?></span>
                                </div>

                                <div class="lp-preview-badge" data-preview="badge">
                                    <span class="lp-preview-dot"></span>
                                    <span><?= esc($lp['badge_text']) ?></span>
                                </div>

                                <h4 class="lp-preview-title" data-preview="headline"><?= esc($lp['headline']) ?></h4>
                                <p class="lp-preview-sub" data-preview="subheadline"><?= esc($lp['subheadline']) ?></p>

                                <div class="lp-preview-form-card">
                                    <div class="lp-preview-card-header">
                                        <div class="lp-card-tag"><i class="ti ti-lock"></i> Acesso Restrito</div>
                                        <strong>Liberar Convite Exclusivo</strong>
                                        <small>Preencha para receber o link direto</small>
                                    </div>
                                    <div class="lp-mock-input"><i class="ti ti-user"></i> <span>Seu Nome Completo</span></div>
                                    <div class="lp-mock-input"><i class="ti ti-brand-whatsapp"></i> <span>(00) 00000-0000</span></div>
                                    <div class="lp-preview-btn">
                                        <i class="ti ti-arrow-narrow-right"></i>
                                        <span data-preview="button-text"><?= esc($lp['button_text']) ?></span>
                                    </div>
                                    <small class="lp-preview-btn-sub" data-preview="button-subtext"><?= esc($lp['button_subtext']) ?></small>
                                </div>

                                <div class="lp-preview-benefits">
                                    <div class="lp-preview-bcard">
                                        <div class="lp-preview-bicon"><i class="ti <?= esc($lp['card1_icon']) ?>" data-preview="card1-icon"></i></div>
                                        <div class="lp-preview-binfo">
                                            <strong data-preview="card1-title"><?= esc($lp['card1_title']) ?></strong>
                                            <p data-preview="card1-desc"><?= esc($lp['card1_desc']) ?></p>
                                        </div>
                                    </div>
                                    <div class="lp-preview-bcard">
                                        <div class="lp-preview-bicon"><i class="ti <?= esc($lp['card2_icon']) ?>" data-preview="card2-icon"></i></div>
                                        <div class="lp-preview-binfo">
                                            <strong data-preview="card2-title"><?= esc($lp['card2_title']) ?></strong>
                                            <p data-preview="card2-desc"><?= esc($lp['card2_desc']) ?></p>
                                        </div>
                                    </div>
                                    <div class="lp-preview-bcard">
                                        <div class="lp-preview-bicon"><i class="ti <?= esc($lp['card3_icon']) ?>" data-preview="card3-icon"></i></div>
                                        <div class="lp-preview-binfo">
                                            <strong data-preview="card3-title"><?= esc($lp['card3_title']) ?></strong>
                                            <p data-preview="card3-desc"><?= esc($lp['card3_desc']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modo 2: Preview do Modal VIP -->
                            <div class="lp-preview-modal-view" data-preview-screen="modal" hidden>
                                <div class="lp-modal-preview-box">
                                    <div class="lp-modal-preview-icon"><i class="ti ti-circle-check-filled"></i></div>
                                    <h4 class="lp-modal-preview-title" data-preview="modal-title"><?= esc($lp['modal_title']) ?></h4>
                                    <p class="lp-modal-preview-desc" data-preview="modal-desc"><?= esc($lp['modal_desc']) ?></p>
                                    <div class="lp-modal-preview-btn">
                                        <i class="ti ti-brand-whatsapp"></i>
                                        <span data-preview="modal-button-text"><?= esc($lp['modal_button_text']) ?></span>
                                    </div>
                                    <div class="lp-modal-preview-tip">
                                        <i class="ti ti-info-circle"></i>
                                        <span>Você será redirecionado para o WhatsApp</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Barra Home Indicator do iOS -->
                        <div class="mobile-home-indicator"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra Flutuante Salvar/Cancelar -->
        <?php if (\App\Libraries\UserPermissions::hasPermission('landing_leads', 'edit')): ?>
        <div class="save-bar" data-landing-save-bar hidden aria-hidden="true">
            <p>
                <strong>Alterações não salvas na Landing Page</strong>
                <span>Salve para publicar as alterações na página pública de leads.</span>
            </p>
            <div class="save-actions">
                <button class="button secondary" type="button" data-cancel-landing>Cancelar</button>
                <button class="button primary" type="submit"><i class="ti ti-device-floppy" aria-hidden="true"></i>Salvar Alterações</button>
            </div>
        </div>
        <?php endif; ?>
    </form>
</section>

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

        <div class="cropper-container-wrapper" style="width: 100%; max-height: 380px; background: #111; overflow: hidden; border-radius: 8px; margin: 16px 0;">
            <img id="cropper_image_element" src="" alt="Imagem para recorte" style="max-width: 100%; display: block;">
        </div>

        <div class="template-dialog-footer" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
            <div class="cropper-controls" style="display: flex; gap: 6px;">
                <button type="button" class="button secondary compact" id="btn_cropper_zoom_in" title="Aumentar Zoom"><i class="ti ti-zoom-in"></i></button>
                <button type="button" class="button secondary compact" id="btn_cropper_zoom_out" title="Diminuir Zoom"><i class="ti ti-zoom-out"></i></button>
                <button type="button" class="button secondary compact" id="btn_cropper_rotate" title="Girar 90°"><i class="ti ti-rotate-clockwise"></i></button>
                <button type="button" class="button secondary compact" id="btn_cropper_reset" title="Resetar"><i class="ti ti-refresh"></i></button>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="button secondary" id="btn_cancel_cropper">Cancelar</button>
                <button type="button" class="button primary" id="btn_apply_crop"><i class="ti ti-check"></i> Aplicar Recorte</button>
            </div>
        </div>
    </section>
</div>

<!-- Modal Selecionador de Ícones (Icon Picker) -->
<div class="icon-picker-dialog" id="icon_picker_modal" data-icon-picker-dialog hidden aria-hidden="true">
    <section class="icon-picker-card" role="dialog" aria-modal="true">
        <button class="icon-picker-close" type="button" id="btn_close_icon_picker" data-close-icon-picker aria-label="Fechar seletor de ícones"><i class="ti ti-x" aria-hidden="true"></i></button>
        
        <div class="icon-picker-header">
            <div class="card-header-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;"><i class="ti ti-icons"></i></div>
            <div>
                <h2 class="icon-picker-title">Escolha um Ícone para o Benefício</h2>
                <p class="icon-picker-desc">Selecione o símbolo que melhor representa a vantagem exclusiva para seus clientes.</p>
            </div>
        </div>

        <div class="icon-picker-search-row">
            <div class="icon-search-input-wrapper">
                <i class="ti ti-search" aria-hidden="true"></i>
                <input type="search" id="icon_picker_search" data-icon-search-input placeholder="Buscar ícone (ex: fogo, desconto, estrela, raio, escudo...)" autocomplete="off">
            </div>
        </div>

        <div class="icon-picker-grid" id="icon_picker_grid" data-icon-grid></div>

        <div class="icon-picker-footer">
            <span class="icon-picker-tip"><i class="ti ti-info-circle"></i> Clique em qualquer ícone acima para aplicá-lo instantaneamente ao benefício.</span>
            <button type="button" class="button secondary" id="btn_cancel_icon_picker" data-close-icon-picker>Fechar</button>
        </div>
    </section>
</div>
<?= $this->section('scripts') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
