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
                                            @if($peminjaman->status_transaksi == 'selesai')
                                                <td class="p-4 text-center font-bold text-green-600">Dikembalikan</td>
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

                    <!-- Informasi Peminjaman -->
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-primary">calendar_clock</span>
                            <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Peminjaman</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20">
                                <p
                                    class="text-xs text-blue-600 dark:text-blue-300 uppercase font-bold tracking-widest mb-1">
                                    Waktu Pengajuan</p>
                                <p class="font-bold text-lg text-blue-800 dark:text-blue-100">
                                    {{ \Carbon\Carbon::parse($peminjaman->created_at)->translatedFormat('d F Y, H:i:s') }}
                                </p>
                            </div>

                            @if($peminjaman->status_transaksi == 'berjalan' || $peminjaman->status_transaksi == 'selesai')
                                <div
                                    class="p-4 rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20">
                                    <p
                                        class="text-xs text-orange-600 dark:text-orange-300 uppercase font-bold tracking-widest mb-1">
                                        Status Pengajuan</p>
                                    <p class="font-bold text-lg text-orange-800 dark:text-orange-100">
                                        Diverifikasi
                                    </p>
                                </div>
                                <div
                                    class="p-4 rounded-xl bg-teal-50 dark:bg-teal-500/10 border border-teal-100 dark:border-teal-500/20">
                                    <p
                                        class="text-xs text-teal-600 dark:text-teal-300 uppercase font-bold tracking-widest mb-1">
                                        Tanggal Mulai Pinjam</p>
                                    <p class="font-bold text-lg text-teal-800 dark:text-teal-100">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                                <div
                                    class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20">
                                    <p
                                        class="text-xs text-rose-600 dark:text-rose-300 uppercase font-bold tracking-widest mb-1">
                                        Batas Pengembalian</p>
                                    <p class="font-bold text-lg text-rose-800 dark:text-rose-100">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            @else
                                <div
                                    class="p-4 rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20">
                                    <p
                                        class="text-xs text-orange-600 dark:text-orange-300 uppercase font-bold tracking-widest mb-1">
                                        Status Pengajuan</p>
                                    <p class="font-bold text-lg text-orange-800 dark:text-orange-100">
                                        Menunggu Verifikasi
                                    </p>
                                    <p class="text-xs text-orange-600/80 dark:text-orange-300/80 mt-1">*Tanggal pinjam &
                                        kembali akan ditetapkan saat verifikasi.</p>
                                </div>
                            @endif
                        </div>
                    </div>

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