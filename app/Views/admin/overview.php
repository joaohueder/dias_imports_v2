<div class="overview-dashboard" id="overviewDashboard" data-overview-module data-realtime-active="<?= !empty($isRealtimeActive) ? '1' : '0' ?>" data-realtime-interval="<?= esc((string) ($realtimeInterval ?? 5)) ?>">
    <div id="overviewContentWrapper">
        <?= view('admin/overview_content', [
            'overviewData' => $overviewData,
            'firstName' => $firstName,
            'navigation' => $navigation,
        ]) ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overviewModule = document.querySelector('[data-overview-module]');
    if (!overviewModule) return;

    const isRealtimeActive = overviewModule.getAttribute('data-realtime-active') === '1';
    const realtimeInterval = parseInt(overviewModule.getAttribute('data-realtime-interval'), 10) || 5;
    const contentWrapper = document.getElementById('overviewContentWrapper');
    const footerTelemetry = document.querySelector('[data-footer-telemetry]');

    function initTooltipHandlers() {
        const tooltip = document.getElementById('chartTooltip');
        const points = document.querySelectorAll('.chart-point, .chart-bar');

        if (!tooltip || !points.length) return;

        points.forEach(point => {
            point.addEventListener('mouseenter', function (e) {
                const val = this.getAttribute('data-val') || 0;
                const label = this.getAttribute('data-label') || '';
                const type = this.getAttribute('data-type') || '';
                const isPurple = type === 'PageViews';

                tooltip.innerHTML = `
                    <div class="tooltip-header">${label}</div>
                    <div class="tooltip-body">
                        <span class="tooltip-dot ${isPurple ? 'dot-purple' : 'dot-green'}"></span>
                        <span>${type}: <strong>${val}</strong></span>
                    </div>
                `;

                tooltip.style.display = 'block';
                tooltip.style.opacity = '1';

                const rect = this.getBoundingClientRect();
                const container = this.closest('.overview-chart-container');
                if (!container) return;
                const parentRect = container.getBoundingClientRect();
                const tooltipRect = tooltip.getBoundingClientRect();
                
                let left = rect.left - parentRect.left + (rect.width / 2);
                let top = rect.top - parentRect.top - 8;

                // Ajuste horizontal para não vazar nas bordas
                const halfWidth = tooltipRect.width / 2;
                if (left + halfWidth > parentRect.width - 8) {
                    left = parentRect.width - halfWidth - 8;
                } else if (left - halfWidth < 8) {
                    left = halfWidth + 8;
                }

                // Ajuste vertical se estiver muito no topo
                if (top - tooltipRect.height < 0) {
                    top = rect.top - parentRect.top + rect.height + 8;
                    tooltip.style.transform = 'translate(-50%, 0)';
                } else {
                    tooltip.style.transform = 'translate(-50%, -100%)';
                }

                tooltip.style.left = left + 'px';
                tooltip.style.top = top + 'px';
            });

            point.addEventListener('mouseleave', function () {
                tooltip.style.opacity = '0';
                tooltip.style.display = 'none';
            });
        });
    }

    // Inicializa tooltips inicialmente
    initTooltipHandlers();

    let abortController = null;
    let pollingTimer = null;

    const fetchOverviewFeed = async () => {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        try {
            const response = await fetch(`${window.location.origin}/visao-geral/feed?_t=${Date.now()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: abortController.signal
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();
            if (data.success && data.htmlContent) {
                // Atualiza conteúdo completo do dashboard
                if (contentWrapper) {
                    contentWrapper.innerHTML = data.htmlContent;
                    initTooltipHandlers();
                }

                // Atualiza telemetria de rodapé se presente
                if (footerTelemetry && data.footerHtml) {
                    footerTelemetry.innerHTML = data.footerHtml;
                }
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error fetching overview feed:', error);
            }
        } finally {
            if (isRealtimeActive) {
                pollingTimer = setTimeout(fetchOverviewFeed, realtimeInterval * 1000);
            }
        }
    };

    if (isRealtimeActive) {
        pollingTimer = setTimeout(fetchOverviewFeed, realtimeInterval * 1000);
    }
});
</script>
