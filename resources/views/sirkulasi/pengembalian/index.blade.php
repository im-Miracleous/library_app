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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
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
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-white/60">
                        <span class="material-symbols-outlined text-base">home</span>
                        <span>/</span>
                        <span>Sirkulasi</span>
                        <span>/</span>
                        <span class="font-bold text-primary dark:text-white">Pengembalian</span>
                    </div>
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark overflow-hidden animate-enter delay-100 shadow-sm">
                    <div
                        class="p-4 border-b border-primary/20 dark:border-[#36271F] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface dark:bg-[#1A1410]">
                        <div class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400">assignment_return</span>
                            Peminjaman Aktif (Berjalan)
                        </div>
                        <form method="GET" action="{{ route('pengembalian.index') }}" class="relative w-full sm:w-64">
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 dark:text-white/40 text-lg">search</span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Kode atau Peminjam..."
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg pl-10 pr-4 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-all placeholder-primary-mid/60 dark:placeholder-white/40">
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr
                                    class="border-b border-primary/20 dark:border-border-dark text-slate-500 dark:text-white/40 text-xs uppercase tracking-wider bg-surface dark:bg-[#1A1410]">
                                    <th class="p-4 pl-6 font-medium w-32">Kode</th>
                                    <th class="p-4 font-medium">Peminjam</th>
                                    <th class="p-4 font-medium">Tgl Pinjam</th>
                                    <th class="p-4 font-medium">Jatuh Tempo</th>
                                    <th class="p-4 font-medium text-center">Sisa Waktu</th>
                                    <th class="p-4 font-medium text-right pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-[#36271F] text-sm text-slate-600 dark:text-white/80">
                                @forelse($peminjaman as $item)
                                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                                        <td class="p-4 pl-6 font-mono font-bold text-primary dark:text-accent">
                                            {{ $item->kode_peminjaman }}
                                        </td>
                                        <td class="p-4">
                                            <div class="font-bold text-slate-800 dark:text-white">
                                                {{ $item->pengguna->nama ?? 'Unknown' }}</div>
                                            <div class="text-xs text-slate-500">{{ $item->pengguna->email ?? '-' }}</div>
                                        </td>
                                        <td class="p-4 text-slate-500 dark:text-white/60">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="p-4">
                                            {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="p-4 text-center">
                                            @php
                                                $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo);
                                                $daysLeft = now()->diffInDays($jatuhTempo, false);
                                                $isLate = $daysLeft < 0;
                                            @endphp
                                            @if($isLate)
                                                <span
                                                    class="px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-xs font-bold">
                                                    Telat {{ abs(intval($daysLeft)) }} Hari
                                                </span>
                                            @else
                                                <span
                                                    class="px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-xs font-bold">
                                                    {{ intval($daysLeft) }} Hari Lagi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-right pr-6">
                                            <a href="{{ route('pengembalian.show', $item->id_peminjaman) }}"
                                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-surface dark:bg-accent text-primary-dark hover:bg-amber-300 dark:hover:bg-amber-500 rounded-lg text-xs font-bold transition-all shadow-sm hover:translate-y-[-1px]">
                                                <span class="material-symbols-outlined text-base">keyboard_return</span>
                                                Proses
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
                                            Tidak ada peminjaman aktif saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-200 dark:border-border-dark">
                        {{ $peminjaman->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>