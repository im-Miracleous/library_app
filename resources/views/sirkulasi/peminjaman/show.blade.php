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
                                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide 
                                    @if($peminjaman->status_transaksi == 'berjalan') bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400
                                    @elseif($peminjaman->status_transaksi == 'menunggu_verifikasi') bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400
                                    @elseif($peminjaman->status_transaksi == 'selesai') bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400
                                    @elseif($peminjaman->status_transaksi == 'ditolak') bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400
                                    @else bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-400 @endif">
                                    {{ str_replace('_', ' ', $peminjaman->status_transaksi) }}
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
                            @if($peminjaman->status_transaksi == 'menunggu_verifikasi')
                                <div class="p-4 bg-orange-50 dark:bg-orange-500/10 rounded-xl border border-orange-100 dark:border-white/5 mb-6">
                                    <div class="text-sm font-bold text-orange-800 dark:text-orange-400 mb-2 flex items-center gap-2">
                                        <span class="material-symbols-outlined">verified_user</span>
                                        Verifikasi Diperlukan
                                    </div>
                                    <p class="text-xs text-orange-700 dark:text-orange-400/80 mb-4">
                                        Setujui untuk menetapkan tanggal pinjam hari ini, atau tolak untuk membatalkan pengajuan.
                                    </p>
                                    
                                    <form id="approveForm" action="{{ route('peminjaman.approve', $peminjaman->id_peminjaman) }}" method="POST" class="mb-2">
                                        @csrf
                                        <button type="button" onclick="showApproveModal()"
                                            class="w-full py-2.5 bg-green-600 text-white hover:bg-green-700 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                            Setujui Peminjaman
                                        </button>
                                    </form>

                                    <form id="rejectForm" action="{{ route('peminjaman.reject', $peminjaman->id_peminjaman) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="showRejectModal()"
                                            class="w-full py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 dark:bg-transparent dark:border-red-500/30 dark:text-red-400 dark:hover:bg-red-500/10 rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
                                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                                            Tolak Pengajuan
                                        </button>
                                    </form>
                                </div>
                            @endif

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
                            @elseif($peminjaman->status_transaksi == 'selesai' && auth()->user()->peran == 'owner')
                                {{-- Owner privilege: Edit finished transactions --}}
                                <a href="{{ route('peminjaman.edit', $peminjaman->id_peminjaman) }}"
                                    class="w-full py-3 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-500/30 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-2 mb-3">
                                    <span class="material-symbols-outlined">edit_square</span>
                                    Edit Transaksi
                                </a>
                            @endif

                            @if(auth()->user()->peran == 'owner')
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
                            @endif

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
    <!-- MODALS -->
    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="closeApproveModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div id="approveModalContent" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative transform transition-all duration-300 scale-95 opacity-0">
                <div class="bg-white dark:bg-surface-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-500/20 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">Konfirmasi Persetujuan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-white/60">
                                    Apakah Anda yakin ingin menyetujui peminjaman ini? 
                                    <br><br>
                                    <strong class="text-slate-700 dark:text-white/80">Konsekuensi:</strong>
                                    <ul class="list-disc list-inside mt-1 text-xs space-y-1">
                                        <li>Tanggal pinjam akan ditetapkan menjadi <span class="font-bold underline text-green-600">{{ now()->format('d F Y') }}</span>.</li>
                                        <li>Batas waktu kembali akan dihitung otomatis (7 hari).</li>
                                        <li>Status transaksi akan berubah menjadi "Berjalan".</li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-white/5 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" onclick="submitApprove()" id="btnApproveConfirm"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-bold text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all active:scale-95 flex items-center gap-2">
                        <span id="approveText">Ya, Setujui Peminjaman</span>
                        <div id="approveSpinner" class="hidden animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></div>
                    </button>
                    <button type="button" onclick="closeApproveModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-white/10 shadow-sm px-4 py-2 bg-white dark:bg-transparent text-base font-bold text-slate-700 dark:text-white/60 hover:bg-slate-50 dark:hover:bg-white/10 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="closeRejectModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div id="rejectModalContent" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative transform transition-all duration-300 scale-95 opacity-0">
                <div class="bg-white dark:bg-surface-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-500/20 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">Konfirmasi Penolakan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-white/60">
                                    Apakah Anda yakin ingin <strong class="text-red-600 underline">MENOLAK</strong> pengajuan peminjaman ini?
                                    <br><br>
                                    <strong class="text-slate-700 dark:text-white/80">Konsekuensi:</strong>
                                    <ul class="list-disc list-inside mt-1 text-xs space-y-1">
                                        <li>Status transaksi akan menjadi "Ditolak".</li>
                                        <li>Stok buku yang terkait akan dikembalikan secara otomatis.</li>
                                        <li>Pengguna harus mengajukan ulang jika ingin meminjam kembali.</li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-white/5 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" onclick="submitReject()" id="btnRejectConfirm"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all active:scale-95 flex items-center gap-2">
                        <span id="rejectText">Ya, Tolak Pengajuan</span>
                        <div id="rejectSpinner" class="hidden animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></div>
                    </button>
                    <button type="button" onclick="closeRejectModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-white/10 shadow-sm px-4 py-2 bg-white dark:bg-transparent text-base font-bold text-slate-700 dark:text-white/60 hover:bg-slate-50 dark:hover:bg-white/10 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showApproveModal() {
            const modal = document.getElementById('approveModal');
            const content = document.getElementById('approveModalContent');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Animation In
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeApproveModal() {
            const modal = document.getElementById('approveModal');
            const content = document.getElementById('approveModalContent');
            
            // Animation Out
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }

        function submitApprove() {
            const btn = document.getElementById('btnApproveConfirm');
            const text = document.getElementById('approveText');
            const spinner = document.getElementById('approveSpinner');

            // Set Loading State
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.innerText = 'Memproses...';
            spinner.classList.remove('hidden');

            document.getElementById('approveForm').submit();
        }

        function showRejectModal() {
            const modal = document.getElementById('rejectModal');
            const content = document.getElementById('rejectModalContent');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Animation In
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            const content = document.getElementById('rejectModalContent');
            
             // Animation Out
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }

        function submitReject() {
            const btn = document.getElementById('btnRejectConfirm');
            const text = document.getElementById('rejectText');
            const spinner = document.getElementById('rejectSpinner');

            // Set Loading State
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.innerText = 'Memproses...';
            spinner.classList.remove('hidden');

            document.getElementById('rejectForm').submit();
        }
    </script>
</body>

</html>