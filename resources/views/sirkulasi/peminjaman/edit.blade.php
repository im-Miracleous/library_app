<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Transaksi - Library App</title>
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
            <x-header-component title="Edit Transaksi" />

            <div class="p-4 sm:p-8">
                <x-breadcrumb-component parent="Sirkulasi" middle="Peminjaman" :middleLink="route('peminjaman.index')"
                    current="Edit" class="mb-6 animate-enter" />

                @if ($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex flex-col gap-1 animate-enter shadow-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span>
                                {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('peminjaman.update', $peminjaman->id_peminjaman) }}" method="POST"
                    class="flex flex-col gap-6 animate-enter delay-100">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left: Info that cannot be changed easily -->
                        <div class="lg:col-span-1 flex flex-col gap-6">
                            <div
                                class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                                <h3
                                    class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                    <div
                                        class="size-8 rounded-lg bg-slate-100 dark:bg-white/10 flex items-center justify-center">
                                        <span
                                            class="material-symbols-outlined text-slate-500 dark:text-white/60">lock</span>
                                    </div>
                                    Info Tetap
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1 block">Kode
                                            Transaksi</label>
                                        <input type="text" value="{{ $peminjaman->kode_peminjaman }}" disabled
                                            class="w-full bg-slate-100 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-lg px-3 py-2 text-slate-500 dark:text-white/50 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1 block">Peminjam</label>
                                        <input type="text"
                                            value="{{ $peminjaman->pengguna->nama }} ({{ $peminjaman->pengguna->email }})"
                                            disabled
                                            class="w-full bg-slate-100 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-lg px-3 py-2 text-slate-500 dark:text-white/50 cursor-not-allowed">
                                        <p class="text-[10px] text-slate-400 mt-1 dark:text-white/40">*Peminjam tidak
                                            dapat diubah setelah transaksi dibuat.</p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1 block">Daftar
                                            Buku</label>
                                        <ul
                                            class="text-sm text-slate-600 dark:text-white/70 list-disc list-inside bg-slate-50 dark:bg-white/5 p-3 rounded-lg border border-slate-100 dark:border-white/5">
                                            @foreach($peminjaman->details as $detail)
                                                <li>{{ $detail->buku->judul }}</li>
                                            @endforeach
                                        </ul>
                                        <p class="text-[10px] text-slate-400 mt-1 dark:text-white/40">*Untuk mengubah
                                            buku, hapus transaksi ini dan buat baru.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Editable Fields -->
                        <div class="lg:col-span-2 flex flex-col gap-6">
                            <div
                                class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                                <h3
                                    class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                    <div
                                        class="size-8 rounded-lg bg-yellow-100 dark:bg-yellow-500/20 flex items-center justify-center">
                                        <span
                                            class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">edit</span>
                                    </div>
                                    Edit Detail
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label
                                            class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Tanggal
                                            Pinjam</label>
                                        <input type="date" name="tanggal_pinjam"
                                            value="{{ $peminjaman->tanggal_pinjam->format('Y-m-d') }}" required
                                            class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Tanggal
                                            Jatuh Tempo</label>
                                        <input type="date" name="tanggal_jatuh_tempo"
                                            value="{{ $peminjaman->tanggal_jatuh_tempo->format('Y-m-d') }}" required
                                            class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Keterangan</label>
                                    <textarea name="keterangan" rows="4" placeholder="Catatan tambahan..."
                                        class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">{{ $peminjaman->keterangan }}</textarea>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <a href="{{ route('peminjaman.show', $peminjaman->id_peminjaman) }}"
                                    class="px-6 py-3 rounded-xl border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 font-bold transition-colors">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="px-6 py-3 rounded-xl bg-primary text-white dark:bg-accent dark:text-primary-dark hover:brightness-110 font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center gap-2">
                                    <span class="material-symbols-outlined">save</span>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>