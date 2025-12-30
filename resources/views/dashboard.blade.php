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

    <link rel="icon" type="image/png"
        href="{{ !empty($pengaturan->logo_path) ? asset('storage/' . $pengaturan->logo_path) : 'https://laravel.com/img/favicon/favicon-32x32.png' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">

        <div id="mobile-overlay"
            class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Dashboard" />

            <div class="p-4 sm:p-8 flex flex-col gap-8 max-w-[1600px] mx-auto w-full">

                {{-- Flash Messages --}}

                @if (session('error'))
                    <div class="mb-4 lg:mb-6 p-4 flex items-center gap-3 text-sm font-medium text-red-800 dark:text-red-200 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl animate-enter"
                        role="alert">
                        <span class="material-symbols-outlined text-xl">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="animate-enter flex justify-between items-end">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-primary-dark dark:text-white">Selamat Datang,
                            {{ Auth::user()->nama }}!
                        </h1>
                        <p class="text-primary-mid dark:text-white/60 mt-1">Berikut adalah ringkasan aktivitas
                            perpustakaan
                            hari ini.</p>
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
                                <a href="{{ route('anggota.index') }}"
                                    class="w-full py-3 px-4 bg-background-light dark:bg-[#36271F] text-primary-dark dark:text-white border border-primary/10 dark:border-transparent rounded-xl font-bold text-sm hover:bg-primary/10 dark:hover:bg-[#4D3A2F] flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md btn-animated">
                                    <span class="material-symbols-outlined">person_add</span>
                                    Tambah Anggota
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-primary/10 dark:border-white/5 lg:hidden">
                            <!-- Mobile status removed, moved to sidebar -->
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        // System status moved to global script (resources/js/system-status.js)
    </script>

</html>