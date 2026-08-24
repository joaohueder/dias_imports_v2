<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="card bg-dark-eval-1 border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <div>
                    <h3 class="card-title text-light mb-1">
                        <i class="ti ti-chart-bar text-primary me-2"></i>Estatísticas de Acesso: <?= esc($product->name) ?>
                    </h3>
                    <div class="text-muted small">
                        Visualizações, cliques no botão de compra/WhatsApp e taxas de conversão da landing page pública.
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= site_url('p/' . $product->slug) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="ti ti-external-link me-1"></i>Ver Landing Page
                    </a>
                    <a href="<?= site_url('produtos/' . $product->id . '/editar') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-edit me-1"></i>Editar Produto
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtro de Período -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div class="btn-group" role="group" id="statsPeriodButtons">
                        <button type="button" class="btn btn-sm btn-outline-primary <?= ($stats['period'] ?? 7) == 7 ? 'active' : '' ?>" onclick="loadProductStats(7)">Últimos 7 dias</button>
                        <button type="button" class="btn btn-sm btn-outline-primary <?= ($stats['period'] ?? 7) == 14 ? 'active' : '' ?>" onclick="loadProductStats(14)">14 dias</button>
                        <button type="button" class="btn btn-sm btn-outline-primary <?= ($stats['period'] ?? 7) == 21 ? 'active' : '' ?>" onclick="loadProductStats(21)">21 dias</button>
                        <button type="button" class="btn btn-sm btn-outline-primary <?= ($stats['period'] ?? 7) == 30 ? 'active' : '' ?>" onclick="loadProductStats(30)">30 dias</button>
                    </div>
                    <span class="text-muted small"><i class="ti ti-clock me-1"></i>Dados atualizados em tempo real</span>
                </div>

                <!-- Cards de Métricas Principais -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card bg-dark-eval-2 border-0 p-3 h-100">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-primary-lt text-primary avatar-md me-3">
                                    <i class="ti ti-eye fs-2"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">Visualizações (Pageviews)</div>
                                    <div class="h2 mb-0 text-light fw-bold" id="stat-pageviews"><?= number_format($stats['totals']['pageviews'] ?? 0, 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card bg-dark-eval-2 border-0 p-3 h-100">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-success-lt text-success avatar-md me-3">
                                    <i class="ti ti-click fs-2"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">Cliques em Compra (CTA)</div>
                                    <div class="h2 mb-0 text-success fw-bold" id="stat-cta-clicks"><?= number_format($stats['totals']['cta_clicks'] ?? 0, 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card bg-dark-eval-2 border-0 p-3 h-100">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-info-lt text-info avatar-md me-3">
                                    <i class="ti ti-pinned-filled fs-2"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">Cliques no Botão Flutuante</div>
                                    <div class="h2 mb-0 text-info fw-bold" id="stat-sticky-clicks"><?= number_format($stats['totals']['sticky_cta_clicks'] ?? 0, 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card bg-dark-eval-2 border-0 p-3 h-100">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-warning-lt text-warning avatar-md me-3">
                                    <i class="ti ti-percentage fs-2"></i>
                                </span>
                                <div>
                                    <div class="text-muted small">Taxa de Conversão (CTR)</div>
                                    <div class="h2 mb-0 text-warning fw-bold" id="stat-ctr"><?= number_format($stats['conversionRate'] ?? 0, 1, ',', '.') ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Detalhamento Diário -->
                <div class="table-responsive mt-3">
                    <table class="table table-vcenter card-table table-dark">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th class="text-center">Visualizações</th>
                                <th class="text-center">Cliques CTA</th>
                                <th class="text-center">Cliques Flutuante</th>
                                <th class="text-center">Total de Cliques</th>
                                <th class="text-end">Taxa de Conversão</th>
                            </tr>
                        </thead>
                        <tbody id="statsDailyTableBody">
                            <?php if (!empty($stats['daily'])): ?>
                                <?php foreach (array_reverse($stats['daily']) as $day): ?>
                                    <?php 
                                        $dTotalClicks = ($day['cta_click'] ?? 0) + ($day['sticky_cta_click'] ?? 0) + ($day['whatsapp_click'] ?? 0);
                                        $dPvs = $day['pageview'] ?? 0;
                                        $dCtr = $dPvs > 0 ? round(($dTotalClicks / $dPvs) * 100, 1) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= date('d/m/Y', strtotime($day['date'])) ?></strong>
                                            <span class="text-muted ms-1">(<?= date('D', strtotime($day['date'])) ?>)</span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-blue-lt"><?= $dPvs ?></span></td>
                                        <td class="text-center"><span class="badge bg-green-lt"><?= $day['cta_click'] ?? 0 ?></span></td>
                                        <td class="text-center"><span class="badge bg-cyan-lt"><?= $day['sticky_cta_click'] ?? 0 ?></span></td>
                                        <td class="text-center"><strong><?= $dTotalClicks ?></strong></td>
                                        <td class="text-end text-success fw-bold"><?= number_format($dCtr, 1, ',', '.') ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Nenhum evento registrado no período selecionado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadProductStats(days) {
    const btnGroup = document.getElementById('statsPeriodButtons');
    btnGroup.querySelectorAll('button').forEach(btn => {
        btn.classList.remove('active');
        if (btn.innerText.includes(days + ' dias') || (days === 7 && btn.innerText.includes('7 dias'))) {
            btn.classList.add('active');
        }
    });

    fetch('<?= site_url('produtos/' . $product->id . '/stats-data') ?>?stats_period=' + days)
        .then(res => res.json())
        .then(data => {
            if (data.error) return;
            document.getElementById('stat-pageviews').innerText = new Intl.NumberFormat('pt-BR').format(data.totals?.pageviews || 0);
            document.getElementById('stat-cta-clicks').innerText = new Intl.NumberFormat('pt-BR').format(data.totals?.cta_clicks || 0);
            document.getElementById('stat-sticky-clicks').innerText = new Intl.NumberFormat('pt-BR').format(data.totals?.sticky_cta_clicks || 0);
            document.getElementById('stat-ctr').innerText = (data.conversionRate || 0).toFixed(1).replace('.', ',') + '%';

            const tbody = document.getElementById('statsDailyTableBody');
            if (!data.daily || Object.keys(data.daily).length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum evento registrado no período selecionado.</td></tr>';
                return;
            }

            let rows = '';
            const dailyArr = Object.values(data.daily).reverse();
            dailyArr.forEach(d => {
                const totalCl = (d.cta_click || 0) + (d.sticky_cta_click || 0) + (d.whatsapp_click || 0);
                const pvs = d.pageview || 0;
                const ctr = pvs > 0 ? ((totalCl / pvs) * 100).toFixed(1).replace('.', ',') : '0,0';
                const parts = d.date.split('-');
                const dFormatted = `${parts[2]}/${parts[1]}/${parts[0]}`;

                rows += `
                    <tr>
                        <td><strong>${dFormatted}</strong></td>
                        <td class="text-center"><span class="badge bg-blue-lt">${pvs}</span></td>
                        <td class="text-center"><span class="badge bg-green-lt">${d.cta_click || 0}</span></td>
                        <td class="text-center"><span class="badge bg-cyan-lt">${d.sticky_cta_click || 0}</span></td>
                        <td class="text-center"><strong>${totalCl}</strong></td>
                        <td class="text-end text-success fw-bold">${ctr}%</td>
                    </tr>
                `;
            });
            tbody.innerHTML = rows;
        })
        .catch(err => console.error('Erro ao atualizar estatísticas:', err));
}
</script>
