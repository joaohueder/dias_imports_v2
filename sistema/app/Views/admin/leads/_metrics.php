<?php
    $total = $totalLeads ?? 0;
    $today = $todayCount ?? 0;
    $yesterday = $yesterdayCount ?? 0;
    $period = $periodDays ?? 7;
    $evoList = $evolution ?? [];

    $diff = $today - $yesterday;
    $diffClass = 'neutral';
    $diffIcon = 'ti-minus';
    $diffText = 'igual a ontem';

    if ($diff > 0) {
        $diffClass = 'positive';
        $diffIcon = 'ti-trending-up';
        $diffText = "+{$diff} em relação a ontem";
    } elseif ($diff < 0) {
        $diffClass = 'negative';
        $diffIcon = 'ti-trending-down';
        $absDiff = abs($diff);
        $diffText = "-{$absDiff} em relação a ontem";
    }
?>
<div class="leads-dash-grid">
    <!-- Card 1: Total de Leads -->
    <div class="lead-kpi-card total-leads">
        <div class="kpi-header-row">
            <span class="kpi-label">Total de Leads</span>
            <div class="kpi-icon-pill" aria-hidden="true">
                <i class="ti ti-users-group"></i>
            </div>
        </div>
        <div>
            <div class="kpi-big-number" data-kpi-total><?= number_format($total, 0, ',', '.') ?></div>
            <div class="kpi-subtext" style="margin-top: 6px;">Base consolidada de contatos</div>
        </div>
        <div class="kpi-subtext">
            <i class="ti ti-database-check" style="color: #10b981;"></i> Captura ativa na landing
        </div>
    </div>

    <!-- Card 2: Captados Hoje x Ontem -->
    <div class="lead-kpi-card daily-comparison">
        <div class="kpi-header-row">
            <span class="kpi-label">Hoje x Ontem</span>
            <div class="kpi-icon-pill success" aria-hidden="true">
                <i class="ti ti-chart-arrows"></i>
            </div>
        </div>
        <div class="kpi-comparison-body">
            <div class="compare-numbers">
                <div>
                    <span class="num-today" data-kpi-today><?= $today ?></span>
                    <span class="num-yesterday-pill">hoje</span>
                </div>
                <div style="color: rgb(var(--muted)); font-size: 16px;">/</div>
                <div>
                    <span style="font-size: 22px; font-weight: 750; color: rgb(var(--muted));" data-kpi-yesterday><?= $yesterday ?></span>
                    <span class="num-yesterday-pill">ontem</span>
                </div>
            </div>
        </div>
        <div>
            <span class="kpi-trend-pill <?= esc($diffClass) ?>">
                <i class="ti <?= esc($diffIcon) ?>" aria-hidden="true"></i>
                <span><?= esc($diffText) ?></span>
            </span>
        </div>
    </div>

    <!-- Card 3: Evolução dos Últimos Dias (com filtro) -->
    <div class="lead-kpi-card evolution-chart">
        <div class="kpi-header-row">
            <div>
                <span class="kpi-label">Evolução de Captação</span>
                <div class="kpi-subtext" style="margin-top: 2px;">Desempenho diário de cadastros</div>
            </div>
            <div class="evolution-filter-pills" role="group" aria-label="Filtrar período do gráfico">
                <?php foreach ([7, 14, 21, 30] as $p): ?>
                    <button type="button" class="period-pill-btn <?= $period === $p ? 'active' : '' ?>" data-period-btn="<?= $p ?>" title="<?= $p ?> dias">
                        <?= $p ?>d
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <div class="evolution-mini-chart" role="img" aria-label="Gráfico de evolução dos leads nos últimos <?= $period ?> dias">
                <?php foreach ($evoList as $day): ?>
                    <?php
                        $barHeight = max(8, $day['percentage']);
                        $isTodayBar = ($day['date'] === date('Y-m-d'));
                    ?>
                    <div class="chart-bar-column <?= $isTodayBar ? 'is-today-bar' : '' ?>">
                        <span class="chart-bar-tooltip"><?= esc($day['dayLabel']) ?>: <?= $day['count'] ?> lead(s)</span>
                        <div class="chart-bar-track">
                            <div class="chart-bar-fill" style="height: <?= $barHeight ?>%;"></div>
                        </div>
                        <span class="chart-bar-label"><?= esc($day['dayLabel']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

