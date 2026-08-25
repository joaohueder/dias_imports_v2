<div class="overview-dashboard" id="overviewDashboard" data-overview-module>
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

    function initTooltipHandlers() {
        const containers = document.querySelectorAll('.overview-chart-container');
        if (!containers.length) return;

        containers.forEach(container => {
            const tooltip = container.querySelector('.chart-tooltip');
            const points = container.querySelectorAll('.chart-point, .chart-bar');

            if (!tooltip || !points.length) return;

            points.forEach(point => {
                point.addEventListener('mouseenter', function (e) {
                    const val = this.getAttribute('data-val') || 0;
                    const label = this.getAttribute('data-label') || '';
                    const type = this.getAttribute('data-type') || '';
                    
                    let dotClass = 'dot-purple';
                    if (type === 'PageViews') {
                        dotClass = 'dot-purple';
                    } else if (type === 'Visualizações') {
                        dotClass = 'dot-blue';
                    } else if (type.includes('Cliques') || type === 'Cliques CTA') {
                        dotClass = this.classList.contains('bar-amber') ? 'dot-amber' : 'dot-green';
                    } else if (type.includes('Lead') || type === 'Leads VIP') {
                        dotClass = 'dot-emerald';
                    }

                    tooltip.innerHTML = `
                        <div class="tooltip-header">${label}</div>
                        <div class="tooltip-body">
                            <span class="tooltip-dot ${dotClass}"></span>
                            <span>${type}: <strong>${val}</strong></span>
                        </div>
                    `;

                    tooltip.style.display = 'block';
                    tooltip.style.opacity = '1';

                    const rect = this.getBoundingClientRect();
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
        });
    }

    // Inicializa tooltips inicialmente
    initTooltipHandlers();
});
</script>
