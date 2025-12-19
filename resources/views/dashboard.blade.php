<!DOCTYPE html>
<html class="dark" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard - Library App</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    
    <!-- VITE -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        
        <!-- MOBILE OVERLAY -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity opacity-0"></div>

        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 w-72 h-full bg-background-dark border-r border-[#36271F] p-6 flex flex-col justify-between z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none overflow-y-auto">
            
            <!-- Close Sidebar Button -->
            <button id="close-sidebar" class="cursor-pointer lg:hidden absolute top-4 right-4 text-white/60 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="flex flex-col gap-8 mt-2 lg:mt-0">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-2">
                    <div class="bg-accent/20 flex items-center justify-center rounded-full size-12 hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-accent" style="font-size: 28px;">local_library</span>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-white text-lg font-bold leading-tight">Library App</h1>
                        <p class="text-white/60 text-xs font-medium">Panel Manajemen</p>
                    </div>
                </div>
                
                <nav class="flex flex-col gap-2">
                    
                    <!-- Menu Utama (All Role) -->
                    <a class="flex items-center gap-3 px-4 py-3 rounded-full bg-accent text-primary-dark transition-all hover:brightness-110 shadow-[0_0_15px_rgba(236,177,118,0.3)]" href="{{ route('dashboard') }}">
                        <span class="material-symbols-outlined filled" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                        <p class="text-sm font-bold">Dashboard</p>
                    </a>

                    <!-- GROUP: ADMINISTRATOR -->
                    @if(Auth::user()->peran == 'admin')
                        <div class="mt-4 mb-2 px-4 text-xs font-bold text-white/40 uppercase tracking-widest">Administrator</div>
                        
                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="{{ route('pengguna.index') }}">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">group</span>
                            <p class="text-sm font-medium">Kelola Pengguna</p>
                        </a>
                        
                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="{{ route('buku.index') }}">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">library_books</span>
                            <p class="text-sm font-medium">Kelola Buku</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="{{ route('kategori.index') }}">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">category</span>
                            <p class="text-sm font-medium">Kategori</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="#">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">monitoring</span>
                            <p class="text-sm font-medium">Laporan</p>
                        </a>
                    @endif

                    <!-- GROUP: SIRKULASI (Admin & Petugas) -->
                    @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'petugas')
                        <div class="mt-4 mb-2 px-4 text-xs font-bold text-white/40 uppercase tracking-widest">Sirkulasi</div>
                        
                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="#">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">sync_alt</span>
                            <p class="text-sm font-medium">Transaksi Peminjaman</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="#">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">assignment_return</span>
                            <p class="text-sm font-medium">Pengembalian & Denda</p>
                        </a>
                    @endif

                    <!-- GROUP: ANGGOTA -->
                    @if(Auth::user()->peran == 'anggota')
                        <div class="mt-4 mb-2 px-4 text-xs font-bold text-white/40 uppercase tracking-widest">Menu Anggota</div>
                        
                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="#">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">search</span>
                            <p class="text-sm font-medium">Cari Buku</p>
                        </a>

                        <a class="flex items-center gap-3 px-4 py-3 rounded-full text-white/70 hover:bg-[#36271F] hover:text-white transition-colors group" href="#">
                            <span class="material-symbols-outlined group-hover:text-accent transition-colors">history</span>
                            <p class="text-sm font-medium">Riwayat Saya</p>
                        </a>
                    @endif

                </nav>
            </div>

            <!-- User Profile & Logout -->
            <div class="flex flex-col gap-4 shrink-0 pt-4 border-t border-[#36271F]">
                <div class="bg-surface-dark p-4 rounded-xl flex items-center gap-3 border border-[#36271F] hover-card cursor-default">
                    <div class="size-10 rounded-full bg-accent/20 flex items-center justify-center text-accent font-bold">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <p class="text-sm font-bold truncate">{{ Auth::user()->nama }}</p>
                        <p class="text-xs text-white/60 truncate capitalize">{{ Auth::user()->peran }}</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="w-full form-logout">
                    @csrf
                    <button type="submit" class="flex w-full cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-full h-12 bg-[#36271F] hover:bg-[#4D3A2F] text-white text-sm font-bold transition-all active:scale-95">
                        <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <header class="animate-enter flex items-center justify-between sticky top-0 bg-background-dark/95 backdrop-blur-sm z-30 px-4 sm:px-8 py-4 border-b border-[#36271F]">
                
                <div class="flex items-center gap-4">
                    <!-- Hamburger Button -->
                    <button id="open-sidebar" class="cursor-pointer **:lg:hidden text-white hover:text-accent transition-colors">
                        <span class="material-symbols-outlined text-3xl">menu</span>
                    </button>

                    <h2 class="text-white text-xl sm:text-2xl font-bold tracking-tight">Overview</h2>
                </div>
                
                <!-- Search Bar -->
                <div class="flex-1 max-w-xl px-8 hidden md:block">
                    <div class="relative group input-focus-effect">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-accent">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input class="block w-full p-3 pl-12 text-sm text-white bg-surface-dark border-none rounded-full placeholder-white/40 focus:ring-2 focus:ring-accent focus:bg-[#36271F] transition-all" placeholder="Cari buku, ISBN, atau anggota..." type="text" />
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="hidden sm:flex flex-col items-end mr-2">
                        <span class="text-white text-sm font-bold">{{ Auth::user()->nama }}</span>
                        <span class="text-accent text-xs uppercase tracking-wider font-bold">{{ Auth::user()->id_pengguna }}</span>
                    </div>

                    <button class="cursor-pointer flex items-center justify-center size-10 rounded-full bg-surface-dark text-white hover:bg-[#36271F] transition-all hover:rotate-12 relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-surface-dark animate-pulse"></span>
                    </button>
                </div>
            </header>

            <!-- Dashboard -->
            <div class="p-4 sm:p-8 flex flex-col gap-8 max-w-400 mx-auto w-full">
                
                <!-- Welcome Section -->
                <div class="animate-enter">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Selamat Datang, {{ Auth::user()->nama }}!</h1>
                    <p class="text-white/60 mt-1">Berikut adalah ringkasan aktivitas perpustakaan hari ini.</p>
                </div>

                <!-- STATS CARDS GRID -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    
                    <!-- Card 1: Total Buku -->
                    <div class="bg-surface-dark p-6 rounded-2xl border border-[#36271F] hover-card animate-enter delay-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="size-12 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-500">
                                <span class="material-symbols-outlined text-3xl">library_books</span>
                            </div>
                            <span class="text-xs font-bold text-blue-500 bg-blue-500/10 px-2 py-1 rounded-lg">Koleksi</span>
                        </div>
                        <h3 class="text-3xl font-bold text-white">{{ number_format($stats['total_buku']) }}</h3>
                        <p class="text-white/40 text-sm font-medium mt-1">Total Judul Buku</p>
                    </div>

                    <!-- Card 2: Total Anggota -->
                    <div class="bg-surface-dark p-6 rounded-2xl border border-[#36271F] hover-card animate-enter delay-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="size-12 rounded-xl bg-purple-500/20 flex items-center justify-center text-purple-500">
                                <span class="material-symbols-outlined text-3xl">group</span>
                            </div>
                            <span class="text-xs font-bold text-purple-500 bg-purple-500/10 px-2 py-1 rounded-lg">Aktif</span>
                        </div>
                        <h3 class="text-3xl font-bold text-white">{{ number_format($stats['total_anggota']) }}</h3>
                        <p class="text-white/40 text-sm font-medium mt-1">Total Anggota</p>
                    </div>

                    <!-- Card 3: Peminjaman Berjalan -->
                    <div class="bg-surface-dark p-6 rounded-2xl border border-[#36271F] hover-card animate-enter delay-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="size-12 rounded-xl bg-orange-500/20 flex items-center justify-center text-orange-500">
                                <span class="material-symbols-outlined text-3xl">sync_alt</span>
                            </div>
                            <span class="text-xs font-bold text-orange-500 bg-orange-500/10 px-2 py-1 rounded-lg">Proses</span>
                        </div>
                        <h3 class="text-3xl font-bold text-white">{{ number_format($stats['peminjaman_aktif']) }}</h3>
                        <p class="text-white/40 text-sm font-medium mt-1">Peminjaman Aktif</p>
                    </div>

                    <!-- Card 4: Total Denda -->
                    <div class="bg-surface-dark p-6 rounded-2xl border border-[#36271F] hover-card animate-enter delay-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="size-12 rounded-xl bg-red-500/20 flex items-center justify-center text-red-500">
                                <span class="material-symbols-outlined text-3xl">payments</span>
                            </div>
                            <span class="text-xs font-bold text-red-500 bg-red-500/10 px-2 py-1 rounded-lg">Piutang</span>
                        </div>
                        <h3 class="text-3xl font-bold text-white">Rp{{ number_format($stats['total_denda'], 0, ',', '.') }}</h3>
                        <p class="text-white/40 text-sm font-medium mt-1">Denda Belum Bayar</p>
                    </div>
                </div>

                <!-- Recent Borrowings Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-surface-dark rounded-2xl border border-[#36271F] p-6 animate-enter delay-300">
                        <h3 class="text-lg font-bold text-white mb-6">Peminjaman Terbaru</h3>
                        <div class="flex flex-col gap-4">
                            @forelse($peminjamanTerbaru as $pinjam)
                                <div class="flex items-center justify-between p-4 bg-[#261C16] rounded-xl border border-white/5">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-full bg-accent/20 flex items-center justify-center text-accent font-bold">
                                            {{ substr($pinjam->pengguna->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-bold text-sm">{{ $pinjam->pengguna->nama }}</p>
                                            <p class="text-white/40 text-xs">{{ $pinjam->id_peminjaman }}</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-orange-500/10 text-orange-500 text-xs font-bold uppercase">
                                        {{ $pinjam->status_transaksi }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-white/40 text-center py-8">Belum ada data peminjaman.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Access Section -->
                    <div class="bg-surface-dark rounded-2xl border border-[#36271F] p-6 animate-enter delay-300">
                        <h3 class="text-lg font-bold text-white mb-4">Akses Cepat</h3>
                        <div class="flex flex-col gap-3">
                            <button class="cursor-pointer w-full py-3 px-4 bg-accent text-primary-dark rounded-xl font-bold text-sm hover:brightness-110 transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">add</span>
                                Transaksi Baru
                            </button>
                            <button class="cursor-pointer w-full py-3 px-4 bg-[#36271F] text-white rounded-xl font-bold text-sm hover:bg-[#4D3A2F] transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">person_add</span>
                                Tambah Anggota
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>