@extends('layouts.app')

@section('title', 'Laporan & Analisis - Library App')
@section('header-title', 'Laporan & Analisis')

@section('content')
    <div class="flex flex-col gap-6">
        
        <!-- Filter Section -->
        <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm animate-enter">
            <form action="{{ route('laporan.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
                
                <!-- Report Type -->
                <div class="w-full lg:w-64">
                    <x-select name="type" label="Jenis Laporan" :selected="$type" required="true" icon="description" onchange="this.form.submit()" placeholder="Pilih Jenis Laporan..."
                        class="bg-slate-50 dark:bg-white/5 border-primary/10 dark:border-border-dark h-[42px] !py-2 shadow-sm text-sm font-bold text-slate-600 dark:text-white/80 dark:[&>option]:bg-[#1E1E1E] dark:[&>option]:text-white">
                        <option value="transaksi" {{ $type == 'transaksi' ? 'selected' : '' }}>Laporan Transaksi</option>

                        <option value="denda" {{ $type == 'denda' ? 'selected' : '' }}>Laporan Denda</option>
                        <optgroup label="Analisis Data">
                            <option value="buku_top" {{ $type == 'buku_top' ? 'selected' : '' }}>Buku Terpopuler</option>
                            <option value="anggota_top" {{ $type == 'anggota_top' ? 'selected' : '' }}>Anggota Teraktif</option>
                        </optgroup>
                    </x-select>
                </div>

                @if($type)

                <!-- Date Range (Hidden Inputs) -->
                <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">

                <!-- Periode Selector -->
                <div class="w-full lg:w-48 flex flex-col gap-1">
                    <div class="flex items-center gap-2 mb-1">
                        <label for="periode_filter" class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">
                            Periode Waktu
                        </label>
                        <!-- Custom Date Edit Icon -->
                        <div id="custom_date_trigger" class="hidden text-primary cursor-pointer hover:text-primary-dark transition-colors" title="Ubah Tanggal">
                            <span class="material-symbols-outlined text-[16px]">edit_calendar</span>
                        </div>
                    </div>
                    
                    <!-- Improved Time Filter -->
                    <div class="min-w-[150px] w-full lg:w-48">
                        <x-select id="periode_filter" name="periode" icon="calendar_today" placeholder=""
                            class="bg-slate-50 dark:bg-white/5 border-primary/10 dark:border-border-dark h-[42px] !py-2 shadow-sm text-sm font-bold text-slate-600 dark:text-white/80 dark:[&>option]:bg-[#1E1E1E] dark:[&>option]:text-white">
                            <option value="all" selected>Semua</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="custom" id="option_custom">Lainnya...</option>
                        </x-select>
                    </div>
                </div>

                <!-- Extra Filter based on Type -->
                @if($type == 'transaksi')
                    <div class="w-full lg:w-48">
                        <x-select name="status" label="Status Transaksi" placeholder="Semua" :selected="request('status')" icon="check_circle" onchange="this.form.submit()"
                            class="bg-slate-50 dark:bg-white/5 border-primary/10 dark:border-border-dark h-[42px] !py-2 shadow-sm text-sm font-bold text-slate-600 dark:text-white/80 dark:[&>option]:bg-[#1E1E1E] dark:[&>option]:text-white">
                            <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </x-select>
                    </div>
                @endif

                @if($type == 'denda')
                    <div class="w-full lg:w-48">
                        <x-select name="status_bayar" label="Status Pembayaran" placeholder="Semua" :selected="request('status_bayar')" icon="payments" onchange="this.form.submit()"
                            class="bg-slate-50 dark:bg-white/5 border-primary/10 dark:border-border-dark h-[42px] !py-2 shadow-sm text-sm font-bold text-slate-600 dark:text-white/80 dark:[&>option]:bg-[#1E1E1E] dark:[&>option]:text-white">
                            <option value="belum_bayar" {{ request('status_bayar') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="lunas" {{ request('status_bayar') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        </x-select>
                    </div>
                @endif

                <button type="button" onclick="window.print()"
                    class="w-full lg:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-accent dark:text-primary-dark rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-sm duration-200 h-[42px]">
                    <span class="material-symbols-outlined text-lg">print</span>
                    Cetak Laporan
                </button>
                @endif
            </form>
        </div>

        @if($type)
            <!-- Charts Section -->
            <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm animate-enter delay-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">bar_chart</span>
                        Visualisasi Data
                    </h3>
                </div>
                <div class="relative h-80 w-full group">
                    <canvas id="mainChart" class="transition-opacity duration-300"></canvas>
                    
                    <!-- Empty State -->
                    <div id="chartEmptyState" class="hidden absolute inset-0 flex-col items-center justify-center text-slate-400 dark:text-white/40">
                        <div class="p-4 rounded-full bg-slate-100 dark:bg-white/5 mb-3 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl">manage_search</span>
                        </div>
                        <p class="text-base font-semibold">Tidak ada data untuk ditampilkan</p>
                        <p class="text-sm">Silakan pilih filter lain</p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-enter delay-200">
                @if($type == 'transaksi')
                    <div id="stat_transaksi_total"><x-stat-card-component title="Total Transaksi" :value="$stats['total_transaksi']" icon="receipt_long" color="blue" desc="Semua transaksi tercatat" /></div>
                    <div id="stat_transaksi_buku"><x-stat-card-component title="Buku Keluar" :value="$stats['total_buku']" icon="menu_book" color="emerald" desc="Total buku dipinjam" /></div>
                    <div id="stat_transaksi_berjalan"><x-stat-card-component title="Sedang Berjalan" :value="$stats['berjalan']" icon="pending" color="orange" desc="Belum dikembalikan" /></div>
                    <div id="stat_transaksi_selesai"><x-stat-card-component title="Selesai" :value="$stats['selesai']" icon="check_circle" color="indigo" desc="Sudah dikembalikan" /></div>
                @elseif($type == 'denda')
                    <div id="stat_denda_total"><x-stat-card-component title="Total Denda" value="Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}" icon="payments" color="rose" desc="Akumulasi denda" /></div>
                    <div id="stat_denda_dibayar"><x-stat-card-component title="Sudah Dibayar" value="Rp {{ number_format($stats['dibayar'], 0, ',', '.') }}" icon="check_circle" color="emerald" desc="Denda lunas" /></div>
                    <div id="stat_denda_belum"><x-stat-card-component title="Belum Dibayar" value="Rp {{ number_format($stats['belum_bayar'], 0, ',', '.') }}" icon="warning" color="orange" desc="Tunggakan denda" /></div>
                @elseif($type == 'buku_top')
                    <div class="col-span-2" id="stat_buku_top_1">
                        <x-stat-card-component title="Top 1 Buku" :value="$stats['top_1_judul']" icon="emoji_events" color="yellow" desc="Paling diminati" />
                    </div>
                    <div id="stat_buku_total"><x-stat-card-component title="Total Dipinjam " :value="$stats['top_1_total']" icon="repeat" color="blue" desc="Kali dipinjam" /></div>
                    <div id="stat_buku_unik"><x-stat-card-component title="Total Buku Unik" :value="$stats['total_buku_unik_dipinjam']" icon="library_books" color="purple" desc="Judul berbeda" /></div>
                @elseif($type == 'anggota_top')
                    <div class="col-span-2" id="stat_anggota_top_1">
                        <x-stat-card-component title="Top 1 Anggota" :value="$stats['top_1_nama']" icon="emoji_events" color="yellow" desc="Peminjam teraktif" />
                    </div>
                    <div id="stat_anggota_total"><x-stat-card-component title="Total Transaksi" :value="$stats['top_1_total']" icon="history" color="blue" desc="Kali meminjam" /></div>
                    <div id="stat_anggota_aktif"><x-stat-card-component title="Anggota Aktif" :value="$stats['total_anggota_aktif']" icon="group" color="purple" desc="Meminjam periode ini" /></div>
                @endif
            </div>

            <!-- Detailed Table -->
            <div class="animate-enter delay-300">
                @if($type == 'transaksi')
                    @include('admin.laporan.partials.table-transaksi', ['data' => $data])
                @elseif($type == 'denda')
                    @include('admin.laporan.partials.table-denda', ['data' => $data])
                @elseif($type == 'buku_top')
                    @include('admin.laporan.partials.table-buku-top', ['data' => $data])
                @elseif($type == 'anggota_top')
                    @include('admin.laporan.partials.table-anggota-top', ['data' => $data])
                @endif
            </div>
        @else
            <!-- EMPTY STATE UI -->
            <div class="flex flex-col items-center justify-center min-h-[400px] text-center animate-enter">
                <div class="p-6 rounded-full bg-slate-100 dark:bg-white/5 mb-6">
                     <span class="material-symbols-outlined text-[64px] text-slate-400 dark:text-white/20">assignment</span>
                </div>
                <h3 class="text-xl font-bold text-primary-dark dark:text-white mb-2">Pilih Jenis Laporan</h3>
                <p class="text-slate-500 dark:text-white/60 max-w-md">
                     Silakan pilih jenis laporan yang ingin ditampilkan dari dropdown di atas untuk melihat data statistik.
                </p>
            </div>
        @endif
    </div>

    <!-- Chart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pass data to external JS
            window.laporanChartData = @json($chartData);
            window.laporanType = '{{ $type }}';
        });
    </script>
    @push('scripts')
        @vite(['resources/js/laporan/index.js'])
    @endpush
    <!-- Custom Date Modal -->
    <x-modal id="customDateModal" title="Pilih Rentang Tanggal" maxWidth="md">
        <x-slot:title_icon>
            <span class="material-symbols-outlined text-primary">date_range</span>
        </x-slot:title_icon>

        <div class="flex flex-col gap-4">
            <x-input type="date" id="modal_start_date" name="modal_start_date" label="Dari Tanggal" />
            <x-input type="date" id="modal_end_date" name="modal_end_date" label="Sampai Tanggal" />
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
            <button type="button" onclick="closeModal('customDateModal'); resetPeriodeSelector();"
                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">
                Batal
            </button>
            <button type="button" id="applyCustomDate"
                class="px-4 py-2 rounded-lg bg-primary dark:bg-accent text-white dark:text-primary-dark text-sm font-bold transition-all shadow-sm hover:brightness-110 active:scale-95 duration-200">
                Terapkan
            </button>
        </div>
    </x-modal>

@endsection
