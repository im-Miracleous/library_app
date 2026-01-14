document.addEventListener('DOMContentLoaded', function () {
    // State
    let state = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        page: new URLSearchParams(window.location.search).get('page') || 1,
        limit: new URLSearchParams(window.location.search).get('limit') || 10,
        sort: new URLSearchParams(window.location.search).get('sort') || '',
        direction: new URLSearchParams(window.location.search).get('direction') || '',
        status: new URLSearchParams(window.location.search).get('status') || ''
    };

    // Elements
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const statusFilter = document.getElementById('statusFilter');
    let timeout = null;

    // Initialize Controls
    setupControls();

    function setupControls() {
        // LIMIT SELECT
        const limitSelect = document.querySelector('select:not(#statusFilter)');
        if (limitSelect) {
            limitSelect.removeAttribute('onchange');

            Array.from(limitSelect.options).forEach(opt => {
                const optVal = opt.value;
                if (optVal === state.limit || new RegExp(`[?&]limit=${state.limit}(?:&|$)`).test(optVal)) {
                    limitSelect.value = optVal;
                }
            });

            limitSelect.addEventListener('change', (e) => {
                const val = e.target.value;
                const match = val.match(/limit=(\d+)/);
                state.limit = match ? match[1] : val;

                state.page = 1;
                fetchData();
            });
        }

        // STATUS FILTER
        if (statusFilter) {
            statusFilter.value = state.status;
            statusFilter.addEventListener('change', (e) => {
                state.status = e.target.value;
                state.page = 1;
                fetchData();
            });
        }

        // SORT HEADERS
        const headers = document.querySelectorAll('th[onclick]');
        headers.forEach(th => {
            const originalOnclick = th.getAttribute('onclick');
            th.removeAttribute('onclick');
            th.style.cursor = 'pointer';

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
    }

    function setupPaginationDelegation() {
        const paginationContainer = document.querySelector('.p-4.border-t');
        if (!paginationContainer) return;

        paginationContainer.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            e.preventDefault();

            let targetPage = null;

            // Priority 1: Check data-page attribute (AJAX generated)
            if (link.dataset.page) {
                targetPage = link.dataset.page;
            }
            // Priority 2: Check standard href (Server-side generated)
            else if (link.href && link.href !== '#' && !link.href.endsWith('#')) {
                try {
                    const url = new URL(link.href);
                    targetPage = url.searchParams.get('page');
                } catch (err) {
                    console.error('Invalid Pagination URL', link.href);
                }
            }

            if (targetPage) {
                state.page = targetPage;
                fetchData();
            }
        });
    }

    async function fetchData() {
        const params = new URLSearchParams();
        Object.keys(state).forEach(key => {
            if (state[key]) params.set(key, state[key]);
        });

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
                updateSortIcons();
            }
        } catch (error) {
            console.error('Fetch error:', error);
            tableBody.innerHTML = `<tr><td colspan="7" class="p-8 text-center text-red-500">Gagal memuat data.</td></tr>`;
        } finally {
            tableBody.style.opacity = '1';
        }
    }

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
                <td colspan="7" class="p-12 text-center text-slate-400 dark:text-white/40">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-4xl opacity-50">data_loss_prevention</span>
                        <span>Tidak ada data peminjaman ditemukan.</span>
                    </div>
                </td>
            </tr>`;
            return;
        }

        data.forEach(item => {
            const datePinjam = new Date(item.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            // Late Check Logic
            const dueTime = new Date(item.tanggal_jatuh_tempo);
            const dueStr = dueTime.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            dueTime.setHours(0, 0, 0, 0);

            const isLate = dueTime < today && item.status_transaksi === 'berjalan';
            const dueClass = isLate ? 'text-red-600 font-bold animate-pulse' : 'text-slate-600 dark:text-white/70';

            // Badges
            const badges = {
                'berjalan': 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                'selesai': 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
                'menunggu_verifikasi': 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400',
                'ditolak': 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400',
                'terlambat': 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400'
            };
            const badgeClass = badges[item.status_transaksi] || 'bg-slate-100 text-slate-600';

            // Highlight
            const codeHighlighted = highlightText(item.id_peminjaman, searchQuery);
            const nameHighlighted = highlightText(item.nama_anggota, searchQuery);

            const row = document.createElement('tr');
            row.className = 'hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group animate-enter';

            row.innerHTML = `
                <td class="p-4 pl-6 font-mono font-bold text-primary dark:text-accent whitespace-nowrap">
                    ${codeHighlighted}
                </td>
                <td class="p-4">
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-800 dark:text-white">${nameHighlighted}</span>
                        <span class="text-xs text-slate-500 dark:text-white/50">${item.email_anggota || '-'}</span>
                    </div>
                </td>
                <td class="p-4 text-center font-bold text-slate-700 dark:text-white">
                    <div class="flex flex-col items-center gap-0.5">
                        <span>${item.total_buku}</span>
                        ${item.is_extended && item.total_dikembalikan > 0 ? `<span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">R:${item.total_dikembalikan}</span>` : ''}
                    </div>
                </td>
                <td class="p-4 text-slate-600 dark:text-white/70">
                    ${datePinjam}
                </td>
                <td class="p-4">
                    <div class="flex flex-col">
                        <span class="${dueClass}">
                            ${dueStr}
                        </span>
                        <div class="flex items-center gap-1 mt-1">
                            ${isLate ? '<span class="text-[10px] bg-red-100 text-red-600 px-1 rounded uppercase font-bold">Telat</span>' : ''}
                            ${item.is_extended ? '<span class="text-[9px] bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 px-1.5 py-0.5 rounded uppercase font-bold tracking-wider w-fit">Extend</span>' : ''}
                        </div>
                    </div>
                </td>
                <td class="p-4">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide ${badgeClass}">
                        ${item.status_transaksi.replace(/_/g, ' ')}
                    </span>
                </td>
                <td class="p-4 text-right pr-6">
                    <a href="/peminjaman/${item.id_peminjaman}"
                        class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 dark:hover:bg-white/10 transition-colors inline-block"
                        title="Lihat Detail">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                    </a>
                </td>
            `;
            tableBody.appendChild(row);
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

        if (page > 1) {
            html += `<a href="#" data-page="${page - 1}" class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 text-xs">Previous</a>`;
        } else {
            html += `<span class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-400 dark:text-white/40 text-xs cursor-not-allowed">Previous</span>`;
        }

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
