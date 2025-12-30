<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Denda - Library App</title>
    <link rel="icon" type="image/png" href="https://laravel.com/img/favicon/favicon-32x32.png">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/live-search-laporan-denda.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Laporan Denda" />

            <div class="p-4 sm:p-8">
                <!-- Header & Breadcrumb -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <x-breadcrumb-component parent="Laporan" current="Denda" />
                </div>

                <!-- Filters -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm mb-6 animate-enter delay-100">
                    <form action="{{ route('laporan.denda') }}" method="GET"
                        class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="w-full md:w-auto">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Dari
                                Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                        </div>
                        <div class="w-full md:w-auto">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Sampai
                                Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                        </div>
                        <div class="w-full md:w-48">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Status
                                Pembayaran</label>
                            <select name="status_bayar"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                                <option value="">Semua</option>
                                <option value="lunas" {{ $statusBayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="belum_bayar" {{ $statusBayar == 'belum_bayar' ? 'selected' : '' }}>Belum
                                    Bayar</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full md:w-auto px-6 py-2.5 bg-primary text-white dark:bg-accent dark:text-primary-dark rounded-xl font-bold hover:brightness-110 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">filter_list</span>
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 animate-enter delay-200">
                    <div class="bg-red-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">money_off</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-red-100 text-sm font-bold uppercase tracking-wider mb-1">Total Denda
                                (Akumulasi)</div>
                            <div class="text-3xl font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="bg-green-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">attach_money</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-green-100 text-sm font-bold uppercase tracking-wider mb-1">Sudah Dibayar
                            </div>
                            <div class="text-3xl font-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="bg-yellow-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">hourglass_empty</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-yellow-100 text-sm font-bold uppercase tracking-wider mb-1">Belum Dibayar
                            </div>
                            <div class="text-3xl font-bold">Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark overflow-hidden shadow-sm animate-enter delay-300">
                    <div
                        class="p-4 border-b border-primary/20 dark:border-dark-border flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <span class="text-sm font-bold text-slate-600 dark:text-white/80">Show</span>
                            <div class="relative">
                                <select
                                    class="appearance-none bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg pl-3 pr-8 py-1.5 text-xs font-bold focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-2 flex items-center pointer-events-none text-slate-500">
                                    <span class="material-symbols-outlined text-sm">expand_more</span>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-slate-600 dark:text-white/80">entries</span>
                        </div>

                        <div class="relative w-full sm:w-64">
                            <input type="text" id="searchDendaInput" placeholder="Cari nama atau buku..."
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-all placeholder:text-slate-400">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-lg">search</span>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto relative min-h-[300px]">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                                        onclick="window.location.search = '?sort=created_at&direction=desc'"
                                        data-sort="created_at">
                                        <div class="flex items-center gap-1">Tanggal <span
                                                class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                        </div>
                                    </th>
                                    <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                                        onclick="window.location.search = '?sort=nama_anggota&direction=asc'"
                                        data-sort="nama_anggota">
                                        <div class="flex items-center gap-1">Peminjam <span
                                                class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                        </div>
                                    </th>
                                    <th class="p-4 font-medium">Jenis Denda</th>
                                    <th class="p-4 font-medium">Buku</th>
                                    <th class="p-4 font-medium text-right">Nominal</th>
                                    <th class="p-4 font-medium text-center">Status</th>
                                    <th class="p-4 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-white/10 text-sm text-slate-600 dark:text-white/80">
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination -->
                    <div
                        class="p-4 border-t border-primary/20 dark:border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50 dark:bg-white/5">
                        <div class="text-xs text-slate-500 dark:text-white/60 font-medium">
                            Showing <span class="font-bold">0</span> to <span class="font-bold">0</span> of <span
                                class="font-bold">0</span> entries
                        </div>
                        <div id="paginationContainer" class="flex gap-2">
                            <!-- JS Populated -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>