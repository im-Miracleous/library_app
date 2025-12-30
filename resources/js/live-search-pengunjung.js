
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const paginationContainer = document.getElementById('paginationContainer');
    const showingText = document.querySelector('.text-xs.font-medium');
    const limitSelect = document.querySelector('select.appearance-none');

    let debounceTimer;
    let sortColumn = 'created_at';
    let sortDirection = 'desc';

    if (!searchInput || !tableBody) return;

    // Highlight Helper
    function highlightText(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return String(text).replace(regex, '<span class="bg-yellow-200 text-slate-800 font-bold px-0.5 rounded">$1</span>');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Default Filters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('sort')) sortColumn = urlParams.get('sort');
    if (urlParams.has('direction')) sortDirection = urlParams.get('direction');

    fetchData();

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchData(1), 500);
    });

    limitSelect.addEventListener('change', () => fetchData(1));

    document.querySelectorAll('th[data-sort]').forEach(th => {
        th.addEventListener('click', function (e) {
            e.preventDefault();
            const col = this.getAttribute('data-sort');
            if (sortColumn === col) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = col;
                sortDirection = 'asc';
            }
            fetchData(1);
            updateSortIcons();
        });
    });

    function updateSortIcons() {
        document.querySelectorAll('th[data-sort] .material-symbols-outlined').forEach(icon => {
            icon.textContent = 'unfold_more';
            icon.style.opacity = '0.3';
        });
        const activeTh = document.querySelector(`th[data-sort="${sortColumn}"]`);
        if (activeTh) {
            const icon = activeTh.querySelector('.material-symbols-outlined');
            icon.textContent = sortDirection === 'asc' ? 'keyboard_arrow_up' : 'keyboard_arrow_down';
            icon.style.opacity = '1';
        }
    }
    updateSortIcons();

    function fetchData(page = 1) {
        const query = searchInput.value;
        const limit = limitSelect.value;

        const params = new URLSearchParams({
            page: page,
            limit: limit,
            search: query,
            sort: sortColumn,
            direction: sortDirection
        });

        tableBody.innerHTML = '<tr><td colspan="6" class="p-8 text-center"><span class="material-symbols-outlined animate-spin text-4xl text-primary/50">progress_activity</span></td></tr>';

        fetch(`${window.location.pathname}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.json())
            .then(response => {
                renderTable(response.data, query);
                renderPagination(response.total, response.data.length, page, limit);
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-red-500">Gagal memuat data.</td></tr>';
            });
    }

    function renderTable(data, query) {
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40"><div class="flex flex-col items-center justify-center gap-2"><span class="material-symbols-outlined text-4xl opacity-50">event_busy</span><span>Tidak ada data pengunjung.</span></div></td></tr>';
            return;
        }

        data.forEach((item, index) => {
            const date = new Date(item.created_at);
            const dateStr = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            // Simplified Numbering
            const displayNo = index + 1;

            // Badges
            const badges = {
                'umum': 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300',
                'anggota': 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                'petugas': 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400',
                'admin': 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400',
            };
            const badgeClass = badges[item.jenis_pengunjung] || 'bg-slate-100';
            const roleDisplay = item.jenis_pengunjung === 'petugas' ? 'Staff' : (item.jenis_pengunjung.charAt(0).toUpperCase() + item.jenis_pengunjung.slice(1));

            const row = document.createElement('tr');
            row.className = 'hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group';

            // Registered Check
            let registeredHtml = '';
            if (item.id_pengguna) {
                registeredHtml = `<div class="text-[10px] text-green-600 dark:text-green-400 flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[10px]">verified</span>Terdaftar</div>`;
            }

            // Edit Data
            const itemJson = JSON.stringify(item).replace(/"/g, '&quot;');

            row.innerHTML = `
                <td class="p-4 pl-6 font-mono text-slate-400 font-bold">${displayNo}</td>
                <td class="p-4">
                    <span class="font-bold text-slate-800 dark:text-white">${highlightText(item.nama_pengunjung, query)}</span>
                    ${registeredHtml}
                </td>
                <td class="p-4">
                     <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide ${badgeClass}">
                        ${roleDisplay}
                    </span>
                </td>
                <td class="p-4 text-slate-600 dark:text-white/70">${highlightText(item.keperluan || '-', query)}</td>
                <td class="p-4 font-mono text-slate-500 dark:text-white/50">
                    ${dateStr}, <span class="text-slate-800 dark:text-white font-bold">${timeStr}</span>
                </td>
                <td class="p-4 text-right pr-6 flex justify-end gap-2">
                    <button onclick='window.openEditPengunjung(${itemJson})'
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

    function renderPagination(total, count, page, limit) {
        page = parseInt(page);
        limit = parseInt(limit);
        const totalPages = Math.ceil(total / limit);
        const from = (page - 1) * limit + 1;
        const to = from + count - 1;

        if (total === 0) {
            showingText.innerHTML = 'Showing <span class="font-bold">0</span> to <span class="font-bold">0</span> of <span class="font-bold">0</span> entries';
            paginationContainer.innerHTML = '';
            return;
        }

        showingText.innerHTML = `Showing <span class="font-bold">${from}</span> to <span class="font-bold">${to}</span> of <span class="font-bold">${total}</span> entries`;

        let paginationHTML = '';
        paginationHTML += `<button ${page === 1 ? 'disabled' : ''} onclick="changePage(${page - 1})" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50 disabled:opacity-50">Prev</button>`;
        let startPage = Math.max(1, page - 2);
        let endPage = Math.min(totalPages, page + 2);
        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === page ? 'bg-primary text-white border-primary dark:bg-accent dark:text-primary-dark dark:border-accent' : 'bg-white dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50';
            paginationHTML += `<button onclick="changePage(${i})" class="px-3 py-1 rounded border text-xs font-bold ${activeClass}">${i}</button>`;
        }
        paginationHTML += `<button ${page === totalPages ? 'disabled' : ''} onclick="changePage(${page + 1})" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50 disabled:opacity-50">Next</button>`;
        paginationContainer.innerHTML = paginationHTML;
        window.changePage = function (p) {
            fetchData(p);
        };
    }
});
