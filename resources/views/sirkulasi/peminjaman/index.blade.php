<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transaksi Peminjaman - Library App</title>
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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/live-search-sirkulasi-peminjaman.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Transaksi Peminjaman" />

            <div class="p-4 sm:p-8">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <x-breadcrumb-component parent="Sirkulasi" current="Peminjaman" />

                    <a href="{{ route('peminjaman.create') }}"
                        class="flex items-center gap-2 px-4 py-2.5 bg-surface dark:bg-accent text-primary-dark hover:bg-amber-300 dark:hover:bg-amber-500 rounded-xl font-bold text-sm transition-all shadow-sm hover:translate-y-[-2px]">
                        <span class="material-symbols-outlined text-lg">add_circle</span>
                        Transaksi Baru
                    </a>
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Table Container replaced with x-datatable -->
                <x-datatable :data="$peminjaman" search-placeholder="Cari Kode atau Peminjam..." search-id="searchInput" :search-value="request('search')">
                    <x-slot:filters>
                        <select name="status" id="statusFilter"
                            class="bg-white dark:bg-[#120C0A] border border-primary/20 dark:border-primary/20 rounded-lg px-3 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer h-full shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </x-slot:filters>

                    <x-slot:header>
                        <th class="p-4 pl-6 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_peminjaman', 'direction' => request('direction') == 'desc' ? 'asc' : 'desc']) }}'">
                            <div class="flex items-center gap-1">
                                Kode
                                @if(request('sort') == 'id_peminjaman')
                                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_anggota', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Peminjam
                                @if(request('sort') == 'nama_anggota')
                                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium text-center">Buku</th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_pinjam', 'direction' => request('direction') == 'desc' ? 'asc' : 'desc']) }}'">
                            <div class="flex items-center gap-1">
                                Tgl Pinjam
                                @if(request('sort') == 'tanggal_pinjam')
                                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium">Jatuh Tempo</th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'status_transaksi', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Status
                                @if(request('sort') == 'status_transaksi')
                                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium text-right pr-6">Aksi</th>
                    </x-slot:header>

                    <x-slot:body>
                        @forelse($peminjaman as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                                <td class="p-4 pl-6 font-mono font-bold text-primary dark:text-accent whitespace-nowrap">
                                    {{ $item->id_peminjaman }}
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 dark:text-white">{{ $item->nama_anggota }}</span>
                                        <span class="text-xs text-slate-500 dark:text-white/50">{{ $item->email_anggota ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-center font-bold text-slate-700 dark:text-white">{{ $item->total_buku }}</td>
                                <td class="p-4 text-slate-600 dark:text-white/70">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-4">
                                    @php
                                        $tglJatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo);
                                        $isLate = $tglJatuhTempo->startOfDay()->lt(now()->startOfDay()) && $item->status_transaksi == 'berjalan';
                                    @endphp
                                    <span class="{{ $isLate ? 'text-red-600 font-bold animate-pulse' : 'text-slate-600 dark:text-white/70' }}">
                                        {{ $tglJatuhTempo->translatedFormat('d M Y') }}
                                        @if($isLate)
                                            <span class="ml-2 text-[10px] bg-red-100 text-red-600 px-1 rounded uppercase">Telat</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="p-4">
                                    @php
                                        $badgeClass = match($item->status_transaksi) {
                                            'berjalan' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                                            'selesai' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
                                            'terlambat' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                        {{ $item->status_transaksi }}
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6">
                                    <a href="{{ route('peminjaman.show', $item->id_peminjaman) }}"
                                        class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 dark:hover:bg-white/10 transition-colors inline-block"
                                        title="Lihat Detail">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center text-slate-400 dark:text-white/40">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-4xl opacity-50">event_busy</span>
                                        <span>Tidak ada peminjaman yang sedang berjalan.</span>
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