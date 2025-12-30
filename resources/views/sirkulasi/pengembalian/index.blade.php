<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengembalian & Denda - Library App</title>
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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/live-search-sirkulasi-pengembalian.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Pengembalian & Denda" />

            <div class="p-4 sm:p-8">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <x-breadcrumb-component parent="Sirkulasi" current="Pengembalian" />
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Table Container replaced with x-datatable -->
                <x-datatable :data="$peminjaman" search-placeholder="Cari Kode atau Peminjam..." search-id="returnSearchInput" :search-value="request('search')">
                    <x-slot:header>
                        <th class="p-4 pl-6 font-medium w-44 cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_peminjaman', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Kode
                                @if(request('sort') == 'id_peminjaman')
                                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium w-72 cursor-pointer hover:text-primary transition-colors select-none"
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
                        <th class="p-4 font-medium w-40 cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_pinjam', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Tgl Pinjam
                                @if(request('sort') == 'tanggal_pinjam')
                                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium w-40 cursor-pointer hover:text-primary transition-colors select-none"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_jatuh_tempo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Jatuh Tempo
                                @if(request('sort') == 'tanggal_jatuh_tempo')
                                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium text-center w-40">Sisa Waktu</th>
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
                                <td class="p-4 text-slate-600 dark:text-white/70">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-4 text-slate-600 dark:text-white/70">
                                    {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-4 text-center">
                                    @php
                                        $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();
                                        $diffDays = now()->startOfDay()->diffInDays($jatuhTempo, false);
                                    @endphp
                                    
                                    @if($diffDays < 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 border-red-200 dark:border-red-500/30 animate-pulse">
                                            <span class="material-symbols-outlined text-sm">warning</span>
                                            Telat {{ abs($diffDays) }} hari
                                        </span>
                                    @elseif($diffDays == 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 border-amber-200 dark:border-amber-500/30">
                                            <span class="material-symbols-outlined text-sm">event</span>
                                            Hari Ini
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 border-blue-200 dark:border-blue-500/30">
                                            <span class="material-symbols-outlined text-sm">schedule</span>
                                            {{ $diffDays }} hari lagi
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right pr-6">
                                    <a href="{{ route('pengembalian.show', $item->id_peminjaman) }}"
                                        class="px-3 py-1 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary-dark shadow-md shadow-primary/30 transition-all inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">sync_alt</span>
                                        Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-4xl opacity-50">data_loss_prevention</span>
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