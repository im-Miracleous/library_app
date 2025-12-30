
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

        tableBody.innerHTML = '<tr><td colspan="7" class="p-8 text-center"><span class="material-symbols-outlined animate-spin text-4xl text-primary/50">progress_activity</span></td></tr>';

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
                tableBody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-red-500">Gagal memuat data.</td></tr>';
            });
    }

    function renderTable(data, query) {
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40"><div class="flex flex-col items-center justify-center gap-2"><span class="material-symbols-outlined text-4xl opacity-50">event_busy</span><span>Tidak ada peminjaman yang sedang berjalan.</span></div></td></tr>';
            return;
        }

        data.forEach((item, index) => {
            const date = new Date(item.tanggal_pinjam);
            const dateStr = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            const due = new Date(item.tanggal_jatuh_tempo);
            const dueStr = due.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

            // Due Date Logic
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const dueTime = new Date(item.tanggal_jatuh_tempo);
            dueTime.setHours(0, 0, 0, 0);
            const isLate = dueTime < today && item.status_transaksi === 'berjalan';

            const dueClass = isLate ? 'text-red-600 font-bold animate-pulse' : 'text-slate-600 dark:text-white/70';

            // Badges
            const badges = {
                'berjalan': 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                'selesai': 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
                'terlambat': 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400'
            };
            const badgeClass = badges[item.status_transaksi] || 'bg-slate-100';

            const row = document.createElement('tr');
            row.className = 'hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group';

            row.innerHTML = `
                <td class="p-4 pl-6 font-mono text-primary font-bold dark:text-accent whitespace-nowrap">${highlightText(item.id_peminjaman, query)}</td>
                <td class="p-4">
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-800 dark:text-white">${highlightText(item.nama_anggota, query)}</span>
                        <span class="text-xs text-slate-500 dark:text-white/50">${item.email_anggota || '-'}</span>
                    </div>
                </td>
                <td class="p-4 text-center font-bold text-slate-700 dark:text-white">${item.total_buku}</td>
                <td class="p-4 text-slate-600 dark:text-white/70">${dateStr}</td>
                <td class="p-4 ${dueClass}">
                    ${dueStr}
                    ${isLate ? '<span class="ml-2 text-[10px] bg-red-100 text-red-600 px-1 rounded uppercase">Telat</span>' : ''}
                </td>
                <td class="p-4">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide ${badgeClass}">
                        ${item.status_transaksi}
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
