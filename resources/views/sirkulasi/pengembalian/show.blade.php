<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proses Pengembalian - Library App</title>
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
            <x-header-component title="Proses Pengembalian" />

            <div class="p-4 sm:p-8">
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-white/60 mb-6 animate-enter">
                    <span class="material-symbols-outlined text-base">home</span>
                    <span>/</span>
                    <span>Sirkulasi</span>
                    <span>/</span>
                    <a href="{{ route('pengembalian.index') }}"
                        class="hover:text-primary dark:hover:text-white transition-colors">Pengembalian</a>
                    <span>/</span>
                    <span class="font-bold text-primary dark:text-white">Proses</span>
                </div>

                @if ($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex flex-col gap-1 animate-enter shadow-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span>
                                {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-enter delay-100">

                    <!-- Left Column: Transaction Details -->
                    <div class="lg:col-span-2 flex flex-col gap-6">
                        <div
                            class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                <div
                                    class="size-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">info</span>
                                </div>
                                Informasi Peminjaman
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                    <div
                                        class="text-xs text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1">
                                        Kode Transaksi</div>
                                    <div class="font-mono font-bold text-lg text-primary dark:text-accent">
                                        {{ $peminjaman->kode_peminjaman }}</div>
                                </div>
                                <div
                                    class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                                    <div
                                        class="text-xs text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1">
                                        Peminjam</div>
                                    <div class="font-bold text-slate-800 dark:text-white">
                                        {{ $peminjaman->pengguna->nama }}</div>
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
                                    class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5 relative overflow-hidden">
                                    @if($terlambatHari > 0)
                                        <div
                                            class="absolute right-0 top-0 bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-bl-lg font-bold">
                                            TERLAMBAT</div>
                                    @endif
                                    <div
                                        class="text-xs text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1">
                                        Jatuh Tempo</div>
                                    <div
                                        class="font-bold {{ $terlambatHari > 0 ? 'text-red-600' : 'text-slate-800 dark:text-white' }}">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}
                                    </div>
                                    @if($terlambatHari > 0)
                                        <div class="text-xs text-red-500 mt-1 font-medium">Telat {{ $terlambatHari }} Hari
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('pengembalian.store') }}" method="POST" id="returnForm">
                            @csrf
                            <input type="hidden" name="id_peminjaman" value="{{ $peminjaman->id_peminjaman }}">

                            <div
                                class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                                <h3
                                    class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                    <div
                                        class="size-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                        <span
                                            class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">checklist</span>
                                    </div>
                                    Pilih Buku Kembali
                                </h3>

                                <div class="overflow-x-auto border border-slate-200 dark:border-border-dark rounded-xl">
                                    <table class="w-full text-left text-sm">
                                        <thead
                                            class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 uppercase text-xs">
                                            <tr>
                                                <th class="p-3 pl-4 w-10">
                                                    <input type="checkbox" id="checkAll" checked
                                                        class="rounded border-slate-300 text-primary focus:ring-primary">
                                                </th>
                                                <th class="p-3">Judul Buku</th>
                                                <th class="p-3">Status Saat Ini</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-100 dark:divide-white/10 text-slate-600 dark:text-white/80">
                                            @foreach($peminjaman->details as $detail)
                                                @if($detail->status_buku == 'dipinjam')
                                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                                        <td class="p-3 pl-4">
                                                            <input type="checkbox" name="details[]"
                                                                value="{{ $detail->id_detail_peminjaman }}" checked
                                                                class="book-checkbox rounded border-slate-300 text-primary focus:ring-primary">
                                                        </td>
                                                        <td class="p-3">
                                                            <div class="font-bold text-slate-800 dark:text-white">
                                                                {{ $detail->buku->judul }}</div>
                                                            <div class="text-xs text-slate-500">{{ $detail->buku->penulis }}
                                                            </div>
                                                        </td>
                                                        <td class="p-3">
                                                            <span
                                                                class="bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-xs px-2 py-0.5 rounded-full font-bold uppercase">Dipinjam</span>
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr class="bg-slate-50/50 dark:bg-black/20 opacity-60">
                                                        <td class="p-3 pl-4">
                                                            <span
                                                                class="material-symbols-outlined text-slate-400 text-lg">check_box_outline_blank</span>
                                                        </td>
                                                        <td class="p-3">
                                                            <div class="font-bold text-slate-800 dark:text-white">
                                                                {{ $detail->buku->judul }}</div>
                                                        </td>
                                                        <td class="p-3">
                                                            <span
                                                                class="bg-gray-200 dark:bg-white/10 text-slate-600 dark:text-white/60 text-xs px-2 py-0.5 rounded-full font-bold uppercase">{{ $detail->status_buku }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Right Column: Summary & Submit -->
                    <div class="flex flex-col gap-6">
                        <div
                            class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm sticky top-6">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Ringkasan</h3>

                            <div
                                class="flex justify-between items-center mb-4 pb-4 border-b border-dashed border-slate-200 dark:border-white/10">
                                <span class="text-slate-500 dark:text-white/60 text-sm">Keterlambatan</span>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $terlambatHari }} Hari</span>
                            </div>

                            <div
                                class="flex justify-between items-center mb-4 pb-4 border-b border-dashed border-slate-200 dark:border-white/10">
                                <span class="text-slate-500 dark:text-white/60 text-sm">Buku Dikembalikan</span>
                                <span class="font-bold text-slate-800 dark:text-white" id="countDisplay">0</span>
                            </div>

                            <div class="flex justify-between items-center mb-6">
                                <span class="text-slate-500 dark:text-white/60 text-sm">Estimasi Denda</span>
                                <span class="font-bold text-red-600 text-xl" id="dendaDisplay">rp 0</span>
                            </div>

                            <!-- Info Box -->
                            <div
                                class="bg-primary/5 dark:bg-white/5 p-4 rounded-xl border border-primary/10 dark:border-white/10 mb-6 text-xs text-primary-mid dark:text-white/80 leading-relaxed">
                                Pastikan buku yang dikembalikan dalam kondisi baik. Denda dihitung otomatis berdasarkan
                                jumlah hari keterlambatan x Rp 1.000 per buku.
                            </div>

                            <button type="button" id="btnConfirm"
                                class="w-full py-3.5 bg-primary text-white dark:bg-accent dark:text-primary-dark rounded-xl font-bold shadow-lg hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">save</span>
                                Konfirmasi Pengembalian
                            </button>

                            <a href="{{ route('pengembalian.index') }}"
                                class="block w-full text-center py-3 text-slate-500 dark:text-white/50 text-sm font-bold hover:text-slate-800 dark:hover:text-white transition-colors mt-2">
                                Batal
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Inject JS for Pengembalian Logic -->
    <script id="pengembalian-script" data-terlambat-hari="{{ $terlambatHari }}"></script>
    @vite('resources/js/pengembalian.js')
</body>

</html>