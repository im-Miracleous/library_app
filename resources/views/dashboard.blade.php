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

                    <!-- Status Sistem (Desktop Only) -->
                    <div class="hidden lg:flex flex-col items-end gap-2 mb-1">
                        <p class="text-xs font-bold text-primary-mid dark:text-white/40 uppercase tracking-widest mr-1">
                            Status Sistem</p>
                        <div class="flex items-center gap-3">
                            <!-- DB Status (JS Monitor) -->
                            <div
                                class="sys-db-container flex items-center gap-2 px-3 py-1.5 rounded-full border shadow-sm transition-colors duration-300 bg-green-100 dark:bg-green-500/10 border-green-200 dark:border-green-500/20">
                                <span class="relative flex size-2">
                                    <span
                                        class="sys-db-dot-animate animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-green-400"></span>
                                    <span
                                        class="sys-db-dot-static relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                <span class="sys-db-text text-xs font-bold text-green-700 dark:text-green-400">
                                    Checking...
                                </span>
                            </div>

                            <!-- Server Status (JS Monitor) -->
                            <div
                                class="sys-server-container flex items-center gap-2 px-3 py-1.5 rounded-full border shadow-sm transition-colors duration-300 bg-green-100 dark:bg-green-500/10 border-green-200 dark:border-green-500/20">
                                <span class="relative flex size-2">
                                    <span
                                        class="sys-server-dot-animate animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-green-400"></span>
                                    <span
                                        class="sys-server-dot-static relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                <span
                                    class="sys-server-text text-xs font-bold text-green-700 dark:text-green-400">Checking...</span>
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
                                <a href="{{ route('anggota.index') }}"
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
                                class="sys-db-text-mobile flex items-center gap-2 text-sm font-bold text-primary-dark dark:text-white cursor-default">
                                <span class="relative flex size-2">
                                    <span
                                        class="sys-db-dot-animate animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-green-400"></span>
                                    <span
                                        class="sys-db-dot-static relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                <span class="sys-db-label-mobile">Checking...</span>
                            </div>
                            <div
                                class="sys-server-text-mobile flex items-center gap-2 text-sm text-primary-dark dark:text-white mt-2 cursor-default font-bold">
                                <span class="relative flex size-2">
                                    <span
                                        class="sys-server-dot-animate animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-green-400"></span>
                                    <span
                                        class="sys-server-dot-static relative inline-flex rounded-full size-2 bg-green-500"></span>
                                </span>
                                <span class="sys-server-label-mobile">Checking...</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Function to update DB status UI
            function updateDbStatus(isOnline) {
                // Desktop Elements
                const containers = document.querySelectorAll('.sys-db-container');
                const texts = document.querySelectorAll('.sys-db-text');
                const dotsAnimate = document.querySelectorAll('.sys-db-dot-animate');
                const dotsStatic = document.querySelectorAll('.sys-db-dot-static');

                // Mobile Elements
                const mobileTexts = document.querySelectorAll('.sys-db-text-mobile');
                const mobileLabels = document.querySelectorAll('.sys-db-label-mobile');

                if (isOnline) {
                    // Update Desktop
                    containers.forEach(el => {
                        el.classList.remove('bg-red-100', 'border-red-200', 'dark:bg-red-500/10', 'dark:border-red-500/20');
                        el.classList.add('bg-green-100', 'border-green-200', 'dark:bg-green-500/10', 'dark:border-green-500/20');
                    });
                    texts.forEach(el => {
                        el.classList.remove('text-red-700', 'dark:text-red-400');
                        el.classList.add('text-green-700', 'dark:text-green-400');
                        el.textContent = 'Database Terhubung';
                    });
                    dotsAnimate.forEach(el => {
                        el.classList.remove('bg-red-400');
                        el.classList.add('bg-green-400');
                    });
                    dotsStatic.forEach(el => {
                        el.classList.remove('bg-red-500');
                        el.classList.add('bg-green-500');
                    });

                    // Update Mobile
                    mobileTexts.forEach(el => {
                        el.classList.remove('text-red-600', 'dark:text-red-400');
                        el.classList.add('text-primary-dark', 'dark:text-white');
                    });
                    mobileLabels.forEach(el => el.textContent = 'Database Terhubung');

                } else {
                    // Update Desktop (Offline)
                    containers.forEach(el => {
                        el.classList.remove('bg-green-100', 'border-green-200', 'dark:bg-green-500/10', 'dark:border-green-500/20');
                        el.classList.add('bg-red-100', 'border-red-200', 'dark:bg-red-500/10', 'dark:border-red-500/20');
                    });
                    texts.forEach(el => {
                        el.classList.remove('text-green-700', 'dark:text-green-400');
                        el.classList.add('text-red-700', 'dark:text-red-400');
                        el.textContent = 'Koneksi DB Gagal';
                    });
                    dotsAnimate.forEach(el => {
                        el.classList.remove('bg-green-400');
                        el.classList.add('bg-red-400');
                    });
                    dotsStatic.forEach(el => {
                        el.classList.remove('bg-green-500');
                        el.classList.add('bg-red-500');
                    });

                    // Update Mobile (Offline)
                    mobileTexts.forEach(el => {
                        el.classList.remove('text-primary-dark', 'dark:text-white');
                        el.classList.add('text-red-600', 'dark:text-red-400');
                    });
                    mobileLabels.forEach(el => el.textContent = 'Koneksi DB Gagal');
                }
            }

            // Function to update Server status UI
            function updateServerStatus(isOnline) {
                const containers = document.querySelectorAll('.sys-server-container');
                const texts = document.querySelectorAll('.sys-server-text');
                const dotsAnimate = document.querySelectorAll('.sys-server-dot-animate');
                const dotsStatic = document.querySelectorAll('.sys-server-dot-static');

                const mobileTexts = document.querySelectorAll('.sys-server-text-mobile');
                const mobileLabels = document.querySelectorAll('.sys-server-label-mobile');

                if (isOnline) {
                    // Desktop
                    containers.forEach(el => {
                        el.classList.remove('bg-red-100', 'border-red-200', 'dark:bg-red-500/10', 'dark:border-red-500/20');
                        el.classList.add('bg-green-100', 'border-green-200', 'dark:bg-green-500/10', 'dark:border-green-500/20');
                    });
                    texts.forEach(el => {
                        el.classList.remove('text-red-700', 'dark:text-red-400');
                        el.classList.add('text-green-700', 'dark:text-green-400');
                        el.textContent = 'Server Normal';
                    });
                    dotsAnimate.forEach(el => {
                        el.classList.remove('bg-red-400');
                        el.classList.add('bg-green-400');
                    });
                    dotsStatic.forEach(el => {
                        el.classList.remove('bg-red-500');
                        el.classList.add('bg-green-500');
                    });
                    // Mobile
                    mobileTexts.forEach(el => {
                        el.classList.remove('text-red-600', 'dark:text-red-400');
                        el.classList.add('text-primary-dark', 'dark:text-white');
                    });
                    mobileLabels.forEach(el => el.textContent = 'Server Normal');

                } else {
                    // Desktop (Offline)
                    containers.forEach(el => {
                        el.classList.remove('bg-green-100', 'border-green-200', 'dark:bg-green-500/10', 'dark:border-green-500/20');
                        el.classList.add('bg-red-100', 'border-red-200', 'dark:bg-red-500/10', 'dark:border-red-500/20');
                    });
                    texts.forEach(el => {
                        el.classList.remove('text-green-700', 'dark:text-green-400');
                        el.classList.add('text-red-700', 'dark:text-red-400');
                        el.textContent = 'Server Down';
                    });
                    dotsAnimate.forEach(el => {
                        el.classList.remove('bg-green-400');
                        el.classList.add('bg-red-400');
                    });
                    dotsStatic.forEach(el => {
                        el.classList.remove('bg-green-500');
                        el.classList.add('bg-red-500');
                    });
                    // Mobile
                    mobileTexts.forEach(el => {
                        el.classList.remove('text-primary-dark', 'dark:text-white');
                        el.classList.add('text-red-600', 'dark:text-red-400');
                    });
                    mobileLabels.forEach(el => el.textContent = 'Server Down');
                }
            }

            // Polling Function
            function checkSystemStatus() {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 5000); // Timeout 5 detik

                fetch('/api/system-status', { signal: controller.signal })
                    .then(response => {
                        clearTimeout(timeoutId); // Clear timeout jika sukses
                        if (!response.ok) {
                            throw new Error('Network error');
                        }
                        return response.json();
                    })
                    .then(data => {
                        updateDbStatus(data.db_status);
                        updateServerStatus(true);
                    })
                    .catch(error => {
                        // Error bisa karena Network Error (Server Mati) atau Timeout (Abort)
                        updateDbStatus(false);
                        updateServerStatus(false);
                    });
            }

            // Initial Check
            checkSystemStatus();

            // Interval Check (every 5s)
            setInterval(checkSystemStatus, 5000);
        });
    </script>

</html>