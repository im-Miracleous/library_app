document.addEventListener('DOMContentLoaded', function () {
    // State
    let state = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        page: new URLSearchParams(window.location.search).get('page') || 1,
        limit: new URLSearchParams(window.location.search).get('limit') || 10,
        sort: new URLSearchParams(window.location.search).get('sort') || '',
        direction: new URLSearchParams(window.location.search).get('direction') || ''
    };

    // Elements
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    let timeout = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize Controls
    setupControls();

    // Expose for onClick
    window.openEditPengunjung = function (item) {
        document.getElementById('edit_nama').value = item.nama_pengunjung;
        document.getElementById('edit_jenis').value = item.jenis_pengunjung;
        document.getElementById('edit_keperluan').value = item.keperluan || '';
        document.getElementById('editForm').action = `/pengunjung/${item.id_pengunjung}`;

        const modal = document.getElementById('editModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
    };

    function setupControls() {
        // LIMIT SELECT
        // Use :not for potential specificity if other selects exist, though standard is generic select in x-datatable
        const limitSelect = document.querySelector('select:not([name="jenis_pengunjung"])');
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
                renderTable(json); // Pass full json for total/pagination info if needed, or just json.data
                updatePagination(json);
                updateSortIcons();
            }
        } catch (error) {
            console.error('Fetch error:', error);
            tableBody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-red-500">Gagal memuat data.</td></tr>`;
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

    function renderTable(json) {
        tableBody.innerHTML = '';
        const data = json.data;
        const from = json.from || 1; // Fallback if not provided, though Controller passes it in Paginator object logic usually (or we calculate)
        // Note: Controller returns 'data' (array) and 'total' and 'links'. It doesn't strictly return 'from' in the root JSON unless we added it.
        // Let's calculate 'from' manually for the "No" column.

        const currentPage = parseInt(state.page);
        const perPage = parseInt(state.limit);
        const startNo = ((currentPage - 1) * perPage) + 1;

        const searchQuery = searchInput ? searchInput.value.trim() : '';

        if (data.length === 0) {
            tableBody.innerHTML = `<tr>
                <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-4xl opacity-50">data_loss_prevention</span>
                        <span>Tidak ada data pengunjung.</span>
                    </div>
                </td>
            </tr>`;
            return;
        }

        data.forEach((item, index) => {
            const date = new Date(item.created_at);
            const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            // Badges
            const badges = {
                'umum': 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300',
                'anggota': 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                'petugas': 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400',
                'admin': 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400',
            };
            const badgeClass = badges[item.jenis_pengunjung] || 'bg-slate-100';
            const roleDisplay = item.jenis_pengunjung === 'petugas' ? 'Staff' : (item.jenis_pengunjung.charAt(0).toUpperCase() + item.jenis_pengunjung.slice(1));

            // Registered Check
            let registeredHtml = '';
            if (item.id_pengguna) {
                registeredHtml = `<div class="text-[10px] text-green-600 dark:text-green-400 flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[10px]">verified</span>Terdaftar</div>`;
            }

            // Highlight
            const nameHighlighted = highlightText(item.nama_pengunjung, searchQuery);
            const keperluanHighlighted = highlightText(item.keperluan || '-', searchQuery);

            const row = document.createElement('tr');
            row.className = 'hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group animate-enter';

            // Prepare Item for Edit
            const itemString = JSON.stringify(item).replace(/'/g, "&#39;").replace(/"/g, "&quot;");

            row.innerHTML = `
                <td class="p-4 pl-6 font-mono text-slate-400 font-bold">
                    ${startNo + index}
                </td>
                <td class="p-4">
                    <span class="font-bold text-slate-800 dark:text-white">${nameHighlighted}</span>
                    ${registeredHtml}
                </td>
                <td class="p-4">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide ${badgeClass}">
                        ${roleDisplay}
                    </span>
                </td>
                <td class="p-4 text-slate-600 dark:text-white/70">${keperluanHighlighted}</td>
                <td class="p-4 font-mono text-slate-500 dark:text-white/50">
                    ${dateStr}, <span class="text-slate-800 dark:text-white font-bold">${timeStr}</span>
                </td>
                <td class="p-4 text-right pr-6 flex justify-end gap-2">
                    <button onclick='openEditPengunjung(${itemString})'
                        class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                        title="Edit Log">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                    <form action="/pengunjung/${item.id_pengunjung}" method="POST" onsubmit="return confirm('Yakin hapus log ini?');" class="inline-block">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit"
                            class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                            title="Hapus Log">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </form>
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
