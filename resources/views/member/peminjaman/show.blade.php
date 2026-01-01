<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Peminjaman - Library App</title>
    <link rel="icon" type="image/png"
        href="{{ !empty($pengaturan->logo_path) ? asset('storage/' . $pengaturan->logo_path) : 'https://laravel.com/img/favicon/favicon-32x32.png' }}">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full animate-enter">
            <x-header-component title="Detail Peminjaman" />

            <div class="p-4 sm:p-8 max-w-4xl mx-auto w-full">

                <div
                    class="bg-white dark:bg-surface-dark rounded-3xl shadow-sm border border-primary/10 dark:border-white/5 overflow-hidden p-6 sm:p-8">

                    <!-- Header Status -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 border-b border-primary/5 dark:border-white/5 pb-8">
                        <div>
                            <p
                                class="text-xs text-primary-mid dark:text-white/60 uppercase font-bold tracking-widest mb-1">
                                Kode Transaksi</p>
                            <h2 class="text-2xl font-bold text-primary-dark dark:text-white">
                                {{ $peminjaman->id_peminjaman }}
                            </h2>
                        </div>
                        <div>
                            @if($peminjaman->status_transaksi == 'menunggu_verifikasi')
                                <span
                                    class="px-4 py-2 rounded-xl bg-orange-100 text-orange-700 font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined">pending</span>
                                    Menunggu Verifikasi
                                </span>
                            @elseif($peminjaman->status_transaksi == 'berjalan')
                                <span
                                    class="px-4 py-2 rounded-xl bg-blue-100 text-blue-700 font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined">auto_stories</span>
                                    Sedang Berjalan
                                </span>
                            @elseif($peminjaman->status_transaksi == 'selesai')
                                <span
                                    class="px-4 py-2 rounded-xl bg-green-100 text-green-700 font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Selesai
                                </span>
                            @elseif($peminjaman->status_transaksi == 'ditolak')
                                <span
                                    class="px-4 py-2 rounded-xl bg-red-100 text-red-700 font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined">cancel</span>
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Peminjaman -->
                    <div class="mb-8 border-b border-primary/5 dark:border-white/5 pb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-primary">calendar_month</span>
                            <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Peminjaman</h2>
                        </div>

                        @if($peminjaman->status_transaksi == 'berjalan' && \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->isPast())
                            <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20 flex items-start gap-3 animate-pulse">
                                <span class="material-symbols-outlined text-red-500">warning</span>
                                <div>
                                    <p class="text-sm font-bold text-red-800 dark:text-red-400">Peminjaman Melewati Batas Waktu!</p>
                                    <p class="text-xs text-red-600 dark:text-red-400/70">Harap segera mengembalikan buku ke perpustakaan untuk menghindari akumulasi denda keterlambatan.</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-primary/5 dark:border-white/5">
                                <p class="text-xs text-primary-mid dark:text-white/40 uppercase font-bold tracking-widest mb-1">Tanggal Pinjam</p>
                                <p class="font-bold text-lg text-primary-dark dark:text-white">
                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-primary/5 dark:border-white/5">
                                <p class="text-xs text-primary-mid dark:text-white/40 uppercase font-bold tracking-widest mb-1">Jatuh Tempo</p>
                                <p class="font-bold text-lg {{ $peminjaman->status_transaksi == 'berjalan' && \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->isPast() ? 'text-red-600 dark:text-red-400' : 'text-primary-dark dark:text-white' }}">
                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pustaka -->
                    <div class="mb-8 border-b border-primary/5 dark:border-white/5 pb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-primary">library_books</span>
                            <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Pustaka</h2>
                        </div>

                        <div
                            class="bg-slate-50 dark:bg-white/5 rounded-xl border border-primary/5 dark:border-white/5 overflow-hidden">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-primary/5 dark:bg-white/5 text-primary-dark dark:text-white font-bold">
                                    <tr>
                                        <th class="p-4">Judul Buku</th>
                                        <th class="p-4">Penulis</th>
                                        <th class="p-4 text-center">Jumlah</th>
                                        <th class="p-4 text-center">Kondisi</th>
                                        @if($peminjaman->status_transaksi == 'selesai')
                                            <th class="p-4 text-center">Status</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-primary/5 dark:divide-white/5">
                                    @foreach($peminjaman->details as $detail)
                                        <tr class="hover:bg-white dark:hover:bg-white/5 transition-colors">
                                            <td class="p-4 font-bold text-primary-dark dark:text-white">
                                                {{ $detail->buku->judul }}
                                            </td>
                                            <td class="p-4 text-primary-mid dark:text-white/60">{{ $detail->buku->penulis }}
                                            </td>
                                            <td class="p-4 text-center font-bold">{{ $detail->jumlah }}</td>
                                            <td class="p-4">
                                                @php
                                                    $conditionDenda = $detail->denda->whereIn('jenis_denda', ['rusak', 'hilang'])->first();
                                                    $condition = $conditionDenda ? $conditionDenda->jenis_denda : ($detail->status_buku == 'dikembalikan' ? 'baik' : null);
                                                @endphp

                                                <div class="flex justify-center">
                                                    @if($condition == 'baik')
                                                        <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold text-xs uppercase">
                                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                                            Baik
                                                        </div>
                                                    @elseif($condition == 'rusak')
                                                        <div class="flex items-center gap-1.5 text-orange-600 dark:text-orange-400 font-bold text-xs uppercase">
                                                            <span class="material-symbols-outlined text-sm">warning</span>
                                                            Rusak
                                                        </div>
                                                    @elseif($condition == 'hilang')
                                                        <div class="flex items-center gap-1.5 text-red-600 dark:text-red-400 font-bold text-xs uppercase">
                                                            <span class="material-symbols-outlined text-sm">dangerous</span>
                                                            Hilang
                                                        </div>
                                                    @else
                                                        <span class="text-slate-400 dark:text-white/20">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            @if($peminjaman->status_transaksi == 'selesai')
                                                <td class="p-4 text-center">
                                                    <span class="text-green-600 dark:text-green-400 font-bold">Dikembalikan</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi Peminjam -->
                    <div class="mb-8 border-b border-primary/5 dark:border-white/5 pb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-primary">person</span>
                            <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Peminjam</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-primary/5 dark:border-white/5">
                                <p
                                    class="text-xs text-primary-mid dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                                    Nama Peminjam</p>
                                <p class="font-bold text-lg text-primary-dark dark:text-white">
                                    {{ Auth::user()->nama }}
                                </p>
                            </div>
                            <div
                                class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-primary/5 dark:border-white/5">
                                <p
                                    class="text-xs text-primary-mid dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                                    Email</p>
                                <p class="font-bold text-lg text-primary-dark dark:text-white">{{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Denda -->
                    @php
                        $allFines = $peminjaman->details->flatMap->denda;
                    @endphp

                    @if($allFines->count() > 0)
                        @php
                            $isAllPaid = $allFines->every(fn($f) => $f->status_bayar == 'lunas');
                        @endphp
                        <div class="mb-8 border-b border-primary/5 dark:border-white/5 pb-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-red-500">payments</span>
                                <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Denda</h2>
                            </div>

                            <div class="bg-red-50/50 dark:bg-red-500/5 rounded-2xl border border-red-100 dark:border-red-500/20 overflow-hidden">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-red-100/50 dark:bg-red-500/10 text-red-800 dark:text-red-300 font-bold">
                                        <tr>
                                            <th class="p-4">Jenis Denda</th>
                                            <th class="p-4">Buku</th>
                                            <th class="p-4 text-right">Jumlah</th>
                                            <th class="p-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-red-100 dark:divide-red-500/10">
                                        @foreach($allFines as $fine)
                                            <tr class="hover:bg-red-100/20 dark:hover:bg-red-500/5 transition-colors">
                                                <td class="p-4 font-bold text-red-900 dark:text-red-200 uppercase text-xs">
                                                    {{ str_replace('_', ' ', $fine->jenis_denda) }}
                                                </td>
                                                <td class="p-4 text-slate-600 dark:text-red-300/70">
                                                    {{ $fine->detail->buku->judul ?? '-' }}
                                                </td>
                                                <td class="p-4 text-right font-bold text-red-700 dark:text-red-400">
                                                    Rp {{ number_format($fine->jumlah_denda, 0, ',', '.') }}
                                                </td>
                                                <td class="p-4 text-center">
                                                    @if($fine->status_bayar == 'lunas')
                                                        <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] font-bold uppercase">Lunas</span>
                                                    @else
                                                        <span class="px-2 py-1 rounded-lg bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-[10px] font-bold uppercase">Belum Bayar</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-red-100/30 dark:bg-red-500/10 font-bold">
                                        <tr>
                                            <td colspan="2" class="p-4 text-red-800 dark:text-red-300">Total Denda</td>
                                            <td class="p-4 text-right text-red-900 dark:text-red-200">
                                                Rp {{ number_format($allFines->sum('jumlah_denda'), 0, ',', '.') }}
                                            </td>
                                            <td class="p-4 text-center">
                                                @if($isAllPaid)
                                                    <span class="text-emerald-600 dark:text-emerald-400 text-xs uppercase font-bold">Lunas</span>
                                                @else
                                                    <span class="text-red-600 dark:text-red-400 text-xs uppercase font-bold">Belum Tuntas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div
                        class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4 pt-6 border-t border-dashed border-primary/10 dark:border-white/10">
                        <a href="{{ route('member.peminjaman.index') }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20 text-center transition-colors">
                            Kembali ke Riwayat
                        </a>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>

</html>