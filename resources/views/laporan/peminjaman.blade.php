<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Peminjaman - Library App</title>
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

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Laporan Peminjaman" />

            <div class="p-4 sm:p-8">
                <!-- Header & Breadcrumb -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-white/60">
                        <span class="material-symbols-outlined text-base">home</span>
                        <span>/</span>
                        <span>Laporan</span>
                        <span>/</span>
                        <span class="font-bold text-primary dark:text-white">Peminjaman</span>
                    </div>
                </div>

                <!-- Filters -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm mb-6 animate-enter delay-100">
                    <form action="{{ route('laporan.peminjaman') }}" method="GET"
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
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Status</label>
                            <select name="status"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                                <option value="">Semua</option>
                                <option value="berjalan" {{ $status == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                                <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 animate-enter delay-200">
                    <div class="bg-blue-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">receipt_long</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-1">Total Transaksi
                            </div>
                            <div class="text-3xl font-bold">{{ number_format($totalTransaksi) }}</div>
                        </div>
                    </div>
                    <div class="bg-emerald-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">menu_book</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-emerald-100 text-sm font-bold uppercase tracking-wider mb-1">Total Buku
                                Keluar</div>
                            <div class="text-3xl font-bold">{{ number_format($totalBukuDipinjam) }}</div>
                        </div>
                    </div>
                    <div class="bg-orange-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">pending</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-orange-100 text-sm font-bold uppercase tracking-wider mb-1">Sedang Berjalan
                            </div>
                            <div class="text-3xl font-bold">{{ number_format($transaksiBerjalan) }}</div>
                        </div>
                    </div>
                    <div class="bg-indigo-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">check_circle</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-1">Selesai</div>
                            <div class="text-3xl font-bold">{{ number_format($transaksiSelesai) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark overflow-hidden shadow-sm animate-enter delay-300">
                    <div
                        class="p-4 border-b border-primary/20 dark:border-white/10 flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="font-bold text-slate-800 dark:text-white">Detail Data</h3>
                        <div class="text-xs text-slate-500 dark:text-white/60">
                            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-medium">No</th>
                                    <th class="p-4 font-medium">Tanggal</th>
                                    <th class="p-4 font-medium">Kode</th>
                                    <th class="p-4 font-medium">Peminjam</th>
                                    <th class="p-4 font-medium">Jml Buku</th>
                                    <th class="p-4 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-white/10 text-sm text-slate-600 dark:text-white/80">
                                @forelse ($peminjaman as $index => $item)
                                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors">
                                        <td class="p-4 pl-6">{{ $index + 1 }}</td>
                                        <td class="p-4">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                        </td>
                                        <td class="p-4 font-mono text-primary dark:text-accent font-bold">
                                            {{ $item->kode_peminjaman }}</td>
                                        <td class="p-4">
                                            <div class="font-bold">{{ $item->pengguna->nama }}</div>
                                            <div class="text-xs text-slate-400">{{ $item->pengguna->email }}</div>
                                        </td>
                                        <td class="p-4">{{ $item->details->count() }}</td>
                                        <td class="p-4">
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ $item->status_transaksi == 'berjalan' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' }}">
                                                {{ $item->status_transaksi }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 dark:text-white/40 italic">
                                            Tidak ada data transaksi pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>

</html>