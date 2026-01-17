import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function () {
    // State
    const state = {
        search: new URLSearchParams(window.location.search).get('search') || '',
        page: new URLSearchParams(window.location.search).get('page') || 1,
        limit: new URLSearchParams(window.location.search).get('limit') || 10,
        sort: new URLSearchParams(window.location.search).get('sort') || '',
        direction: new URLSearchParams(window.location.search).get('direction') || 'desc',

        // Critical for unified controller
        type: document.querySelector('select[name="type"]')?.value || 'transaksi',

        start_date: document.querySelector('input[name="start_date"]')?.value || '',
        end_date: document.querySelector('input[name="end_date"]')?.value || '',
        status: document.querySelector('select[name="status"]')?.value || '',
        status_bayar: document.querySelector('select[name="status_bayar"]')?.value || ''
    };

    // DOM Elements
    const searchInputs = [
        document.getElementById('searchTransaksiInput'),
        document.getElementById('searchDendaInput'),
        // Add generic search input selector if needed
    ];
    // Use the first found search input
    const searchInput = searchInputs.find(el => el !== null);

    const tableBody = document.querySelector('tbody');
    let timeout = null;
    let mainChart = null;

    // Initialize Chart if data exists
    if (window.laporanChartData) {
        initChart(window.laporanChartData, window.laporanType);
    }

    // Initialize Controls
    setupControls();

    function updateChartVisibility(chartData) {
        const ctx = document.getElementById('mainChart');
        const emptyState = document.getElementById('chartEmptyState');
        if (!ctx || !emptyState) return;

        // Calculate total to check for emptiness
        // Handle potential missing data gracefully
        const total = chartData.datasets[0]?.data.reduce((a, b) => a + Number(b), 0) || 0;

        if (total === 0) {
            ctx.classList.add('opacity-0', 'pointer-events-none');
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
        } else {
            ctx.classList.remove('opacity-0', 'pointer-events-none');
            emptyState.classList.add('hidden');
            emptyState.classList.remove('flex');
        }
    }

    function initChart(chartData, type) {
        const ctx = document.getElementById('mainChart');
        if (!ctx) return;

        // --- COLOR PALETTES ---
        const PALETTE = {
            light: {
                transaksi: ['#f59e0b', '#3b82f6', '#ef4444', '#10b981', '#f97316', '#64748b'], // Orange, Blue, Red, Emerald, DkOrange, Slate
                denda: { lunas: '#10b981', belum: '#ef4444' },
                buku: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#64748b'],
                anggota: '#8b5cf6' // Violet
            },
            dark: {
                transaksi: ['#fbbf24', '#60a5fa', '#f87171', '#34d399', '#fb923c', '#94a3b8'], // Brighter shades
                denda: { lunas: '#34d399', belum: '#f87171' },
                buku: ['#60a5fa', '#34d399', '#fbbf24', '#f87171', '#a78bfa', '#f472b6', '#818cf8', '#2dd4bf', '#fb923c', '#94a3b8'],
                anggota: '#a78bfa' // Light Violet
            }
        };

        function getColors(type, isDark) {
            const p = isDark ? PALETTE.dark : PALETTE.light;
            if (type === 'transaksi') return p.transaksi;
            if (type === 'denda') return [p.denda.lunas, p.denda.belum]; // Specific order for stacked/grouped
            if (type === 'buku_top') return p.buku;
            if (type === 'anggota_top') return p.anggota;
            return '#cbd5e1';
        }

        // Check for Dark Mode
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#e2e8f0' : '#64748b'; // slate-200 vs slate-500
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0,0,0,0.05)';

        updateChartVisibility(chartData);

        // Determine Chart Configuration
        let chartType = 'line';
        let legendDisplay = false;
        let indexAxis = 'x';
        let scales = {
            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
            x: { grid: { display: false }, ticks: { color: textColor } }
        };

        // Apply Colors Initial
        const colors = getColors(type, isDarkMode);
        if (type === 'denda') {
            // Denda is unique: 2 Datasets
            if (chartData.datasets.length >= 2) {
                chartData.datasets[0].backgroundColor = isDarkMode ? PALETTE.dark.denda.lunas : PALETTE.light.denda.lunas;
                chartData.datasets[0].borderColor = chartData.datasets[0].backgroundColor;
                chartData.datasets[1].backgroundColor = isDarkMode ? PALETTE.dark.denda.belum : PALETTE.light.denda.belum;
                chartData.datasets[1].borderColor = chartData.datasets[1].backgroundColor;
            }
        } else {
            // Single Dataset types
            if (chartData.datasets[0]) {
                chartData.datasets[0].backgroundColor = colors;
                // For bar charts, border can match background to avoid outline issues
                chartData.datasets[0].borderColor = colors;
            }
        }

        if (type === 'transaksi') {
            chartType = 'pie';
            legendDisplay = true;
            scales = {};
        } else if (type === 'buku_top') {
            chartType = 'bar';
            indexAxis = 'y';
        } else if (type === 'anggota_top') {
            chartType = 'bar';
        } else if (type === 'denda') {
            chartType = 'bar';
            scales = {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: textColor,
                        callback: function (value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: { grid: { display: false }, ticks: { color: textColor } }
            };
        }

        mainChart = new Chart(ctx, {
            type: chartType,
            data: chartData,
            options: {
                indexAxis: indexAxis,
                responsive: true,
                maintainAspectRatio: false,
                cutout: 0,
                elements: {
                    arc: { borderWidth: 0 }
                },
                plugins: {
                    legend: {
                        display: legendDisplay,
                        position: 'right',
                        labels: {
                            color: textColor,
                            font: { family: "'Inter', sans-serif", weight: 'bold' }
                        }
                    },
                    tooltip: {
                        mode: 'nearest',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) label += ': ';
                                let value = context.parsed.y !== undefined ? context.parsed.y : context.parsed;

                                if (type === 'transaksi') {
                                    let total = context.chart._metasets[context.datasetIndex].total;
                                    let percentage = ((value / total) * 100).toFixed(1) + '%';
                                    return label + value + ' (' + percentage + ')';
                                } else if (type === 'denda') {
                                    return label + 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                } else {
                                    return label + value;
                                }
                            }
                        }
                    }
                },
                scales: scales
            }
        });

        // Dynamic Theme Switch Listener
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    const text = isDark ? '#e2e8f0' : '#64748b';
                    const grid = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0,0,0,0.05)';

                    if (mainChart) {
                        // 1. Update Layout Colors
                        if (mainChart.options.plugins.legend) {
                            mainChart.options.plugins.legend.labels.color = text;
                        }
                        if (mainChart.options.scales.x) mainChart.options.scales.x.ticks.color = text;
                        if (mainChart.options.scales.y) {
                            mainChart.options.scales.y.ticks.color = text;
                            mainChart.options.scales.y.grid.color = grid;
                        }

                        // 2. Update Dataset Colors
                        if (type === 'denda') {
                            if (mainChart.data.datasets.length >= 2) {
                                const lunas = isDark ? PALETTE.dark.denda.lunas : PALETTE.light.denda.lunas;
                                const belum = isDark ? PALETTE.dark.denda.belum : PALETTE.light.denda.belum;
                                mainChart.data.datasets[0].backgroundColor = lunas;
                                mainChart.data.datasets[0].borderColor = lunas;
                                mainChart.data.datasets[1].backgroundColor = belum;
                                mainChart.data.datasets[1].borderColor = belum;
                            }
                        } else {
                            const newColors = getColors(type, isDark);
                            if (mainChart.data.datasets[0]) {
                                mainChart.data.datasets[0].backgroundColor = newColors;
                                mainChart.data.datasets[0].borderColor = newColors;
                            }
                        }

                        mainChart.update();
                    }
                }
            });
        });

        observer.observe(document.documentElement, { attributes: true });
    }

    function initChartReference() {
        // Deprecated by initChart above
    }

    function setupControls() {
        // REPORT TYPE SELECT CHANGE -> Redirect (Don't AJAX this, structure changes too much)
        // PERIODE SELECTOR
        const periodeSelect = document.getElementById('periode_filter');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const applyCustomDateBtn = document.getElementById('applyCustomDate');

        const customDateTrigger = document.getElementById('custom_date_trigger');
        const optionCustom = document.getElementById('option_custom');

        // Initial State for Dropdown
        if (periodeSelect && startDateInput && endDateInput) {
            determinePeriodeState(periodeSelect, startDateInput.value, endDateInput.value);

            periodeSelect.addEventListener('change', (e) => {
                const val = e.target.value;
                const today = new Date();

                if (val === 'custom') {
                    openCustomModal();
                } else {
                    let start, end;
                    end = new Date(today);
                    start = new Date(today);

                    if (val === 'all') {
                        start = new Date('2000-01-01');
                    } else if (val === 'today') {
                        // start and end are today
                    } else if (val === 'week') {
                        start.setDate(today.getDate() - 7);
                    } else if (val === 'month') {
                        start.setDate(today.getDate() - 30);
                    }

                    // Set values
                    startDateInput.value = formatDate(start);
                    endDateInput.value = formatDate(end);

                    // Reset custom text if moving away from custom (optional, but good for cleanup)
                    if (optionCustom) optionCustom.innerText = 'Lainnya...';
                    if (customDateTrigger) customDateTrigger.classList.add('hidden');

                    // Auto-submit form
                    const form = document.querySelector('form[action*="laporan"]');
                    if (form) form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            });

            // Re-open modal if icon is clicked
            if (customDateTrigger) {
                customDateTrigger.addEventListener('click', () => {
                    openCustomModal();
                });
            }
        }

        function openCustomModal() {
            // Open Modal, prefill with current hidden values
            document.getElementById('modal_start_date').value = startDateInput.value;
            document.getElementById('modal_end_date').value = endDateInput.value;
            openModal('customDateModal');
        }

        // Custom Modal Apply
        if (applyCustomDateBtn) {
            applyCustomDateBtn.addEventListener('click', () => {
                const s = document.getElementById('modal_start_date').value;
                const e = document.getElementById('modal_end_date').value;

                if (s && e) {
                    startDateInput.value = s;
                    endDateInput.value = e;

                    // Update Dropdown to 'custom' and show indicator
                    periodeSelect.value = 'custom';
                    updateCustomUI(s, e);

                    // Auto-submit form
                    const form = document.querySelector('form[action*="laporan"]');
                    if (form) form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));

                    closeModal('customDateModal');
                } else {
                    alert('Harap isi kedua tanggal.');
                }
            });
        }

        // Helper: Format Date YYYY-MM-DD
        function formatDate(date) {
            const d = new Date(date);
            let month = '' + (d.getMonth() + 1);
            let day = '' + d.getDate();
            const year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Helper: Format Readable Date DD MMM YYYY
        function formatReadableDate(dateStr) {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function updateCustomUI(start, end) {
            if (optionCustom) {
                optionCustom.innerText = `${formatReadableDate(start)} - ${formatReadableDate(end)}`;
            }
            if (customDateTrigger) {
                customDateTrigger.classList.remove('hidden');
            }
        }

        // Helper: Determine Dropdown State from Dates
        function determinePeriodeState(select, start, end) {
            const today = formatDate(new Date());

            const weekStart = new Date();
            weekStart.setDate(weekStart.getDate() - 7);
            const weekStartStr = formatDate(weekStart);

            const monthStart = new Date();
            monthStart.setDate(monthStart.getDate() - 30);
            const monthStartStr = formatDate(monthStart);

            if (start === '2000-01-01') {
                select.value = 'all';
                if (customDateTrigger) customDateTrigger.classList.add('hidden');
                if (optionCustom) optionCustom.innerText = 'Lainnya...';
            } else if (start === today && end === today) {
                select.value = 'today';
                if (customDateTrigger) customDateTrigger.classList.add('hidden');
                if (optionCustom) optionCustom.innerText = 'Lainnya...';
            } else if (start === weekStartStr && end === today) {
                select.value = 'week';
                if (customDateTrigger) customDateTrigger.classList.add('hidden');
                if (optionCustom) optionCustom.innerText = 'Lainnya...';
            } else if (start === monthStartStr && end === today) {
                select.value = 'month';
                if (customDateTrigger) customDateTrigger.classList.add('hidden');
                if (optionCustom) optionCustom.innerText = 'Lainnya...';
            } else {
                select.value = 'custom';
                updateCustomUI(start, end);
            }
        }

        // Expose reset for Cancel button
        window.resetPeriodeSelector = function () {
            // Re-determine in case they cancelled custom selection
            determinePeriodeState(periodeSelect, startDateInput.value, endDateInput.value);
        };

        // REPORT TYPE SELECT CHANGE -> Redirect (Don't AJAX this, structure changes too much)
        const typeSelect = document.querySelector('select[name="type"]');
        if (typeSelect) {
            typeSelect.addEventListener('change', (e) => {
                // If type changes, reload page with new type to get fresh HTML structure
                const url = new URL(window.location.href);
                url.searchParams.set('type', e.target.value);
                url.searchParams.set('page', 1); // Reset page
                window.location.href = url.toString();
            });
        }

        // PAGINATION
        const paginationContainer = document.querySelector('.p-4.border-t');
        if (paginationContainer) {
            paginationContainer.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                e.preventDefault();

                let targetPage = null;
                if (link.dataset.page) {
                    targetPage = link.dataset.page;
                } else if (link.href && link.href !== '#' && !link.href.endsWith('#')) {
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

        // LIMIT
        const limitSelect = document.getElementById('limitSelector');
        if (limitSelect) {
            limitSelect.removeAttribute('onchange');
            limitSelect.addEventListener('change', (e) => {
                const url = new URL(e.target.value, window.location.origin);
                const limit = url.searchParams.get('limit');
                if (limit) {
                    state.limit = limit;
                    state.page = 1;
                    fetchData();
                }
            });
        }

        // SORTING
        const headers = document.querySelectorAll('th[onclick]');
        headers.forEach(th => {
            th.removeAttribute('onclick'); // Disable default redirection
            th.addEventListener('click', () => {
                // Read sort param from the original onclick intent or data attributes
                // The original code used window.location.href inside onclick.
                // We'll rely on our state.
                // Assuming I updated the render to use data-sort attributes would be better, 
                // but let's parse the onclick string if present, or just use text content?
                // Actually the blade template injects `window.location.href=...sort=...`
                // Let's rely on simple data attributes if I added them? 
                // In my partials I didn't add data-sort. I left the onclick.

                // Hack: we already removed onclick. We can't parse it now unless we cached it.
                // BETTER: Update the partials to use data-sort.
                // For now, let's skip sorting via AJAX or reload page.
                // Implementation Plan said "Verify ... work as expected".
                // Let's try to parse the header text.

                // To be safe and clean, I will NOT AJAX sort for now unless I update partials.
                // I will reload page for sorting for robustness.
                // BUT wait, I removed onclick! So clicking does NOTHING now.
                // I must handle it.
                // Re-adding functionality:
                const text = th.innerText.trim();
                let sortCol = '';
                if (text.includes('Kode') || text.includes('ID')) sortCol = state.type === 'denda' ? 'id_denda' : 'id_peminjaman';
                else if (text.includes('Peminjam') || text.includes('Anggota')) sortCol = 'nama_anggota';
                else if (text.includes('Tanggal Pinjam')) sortCol = 'tanggal_pinjam';
                else if (text.includes('Jatuh Tempo')) sortCol = 'tanggal_jatuh_tempo';
                else if (text.includes('Jumlah')) sortCol = 'jumlah_denda';
                else if (text.includes('Status')) sortCol = state.type === 'denda' ? 'status_bayar' : 'status_transaksi';

                if (sortCol) {
                    if (state.sort === sortCol) {
                        state.direction = state.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        state.sort = sortCol;
                        state.direction = 'asc';
                    }
                    fetchData();
                }
            });
        });

        // LIVE SEARCH
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

        // FORM FILTER (Submit Button)
        const filterForm = document.querySelector('form[action*="laporan"]');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(filterForm);
                state.start_date = formData.get('start_date');
                state.end_date = formData.get('end_date');
                state.type = formData.get('type');
                state.status = formData.get('status') || '';
                state.status_bayar = formData.get('status_bayar') || '';
                state.page = 1;
                fetchData();
            });
        }
    }
    async function fetchData() {
        // ... (previous fetch logic) ...
        const params = new URLSearchParams();
        Object.keys(state).forEach(key => {
            if (state[key]) params.set(key, state[key]);
        });

        // Update URL
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({}, '', newUrl);

        if (tableBody) tableBody.style.opacity = '0.5';

        try {
            const response = await fetch(newUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const json = await response.json();

            // 1. Update Table
            if (tableBody) tableBody.innerHTML = json.html;

            // 2. Update Pagination
            updatePaginationInfo(json.total);

            // 3. Update Chart
            if (json.chartData && mainChart) {
                mainChart.data = json.chartData;
                mainChart.update();
                updateChartVisibility(json.chartData); // Call the helper!
            }

            // 4. Update Stats Cards
            if (json.stats) {
                const updateStat = (id, value) => {
                    const el = document.querySelector(`#${id} h3`);
                    if (el) el.innerText = value;
                };

                const fmtMoney = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val);

                // Transaction
                if (json.stats.total_transaksi !== undefined) updateStat('stat_transaksi_total', json.stats.total_transaksi);
                if (json.stats.total_buku !== undefined) updateStat('stat_transaksi_buku', json.stats.total_buku);
                if (json.stats.berjalan !== undefined) updateStat('stat_transaksi_berjalan', json.stats.berjalan);
                if (json.stats.selesai !== undefined) updateStat('stat_transaksi_selesai', json.stats.selesai);

                // Denda
                if (json.stats.total_denda !== undefined) updateStat('stat_denda_total', fmtMoney(json.stats.total_denda));
                if (json.stats.dibayar !== undefined) updateStat('stat_denda_dibayar', fmtMoney(json.stats.dibayar));
                if (json.stats.belum_bayar !== undefined) updateStat('stat_denda_belum', fmtMoney(json.stats.belum_bayar));

                // Buku Top
                if (json.stats.top_1_judul !== undefined) updateStat('stat_buku_top_1', json.stats.top_1_judul);
                if (json.stats.top_1_total !== undefined) updateStat('stat_buku_total', json.stats.top_1_total);
                if (json.stats.total_buku_unik_dipinjam !== undefined) updateStat('stat_buku_unik', json.stats.total_buku_unik_dipinjam);

                // Anggota Top
                if (json.stats.top_1_nama !== undefined) updateStat('stat_anggota_top_1', json.stats.top_1_nama);
                if (json.stats.top_1_total !== undefined && document.getElementById('stat_anggota_total')) updateStat('stat_anggota_total', json.stats.top_1_total);
                if (json.stats.total_anggota_aktif !== undefined) updateStat('stat_anggota_aktif', json.stats.total_anggota_aktif);
            }

        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            if (tableBody) tableBody.style.opacity = '1';
        }
    }

    function updatePaginationInfo(total) {
        const footer = document.querySelector('.p-4.border-t');
        if (!footer) return;

        const perPage = parseInt(state.limit);
        const currentPage = parseInt(state.page);
        const from = total === 0 ? 0 : ((currentPage - 1) * perPage) + 1;
        const to = Math.min(currentPage * perPage, total);

        const infoDiv = footer.querySelector('div.text-xs');
        if (infoDiv) {
            infoDiv.innerHTML = `Showing <span class="font-bold">${from}</span> to <span class="font-bold">${to}</span> of <span class="font-bold">${total}</span> entries`;
        }

        const linksContainer = footer.querySelector('.flex.gap-2') || footer.querySelector('nav') || footer.querySelector('div.flex.justify-between') || footer.lastElementChild;
        if (linksContainer) {
            // Ensure container is ready for flex items
            if (!linksContainer.classList.contains('flex')) {
                // If it uses the default laravel nav style or something else, we might want to replace innerHTML directly
                // but keeping classes safe is good.
                linksContainer.className = 'flex flex-wrap gap-1';
            }
            // Actually, x-datatable footer has a specific structure. 
            // Let's target the container that holds the buttons.
            // If we found 'nav', it might be the container.

            let html = buildPaginationHTML(total, perPage, currentPage);
            linksContainer.innerHTML = html;
        }
    }

    function buildPaginationHTML(total, limit, page) {
        const totalPages = Math.ceil(total / limit);
        if (totalPages <= 1) return '';

        let html = '<div class="flex flex-wrap gap-1">';

        // Previous
        if (page > 1) {
            html += `<a href="#" data-page="${page - 1}" class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 text-xs">Previous</a>`;
        } else {
            html += `<span class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-400 dark:text-white/40 text-xs cursor-not-allowed">Previous</span>`;
        }

        let start = Math.max(1, page - 2);
        let end = Math.min(totalPages, page + 2);

        // First Page
        if (start > 1) {
            html += `<a href="#" data-page="1" class="px-3 py-1 rounded border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 text-xs">1</a>`;
            if (start > 2) html += `<span class="px-2 text-slate-400">...</span>`;
        }

        // Window
        for (let i = start; i <= end; i++) {
            const active = i === page ? 'bg-primary text-white border-primary dark:bg-accent dark:text-primary-dark dark:border-accent' : 'bg-white dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-white/5';
            html += `<a href="#" data-page="${i}" class="px-3 py-1 rounded border text-xs font-bold ${active}">${i}</a>`;
        }

        // Last Page
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
});
