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

        <aside id="sidebar"
            class="fixed lg:static inset-y-0 left-0 w-72 h-full bg-surface dark:bg-surface-dark border-r border-primary/20 dark:border-border-dark flex flex-col z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none overflow-hidden">

            <button id="close-sidebar"
                class="lg:hidden absolute top-4 right-4 text-primary-dark dark:text-white/60 hover:text-primary dark:hover:text-white cursor-pointer transition-colors z-50">
                <span class="material-symbols-outlined">close</span>
            </button>

            <!-- Fixed Header -->
            <div class="flex-none flex items-center px-6 py-5 border-b border-primary/20 dark:border-border-dark gap-3">
                <div
                    class="bg-primary/20 dark:bg-accent/20 flex items-center justify-center rounded-full size-10 flex-shrink-0 cursor-default">
                    <span class="material-symbols-outlined text-primary-dark dark:text-accent"
                        style="font-size: 24px;">local_library</span>
                </div>
                <div class="flex flex-col cursor-default">
                    <h1 class="text-primary-dark dark:text-white text-base font-bold leading-tight">Library App</h1>
                    <p class="text-primary-mid dark:text-white/60 text-[10px] font-medium uppercase tracking-wider">
                        Panel Manajemen</p>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto flex flex-col justify-between px-4 py-4">
                <nav class="flex flex-col gap-2">

                    <!-- Menu Utama (Dashboard) -->
                    <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]"
                        href="{{ route('dashboard') }}">
                        <span class="material-symbols-outlined filled"
                            style="font-variation-settings: 'FILL' 1;">dashboard</span>
                        <p class="text-sm font-bold">Dashboard</p>
                    </a>

                    @if(Auth::user()->peran == 'admin')
                        <div
                            class="mt-4 mb-2 px-4 text-xs font-bold text-primary-mid/60 dark:text-white/40 uppercase tracking-widest select-none">
                            Administrator</div>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="{{ route('pengguna.index') }}">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">group</span>
                            <p class="text-sm font-medium">Kelola Pengguna</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="{{ route('buku.index') }}">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">library_books</span>
                            <p class="text-sm font-medium">Kelola Buku</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="{{ route('kategori.index') }}">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">category</span>
                            <p class="text-sm font-medium">Kategori Buku</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="#">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">monitoring</span>
                            <p class="text-sm font-medium">Laporan</p>
                        </a>
                    @endif

                    @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'petugas')
                        <div
                            class="mt-4 mb-2 px-4 text-xs font-bold text-primary-mid/60 dark:text-white/40 uppercase tracking-widest select-none">
                            Sirkulasi</div>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="#">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">sync_alt</span>
                            <p class="text-sm font-medium">Transaksi Peminjaman</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="#">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">assignment_return</span>
                            <p class="text-sm font-medium">Pengembalian & Denda</p>
                        </a>
                    @endif

                    @if(Auth::user()->peran == 'anggota')
                        <div
                            class="mt-4 mb-2 px-4 text-xs font-bold text-primary-mid/60 dark:text-white/40 uppercase tracking-widest select-none">
                            Menu Anggota</div>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="#">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">search</span>
                            <p class="text-sm font-medium">Cari Buku</p>
                        </a>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group"
                            href="#">
                            <span
                                class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">history</span>
                            <p class="text-sm font-medium">Riwayat Saya</p>
                        </a>
                    @endif

                </nav>

                <div
                    class="flex flex-col gap-4 flex-shrink-0 pt-4 pb-4 border-t border-primary/20 dark:border-border-dark mt-4">
                    <div
                        class="bg-white/50 dark:bg-surface-dark p-4 rounded-xl flex items-center gap-3 border border-primary/10 dark:border-border-dark cursor-default transition-colors">
                        <div
                            class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold">
                            {{ substr(Auth::user()->nama, 0, 1) }}
                        </div>
                        <div class="flex flex-col overflow-hidden">
                            <p class="text-sm font-bold truncate text-primary-dark dark:text-white">
                                {{ Auth::user()->nama }}
                            </p>
                            <p class="text-xs text-primary-mid dark:text-white/60 truncate capitalize">
                                {{ Auth::user()->peran }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="w-full form-logout">
                        @csrf
                        <button type="submit"
                            class="flex w-full cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-full h-12 bg-white dark:bg-[#36271F] border border-primary/20 dark:border-transparent hover:bg-red-50 dark:hover:bg-[#4D3A2F] text-primary-dark dark:text-white hover:text-red-600 dark:hover:text-white text-sm font-bold transition-all active:scale-95 shadow-sm hover:shadow-md">
                            <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

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
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-green-100 dark:bg-green-500/10 rounded-full border border-green-200 dark:border-green-500/20 shadow-sm">
                                <span class="relative flex size-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                <span class="text-xs font-bold text-green-700 dark:text-green-400">Database
                                    Terhubung</span>
                            </div>
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
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-primary/20 dark:border-border-dark hover:border-primary/40 dark:hover:border-accent/50 hover:shadow-md hover:-translate-y-1 animate-enter delay-100 shadow-sm dark:shadow-none transition-all duration-300 cursor-default">
                        <div class="flex items-center justify-start gap-4 mb-4">
                            <div
                                class="size-14 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-500">
                                <span class="material-symbols-outlined text-3xl">library_books</span>
                            </div>
                            <span
                                class="text-base font-bold text-blue-600 dark:text-blue-500 bg-blue-100 dark:bg-blue-500/10 px-3 py-1.5 rounded-xl">Koleksi</span>
                        </div>
                        <h3 class="text-3xl font-bold text-primary-dark dark:text-white">
                            {{ number_format($stats['total_buku']) }}
                        </h3>
                        <p class="text-primary-mid dark:text-white/40 text-sm font-medium mt-1">Total Judul Buku</p>
                    </div>

                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-primary/20 dark:border-border-dark hover:border-primary/40 dark:hover:border-accent/50 hover:shadow-md hover:-translate-y-1 animate-enter delay-200 shadow-sm dark:shadow-none transition-all duration-300 cursor-default">
                        <div class="flex items-center justify-start gap-4 mb-4">
                            <div
                                class="size-14 rounded-2xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-500">
                                <span class="material-symbols-outlined text-3xl">group</span>
                            </div>
                            <span
                                class="text-base font-bold text-purple-600 dark:text-purple-500 bg-purple-100 dark:bg-purple-500/10 px-3 py-1.5 rounded-xl">Anggota</span>
                        </div>
                        <h3 class="text-3xl font-bold text-primary-dark dark:text-white">
                            {{ number_format($stats['total_anggota']) }}
                        </h3>
                        <p class="text-primary-mid dark:text-white/40 text-sm font-medium mt-1">Total Anggota</p>
                    </div>

                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-primary/20 dark:border-border-dark hover:border-primary/40 dark:hover:border-accent/50 hover:shadow-md hover:-translate-y-1 animate-enter delay-300 shadow-sm dark:shadow-none transition-all duration-300 cursor-default">
                        <div class="flex items-center justify-start gap-4 mb-4">
                            <div
                                class="size-14 rounded-2xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-600 dark:text-orange-500">
                                <span class="material-symbols-outlined text-3xl">sync_alt</span>
                            </div>
                            <span
                                class="text-base font-bold text-orange-600 dark:text-orange-500 bg-orange-100 dark:bg-orange-500/10 px-3 py-1.5 rounded-xl">Sirkulasi</span>
                        </div>
                        <h3 class="text-3xl font-bold text-primary-dark dark:text-white">
                            {{ number_format($stats['peminjaman_aktif']) }}
                        </h3>
                        <p class="text-primary-mid dark:text-white/40 text-sm font-medium mt-1">Peminjaman Aktif</p>
                    </div>

                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-2xl border border-primary/20 dark:border-border-dark hover:border-primary/40 dark:hover:border-accent/50 hover:shadow-md hover:-translate-y-1 animate-enter delay-300 shadow-sm dark:shadow-none transition-all duration-300 cursor-default">
                        <div class="flex items-center justify-start gap-4 mb-4">
                            <div
                                class="size-14 rounded-2xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-500">
                                <span class="material-symbols-outlined text-3xl">payments</span>
                            </div>
                            <span
                                class="text-base font-bold text-red-600 dark:text-red-500 bg-red-100 dark:bg-red-500/10 px-3 py-1.5 rounded-xl">Denda</span>
                        </div>
                        <h3 class="text-3xl font-bold text-primary-dark dark:text-white">
                            Rp{{ number_format($stats['total_denda'], 0, ',', '.') }}</h3>
                        <p class="text-primary-mid dark:text-white/40 text-sm font-medium mt-1">Denda Belum Bayar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div
                        class="lg:col-span-2 bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter delay-300 shadow-sm dark:shadow-none transition-colors">
                        <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-6">Peminjaman Terbaru</h3>
                        <div class="flex flex-col gap-4">
                            @forelse($peminjamanTerbaru as $pinjam)
                                <div
                                    class="flex items-center justify-between p-4 bg-background-light dark:bg-[#261C16] rounded-xl border border-primary/10 dark:border-white/5 transition-colors hover:bg-white dark:hover:bg-[#2F241E] cursor-default">
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
                                class="flex items-center gap-2 text-sm text-primary-dark dark:text-white cursor-default">
                                <span class="relative flex size-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                Database Terhubung
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