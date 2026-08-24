<section class="welcome-panel overview-hero" aria-labelledby="overview-welcome-title">
    <div class="overview-hero-content">
        <div class="overview-hero-meta">
            <span class="overview-date-badge"><i class="ti ti-calendar"></i> <?= esc($overviewData['formattedDate']) ?></span>
            <div class="overview-live-status">
                <?php if ($overviewData['operations']['evolution_configured']): ?>
                    <span class="live-pill live-pill-success" title="Evolution API conectada e pronta para disparos">
                        <span class="live-pulse"></span> Evolution API Conectada
                    </span>
                <?php else: ?>
                    <span class="live-pill live-pill-warning" title="Evolution API não configurada">
                        <span class="live-pulse warning"></span> Evolution API Pendente
                    </span>
                <?php endif; ?>

                <?php if ($overviewData['operations']['meta_ads_configured']): ?>
                    <span class="live-pill live-pill-info" title="Pixel do Meta Ads ativo: <?= esc($overviewData['operations']['meta_ads_pixel_id']) ?>">
                        <i class="ti ti-brand-meta"></i> Meta Ads Ativo
                    </span>
                <?php endif; ?>

                <?php if (($overviewData['operations']['queue_stats']['failed'] ?? 0) > 0): ?>
                    <a href="<?= site_url('central-trabalho') ?>" class="live-pill live-pill-danger" title="Há falhas na fila de processamento">
                        <i class="ti ti-alert-triangle"></i> <?= $overviewData['operations']['queue_stats']['failed'] ?> Falha(s) na Fila
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <h1 class="welcome-title" id="overview-welcome-title">
            <?= esc($overviewData['greeting']) ?>, <span class="highlight-text"><?= esc($firstName) ?></span>! 👋
        </h1>
        <p class="welcome-text">
            Aqui está o panorama completo em tempo real do marketing, tráfego do catálogo, captação de leads e comunidade da <strong>Dias Imports</strong>.
        </p>

        <div class="overview-quick-actions">
            <a href="<?= site_url('produtos/novo') ?>" class="btn-quick-action primary">
                <i class="ti ti-plus"></i> Novo Produto
            </a>
            <a href="<?= site_url('leads-vip') ?>" class="btn-quick-action secondary">
                <i class="ti ti-diamond"></i> Ver Leads VIP
            </a>
            <a href="<?= site_url('grupos-whatsapp') ?>" class="btn-quick-action secondary">
                <i class="ti ti-brand-whatsapp"></i> Grupos WhatsApp
            </a>
            <a href="<?= site_url('central-trabalho') ?>" class="btn-quick-action secondary">
                <i class="ti ti-cpu"></i> Central de Trabalho
            </a>
            <a href="<?= site_url('configuracoes') ?>" class="btn-quick-action secondary">
                <i class="ti ti-settings"></i> Configurações
            </a>
        </div>
    </div>
    <div class="welcome-icon overview-hero-icon" aria-hidden="true">
        <i class="ti ti-chart-dots-3"></i>
    </div>
</section>

