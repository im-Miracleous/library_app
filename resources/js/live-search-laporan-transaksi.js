document.addEventListener('DOMContentLoaded', function () {
    // State
    let state = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        page: new URLSearchParams(window.location.search).get('page') || 1,
        limit: new URLSearchParams(window.location.search).get('limit') || 10,
        sort: new URLSearchParams(window.location.search).get('sort') || 'id_peminjaman',
        direction: new URLSearchParams(window.location.search).get('direction') || 'desc',
        start_date: new URLSearchParams(window.location.search).get('start_date') || '',
        end_date: new URLSearchParams(window.location.search).get('end_date') || '',
        status: new URLSearchParams(window.location.search).get('status') || ''
    };

    // Elements
    const searchInput = document.getElementById('searchTransaksiInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;

    // Initialize Controls
    setupControls();

    function setupControls() {
        // LIMIT SELECT
        const limitSelect = document.querySelector('select');
        if (limitSelect) {
            limitSelect.removeAttribute('onchange'); // Remove inline handler if exists

            // Set initial value from URL
            Array.from(limitSelect.options).forEach(opt => {
                const optVal = opt.value;
                if (optVal === state.limit || new RegExp(`[?&]limit=${state.limit}(?:&|$)`).test(optVal)) {
                    limitSelect.value = optVal;
                }
            });

            limitSelect.addEventListener('change', (e) => {
                // Extract limit if value is URL
                const val = e.target.value;
                const match = val.match(/limit=(\d+)/);
                state.limit = match ? match[1] : val;

                state.page = 1;
                fetchData();
            });
        }

        // SORT HEADERS (Delegated from x-datatable headers)
        const headers = document.querySelectorAll('th[onclick]');
        headers.forEach(th => {
            // Override the onclick attribute
            const originalOnclick = th.getAttribute('onclick');
            th.removeAttribute('onclick'); // Disable default redirection
            th.style.cursor = 'pointer';

            // Parse sort column from the original URL in onclick
            const matchSort = originalOnclick.match(/sort=([^&']+)/);
            if (matchSort) {
                th.dataset.sort = matchSort[1];

                th.addEventListener('click', () => {
                    const col = th.dataset.sort;
                    if (state.sort === col) {
                        state.direction = state.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        state.sort = col;
                        state.direction = 'asc';
                    }
                    state.page = 1;
                    fetchData();
                });
            }
        });

        // PAGINATION
        setupPaginationDelegation();

        // SEARCH INPUT
        if (searchInput) {
            searchInput.value = state.search;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    state.search = e.target.value;
                    state.page = 1;
                    fetchData();
                }, 300);
            });
        }

        // EXTERNAL FILTERS (Date, Status)
        const filterForm = document.querySelector('form[action*="laporan"]');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(filterForm);
                state.start_date = formData.get('start_date');
                state.end_date = formData.get('end_date');
                state.status = formData.get('status');
                state.page = 1;
                fetchData();
            });
        }
    }

    function setupPaginationDelegation() {
        // Delegate click on pagination links
        const paginationContainer = document.querySelector('.p-4.border-t');
        if (!paginationContainer) return;

        paginationContainer.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            e.preventDefault();

            // Extract page number from href
            try {
                const url = new URL(link.href);
                const page = url.searchParams.get('page');
                if (page) {
                    state.page = page;
                    fetchData();
                }
            } catch (err) {
                console.error('Invalid Pagination URL', link.href);
            }
        });
    }

    async function fetchData() {
        const params = new URLSearchParams();
        Object.keys(state).forEach(key => {
            if (state[key]) params.set(key, state[key]);
        });

        // Update Browser URL (optional, good for UX)
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({}, '', newUrl);

        tableBody.style.opacity = '0.5';

        try {
            const response = await fetch(newUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const json = await response.json();

            if (json.data) {
                renderTable(json.data);
                updatePagination(json);
                updateSortIcons(); // Update sort visual indicators
            }
        } catch (error) {
            console.error('Fetch error:', error);
            tableBody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-red-500">Gagal memuat data.</td></tr>`;
        } finally {
            tableBody.style.opacity = '1';
        }
    }

    // Helper for Highlight
    function highlightText(text, query) {
        if (!query || !text) return text;
        const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${safeQuery})`, 'gi');
        return String(text).replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-600/50 text-slate-900 dark:text-white rounded px-0.5">$1</span>');
    }

    function renderTable(data) {
        tableBody.innerHTML = '';
        const searchQuery = searchInput ? searchInput.value.trim() : '';

        if (data.length === 0) {
            tableBody.innerHTML = `<tr>
                <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-4xl opacity-50">search_off</span>
                        <span>Tidak ada data ditemukan.</span>
                    </div>
                </td>
            </tr>`;
            return;
        }

        data.forEach(item => {
            // Badges
            const badges = {
                'berjalan': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                'selesai': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                'terlambat': 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400'
            };
            const badgeClass = badges[item.status_transaksi] || 'bg-slate-100 text-slate-600';

            // Dates
            const datePinjam = new Date(item.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const dateJatuhTempo = new Date(item.tanggal_jatuh_tempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            // Highlight
            const idHighlighted = highlightText(item.id_peminjaman, searchQuery);
            const namaHighlighted = highlightText(item.nama_anggota, searchQuery);

            const row = `
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group animate-enter">
                    <td class="p-4 pl-6 font-mono text-sm text-slate-600 dark:text-white/70 whitespace-nowrap">
                        <span class="font-bold text-primary dark:text-accent">${idHighlighted}</span>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 dark:text-white">${namaHighlighted}</span>
                            <span class="text-xs text-slate-500 dark:text-white/50">${item.email_anggota || '-'}</span>
                        </div>
                    </td>
                    <td class="p-4 text-slate-600 dark:text-white/70">
                        ${datePinjam}
                    </td>
                    <td class="p-4 text-slate-600 dark:text-white/70">
                        ${dateJatuhTempo}
                    </td>
                    <td class="p-4 text-center font-bold text-slate-700 dark:text-white">${item.total_buku}</td>
                    <td class="p-4 text-right pr-6">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide ${badgeClass}">
                            ${item.status_transaksi}
                        </span>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    function updatePagination(json) {
        const footer = document.querySelector('.p-4.border-t');
        if (!footer) return;

        const total = json.total;
        const perPage = parseInt(state.limit);
        const currentPage = parseInt(state.page);
        const from = total === 0 ? 0 : ((currentPage - 1) * perPage) + 1;
        const to = Math.min(currentPage * perPage, total);

        // Update info text
        const infoDiv = footer.querySelector('div.text-xs');
        if (infoDiv) {
            infoDiv.innerHTML = `Showing <span class="font-bold">${from}</span> to <span class="font-bold">${to}</span> of <span class="font-bold">${total}</span> entries`;
        }

        const linksContainer = footer.querySelector('nav') || footer.querySelector('div.flex.justify-between') || footer.lastElementChild;

        if (linksContainer) {
            let html = buildPaginationHTML(total, perPage, currentPage);
            linksContainer.innerHTML = html;
        }
    }

    function buildPaginationHTML(total, limit, page) {
        const totalPages = Math.ceil(total / limit);
        if (totalPages <= 1) return '';

        let html = '<div class="flex flex-wrap gap-1">';

        // Prev
        if (page > 1) {
            html += `<a href="#" data-page="${page - 1}" class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 text-xs">Previous</a>`;
        } else {
            html += `<span class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-400 dark:text-white/40 text-xs cursor-not-allowed">Previous</span>`;
        }

        // Simple range for now
        let start = Math.max(1, page - 2);
        let end = Math.min(totalPages, page + 2);

        if (start > 1) {
            html += `<a href="#" data-page="1" class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 text-xs">1</a>`;
            if (start > 2) html += `<span class="px-2 text-slate-400">...</span>`;
        }

        for (let i = start; i <= end; i++) {
            const active = i === page ? 'bg-primary text-white border-primary dark:bg-accent dark:text-primary-dark dark:border-accent' : 'bg-white dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5';
            html += `<a href="#" data-page="${i}" class="px-3 py-1 rounded border text-xs font-bold ${active}">${i}</a>`;
        }

        if (end < totalPages) {
            if (end < totalPages - 1) html += `<span class="px-2 text-slate-400">...</span>`;
            html += `<a href="#" data-page="${totalPages}" class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 text-xs">${totalPages}</a>`;
        }

        // Next
        if (page < totalPages) {
            html += `<a href="#" data-page="${page + 1}" class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 text-xs ml-1">Next</a>`;
        } else {
            html += `<span class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-400 dark:text-white/40 text-xs cursor-not-allowed ml-1">Next</span>`;
        }

        html += '</div>';
        return html;
    }

    function updateSortIcons() {
        const headers = document.querySelectorAll('th[data-sort]');
        headers.forEach(th => {
            const col = th.dataset.sort;
            const iconContainer = th.querySelector('.material-symbols-outlined');
            if (iconContainer) {
                if (state.sort === col) {
                    iconContainer.textContent = state.direction === 'asc' ? 'arrow_upward' : 'arrow_downward';
                    iconContainer.classList.remove('opacity-30');
                } else {
                    iconContainer.textContent = 'unfold_more';
                    iconContainer.classList.add('opacity-30');
                }
            }
        });
    }
});
