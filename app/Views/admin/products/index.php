<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$counts = [
    'total' => count($products),
    'active' => count(array_filter($products, fn($p) => $p->active)),
    'inactive' => count(array_filter($products, fn($p) => !$p->active)),
];
?>
<section class="users-view-container" data-products-module>
    <!-- Top toolbar -->
    <div class="users-header-actions">
        <div class="users-search-box">
            <i class="ti ti-search users-search-icon" aria-hidden="true"></i>
            <input type="text" id="products-search-input" class="users-search-input" placeholder="Filtrar por nome..." aria-label="Filtrar produtos por nome" data-products-filter-input>
        </div>

        <div class="users-filter-pills" role="tablist" aria-label="Filtros de status de produtos">
            <button type="button" class="filter-pill active" data-filter="all" role="tab" aria-selected="true">
                Todos <span class="pill-badge"><?= $counts['total'] ?></span>
            </button>
            <button type="button" class="filter-pill" data-filter="active" role="tab" aria-selected="false">
                Ativos <span class="pill-badge"><?= $counts['active'] ?></span>
            </button>
            <button type="button" class="filter-pill" data-filter="inactive" role="tab" aria-selected="false">
                Inativos <span class="pill-badge"><?= $counts['inactive'] ?></span>
            </button>
        </div>

        <?php if (\App\Libraries\UserPermissions::hasPermission('products', 'create')): ?>
        <div class="users-create-action">
            <a href="<?= site_url('produtos/novo') ?>" class="button primary">
                <i class="ti ti-plus" aria-hidden="true"></i>
                <span>Novo Produto</span>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cards Grid -->
    <div class="users-grid products-grid" data-products-grid>
        <?= view('admin/products/_cards', ['products' => $products]) ?>
    </div>

    <!-- Empty search state -->
    <div class="users-empty-search col-span-full" data-products-empty style="display: none; grid-column: 1 / -1; width: 100%; padding: 60px 20px; text-align: center; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: rgb(var(--muted));">
        <div class="empty-icon" style="width: 64px; height: 64px; border-radius: 16px; background: rgb(var(--surface-secondary)); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
            <i class="ti ti-inbox" style="font-size: 32px; color: rgb(var(--muted));"></i>
        </div>
        <p style="margin: 0; font-size: 15px; font-weight: 600; color: rgb(var(--foreground));">Nenhum produto encontrado.</p>
        <span style="font-size: 14px; margin-bottom: 12px;">Tente ajustar a busca ou o status selecionado.</span>
        <button type="button" class="button secondary" data-clear-filters style="margin-top: 4px;">
            <i class="ti ti-filter-off" aria-hidden="true"></i>
            <span>Limpar Filtros</span>
        </button>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('[data-products-filter-input]');
    const filterPills = document.querySelectorAll('.filter-pill');
    const productCards = document.querySelectorAll('[data-product-card]');
    const emptyState = document.querySelector('[data-products-empty]');
    const clearFiltersBtn = document.querySelector('[data-clear-filters]');
    
    let currentFilter = 'all';
    let currentSearch = '';

    function applyFilters() {
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const name = card.dataset.name || '';
            const status = card.dataset.status || '';
            
            const matchesSearch = currentSearch === '' || name.includes(currentSearch);
            const matchesStatus = currentFilter === 'all' || status === currentFilter;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value.toLowerCase().trim();
            applyFilters();
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            filterPills.forEach(p => {
                p.classList.remove('active');
                p.setAttribute('aria-selected', 'false');
            });
            pill.classList.add('active');
            pill.setAttribute('aria-selected', 'true');
            currentFilter = pill.dataset.filter;
            applyFilters();
        });
    });

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            currentSearch = '';
            
            const allPill = document.querySelector('.filter-pill[data-filter="all"]');
            if (allPill) {
                allPill.click();
            } else {
                currentFilter = 'all';
                applyFilters();
            }
        });
    }
});

function toggleProductStatus(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?= csrf_hash() ?>';
    const csrfHeader = document.querySelector('meta[name="csrf-header"]')?.getAttribute('content') || '<?= csrf_token() ?>';

    const formData = new FormData();
    formData.append(csrfHeader, csrfToken);

    fetch(`<?= site_url('produtos') ?>/${id}/status`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erro ao alterar status.');
        }
    })
    .catch(() => {
        window.location.reload();
    });
}
</script>
<?= $this->endSection() ?>