<!-- GRUPO 1: PERFORMANCE DE PRODUTOS & CATÁLOGO -->
<div class="dashboard-group">
    <div class="dashboard-group-header">
        <div class="group-header-info">
            <span class="group-header-icon group-icon-purple"><i class="ti ti-package"></i></span>
            <div>
                <h2 class="group-title">Performance do Catálogo & Tráfego</h2>
                <p class="group-subtitle">Visualizações de produtos, engajamento e intenção de compra nas páginas exclusivas</p>
            </div>
        </div>
        <a href="<?= site_url('produtos') ?>" class="group-header-link">
            Gerenciar Catálogo <i class="ti ti-arrow-right"></i>
        </a>
    </div>

    <!-- KPIs Grupo 1 -->
    <div class="overview-kpis-grid kpis-4">
        <div class="kpi-card kpi-purple">
            <div class="kpi-icon-wrap"><i class="ti ti-box"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Produtos Cadastrados</span>
                <strong class="kpi-value"><?= number_format($overviewData['products']['total'], 0, ',', '.') ?></strong>
                <div class="kpi-footer">
                    <span class="kpi-badge badge-success"><i class="ti ti-check"></i> <?= $overviewData['products']['active'] ?> ativos</span>
                    <?php if ($overviewData['products']['inactive'] > 0): ?>
                        <span class="kpi-badge badge-warning"><?= $overviewData['products']['inactive'] ?> inativos</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-icon-wrap"><i class="ti ti-eye"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Total de Pageviews</span>
                <strong class="kpi-value"><?= number_format($overviewData['products']['total_pageviews'], 0, ',', '.') ?></strong>
                <div class="kpi-footer">
                    <span class="kpi-sub-text"><strong><?= number_format($overviewData['products']['today_pageviews'], 0, ',', '.') ?></strong> visualizações hoje</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-icon-wrap"><i class="ti ti-click"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Cliques de Intenção / CTA</span>
                <strong class="kpi-value"><?= number_format($overviewData['products']['total_clicks'], 0, ',', '.') ?></strong>
                <div class="kpi-footer">
                    <span class="kpi-sub-text"><strong><?= number_format($overviewData['products']['today_clicks'], 0, ',', '.') ?></strong> cliques hoje</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="kpi-icon-wrap"><i class="ti ti-percentage"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Taxa Média de Conversão (CTR)</span>
                <strong class="kpi-value"><?= $overviewData['products']['ctr'] ?>%</strong>
                <div class="kpi-footer">
                    <span class="kpi-sub-text">Cliques por visualização de produto</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Rankings de Produtos (45% Gráfico / 55% Top Produtos) -->
    <div class="dashboard-duo-grid dashboard-duo-products">
        <!-- Gráfico de Tráfego 14 Dias -->
        <div class="overview-panel">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <i class="ti ti-chart-line text-purple"></i>
                    <div>
                        <h3 class="panel-title">Evolução do Tráfego do Catálogo</h3>
                        <span class="panel-subtitle">Visualizações e cliques nos últimos 14 dias</span>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="chart-legend-wrap">
                    <span class="legend-item"><span class="legend-dot dot-purple"></span> PageViews (Linha)</span>
                    <span class="legend-item"><span class="legend-bar-sample dot-green"></span> Cliques CTA / WhatsApp (Barras)</span>
                </div>

                <div class="overview-chart-container">
                    <?php
                    $chartPoints = $overviewData['products']['traffic_evolution'];
                    $cMax = max($overviewData['products']['max_traffic_val'], 10);
                    $w = 600;
                    $h = 165;
                    $padX = 28;
                    $padY = 22;
                    $cnt = count($chartPoints);
                    $slotWidth = $cnt > 0 ? ($w - ($padX * 2)) / $cnt : $w;
                    $barWidth = max(round($slotWidth * 0.38), 6);

                    $pvCoords = [];
                    $barItems = [];

                    foreach ($chartPoints as $i => $pt) {
                        $xCenter = round($padX + ($i * $slotWidth) + ($slotWidth / 2), 1);
                        $barX = round($xCenter - ($barWidth / 2), 1);
                        
                        $pvY = round($h - $padY - (($pt['pageviews'] / $cMax) * ($h - ($padY * 2))), 1);
                        $clH = round((($pt['clicks'] / $cMax) * ($h - ($padY * 2))), 1);
                        $clY = round($h - $padY - $clH, 1);
                        
                        $pvCoords[] = [$xCenter, $pvY, $pt['pageviews'], $pt['dayLabel']];
                        $barItems[] = [
                            'x' => $barX,
                            'y' => $clY,
                            'w' => $barWidth,
                            'h' => max($clH, 2),
                            'val' => $pt['clicks'],
                            'label' => $pt['dayLabel'],
                            'hasVal' => $pt['clicks'] > 0
                        ];
                    }

                    $pvPath = '';
                    $pvArea = '';

                    foreach ($pvCoords as $i => $c) {
                        $cmd = $i === 0 ? 'M' : 'L';
                        $pvPath .= "{$cmd} {$c[0]} {$c[1]} ";
                    }

                    if (!empty($pvCoords)) {
                        $lastX = end($pvCoords)[0];
                        $firstX = $pvCoords[0][0];
                        $baseY = $h - $padY;
                        $pvArea = $pvPath . "L {$lastX} {$baseY} L {$firstX} {$baseY} Z";
                    }
                    ?>

                    <svg viewBox="0 0 <?= $w ?> <?= $h ?>" class="overview-svg-chart" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="pvGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#635bff" stop-opacity="0.32"/>
                                <stop offset="100%" stop-color="#635bff" stop-opacity="0.01"/>
                            </linearGradient>
                            <linearGradient id="clBarGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10b981" stop-opacity="0.95"/>
                                <stop offset="100%" stop-color="#059669" stop-opacity="0.5"/>
                            </linearGradient>
                        </defs>

                        <!-- Linhas de Grade Horizontais -->
                        <line x1="<?= $padX ?>" y1="<?= $padY ?>" x2="<?= $w - $padX ?>" y2="<?= $padY ?>" stroke="rgba(255,255,255,0.06)" stroke-dasharray="3,3" />
                        <line x1="<?= $padX ?>" y1="<?= round($h / 2) ?>" x2="<?= $w - $padX ?>" y2="<?= round($h / 2) ?>" stroke="rgba(255,255,255,0.06)" stroke-dasharray="3,3" />
                        <line x1="<?= $padX ?>" y1="<?= $h - $padY ?>" x2="<?= $w - $padX ?>" y2="<?= $h - $padY ?>" stroke="rgba(255,255,255,0.12)" />

                        <!-- Área Preenchida PageViews -->
                        <path d="<?= $pvArea ?>" fill="url(#pvGrad)" />

                        <!-- Linha Principal PageViews -->
                        <path d="<?= $pvPath ?>" fill="none" stroke="#635bff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                        <!-- Barras de Cliques CTA (renderizadas na frente para captura fácil do mouse) -->
                        <?php foreach ($barItems as $b): ?>
                            <rect 
                                x="<?= $b['x'] ?>" 
                                y="<?= $b['y'] ?>" 
                                width="<?= $b['w'] ?>" 
                                height="<?= $b['h'] ?>" 
                                rx="3" 
                                class="chart-bar bar-green <?= $b['hasVal'] ? '' : 'bar-muted' ?>"
                                fill="url(#clBarGrad)" 
                                data-val="<?= $b['val'] ?>" 
                                data-label="<?= esc($b['label']) ?>" 
                                data-type="Cliques CTA"
                            />
                        <?php endforeach; ?>

                        <!-- Pontos Interativos PageViews -->
                        <?php foreach ($pvCoords as $c): ?>
                            <circle cx="<?= $c[0] ?>" cy="<?= $c[1] ?>" r="4" class="chart-point point-purple" data-val="<?= $c[2] ?>" data-label="<?= esc($c[3]) ?>" data-type="PageViews" />
                        <?php endforeach; ?>
                    </svg>

                    <!-- Eixo X Datas - Amostra equilibrada de 5 a 6 datas -->
                    <div class="chart-x-axis-grid">
                        <?php 
                        $totalPts = count($chartPoints);
                        $step = ($totalPts > 6) ? (int) floor(($totalPts - 1) / 4) : 1;
                        $shownIndices = [];
                        for ($k = 0; $k < $totalPts; $k += $step) {
                            $shownIndices[] = $k;
                        }
                        if (!in_array($totalPts - 1, $shownIndices)) {
                            $shownIndices[] = $totalPts - 1;
                        }
                        ?>
                        <?php foreach ($chartPoints as $i => $pt): ?>
                            <?php if (in_array($i, $shownIndices)): ?>
                                <span class="x-label-pill"><?= esc($pt['dayLabel']) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tooltip Flutuante -->
                    <div class="chart-tooltip" id="chartTooltip"></div>
                </div>
            </div>
        </div>

        <!-- Top 3 Produtos em Destaque -->
        <div class="overview-panel">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <i class="ti ti-flame text-amber"></i>
                    <div>
                        <h3 class="panel-title">Top 3 Produtos em Destaque</h3>
                        <span class="panel-subtitle">Mais visitados e com maior engajamento</span>
                    </div>
                </div>
                <a href="<?= site_url('produtos') ?>" class="panel-header-btn" title="Ver catálogo completo">
                    <i class="ti ti-arrow-up-right"></i>
                </a>
            </div>

            <div class="panel-body p-0">
                <?php if (empty($overviewData['products']['top_products'])): ?>
                    <div class="overview-empty-list">
                        <i class="ti ti-package-off"></i>
                        <p>Nenhum produto cadastrado ainda.</p>
                        <a href="<?= site_url('produtos/novo') ?>" class="btn-overview-action">Cadastrar Produto</a>
                    </div>
                <?php else: ?>
                    <div class="top-products-list">
                        <?php foreach ($overviewData['products']['top_products'] as $idx => $prod): ?>
                            <div class="top-product-item <?= $idx === 0 ? 'is-top-1' : '' ?>">
                                <div class="top-product-rank rank-<?= $idx + 1 ?>">
                                    <?php if ($idx === 0): ?>
                                        <i class="ti ti-crown"></i>
                                    <?php else: ?>
                                        <span>#<?= $idx + 1 ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="top-product-thumb">
                                    <?php if (!empty($prod['cover'])): ?>
                                        <img src="<?= base_url('uploads/products/' . $prod['cover']) ?>" alt="<?= esc($prod['name']) ?>" loading="lazy" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="thumb-placeholder" style="display: none;"><i class="ti ti-package"></i></div>
                                    <?php else: ?>
                                        <div class="thumb-placeholder"><i class="ti ti-package"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="top-product-info">
                                    <!-- Linha 1: Nome do produto e Ações -->
                                    <div class="top-product-row-1">
                                        <a href="<?= site_url('produtos/' . $prod['id'] . '/editar') ?>" class="top-product-name" title="<?= esc($prod['name']) ?>">
                                            <?= esc($prod['name']) ?>
                                        </a>

                                        <div class="top-product-inline-actions">
                                            <a href="<?= site_url('p/' . $prod['slug']) ?>" target="_blank" class="btn-icon-soft" title="Visualizar Landing Page">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                            <a href="<?= site_url('produtos/' . $prod['id'] . '/editar?tab=tab-stats') ?>" target="_blank" class="btn-icon-soft btn-stats" title="Estatísticas detalhadas">
                                                <i class="ti ti-chart-bar"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Linha 2: Preços e Métricas -->
                                    <div class="top-product-row-2">
                                        <div class="product-price-box">
                                            <?php if (!empty($prod['promotional_price']) && $prod['promotional_price'] < $prod['price']): ?>
                                                <?php $discount = round((($prod['price'] - $prod['promotional_price']) / $prod['price']) * 100); ?>
                                                <span class="product-price-old">De R$&nbsp;<?= number_format($prod['price'], 2, ',', '.') ?></span>
                                                <span class="product-price-current">Por R$&nbsp;<?= number_format($prod['promotional_price'], 2, ',', '.') ?></span>
                                                <span class="price-discount-tag">-<?= $discount ?>%</span>
                                            <?php else: ?>
                                                <span class="product-price-current">R$&nbsp;<?= number_format($prod['price'], 2, ',', '.') ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="top-product-metrics">
                                            <span class="badge-mini <?= $prod['active'] ? 'badge-active' : 'badge-inactive' ?>">
                                                <?= $prod['active'] ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                            <span class="metric-pill" title="Visualizações"><i class="ti ti-eye"></i> <?= number_format($prod['pageviews'], 0, ',', '.') ?> views</span>
                                            <span class="metric-pill" title="Cliques CTA"><i class="ti ti-click"></i> <?= number_format($prod['clicks'], 0, ',', '.') ?> cliques</span>
                                            <span class="metric-pill metric-ctr" title="Taxa de conversão (CTR)"><i class="ti ti-percentage"></i> <?= $prod['ctr'] ?>% CTR</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- GRUPO 2: CAPTAÇÃO DE LEADS VIP (LANDING PAGE) -->
