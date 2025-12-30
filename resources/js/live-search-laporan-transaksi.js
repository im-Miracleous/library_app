
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const paginationContainer = document.getElementById('paginationContainer');
    const showingText = document.querySelector('.text-xs.font-medium');
    const limitSelect = document.querySelector('select.appearance-none');
    const filterForm = document.getElementById('filterForm');

    // Filter Inputs
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const statusSelect = document.querySelector('select[name="status"]');

    let debounceTimer;

    // Default Filters
    const urlParams = new URLSearchParams(window.location.search);
    let currentPage = parseInt(urlParams.get('page')) || 1;
    let limit = parseInt(limitSelect ? limitSelect.value : 10);

    // Initial Fetch
    fetchData(currentPage);

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchData(1), 500);
        });
    }

    if (limitSelect) {
        limitSelect.addEventListener('change', () => fetchData(1));
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchData(1);
        });
    }

    function fetchData(page = 1) {
        const query = searchInput ? searchInput.value : '';
        limit = limitSelect ? limitSelect.value : 10;

        const params = new URLSearchParams({
            page: page,
            limit: limit,
            search: query,
            start_date: startDateInput ? startDateInput.value : '',
            end_date: endDateInput ? endDateInput.value : '',
            status: statusSelect ? statusSelect.value : ''
        });

        // Current Page Tracking
        currentPage = page;

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

    function highlightText(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return String(text).replace(regex, '<span class="bg-yellow-200 text-slate-800 font-bold px-0.5 rounded">$1</span>');
    }

    function renderTable(data, query) {
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40"><div class="flex flex-col items-center justify-center gap-2"><span class="material-symbols-outlined text-4xl opacity-50">search_off</span><span>Tidak ada data ditemukan.</span></div></td></tr>';
            return;
        }

        data.forEach((item, index) => {
            const date = new Date(item.tanggal_pinjam);
            const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const due = new Date(item.tanggal_jatuh_tempo);
            const dueStr = due.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            // Status Badge
            const badges = {
                'berjalan': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                'selesai': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                'terlambat': 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400'
            };
            const badgeClass = badges[item.status_transaksi] || 'bg-slate-100 text-slate-600';

            // Highlight
            const codeHighlight = highlightText(item.id_peminjaman, query);
            const nameHighlight = highlightText(item.nama_anggota, query);

            const row = document.createElement('tr');
            row.className = 'hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group';
            row.innerHTML = `
                <td class="p-4 pl-6 font-mono text-sm text-slate-600 dark:text-white/70 whitespace-nowrap">
                    <span class="font-bold text-primary dark:text-accent">${codeHighlight}</span>
                </td>
                <td class="p-4">
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-800 dark:text-white">${nameHighlight}</span>
                        <span class="text-xs text-slate-500 dark:text-white/50">${item.email_anggota || '-'}</span>
                    </div>
                </td>
                <td class="p-4 text-slate-600 dark:text-white/70">${dateStr}</td>
                <td class="p-4 text-slate-600 dark:text-white/70">${dueStr}</td>
                <td class="p-4 text-center font-bold text-slate-700 dark:text-white">${item.total_buku}</td>
                <td class="p-4 text-right pr-6">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide ${badgeClass}">
                        ${item.status_transaksi}
                    </span>
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

        // Prev
        paginationHTML += `<button ${page === 1 ? 'disabled' : ''} onclick="window.changePage(${page - 1})" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50 disabled:opacity-50">Prev</button>`;

        // Numbers
        let startPage = Math.max(1, page - 2);
        let endPage = Math.min(totalPages, page + 2);

        if (startPage > 1) {
            paginationHTML += `<button onclick="window.changePage(1)" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50">1</button>`;
            if (startPage > 2) paginationHTML += `<span class="px-1 text-slate-400">...</span>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === page ? 'bg-primary text-white border-primary dark:bg-accent dark:text-primary-dark dark:border-accent' : 'bg-white dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50';
            paginationHTML += `<button onclick="window.changePage(${i})" class="px-3 py-1 rounded border text-xs font-bold ${activeClass}">${i}</button>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) paginationHTML += `<span class="px-1 text-slate-400">...</span>`;
            paginationHTML += `<button onclick="window.changePage(${totalPages})" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50">${totalPages}</button>`;
        }

        // Next
        paginationHTML += `<button ${page === totalPages ? 'disabled' : ''} onclick="window.changePage(${page + 1})" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50 disabled:opacity-50">Next</button>`;

        paginationContainer.innerHTML = paginationHTML;

        // Global function for onclick
        window.changePage = function (p) {
            fetchData(p);
        };
    }
});
