<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard - Library App</title>
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

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">

        <div id="mobile-overlay"
            class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <header
                class="animate-enter flex items-center justify-between sticky top-0 bg-surface/90 dark:bg-background-dark/95 backdrop-blur-sm z-30 px-4 sm:px-8 py-4 border-b border-primary/20 dark:border-border-dark">

                <div class="flex items-center gap-4">
                    <button id="open-sidebar"
                        class="lg:hidden text-primary-dark dark:text-white hover:text-primary dark:hover:text-accent transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-3xl">menu</span>
                    </button>

                    <h2 class="text-primary-dark dark:text-white text-xl sm:text-2xl font-bold tracking-tight">Overview
                    </h2>
                </div>

                <div class="flex-1 max-w-xl px-8 hidden md:block">
                    <div class="relative group input-focus-effect">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-primary-mid dark:text-accent">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input
                            class="block w-full p-3 pl-12 text-sm text-primary-dark dark:text-white bg-white dark:bg-surface-dark border-none rounded-full placeholder-primary-mid/60 dark:placeholder-white/40 focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:bg-white dark:focus:bg-[#36271F] transition-all shadow-sm dark:shadow-none"
                            placeholder="Cari buku, ISBN, atau anggota..." type="text" />
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">

                    <button onclick="toggleTheme()"
                        class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] shadow-sm border border-primary/20 dark:border-transparent cursor-pointer">
                        <span id="theme-icon" class="material-symbols-outlined text-[20px]">dark_mode</span>
                    </button>

                    <div class="hidden sm:flex flex-col items-end mr-2 cursor-default">
                        <span
                            class="text-primary-dark dark:text-white text-sm font-bold">{{ Auth::user()->nama }}</span>
                        <span
                            class="text-primary dark:text-accent text-xs uppercase tracking-wider font-bold">{{ Auth::user()->id_pengguna }}</span>
                    </div>

                    <button
                        class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] transition-all hover:rotate-12 relative shadow-sm border border-primary/20 dark:border-transparent cursor-pointer">
                        <span class="material-symbols-outlined">notifications</span>
                        <span
                            class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-white dark:border-surface-dark animate-pulse"></span>
                    </button>
                </div>
            </header>

            <div class="p-4 sm:p-8 flex flex-col gap-8 max-w-[1600px] mx-auto w-full">

                <div class="animate-enter flex justify-between items-end">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-primary-dark dark:text-white">Selamat Datang,
                            {{ Auth::user()->nama }}!
                        </h1>
                        <p class="text-primary-mid dark:text-white/60 mt-1">Berikut adalah ringkasan aktivitas
                            perpustakaan
                            hari ini.</p>
                    </div>

                    <!-- Status Sistem (Desktop Only) -->
                    <div class="hidden lg:flex flex-col items-end gap-2 mb-1">
                        <p class="text-xs font-bold text-primary-mid dark:text-white/40 uppercase tracking-widest mr-1">
                            Status Sistem</p>
                        <div class="flex items-center gap-3">
                            <!-- DB Status -->
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 rounded-full border shadow-sm {{ $dbStatus ? 'bg-green-100 dark:bg-green-500/10 border-green-200 dark:border-green-500/20' : 'bg-red-100 dark:bg-red-500/10 border-red-200 dark:border-red-500/20' }}">
                                <span class="relative flex size-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $dbStatus ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    <span
                                        class="relative inline-flex rounded-full size-2 {{ $dbStatus ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                </span>
                                <span
                                    class="text-xs font-bold {{ $dbStatus ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                    {{ $dbStatus ? 'Database Terhubung' : 'Koneksi DB Gagal' }}
                                </span>
                            </div>

                            <!-- Server Status -->
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-green-100 dark:bg-green-500/10 rounded-full border border-green-200 dark:border-green-500/20 shadow-sm">
                                <span class="relative flex size-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                <span class="text-xs font-bold text-green-700 dark:text-green-400">Server Normal</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <x-stat-card-component title="Koleksi" value="{{ number_format($stats['total_buku']) }}"
                        desc="Total Judul Buku" icon="library_books" color="blue" />

                    <x-stat-card-component title="Anggota" value="{{ number_format($stats['total_anggota']) }}"
                        desc="Anggota Terdaftar" icon="group" color="purple" />

                    <x-stat-card-component title="Sirkulasi" value="{{ number_format($stats['peminjaman_aktif']) }}"
                        desc="Peminjaman Aktif" icon="sync_alt" color="orange" />

                    <x-stat-card-component title="Denda"
                        value="Rp{{ number_format($stats['total_denda'], 0, ',', '.') }}" desc="Denda Belum Bayar"
                        icon="payments" color="red" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div
                        class="lg:col-span-2 bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter delay-300 shadow-sm dark:shadow-none transition-colors">
                        <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-6">Peminjaman Terbaru</h3>
                        <div class="flex flex-col gap-4">
                            @forelse($peminjamanTerbaru as $pinjam)
                                <div
                                    class="flex items-center justify-between p-4 bg-background-light dark:bg-[#261C16] rounded-xl border border-primary/10 dark:border-white/5 transition-colors hover:bg-primary/10 dark:hover:bg-[#4D3A2F] hover:border-primary/20 cursor-default">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold">
                                            {{ substr($pinjam->pengguna->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-primary-dark dark:text-white font-bold text-sm">
                                                {{ $pinjam->pengguna->nama }}
                                            </p>
                                            <p class="text-primary-mid dark:text-white/40 text-xs">
                                                {{ $pinjam->id_peminjaman }}
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-500 text-xs font-bold uppercase">
                                        {{ $pinjam->status_transaksi }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-primary-mid dark:text-white/40 text-center py-8">Belum ada data peminjaman.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter delay-300 shadow-sm dark:shadow-none transition-colors flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-4">Akses Cepat</h3>
                            <div class="flex flex-col gap-3">
                                <button
                                    class="w-full py-3 px-4 bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark rounded-xl font-bold text-sm hover:brightness-110 flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md">
                                    <span class="material-symbols-outlined">add</span>
                                    Transaksi Baru
                                </button>
                                <a href="{{ route('pengguna.index') }}"
                                    class="w-full py-3 px-4 bg-background-light dark:bg-[#36271F] text-primary-dark dark:text-white border border-primary/10 dark:border-transparent rounded-xl font-bold text-sm hover:bg-primary/10 dark:hover:bg-[#4D3A2F] flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md btn-animated">
                                    <span class="material-symbols-outlined">person_add</span>
                                    Tambah Anggota
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-primary/10 dark:border-white/5 lg:hidden">
                            <p
                                class="text-xs font-bold text-primary-mid dark:text-white/40 uppercase tracking-widest mb-3 select-none">
                                Status Sistem</p>
                            <div
                                class="flex items-center gap-2 text-sm {{ $dbStatus ? 'text-primary-dark dark:text-white' : 'text-red-600 dark:text-red-400 font-bold' }} cursor-default">
                                <span class="relative flex size-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $dbStatus ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    <span
                                        class="relative inline-flex rounded-full size-2 {{ $dbStatus ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                </span>
                                {{ $dbStatus ? 'Database Terhubung' : 'Koneksi DB Gagal' }}
                            </div>
                            <div
                                class="flex items-center gap-2 text-sm text-primary-dark dark:text-white mt-2 cursor-default">
                                <span class="relative flex size-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                Server Berjalan Normal
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>

</html>