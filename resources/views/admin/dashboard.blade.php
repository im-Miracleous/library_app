@extends('layouts.app')

@section('title', 'Dashboard - Library App')
@section('header-title', 'Dashboard')

@section('content')
    {{-- Pending Verifications Notification --}}
    @if (($stats['pending_verifications'] ?? 0) > 0)
        <div class="animate-enter mb-2 p-4 flex items-center justify-between gap-4 text-sm font-medium text-blue-800 dark:text-blue-200 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl shadow-sm"
            role="alert">
            <div class="flex items-center gap-3">
                <div
                    class="size-10 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined">notification_important</span>
                </div>
                <div>
                    <h4 class="font-bold text-base">Permintaan Verifikasi</h4>
                    <p class="text-blue-700/70 dark:text-blue-300/60 font-medium">Ada
                        {{ $stats['pending_verifications'] }} peminjaman baru yang menunggu persetujuan Anda.
                    </p>
                </div>
            </div>
            <a href="{{ route('peminjaman.index', ['status' => 'menunggu_verifikasi']) }}"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-md shadow-blue-500/20 hover:scale-[1.02] active:scale-95 shrink-0">
                Cek Sekarang
            </a>
        </div>
    @endif

    <div class="animate-enter flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-primary-dark dark:text-white">Selamat Datang,
                {{ Auth::user()->nama }}!
            </h1>
            <p class="text-primary-mid dark:text-white/60 mt-1">Berikut adalah ringkasan aktivitas perpustakaan.</p>
        </div>
        
        <!-- Time Filter -->
        <div class="flex bg-white dark:bg-surface-dark rounded-xl border border-primary/10 dark:border-border-dark p-1 shadow-sm">
            <button onclick="updateDashboard('today')" id="btn-today" 
                class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ $filter == 'today' ? 'bg-primary text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 dark:text-white/60 dark:hover:bg-white/5' }}">
                Hari Ini
            </button>
            <button onclick="updateDashboard('week')" id="btn-week" 
                class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ $filter == 'week' ? 'bg-primary text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 dark:text-white/60 dark:hover:bg-white/5' }}">
                Minggu Ini
            </button>
            <button onclick="updateDashboard('month')" id="btn-month" 
                class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ $filter == 'month' ? 'bg-primary text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 dark:text-white/60 dark:hover:bg-white/5' }}">
                Bulan Ini
            </button>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Peminjaman Chart -->
        <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm animate-enter delay-100">
            <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500">pie_chart</span>
                Status Peminjaman
            </h3>
            <div class="relative h-64 w-full group">
                <canvas id="peminjamanChart" class="transition-opacity duration-300"></canvas>
                
                <!-- Empty State -->
                <div id="peminjamanEmptyState" class="hidden absolute inset-0 flex-col items-center justify-center text-slate-400 dark:text-white/40">
                    <div class="p-4 rounded-full bg-slate-100 dark:bg-white/5 mb-3 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl">data_loss_prevention</span>
                    </div>
                    <p class="text-sm font-semibold">Tidak ada aktivitas peminjaman</p>
                    <p class="text-xs">untuk periode ini</p>
                </div>
            </div>
        </div>

        <!-- Pengunjung Chart -->
        <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm animate-enter delay-200">
            <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">groups</span>
                Kunjungan Perpustakaan
            </h3>
            <div class="relative h-64 w-full group">
                <canvas id="pengunjungChart" class="transition-opacity duration-300"></canvas>
                
                <!-- Empty State -->
                <div id="pengunjungEmptyState" class="hidden absolute inset-0 flex-col items-center justify-center text-slate-400 dark:text-white/40">
                    <div class="p-4 rounded-full bg-slate-100 dark:bg-white/5 mb-3 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl">no_accounts</span>
                    </div>
                    <p class="text-sm font-semibold">Tidak ada kunjungan</p>
                    <p class="text-xs">untuk periode ini</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-stat-card-component title="Koleksi" value="{{ number_format($stats['total_buku']) }}" desc="Total Judul Buku"
            icon="library_books" color="blue" />

        <x-stat-card-component title="Anggota" value="{{ number_format($stats['total_anggota']) }}" desc="Anggota Terdaftar"
            icon="group" color="purple" />

        <x-stat-card-component title="Sirkulasi" value="{{ number_format($stats['peminjaman_aktif']) }}"
            desc="Peminjaman Aktif" icon="sync_alt" color="orange" />

        <x-stat-card-component title="Denda" value="Rp{{ number_format($stats['total_denda'], 0, ',', '.') }}"
            desc="Denda Belum Bayar" icon="payments" color="red" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div
            class="lg:col-span-2 bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter delay-300 shadow-sm dark:shadow-none transition-colors">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-primary-dark dark:text-white">Peminjaman Terbaru</h3>
                <a href="{{ route('peminjaman.index') }}"
                    class="text-sm font-semibold text-primary hover:text-primary-dark dark:text-accent dark:hover:text-white transition-colors flex items-center gap-1 group">
                    Lihat Semua
                    <span
                        class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-1">arrow_forward</span>
                </a>
            </div>
            <div class="flex flex-col gap-4">
                @forelse($peminjamanTerbaru as $pinjam)
                    <a href="{{ route('peminjaman.show', $pinjam->id_peminjaman) }}"
                        class="flex items-center justify-between p-4 bg-background-light dark:bg-[#261C16] rounded-xl border border-primary/10 dark:border-white/5 transition-all hover:bg-primary/10 dark:hover:bg-[#4D3A2F] hover:border-primary/20 hover:scale-[1.02] cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div
                                class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold group-hover:bg-primary group-hover:text-white dark:group-hover:bg-accent dark:group-hover:text-primary-dark transition-colors">
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
                        @php
                            $badgeClass = match ($pinjam->status_transaksi) {
                                'berjalan' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400',
                                'selesai' => 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                                'menunggu_verifikasi' => 'bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-500',
                                'ditolak',
                                'dibatalkan'
                                => 'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400',
                                default => 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-white/50',
                            };
                        @endphp
                        <span
                            class="px-3 py-1 rounded-full {{ $badgeClass }} text-[10px] font-bold uppercase tracking-wider transition-colors">
                            {{ str_replace('_', ' ', $pinjam->status_transaksi) }}
                        </span>
                    </a>
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
                <div class="grid grid-cols-1 gap-3">
                    {{-- 1. Transaksi Baru --}}
                    <a href="{{ route('peminjaman.create') }}"
                        class="w-full py-3 px-4 bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark rounded-xl font-bold text-sm hover:brightness-110 flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md transition-all hover:scale-[1.02]">
                        <span class="material-symbols-outlined">add_circle</span>
                        Transaksi Baru
                    </a>

                    {{-- 2. Tambah Anggota --}}
                    <a href="{{ route('anggota.index', ['action' => 'create']) }}"
                        class="w-full py-3 px-4 bg-background-light dark:bg-[#36271F] text-primary-dark dark:text-white border border-primary/10 dark:border-transparent rounded-xl font-bold text-sm hover:bg-primary/10 dark:hover:bg-[#4D3A2F] flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md transition-all hover:scale-[1.02]">
                        <span class="material-symbols-outlined">person_add</span>
                        Tambah Anggota
                    </a>

                    {{-- 3. Tambah Buku --}}
                    <a href="{{ route('buku.index', ['action' => 'create']) }}"
                        class="w-full py-3 px-4 bg-background-light dark:bg-[#36271F] text-primary-dark dark:text-white border border-primary/10 dark:border-transparent rounded-xl font-bold text-sm hover:bg-primary/10 dark:hover:bg-[#4D3A2F] flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md transition-all hover:scale-[1.02]">
                        <span class="material-symbols-outlined">menu_book</span>
                        Tambah Buku
                    </a>

                    {{-- 4. Lihat Pengunjung --}}
                    <a href="{{ route('pengunjung.index') }}"
                        class="w-full py-3 px-4 bg-background-light dark:bg-[#36271F] text-primary-dark dark:text-white border border-primary/10 dark:border-transparent rounded-xl font-bold text-sm hover:bg-primary/10 dark:hover:bg-[#4D3A2F] flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md transition-all hover:scale-[1.02]">
                        <span class="material-symbols-outlined">visibility</span>
                        Lihat Pengunjung
                    </a>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-primary/10 dark:border-white/5 lg:hidden">
                <!-- Mobile status removed, moved to sidebar -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initial Data from Controller
        window.initialChartData = @json($chartData);
        window.activeFilter = '{{ $filter }}';
    </script>
    @vite(['resources/js/dashboard/index.js'])
@endpush