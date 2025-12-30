<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Peminjaman - Library App</title>
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
            <x-header-component title="Detail Peminjaman" />

            <div class="p-4 sm:p-8">
                <!-- Breadcrumbs -->
                <x-breadcrumb-component parent="Sirkulasi" middle="Peminjaman" :middleLink="route('peminjaman.index')"
                    current="Detail" class="mb-6 animate-enter" />

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-enter delay-100">

                    <!-- Left Column: Transaction Details -->
                    <div class="lg:col-span-2 flex flex-col gap-6">
                        <div
                            class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-6">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                    <div
                                        class="size-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                        <span
                                            class="material-symbols-outlined text-blue-600 dark:text-blue-400">receipt_long</span>
                                    </div>
                                    Informasi Transaksi
                                </h3>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $peminjaman->status_transaksi == 'berjalan' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' }}">
                                    {{ $peminjaman->status_transaksi }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                    <div
                                        class="text-xs text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1">
                                        Kode Transaksi</div>
                                    <div class="font-mono font-bold text-lg text-primary dark:text-accent">
                                        {{ $peminjaman->id_peminjaman }}
                                    </div>
                                </div>
                                <div
                                    class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                    <div
                                        class="text-xs text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1">
                                        Peminjam</div>
                                    <div class="font-bold text-slate-800 dark:text-white">
                                        {{ $peminjaman->pengguna->nama }}
                                    </div>
                                    <div class="text-xs text-slate-500">{{ $peminjaman->pengguna->email }}</div>
                                </div>
                                <div
                                    class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                    <div
                                        class="text-xs text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1">
                                        Tanggal Pinjam</div>
                                    <div class="font-bold text-slate-800 dark:text-white">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                                <div
                                    class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                    <div
                                        class="text-xs text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1">
                                        Jatuh Tempo</div>
                                    @php
                                        $isOverdue = \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)) && $peminjaman->status_transaksi == 'berjalan';
                                    @endphp
                                    <div
                                        class="font-bold {{ $isOverdue ? 'text-red-600 dark:text-red-400 animate-pulse' : 'text-slate-800 dark:text-white' }} flex items-center gap-2">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}
                                        @if($isOverdue)
                                            <span
                                                class="text-[10px] uppercase bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 px-2 py-0.5 rounded-full border border-red-200 dark:border-red-500/30 animate-none">Terlambat</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($peminjaman->keterangan)
                                <div
                                    class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-500/10 rounded-xl border border-yellow-100 dark:border-white/5">
                                    <div
                                        class="text-xs text-yellow-700 dark:text-yellow-500 uppercase tracking-wider mb-1 font-bold">
                                        Keterangan</div>
                                    <div class="text-sm text-slate-700 dark:text-white/80">{{ $peminjaman->keterangan }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div
                            class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                <div
                                    class="size-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">menu_book</span>
                                </div>
                                Daftar Buku
                            </h3>

                            <div class="overflow-x-auto border border-slate-200 dark:border-border-dark rounded-xl">
                                <table class="w-full text-left text-sm">
                                    <thead
                                        class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 uppercase text-xs">
                                        <tr>
                                            <th class="p-3 pl-4">Judul Buku</th>
                                            <th class="p-3">Status</th>
                                            <th class="p-3 text-right pr-4">Tgl Kembali</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-slate-100 dark:divide-white/10 text-slate-600 dark:text-white/80">
                                        @foreach($peminjaman->details as $detail)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                                <td class="p-3 pl-4">
                                                    <div class="font-bold text-slate-800 dark:text-white">
                                                        {{ $detail->buku->judul }}
                                                    </div>
                                                    <div class="text-xs text-slate-500">{{ $detail->buku->penulis }}</div>
                                                </td>
                                                <td class="p-3">
                                                    @if($detail->status_buku == 'dipinjam')
                                                        <span
                                                            class="bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-xs px-2 py-0.5 rounded-full font-bold uppercase">Dipinjam</span>
                                                    @elseif($detail->status_buku == 'dikembalikan')
                                                        <span
                                                            class="bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 text-xs px-2 py-0.5 rounded-full font-bold uppercase">Dikembalikan</span>
                                                    @else
                                                        <span
                                                            class="bg-gray-100 dark:bg-white/10 text-slate-600 dark:text-white/60 text-xs px-2 py-0.5 rounded-full font-bold uppercase">{{ $detail->status_buku }}</span>
                                                    @endif
                                                </td>
                                                <td class="p-3 text-right pr-4 font-mono text-xs">
                                                    @if($detail->tanggal_kembali_aktual)
                                                        @php
                                                            $tglKembali = \Carbon\Carbon::parse($detail->tanggal_kembali_aktual);
                                                            $jatuhTempo = \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo);
                                                            $isLateReturn = $tglKembali->startOfDay()->gt($jatuhTempo->startOfDay());
                                                        @endphp
                                                        <span
                                                            class="{{ $isLateReturn ? 'text-red-600 dark:text-red-400 font-bold' : '' }}">
                                                            {{ $tglKembali->format('d/m/Y') }}
                                                        </span>
                                                        @if($isLateReturn)
                                                            <div class="text-[10px] text-red-500 dark:text-red-400/80">Terlambat</div>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Actions -->
                    <div class="flex flex-col gap-6">
                        <div
                            class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm sticky top-6">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Aksi</h3>

                            @if($peminjaman->status_transaksi == 'berjalan')
                                <a href="{{ route('pengembalian.show', $peminjaman->id_peminjaman) }}"
                                    class="w-full py-3.5 bg-primary text-white dark:bg-accent dark:text-primary-dark rounded-xl font-bold shadow-lg hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 mb-3">
                                    <span class="material-symbols-outlined">assignment_return</span>
                                    Proses Pengembalian
                                </a>

                                <a href="{{ route('peminjaman.edit', $peminjaman->id_peminjaman) }}"
                                    class="w-full py-3 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-500/30 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-2 mb-3">
                                    <span class="material-symbols-outlined">edit_square</span>
                                    Edit Transaksi
                                </a>
                            @endif

                            <form action="{{ route('peminjaman.destroy', $peminjaman->id_peminjaman) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat transaksi ini? Data tidak dapat dikembalikan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-3 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl font-bold transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">delete</span>
                                    Hapus Transaksi
                                </button>
                            </form>

                            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-white/10">
                                <div class="text-xs text-slate-400 dark:text-white/40 text-center">
                                    Dibuat: {{ $peminjaman->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>

</html>