
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
    const statusSelect = document.querySelector('select[name="status_bayar"]');

    let debounceTimer;
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Default Filters
    const urlParams = new URLSearchParams(window.location.search);
    let currentPage = parseInt(urlParams.get('page')) || 1;
    let limit = parseInt(limitSelect ? limitSelect.value : 10);

    fetchData(currentPage);

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
            status_bayar: statusSelect ? statusSelect.value : ''
        });

        currentPage = page;

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

    function highlightText(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return String(text).replace(regex, '<span class="bg-yellow-200 text-slate-800 font-bold px-0.5 rounded">$1</span>');
    }

    function renderTable(data, query) {
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="p-12 text-center text-slate-400 dark:text-white/40"><div class="flex flex-col items-center justify-center gap-2"><span class="material-symbols-outlined text-4xl opacity-50">search_off</span><span>Tidak ada data denda ditemukan.</span></div></td></tr>';
            return;
        }

        data.forEach((item, index) => {
            const date = new Date(item.tanggal_denda);
            const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            // Format Currency
            const amount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.jumlah_denda);

            // Status Badge
            const badges = {
                'lunas': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                'belum_bayar': 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                'sebagian': 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400'
            };
            const badgeClass = badges[item.status_bayar] || 'bg-slate-100 text-slate-600';

            // Highlight
            const refHighlight = highlightText(item.kode_peminjaman, query);
            const nameHighlight = highlightText(item.nama_anggota, query);
            const titleHighlight = highlightText(item.judul_buku, query);

            const row = document.createElement('tr');
            row.className = 'hover:bg-slate-50 dark:hover:bg-white/5 transition-colors';

            let actionBtn = '';
            if (item.status_bayar === 'belum_bayar') {
                actionBtn = `
                    <form action="/laporan/denda/${item.id_denda}/bayar" method="POST" onsubmit="return confirm('Konfirmasi pembayaran denda ini?');">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition flex items-center gap-1 shadow-sm shadow-emerald-200 dark:shadow-none">
                            <span class="material-symbols-outlined text-[16px]">payments</span>
                            Bayar
                        </button>
                    </form>
                `;
            } else {
                actionBtn = `<span class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center justify-end gap-1 font-bold"><span class="material-symbols-outlined text-sm">check_circle</span>Lunas</span>`;
            }

            row.innerHTML = `
                <td class="p-4 pl-6 font-mono text-xs text-slate-500 dark:text-white/50">${dateStr}</td>
                <td class="p-4 font-mono font-bold text-primary dark:text-accent">${refHighlight}</td>
                <td class="p-4 max-w-[200px] truncate" title="${item.nama_anggota}">
                    <span class="font-bold text-slate-800 dark:text-white">${nameHighlight}</span>
                </td>
                <td class="p-4 max-w-[250px] truncate text-slate-600 dark:text-white/70" title="${item.judul_buku}">
                    ${titleHighlight}
                </td>
                <td class="p-4 text-xs font-bold uppercase text-slate-500 dark:text-white/50">${item.jenis_denda}</td>
                <td class="p-4 font-mono font-bold text-slate-800 dark:text-white text-right">${amount}</td>
                <td class="p-4 text-right pr-6">
                    <div class="flex flex-col items-end gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider ${badgeClass}">${item.status_bayar.replace('_', ' ')}</span>
                        ${actionBtn}
                    </div>
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
        paginationHTML += `<button ${page === 1 ? 'disabled' : ''} onclick="window.changePage(${page - 1})" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50 disabled:opacity-50">Prev</button>`;
        let startPage = Math.max(1, page - 2);
        let endPage = Math.min(totalPages, page + 2);
        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === page ? 'bg-primary text-white border-primary dark:bg-accent dark:text-primary-dark dark:border-accent' : 'bg-white dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50';
            paginationHTML += `<button onclick="window.changePage(${i})" class="px-3 py-1 rounded border text-xs font-bold ${activeClass}">${i}</button>`;
        }
        paginationHTML += `<button ${page === totalPages ? 'disabled' : ''} onclick="window.changePage(${page + 1})" class="px-3 py-1 rounded bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white text-xs hover:bg-slate-50 disabled:opacity-50">Next</button>`;
        paginationContainer.innerHTML = paginationHTML;
        window.changePage = function (p) {
            fetchData(p);
        };
    }
});
