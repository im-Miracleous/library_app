<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengembalian & Denda - Library App</title>
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

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/live-search-sirkulasi-pengembalian.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Pengembalian & Denda" />

            <div class="p-4 sm:p-8">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <x-breadcrumb-component parent="Sirkulasi" current="Pengembalian" />
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark overflow-hidden animate-enter delay-100 shadow-sm">
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
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 dark:text-white/40 text-lg">search</span>
                            <input type="text" id="returnSearchInput" placeholder="Cari Kode atau Peminjam..."
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg pl-10 pr-4 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-all placeholder-primary-mid/60 dark:placeholder-white/40">
                        </div>
                    </div>

                    <div class="overflow-x-auto relative min-h-[300px]">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr
                                    class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 text-xs uppercase tracking-wider">
                                    <th class="p-4 pl-6 font-medium w-32 cursor-pointer hover:text-primary transition-colors select-none"
                                        onclick="window.location.search = '?sort=id_peminjaman&direction=asc'"
                                        data-sort="id_peminjaman">
                                        <div class="flex items-center gap-1">Kode <span
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
                                    <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                                        onclick="window.location.search = '?sort=tanggal_pinjam&direction=asc'"
                                        data-sort="tanggal_pinjam">
                                        <div class="flex items-center gap-1">Tgl Pinjam <span
                                                class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                        </div>
                                    </th>
                                    <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                                        onclick="window.location.search = '?sort=tanggal_jatuh_tempo&direction=asc'"
                                        data-sort="tanggal_jatuh_tempo">
                                        <div class="flex items-center gap-1">Jatuh Tempo <span
                                                class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                        </div>
                                    </th>
                                    <th class="p-4 font-medium text-center">Sisa Waktu</th>
                                    <th class="p-4 font-medium text-right pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pengembalianTableBody"
                                class="divide-y divide-slate-100 dark:divide-[#36271F] text-sm text-slate-600 dark:text-white/80">
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
                            <!-- Pagination Generated by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>