<div class="dashboard-group">
    <div class="dashboard-group-header">
        <div class="group-header-info">
            <span class="group-header-icon group-icon-amber"><i class="ti ti-diamond"></i></span>
            <div>
                <h2 class="group-title">Captação de Leads VIP & Conversão</h2>
                <p class="group-subtitle">Contatos qualificados captados através da Landing Page Oficial de Leads</p>
            </div>
        </div>
        <div class="group-header-actions">
            <a href="<?= site_url('leads-vip') ?>" class="group-header-link">
                Ver Todos os Leads <i class="ti ti-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- KPIs Leads -->
    <div class="overview-kpis-grid kpis-4">
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon-wrap"><i class="ti ti-eye"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Total de Visualização</span>
                <strong class="kpi-value"><?= number_format($overviewData['leads']['total_views'] ?? 0, 0, ',', '.') ?></strong>
                <div class="kpi-footer">
                    <span class="kpi-sub-text"><strong><?= number_format($overviewData['leads']['today_views'] ?? 0, 0, ',', '.') ?></strong> visualizações hoje</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="kpi-icon-wrap"><i class="ti ti-click"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Total de Cliques</span>
                <strong class="kpi-value"><?= number_format($overviewData['leads']['total_clicks'] ?? 0, 0, ',', '.') ?></strong>
                <div class="kpi-footer">
                    <span class="kpi-sub-text"><strong><?= number_format($overviewData['leads']['today_clicks'] ?? 0, 0, ',', '.') ?></strong> cliques hoje</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="kpi-icon-wrap"><i class="ti ti-crown"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Total de Leads</span>
                <strong class="kpi-value"><?= number_format($overviewData['leads']['total'] ?? 0, 0, ',', '.') ?></strong>
                <div class="kpi-footer">
                    <span class="kpi-badge badge-success"><i class="ti ti-check"></i> <?= number_format($overviewData['leads']['today'] ?? 0, 0, ',', '.') ?> hoje</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="kpi-icon-wrap"><i class="ti ti-percentage"></i></div>
            <div class="kpi-content">
                <span class="kpi-label">Taxa de Conversão</span>
                <strong class="kpi-value"><?= number_format($overviewData['leads']['conversion_rate'] ?? 0, 1, ',', '.') ?>%</strong>
                <div class="kpi-footer">
                    <span class="kpi-sub-text">Leads por visualização</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Duo Leads: Gráfico de Barras 14 Dias + Últimos Leads -->
    <div class="dashboard-duo-grid">
        <!-- Evolução de Leads (Barras) -->
        <div class="overview-panel">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <i class="ti ti-chart-bar text-amber"></i>
                    <div>
                        <h3 class="panel-title">Captação Diária de Leads</h3>
                        <span class="panel-subtitle">Entrada de novos contatos nos últimos 14 dias</span>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <div class="leads-bar-chart-wrap">
                    <?php
                    $lEvolution = $overviewData['leads']['evolution'];
                    $maxL = max($overviewData['leads']['max_count'], 5);
                    ?>
                    <div class="leads-bars-container">
                        <?php foreach ($lEvolution as $lPt): ?>
                            <?php $heightPct = max(round(($lPt['count'] / $maxL) * 100), 6); ?>
                            <div class="leads-bar-col">
                                <div class="leads-bar-tooltip"><?= $lPt['count'] ?> lead<?= $lPt['count'] === 1 ? '' : 's' ?></div>
                                <div class="leads-bar-track">
                                    <div class="leads-bar-fill <?= $lPt['count'] > 0 ? 'has-data' : '' ?>" style="height: <?= $heightPct ?>%;"></div>
                                </div>
                                <span class="leads-bar-label"><?= esc($lPt['dayLabel']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos 6 Leads Captados -->
        <div class="overview-panel">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <i class="ti ti-users text-emerald"></i>
                    <div>
                        <h3 class="panel-title">Últimos Leads Captados</h3>
                        <span class="panel-subtitle">Novos contatos aguardando atendimento</span>
                    </div>
                </div>
                <a href="<?= site_url('leads-vip') ?>" class="panel-header-btn" title="Ver todos os leads">
                    <i class="ti ti-arrow-up-right"></i>
                </a>
            </div>

            <div class="panel-body p-0">
                <?php if (empty($overviewData['leads']['recent_leads'])): ?>
                    <div class="overview-empty-list">
                        <i class="ti ti-mood-empty"></i>
                        <p>Nenhum lead capturado ainda.</p>
                        <a href="<?= site_url('landing-leads') ?>" class="btn-overview-action">Configurar Landing Page</a>
                    </div>
                <?php else: ?>
                    <div class="recent-leads-list">
                        <?php foreach ($overviewData['leads']['recent_leads'] as $lead): ?>
                            <div class="recent-lead-item">
                                <div class="recent-lead-avatar">
                                    <?= mb_strtoupper(mb_substr($lead['name'], 0, 1)) ?>
                                </div>
                                <div class="recent-lead-info">
                                    <span class="lead-name"><?= esc($lead['name']) ?></span>
                                    <div class="lead-meta">
                                        <span class="lead-phone"><i class="ti ti-phone"></i> <?= esc($lead['formatted_phone']) ?></span>
                                        <span class="lead-time"><i class="ti ti-clock"></i> <?= esc($lead['relative_time']) ?></span>
                                    </div>
                                </div>
                                <div class="recent-lead-action">
                                    <?php if (!empty($lead['phone_clean'])): ?>
                                        <a href="https://wa.me/55<?= esc($lead['phone_clean']) ?>?text=<?= urlencode('Olá ' . $lead['name'] . ', tudo bem? Vi seu contato em nossa área VIP da Dias Imports!') ?>" 
                                           target="_blank" 
                                           class="btn-wa-direct" 
                                           title="Conversar com <?= esc($lead['name']) ?> no WhatsApp">
                                            <i class="ti ti-brand-whatsapp"></i> Conversar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- GRUPO 3 & 4: COMUNIDADE WHATSAPP & CENTRAL OPERACIONAL (GRID DUPLO) -->
<div class="dashboard-duo-group-grid">
    <!-- GRUPO 3: COMUNIDADE WHATSAPP -->
    <div class="dashboard-group">
        <div class="dashboard-group-header">
            <div class="group-header-info">
                <span class="group-header-icon group-icon-green"><i class="ti ti-brand-whatsapp"></i></span>
                <div>
                    <h2 class="group-title">Comunidade & Grupos WhatsApp</h2>
                    <p class="group-subtitle">Alcance total de membros e automação de mensagens</p>
                </div>
            </div>
            <a href="<?= site_url('grupos-whatsapp') ?>" class="group-header-link">
                Ver Grupos <i class="ti ti-arrow-right"></i>
            </a>
        </div>

        <!-- KPIs Comunidade -->
        <div class="overview-kpis-grid kpis-3 mb-3">
            <div class="kpi-card kpi-emerald">
                <div class="kpi-icon-wrap"><i class="ti ti-users-group"></i></div>
                <div class="kpi-content">
                    <span class="kpi-label">Grupos Ativos</span>
                    <strong class="kpi-value"><?= number_format($overviewData['whatsapp']['active_groups'], 0, ',', '.') ?></strong>
                    <div class="kpi-footer">
                        <span class="kpi-sub-text">Total: <?= $overviewData['whatsapp']['total_groups'] ?> cadastrados</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card kpi-blue">
                <div class="kpi-icon-wrap"><i class="ti ti-user-check"></i></div>
                <div class="kpi-content">
                    <span class="kpi-label">Membros Totais</span>
                    <strong class="kpi-value"><?= number_format($overviewData['whatsapp']['total_participants'], 0, ',', '.') ?></strong>
                    <div class="kpi-footer">
                        <span class="kpi-sub-text">Nos grupos ativos</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card kpi-purple">
                <div class="kpi-icon-wrap"><i class="ti ti-send"></i></div>
                <div class="kpi-content">
                    <span class="kpi-label">Disparos Feitos</span>
                    <strong class="kpi-value"><?= number_format($overviewData['whatsapp']['total_dispatches'], 0, ',', '.') ?></strong>
                    <div class="kpi-footer">
                        <span class="kpi-sub-text"><?= $overviewData['whatsapp']['total_templates'] ?> modelos cadastrados</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking dos Maiores Grupos -->
        <div class="overview-panel">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <i class="ti ti-trophy text-emerald"></i>
                    <div>
                        <h3 class="panel-title">Maiores Grupos de WhatsApp</h3>
                        <span class="panel-subtitle">Ordenados por número de membros participantes</span>
                    </div>
                </div>
                <a href="<?= site_url('grupos-whatsapp') ?>" class="panel-header-btn">
                    <i class="ti ti-arrow-up-right"></i>
                </a>
            </div>

            <div class="panel-body p-0">
                <?php if (empty($overviewData['whatsapp']['top_groups'])): ?>
                    <div class="overview-empty-list">
                        <i class="ti ti-brand-whatsapp"></i>
                        <p>Nenhum grupo cadastrado ou sincronizado.</p>
                        <a href="<?= site_url('grupos-whatsapp') ?>" class="btn-overview-action">Sincronizar Grupos</a>
                    </div>
                <?php else: ?>
                    <div class="top-groups-list">
                        <?php foreach ($overviewData['whatsapp']['top_groups'] as $gIdx => $group): ?>
                            <div class="top-group-item">
                                <div class="top-group-rank">#<?= $gIdx + 1 ?></div>
                                <div class="top-group-info">
                                    <span class="group-name"><?= esc($group['name']) ?></span>
                                    <span class="group-meta">
                                        <i class="ti ti-users"></i> <?= (int)$group['participants_count'] ?> participantes
                                    </span>
                                </div>
                                <div class="top-group-status">
                                    <span class="badge-mini <?= $group['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                        <?= $group['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- GRUPO 4: CENTRAL DE TRABALHO & INTEGRAÇÕES -->
    <div class="dashboard-group">
        <div class="dashboard-group-header">
            <div class="group-header-info">
                <span class="group-header-icon group-icon-cyan"><i class="ti ti-cpu"></i></span>
                <div>
                    <h2 class="group-title">Central Operacional & Fila</h2>
                    <p class="group-subtitle">Processamento de background, tarefas agendadas e conexões</p>
                </div>
            </div>
            <a href="<?= site_url('central-trabalho') ?>" class="group-header-link">
                Ver Fila <i class="ti ti-arrow-right"></i>
            </a>
        </div>

        <!-- Status Fila de Background -->
        <div class="overview-panel mb-3">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <i class="ti ti-list-check text-cyan"></i>
                    <h3 class="panel-title">Fila de Tarefas (Jobs)</h3>
                </div>
                <a href="<?= site_url('central-trabalho') ?>" class="panel-header-btn">
                    <i class="ti ti-arrow-up-right"></i>
                </a>
            </div>

            <div class="panel-body">
                <?php $qStats = $overviewData['operations']['queue_stats']; ?>
                <div class="queue-stats-tiles">
                    <div class="queue-tile tile-pending">
                        <span class="tile-label"><i class="ti ti-clock"></i> Pendentes</span>
                        <strong class="tile-value"><?= (int)$qStats['pending'] ?></strong>
                    </div>
                    <div class="queue-tile tile-processing">
                        <span class="tile-label"><i class="ti ti-loader"></i> Processando</span>
                        <strong class="tile-value"><?= (int)$qStats['processing'] ?></strong>
                    </div>
                    <div class="queue-tile tile-completed">
                        <span class="tile-label"><i class="ti ti-circle-check"></i> Concluídos</span>
                        <strong class="tile-value"><?= (int)$qStats['completed'] ?></strong>
                    </div>
                    <div class="queue-tile tile-failed <?= $qStats['failed'] > 0 ? 'has-errors' : '' ?>">
                        <span class="tile-label"><i class="ti ti-alert-triangle"></i> Falhas</span>
                        <strong class="tile-value"><?= (int)$qStats['failed'] ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status das Conexões / Integrações -->
        <div class="overview-panel">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <i class="ti ti-plug-connected text-primary"></i>
                    <h3 class="panel-title">Integrações & Conexões Ativas</h3>
                </div>
                <a href="<?= site_url('configuracoes') ?>" class="panel-header-btn">
                    <i class="ti ti-settings"></i>
                </a>
            </div>

            <div class="panel-body p-0">
                <div class="integrations-status-list">
                    <!-- Evolution API -->
                    <div class="integration-status-item">
                        <div class="integration-icon evo"><i class="ti ti-brand-whatsapp"></i></div>
                        <div class="integration-info">
                            <strong>Evolution API (WhatsApp)</strong>
                            <span>
                                <?php if (!empty($overviewData['operations']['evolution_default_instance'])): ?>
                                    Instância Padrão: <code><?= esc($overviewData['operations']['evolution_default_instance']) ?></code>
                                <?php else: ?>
                                    Nenhuma instância padrão definida
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="integration-badge">
                            <?php if ($overviewData['operations']['evolution_configured']): ?>
                                <span class="badge-tag success"><i class="ti ti-check"></i> Ativa</span>
                            <?php else: ?>
                                <span class="badge-tag warning"><i class="ti ti-alert-circle"></i> Pendente</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Meta Ads Pixel -->
                    <div class="integration-status-item">
                        <div class="integration-icon meta"><i class="ti ti-brand-meta"></i></div>
                        <div class="integration-info">
                            <strong>Meta Ads Conversions API</strong>
                            <span>
                                <?php if (!empty($overviewData['operations']['meta_ads_pixel_id'])): ?>
                                    Pixel ID: <code><?= esc($overviewData['operations']['meta_ads_pixel_id']) ?></code>
                                <?php else: ?>
                                    Pixel não configurado
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="integration-badge">
                            <?php if ($overviewData['operations']['meta_ads_configured']): ?>
                                <span class="badge-tag success"><i class="ti ti-check"></i> Integrado</span>
                            <?php else: ?>
                                <span class="badge-tag muted"><i class="ti ti-minus"></i> Opcional</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Usuários & Acessos -->
                    <div class="integration-status-item">
                        <div class="integration-icon users"><i class="ti ti-shield-lock"></i></div>
                        <div class="integration-info">
                            <strong>Segurança & Acessos (RBAC)</strong>
                            <span><?= $overviewData['operations']['total_users'] ?> usuário(s) ativos no sistema</span>
                        </div>
                        <div class="integration-badge">
                            <span class="badge-tag info"><i class="ti ti-lock"></i> Seguro</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NAVEGAÇÃO DE MÓDULOS (ACESSO RÁPIDO) -->
<div class="dashboard-group">
    <div class="section-heading">
        <h2>Módulos do Sistema</h2>
        <span>Acesso direto aos ambientes de gestão</span>
    </div>

    <section class="module-grid" aria-label="Módulos administrativos">
        <?php foreach ($navigation as $key => $item): ?>
            <?php if ($key === 'overview') continue; ?>
            <?php if (! \App\Libraries\UserPermissions::canAccessRouteKey($key)) continue; ?>
            <a class="module-card" href="<?= site_url($item['path']) ?>">
                <div class="module-card-top">
                    <span class="module-icon" aria-hidden="true"><i class="ti <?= esc($item['icon']) ?>"></i></span>
                    <i class="ti ti-arrow-right module-arrow" aria-hidden="true"></i>
                </div>
                <h3><?= esc($item['label']) ?></h3>
                <p><?= esc($item['description']) ?></p>
            </a>
        <?php endforeach; ?>
    </section>
</div>
