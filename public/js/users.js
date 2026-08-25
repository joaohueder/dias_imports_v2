document.addEventListener('DOMContentLoaded', () => {
    const usersModule = document.querySelector('[data-users-module]');
    if (!usersModule) return;

    const usersGrid = usersModule.querySelector('[data-users-grid]');
    const footerTelemetry = document.querySelector('[data-footer-telemetry]');
    
    // Pill badges
    const pillTotal = document.querySelector('.filter-pill[data-filter="all"] .pill-badge');
    const pillActive = document.querySelector('.filter-pill[data-filter="active"] .pill-badge');
    const pillInactive = document.querySelector('.filter-pill[data-filter="inactive"] .pill-badge');
    const pillAdmin = document.querySelector('.filter-pill[data-filter="admin"] .pill-badge');

    let abortController = null;

    const fetchUsersFeed = async () => {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        try {
            const response = await fetch(`${window.location.origin}/usuarios/feed?_t=${Date.now()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: abortController.signal
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();
            if (data.success) {
                // Update grid
                if (usersGrid && data.htmlCards) {
                    usersGrid.innerHTML = data.htmlCards;
                }

                // Update pills
                if (data.counts) {
                    if (pillTotal) pillTotal.textContent = data.counts.total;
                    if (pillActive) pillActive.textContent = data.counts.active;
                    if (pillInactive) pillInactive.textContent = data.counts.inactive;
                    if (pillAdmin) pillAdmin.textContent = data.counts.admin;
                }

                // Update footer telemetry
                if (footerTelemetry && data.footerHtml) {
                    footerTelemetry.innerHTML = data.footerHtml;
                }
                
                // Re-apply current filter if any
                const activeFilterBtn = document.querySelector('.filter-pill.active');
                if (activeFilterBtn) {
                    const filterValue = activeFilterBtn.getAttribute('data-filter');
                    const searchInput = document.getElementById('users-search-input');
                    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                    applyFilters(filterValue, searchTerm);
                }
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error fetching users feed:', error);
            }
        }
    };

    const applyFilters = (filterValue, searchTerm) => {
        const cards = document.querySelectorAll('[data-user-card]');
        let visibleCount = 0;

        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            const role = card.getAttribute('data-role');
            const name = card.getAttribute('data-name') || '';
            const email = card.getAttribute('data-email') || '';

            let matchFilter = false;
            if (filterValue === 'all') matchFilter = true;
            else if (filterValue === 'active' && status === 'active') matchFilter = true;
            else if (filterValue === 'inactive' && status === 'inactive') matchFilter = true;
            else if (filterValue === 'admin' && role === 'admin') matchFilter = true;

            let matchSearch = true;
            if (searchTerm) {
                matchSearch = name.includes(searchTerm) || email.includes(searchTerm);
            }

            if (matchFilter && matchSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const emptyState = document.querySelector('[data-users-empty-search]');
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
        }
    };

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (abortController) abortController.abort();
    });
});