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
                <!-- Table Container replaced with x-datatable -->
                <x-datatable :data="$denda" search-placeholder="Cari nama atau buku..." search-id="searchDendaInput"
                    :search-value="request('search')">
                    <x-slot:header>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_denda', 'direction' => request('direction') == 'desc' ? 'asc' : 'desc']) }}'">
                            <div class="flex items-center gap-1">
                                Tanggal
                                @if(request('sort') == 'tanggal_denda')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium">Kode</th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_anggota', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Peminjam
                                @if(request('sort') == 'nama_anggota')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium">Buku</th>
                        <th class="p-4 font-medium">Jenis Denda</th>
                        <th class="p-4 font-medium text-right">Nominal</th>
                        <th class="p-4 font-medium text-center">Status</th>
                        <th class="p-4 pr-6 font-medium text-center">Aksi</th>
                    </x-slot:header>

                    <x-slot:body>
                        @forelse($denda as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                                <td class="p-4 text-left font-mono text-xs text-slate-500 dark:text-white/50">
                                    {{ \Carbon\Carbon::parse($item->tanggal_denda)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-4 font-mono font-bold text-primary dark:text-accent">
                                    {{ $item->id_peminjaman }}
                                </td>
                                <td class="p-4 max-w-[200px] truncate" title="{{ $item->nama_anggota }}">
                                    <span class="font-bold text-slate-800 dark:text-white">{{ $item->nama_anggota }}</span>
                                </td>
                                <td class="p-4 max-w-[250px] truncate text-slate-600 dark:text-white/70"
                                    title="{{ $item->judul_buku }}">
                                    {{ $item->judul_buku }}
                                </td>
                                <td class="p-4 text-xs font-bold uppercase text-slate-500 dark:text-white/50">
                                    {{ $item->jenis_denda }}
                                </td>
                                <td class="p-4 font-mono font-bold text-slate-800 dark:text-white text-right">
                                    Rp {{ number_format($item->jumlah_denda, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-center">
                                    @php
                                        $badgeClass = match ($item->status_bayar) {
                                            'lunas' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                                            'belum_bayar' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                                            'sebagian' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                        $statusLabel = str_replace('_', ' ', $item->status_bayar);
                                    @endphp
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    @if($item->status_bayar === 'belum_bayar')
                                        <form action="{{ route('laporan.denda.bayar', $item->id_denda) }}" method="POST"
                                            onsubmit="return confirm('Konfirmasi pembayaran denda ini?');">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition flex items-center justify-center gap-1 shadow-sm shadow-emerald-200 dark:shadow-none w-full">
                                                <span class="material-symbols-outlined text-[16px]">payments</span>
                                                Bayar
                                            </button>
                                        </form>
                                    @else
                                        <span
                                            class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center justify-center gap-1 font-bold">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>Lunas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-12 text-center text-slate-400 dark:text-white/40">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-4xl opacity-50">search_off</span>
                                        <span>Tidak ada data denda ditemukan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-slot:body>
                </x-datatable>
            </div>
        </main>
    </div>
</body>

</html>