document.addEventListener('DOMContentLoaded', function () {
    // State
    let state = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        page: new URLSearchParams(window.location.search).get('page') || 1,
        limit: new URLSearchParams(window.location.search).get('limit') || 10,
        sort: new URLSearchParams(window.location.search).get('sort') || 'created_at',
        direction: new URLSearchParams(window.location.search).get('direction') || 'desc',
        status: new URLSearchParams(window.location.search).get('status') || ''
    };

    // Elements
    const searchInput = document.getElementById('searchAnggotaInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;

    // Initialize Controls
    setupControls();

    function setupControls() {
        // LIMIT SELECT
        const limitSelect = document.querySelector('select');
        if (limitSelect) {
            limitSelect.removeAttribute('onchange');

            Array.from(limitSelect.options).forEach(opt => {
                const match = opt.value.match(/limit=(\d+)/);
                if (match) opt.value = match[1];
            });

            limitSelect.value = state.limit;

            limitSelect.addEventListener('change', (e) => {
                state.limit = e.target.value;
                state.page = 1;
                fetchData();
            });
        }

        // SORT HEADERS
        const headers = document.querySelectorAll('th[onclick]');
        headers.forEach(th => {
            const clickStr = th.getAttribute('onclick');
            if (clickStr) {
                const match = clickStr.match(/sort=([^&']+)/);
                if (match) {
                    th.dataset.sort = match[1];
                    th.removeAttribute('onclick');
                    th.style.cursor = 'pointer';

                    th.addEventListener('click', () => {
                        const col = th.dataset.sort;
                        if (state.sort === col) {
                            state.direction = state.direction === 'asc' ? 'desc' : 'asc';
                        } else {
                            state.sort = col;
                            state.direction = 'asc';
                        }
                        fetchData();
                    });
                }
            }
        });

        // PAGINATION (Delegation)
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

        // FILTER TABS
        const filterTabs = document.querySelectorAll('[data-filter-status]');
        filterTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                state.status = tab.dataset.filterStatus || '';
                state.page = 1; // Reset to first page
                fetchData();

                // Update active tab styling
                filterTabs.forEach(t => {
                    t.classList.remove('bg-white', 'shadow-sm', 'text-primary', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
                    t.classList.add('text-slate-500');
                });

                if (!state.status) {
                    tab.classList.add('bg-white', 'shadow-sm', 'text-primary');
                } else if (state.status === 'aktif') {
                    tab.classList.add('bg-green-100', 'text-green-700', 'shadow-sm');
                } else if (state.status === 'nonaktif') {
                    tab.classList.add('bg-red-100', 'text-red-700', 'shadow-sm');
                }
                tab.classList.remove('text-slate-500');
            });
        });

        refreshIcons();
    }

    function setupPaginationDelegation() {
        const tableContainer = tableBody.closest('div.bg-white, div.bg-surface');
        if (!tableContainer) return;

        const paginationFooter = document.querySelector('.p-4.border-t');

        if (paginationFooter) {
            const newFooter = paginationFooter.cloneNode(true);
            paginationFooter.parentNode.replaceChild(newFooter, paginationFooter);

            newFooter.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                e.preventDefault();

                let targetPage = null;
                if (link.dataset.page) {
                    targetPage = link.dataset.page;
                } else if (link.href && link.href !== '#') {
                    try {
                        const url = new URL(link.href);
                        targetPage = url.searchParams.get('page');
                    } catch (err) {
                        console.log('Invalid URL', link.href);
                    }
                }

                if (targetPage) {
                    state.page = targetPage;
                    fetchData();
                }
            });
        }
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
            const response = await fetch(`${window.location.pathname}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const json = await response.json();

            if (json.data) {
                renderTable(json.data);
                updatePagination(json);
                refreshIcons();
            }
        } catch (error) {
            console.error('Fetch error:', error);
        } finally {
            tableBody.style.opacity = '1';
        }
    }

    // Helper for Highlight
    function highlightText(text, query) {
        if (!query || !text) return text;
        const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${safeQuery})`, 'gi');
        return text.toString().replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-600/50 text-slate-900 dark:text-white rounded px-0.5">$1</span>');
    }

    function renderTable(data) {
        tableBody.innerHTML = '';
        const searchQuery = searchInput ? searchInput.value.trim() : '';

        if (data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-slate-400 dark:text-white/40">Tidak ada data anggota.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const statusClass = item.status === 'aktif'
                ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-500'
                : 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-500';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const initial = item.nama ? item.nama.charAt(0).toUpperCase() : '?';

            // Apply Highlight
            const namaHighlighted = highlightText(item.nama, searchQuery);
            const emailHighlighted = highlightText(item.email, searchQuery);
            const idHighlighted = highlightText(item.id_pengguna, searchQuery);

            const row = `
                <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
                    <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold text-xs">
                        ${idHighlighted}
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold flex-shrink-0">
                                ${initial}
                            </div>
                            <div class="flex flex-col max-w-[220px]">
                                <span class="font-bold text-slate-800 dark:text-white line-clamp-2 text-sm leading-tight" title="${item.nama}">
                                    ${namaHighlighted}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-white/60 truncate" title="${item.email}">
                                    ${emailHighlighted}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        ${item.telepon || '-'}
                    </td>
                    <td class="p-4 max-w-[200px] truncate" title="${item.alamat || '-'}">
                        ${item.alamat || '-'}
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold capitalize ${statusClass}">
                            ${item.status}
                        </span>
                    </td>
                    <td class="p-4 pr-6 text-right flex justify-end gap-2">
                        <button onclick="openEditAnggota('${item.id_pengguna}')" 
                            class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                            title="Edit">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <form action="/anggota/${item.id_pengguna}" method="POST" onsubmit="return confirm('Yakin hapus anggota ini?');" class="inline">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" 
                                class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 transition-colors"
                                title="Hapus">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
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

        const infoDiv = footer.querySelector('div.text-xs');
        if (infoDiv) {
            infoDiv.innerHTML = `Showing <span class="font-bold">${from}</span> to <span class="font-bold">${to}</span> of <span class="font-bold">${total}</span> entries`;
        }

        const linksContainer = footer.querySelector('.flex.gap-2') || footer.querySelector('div:last-child');
        if (linksContainer) {
            let html = '';

            if (currentPage > 1) {
                html += `<a href="#" class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-primary hover:bg-primary/5 transition-colors" data-page="${currentPage - 1}">Previous</a>`;
            } else {
                html += `<button disabled class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-400 cursor-not-allowed">Previous</button>`;
            }

            const lastPage = Math.ceil(total / perPage);
            if (currentPage < lastPage) {
                html += `<a href="#" class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-primary hover:bg-primary/5 transition-colors ml-2" data-page="${currentPage + 1}">Next</a>`;
            } else {
                html += `<button disabled class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-400 cursor-not-allowed ml-2">Next</button>`;
            }

            linksContainer.innerHTML = html;
        }
    }

    function refreshIcons() {
        document.querySelectorAll('th[data-sort]').forEach(th => {
            const icon = th.querySelector('.material-symbols-outlined');
            if (!icon) return;

            if (th.dataset.sort === state.sort) {
                icon.textContent = state.direction === 'asc' ? 'arrow_upward' : 'arrow_downward';
                icon.style.opacity = '1';
                th.classList.add('text-primary');
            } else {
                icon.textContent = 'unfold_more';
                icon.style.opacity = '0.3';
                th.classList.remove('text-primary');
            }
        });
    }
});
