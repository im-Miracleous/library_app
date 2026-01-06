document.addEventListener('DOMContentLoaded', function () {
    // State
    let state = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        page: new URLSearchParams(window.location.search).get('page') || 1,
        limit: new URLSearchParams(window.location.search).get('limit') || 10,
        sort: new URLSearchParams(window.location.search).get('sort') || 'created_at',
        direction: new URLSearchParams(window.location.search).get('direction') || 'desc',
        peran: new URLSearchParams(window.location.search).get('peran') || ''
    };

    // Elements
    const searchInput = document.getElementById('searchPegawaiInput');
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

        // FILTER TABS (Peran: Admin/Petugas)
        const filterTabs = document.querySelectorAll('[data-filter-peran]');
        filterTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                state.peran = tab.dataset.filterPeran || '';
                state.page = 1; // Reset to first page
                fetchData();

                // Update active tab styling
                filterTabs.forEach(t => {
                    t.classList.remove('bg-white', 'shadow-sm', 'text-primary', 'bg-purple-100', 'text-purple-700', 'bg-orange-100', 'text-orange-700');
                    t.classList.add('text-slate-500');
                });

                if (!state.peran) {
                    tab.classList.add('bg-white', 'shadow-sm', 'text-primary');
                } else if (state.peran === 'admin') {
                    tab.classList.add('bg-purple-100', 'text-purple-700', 'shadow-sm');
                } else if (state.peran === 'petugas') {
                    tab.classList.add('bg-orange-100', 'text-orange-700', 'shadow-sm');
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
            tableBody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-slate-400 dark:text-white/40">Tidak ada data pegawai.</td></tr>`;
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        data.forEach(item => {
            let roleBadge = '';
            if (item.peran === 'admin') {
                roleBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary-dark dark:bg-accent/10 dark:text-accent border border-primary/20 dark:border-accent/20">Administrator</span>`;
            } else if (item.peran === 'owner') {
                roleBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-900 border border-gray-600 dark:border-gray-400">Owner</span>`;
            } else {
                roleBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">Petugas</span>`;
            }

            const initial = item.nama ? item.nama.charAt(0).toUpperCase() : '?';

            let avatarContent;
            if (item.foto_profil) {
                avatarContent = `<img src="/storage/${item.foto_profil}" alt="${item.nama}" class="w-full h-full object-cover">`;
            } else {
                avatarContent = initial;
            }

            const statusBadge = item.status === 'aktif'
                ? `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-500 border border-green-200 dark:border-green-800">Aktif</span>`
                : `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-500 border border-red-200 dark:border-red-800">Nonaktif</span>`;

            const lockBadge = item.is_locked
                ? `<span class="ml-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white border border-red-700 uppercase">LOCKED</span>`
                : '';

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
                            <div class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold flex-shrink-0 overflow-hidden">
                                ${avatarContent}
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
                        ${roleBadge}
                    </td>
                    <td class="p-4">
                        ${item.telepon || '-'}
                    </td>
                    <td class="p-4 max-w-[200px] truncate" title="${item.alamat || '-'}">
                        ${item.alamat || '-'}
                    </td>
                    <td class="p-4">
                        ${statusBadge}
                        ${lockBadge}
                    </td>
                    <td class="p-4 pr-6 text-right flex justify-end gap-2">
                        ${(() => {
                    const currentRole = window.currentUserRole;
                    const currentId = window.currentUserId;
                    let canEdit = true;

                    // Logic Permission Client-Side
                    if (item.peran === 'owner' && currentRole !== 'owner') canEdit = false;
                    if (currentRole === 'admin' && item.peran === 'admin' && String(item.id_pengguna) !== String(currentId)) {
                        if (!item.is_locked) canEdit = false;
                    }

                    if (canEdit) {
                        return `<button onclick="openEditPegawai(${JSON.stringify(item).replace(/"/g, '&quot;')})" 
                                    class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                                    title="Edit">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>`;
                    } else {
                        return `<button disabled
                                    class="p-2 rounded-lg text-blue-300 dark:text-blue-800 cursor-not-allowed opacity-70"
                                    title="Edit (Dilindungi)">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>`;
                    }
                })()}
                        <form action="/kepegawaian/${item.id_pengguna}" method="POST" onsubmit="return confirm('Yakin hapus pegawai ini?');" class="inline">
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
