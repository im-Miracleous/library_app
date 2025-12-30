document.addEventListener('DOMContentLoaded', function () {
    // State
    let state = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        page: new URLSearchParams(window.location.search).get('page') || 1,
        limit: new URLSearchParams(window.location.search).get('limit') || 10,
        sort: new URLSearchParams(window.location.search).get('sort') || 'created_at',
        direction: new URLSearchParams(window.location.search).get('direction') || 'desc'
    };

    // Elements
    const searchInput = document.getElementById('searchKategoriInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;

    // Initialize Controls
    setupControls();

    function setupControls() {
        // LIMIT
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

        // SORT
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

        // PAGINATION
        setupPaginationDelegation();

        // SEARCH
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
            tableBody.innerHTML = `<tr><td colspan="4" class="p-8 text-center text-slate-500 dark:text-white/40">Belum ada kategori buku.</td></tr>`;
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        data.forEach(item => {
            // Highlighting
            const idHighlighted = highlightText(item.id_kategori, searchQuery);
            const namaHighlighted = highlightText(item.nama_kategori, searchQuery);
            const deskripsiHighlighted = highlightText(item.deskripsi || '', searchQuery);

            const row = `
                <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
                    <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold">
                        #${idHighlighted}
                    </td>
                    <td class="p-4 font-bold text-slate-800 dark:text-white group-hover:text-primary dark:group-hover:text-accent">
                        ${namaHighlighted}
                    </td>
                    <td class="p-4 text-slate-500 dark:text-white/60 truncate max-w-xs">
                        ${deskripsiHighlighted || '-'}
                    </td>
                    <td class="p-4 pr-6 text-right flex justify-end gap-2">
                        <button onclick="openEditKategori('${item.id_kategori}')" 
                            class="cursor-pointer p-2 rounded-lg hover:bg-blue-500/20 text-blue-400 transition-colors"
                            title="Edit">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <form action="/kategori/${item.id_kategori}" method="POST" onsubmit="return confirm('Yakin hapus kategori ini?');" class="inline">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" 
                                class="cursor-pointer p-2 rounded-lg hover:bg-red-500/20 text-red-400 transition-colors"
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
