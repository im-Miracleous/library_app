@extends('layouts.app')

@section('title', 'Detail Peminjaman - Library App')
@section('header-title', 'Detail Peminjaman')

@push('scripts')
    @vite('resources/js/transactions/peminjaman-show.js')
@endpush

@section('content')
    <div class="p-4 sm:p-0">
        <x-breadcrumb-component parent="Sirkulasi" middle="Peminjaman" :middleLink="route('peminjaman.index')"
            current="Detail" class="mb-6 animate-enter" />

        @php
            $now = \Carbon\Carbon::now();
            $jatuhTempo = \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo);
            $isOverdue = $now->startOfDay()->greaterThan($jatuhTempo->startOfDay()) && $peminjaman->status_transaksi == 'berjalan';
            $canExtend = ($peminjaman->status_transaksi == 'berjalan') && !$isOverdue && !$peminjaman->is_extended;
            $canApproveReject = $peminjaman->status_transaksi == 'menunggu_verifikasi';
            $canReturn = $peminjaman->status_transaksi == 'berjalan';
            $canEdit = ($peminjaman->status_transaksi == 'berjalan') || ($peminjaman->status_transaksi == 'selesai' && auth()->user()->peran == 'owner');
            $canDelete = auth()->user()->peran == 'owner';
            $hasAnyAction = $canApproveReject || $canReturn || $canEdit || $canDelete || $canExtend;
        @endphp

        <div class="grid grid-cols-1 {{ $hasAnyAction ? 'lg:grid-cols-3' : '' }} gap-6 animate-enter delay-100">
            <!-- Left Column: Transaction Details -->
            <div class="{{ $hasAnyAction ? 'lg:col-span-2' : '' }} flex flex-col gap-6">
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <div class="size-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">receipt_long</span>
                            </div>
                            Informasi Transaksi
                        </h3>
                        <div class="flex flex-col items-end gap-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide 
                                    @if($peminjaman->status_transaksi == 'berjalan') bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400
                                    @elseif($peminjaman->status_transaksi == 'menunggu_verifikasi') bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400
                                    @elseif($peminjaman->status_transaksi == 'selesai') bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400
                                    @elseif($peminjaman->status_transaksi == 'ditolak') bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400
                                    @else bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-400 @endif">
                                {{ str_replace('_', ' ', $peminjaman->status_transaksi) }}
                            </span>
                        </div>
                    </div>

                    {{-- Indikator Visual Jika Sudah Diperpanjang --}}
                    @if($peminjaman->is_extended)
                        <div class="mb-6 p-3 bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-100 dark:border-cyan-500/20 rounded-xl flex items-center gap-3">
                            <div class="size-8 rounded-full bg-cyan-100 dark:bg-cyan-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400 text-sm">update</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-cyan-800 dark:text-cyan-300 uppercase tracking-wide">Status Perpanjangan</div>
                                <div class="text-xs text-cyan-700 dark:text-cyan-400/80">
                                    Transaksi ini <strong>telah diperpanjang</strong> dari tanggal sebelumnya.
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                            <div
                                class="text-[10px] text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1 font-bold">
                                Kode Transaksi</div>
                            <div class="font-mono font-bold text-lg text-primary dark:text-accent">
                                {{ $peminjaman->id_peminjaman }}
                            </div>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                            <div
                                class="text-[10px] text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1 font-bold">
                                Peminjam</div>
                            <div class="font-bold text-slate-800 dark:text-white">
                                {{ $peminjaman->pengguna->nama }}
                            </div>
                            <div class="text-xs text-slate-500">{{ $peminjaman->pengguna->email }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                            <div
                                class="text-[10px] text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1 font-bold">
                                Tanggal Pinjam</div>
                            <div class="font-bold text-slate-800 dark:text-white">
                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                            <div
                                class="text-[10px] text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1 font-bold">
                                Jatuh Tempo</div>
                            <div
                                class="font-bold {{ $isOverdue ? 'text-red-600 dark:text-red-400 animate-pulse' : 'text-slate-800 dark:text-white' }} flex items-center gap-2">
                                {{ $jatuhTempo->translatedFormat('d F Y') }}
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
                                class="text-[10px] text-yellow-700 dark:text-yellow-500 uppercase tracking-wider mb-1 font-bold">
                                Keterangan</div>
                            <div class="text-sm text-slate-700 dark:text-white/80">{{ $peminjaman->keterangan }}
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-white/10 flex justify-between items-center">
                        <div class="text-[10px] text-slate-400 dark:text-white/40 uppercase tracking-widest font-bold">
                            System Record Item
                        </div>
                        <div class="text-xs text-slate-400 dark:text-white/40 font-medium">
                            Dibuat pada {{ $peminjaman->created_at->translatedFormat('d M Y, H:i') }}
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <div
                            class="size-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">menu_book</span>
                        </div>
                        Daftar Buku
                    </h3>

                    <div class="overflow-x-auto border border-slate-200 dark:border-border-dark rounded-xl">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 uppercase text-xs">
                                <tr>
                                    <th class="p-3 pl-4">Judul Buku</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Kondisi</th>
                                    <th class="p-3 text-right pr-4">Tgl Kembali</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/10 text-slate-600 dark:text-white/80">
                                @foreach($peminjaman->details as $detail)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="p-3 pl-4">
                                            <div class="font-bold text-slate-800 dark:text-white">
                                                {{ $detail->buku->judul }}
                                            </div>
                                            <div class="text-[10px] text-slate-500">{{ $detail->buku->penulis }}</div>
                                        </td>
                                        <td class="p-3">
                                            @if($detail->status_buku == 'dipinjam')
                                                <span
                                                    class="bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Dipinjam</span>
                                            @elseif($detail->status_buku == 'dikembalikan')
                                                <span
                                                    class="bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Dikembalikan</span>
                                            @elseif($detail->status_buku == 'hilang')
                                                <span
                                                    class="bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Hilang</span>
                                            @else
                                                <span
                                                    class="bg-gray-100 dark:bg-white/10 text-slate-600 dark:text-white/60 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">{{ $detail->status_buku }}</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            @php
                                                $conditionDenda = $detail->denda->whereIn('jenis_denda', ['rusak', 'hilang'])->first();
                                                $condition = $conditionDenda ? $conditionDenda->jenis_denda : ($detail->status_buku == 'dikembalikan' ? 'baik' : null);
                                            @endphp

                                            @if($condition == 'baik')
                                                <div
                                                    class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold text-[10px] uppercase">
                                                    <span class="material-symbols-outlined text-xs">check_circle</span>
                                                    Baik
                                                </div>
                                            @elseif($condition == 'rusak')
                                                <div
                                                    class="flex items-center gap-1.5 text-orange-600 dark:text-orange-400 font-bold text-[10px] uppercase">
                                                    <span class="material-symbols-outlined text-xs">warning</span>
                                                    Rusak
                                                </div>
                                            @elseif($condition == 'hilang')
                                                <div
                                                    class="flex items-center gap-1.5 text-red-600 dark:text-red-400 font-bold text-[10px] uppercase">
                                                    <span class="material-symbols-outlined text-xs">dangerous</span>
                                                    Hilang
                                                </div>
                                            @else
                                                <span class="text-slate-400 dark:text-white/20">-</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-right pr-4 font-mono text-[10px]">
                                            @if($detail->tanggal_kembali_aktual)
                                                @php
                                                    $tglKembali = \Carbon\Carbon::parse($detail->tanggal_kembali_aktual);
                                                    $isLateReturn = $tglKembali->startOfDay()->gt($jatuhTempo->startOfDay());
                                                @endphp
                                                <span class="{{ $isLateReturn ? 'text-red-600 dark:text-red-400 font-bold' : '' }}">
                                                    {{ $tglKembali->format('d/m/Y') }}
                                                </span>
                                                @if($isLateReturn)
                                                    <div class="text-[8px] text-red-500 dark:text-red-400/80">Terlambat</div>
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
            @if($hasAnyAction)
                <div class="flex flex-col gap-6">
                    <div
                        class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm lg:sticky lg:top-6">
                        
                        {{-- APPROVE / REJECT --}}
                        @if($canApproveReject)
                            <div
                                class="p-4 bg-orange-50 dark:bg-orange-500/10 rounded-xl border border-orange-100 dark:border-white/5 mb-6">
                                <div class="text-sm font-bold text-orange-800 dark:text-orange-400 mb-2 flex items-center gap-2">
                                    <span class="material-symbols-outlined">verified_user</span>
                                    Verifikasi Diperlukan
                                </div>
                                <p class="text-[10px] text-orange-700 dark:text-orange-400/80 mb-4">
                                    Setujui untuk menetapkan tanggal pinjam hari ini, atau tolak untuk membatalkan pengajuan.
                                </p>

                                <form id="approveForm" action="{{ route('peminjaman.approve', $peminjaman->id_peminjaman) }}"
                                    method="POST" class="mb-2">
                                    @csrf
                                    <button type="button" onclick="showApproveModal()"
                                        class="w-full py-2.5 bg-green-600 text-white hover:bg-green-700 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                        Setujui Peminjaman
                                    </button>
                                </form>

                                <form id="rejectForm" action="{{ route('peminjaman.reject', $peminjaman->id_peminjaman) }}"
                                    method="POST">
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

    
                        @if($canExtend)
                             <a href="{{ route('peminjaman.extend.form', $peminjaman->id_peminjaman) }}"
                                class="w-full py-3.5 bg-orange-500 text-white hover:bg-orange-600 rounded-xl font-bold shadow-lg hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 mb-3">
                                <span class="material-symbols-outlined">more_time</span>
                                Perpanjang Peminjaman
                            </a>
                        @endif

                        @if($canReturn)
                            <a href="{{ route('pengembalian.show', $peminjaman->id_peminjaman) }}"
                                class="w-full py-3.5 bg-primary text-white dark:bg-accent dark:text-primary-dark rounded-xl font-bold shadow-lg hover:brightness-110 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 mb-3">
                                <span class="material-symbols-outlined">assignment_return</span>
                                Proses Pengembalian
                            </a>
                        @endif

                        @if($canEdit)
                            <a href="{{ route('peminjaman.edit', $peminjaman->id_peminjaman) }}"
                                class="w-full py-3 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-500/30 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-2 mb-3 text-sm">
                                <span class="material-symbols-outlined">edit_square</span>
                                Edit Transaksi
                            </a>
                        @endif

                        @if($canDelete)
                            <form action="{{ route('peminjaman.destroy', $peminjaman->id_peminjaman) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat transaksi ini? Data tidak dapat dikembalikan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full py-3 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl font-bold transition-colors flex items-center justify-center gap-2 text-sm">
                                    <span class="material-symbols-outlined font-normal">delete</span>
                                    Hapus Transaksi
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>


    <!-- MODALS -->
    <!-- Approve Modal -->
    <x-modal id="approveModal" title="Konfirmasi Persetujuan" maxWidth="lg">
        <x-slot:title_icon>
            <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
        </x-slot:title_icon>

        <div class="py-2">
            <p class="text-sm text-slate-500 dark:text-white/60">
                Apakah Anda yakin ingin menyetujui peminjaman ini?
                <br><br>
                <strong class="text-slate-700 dark:text-white/80">Konsekuensi:</strong>
            <ul class="list-disc list-inside mt-1 text-xs space-y-1">
                <li>Tanggal pinjam akan ditetapkan menjadi <span
                        class="font-bold underline text-green-600">{{ now()->format('d F Y') }}</span>.</li>
                <li>Batas waktu kembali akan dihitung otomatis (7 hari).</li>
                <li>Status transaksi akan berubah menjadi "Berjalan".</li>
            </ul>
            </p>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-primary/10 dark:border-white/10">
            <button type="button" onclick="closeApproveModal()"
                class="px-4 py-2 rounded-xl border border-slate-300 dark:border-white/10 text-slate-700 dark:text-white/60 font-bold hover:bg-slate-50 dark:hover:bg-white/10 transition-all text-sm">
                Batal
            </button>
            <button type="button" onclick="submitApprove()" id="btnApproveConfirm"
                class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 font-bold shadow-sm transition-all active:scale-95 text-sm flex items-center gap-2">
                <span id="approveText">Ya, Setujui Peminjaman</span>
                <div id="approveSpinner"
                    class="hidden animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></div>
            </button>
        </div>
    </x-modal>

    <!-- Reject Modal -->
    <x-modal id="rejectModal" title="Konfirmasi Penolakan" maxWidth="lg">
        <x-slot:title_icon>
            <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
        </x-slot:title_icon>

        <div class="py-2">
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

            <div class="mt-4">
                <label for="alasanPenolakan" class="block text-xs font-bold text-slate-700 dark:text-white/80 mb-1">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea id="alasanPenolakan" name="alasan" form="rejectForm" required
                    class="w-full rounded-xl border border-slate-300 dark:border-white/10 dark:bg-white/5 text-sm focus:ring-primary focus:border-primary p-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-white/50"
                    rows="3" placeholder="Contoh: Stok buku habis, atau anggota memiliki tanggungan denda..."></textarea>
                <p class="mt-1 text-[10px] text-slate-400">Alasan ini akan ditampilkan kepada anggota.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-primary/10 dark:border-white/10">
            <button type="button" onclick="closeRejectModal()"
                class="px-4 py-2 rounded-xl border border-slate-300 dark:border-white/10 text-slate-700 dark:text-white/60 font-bold hover:bg-slate-50 dark:hover:bg-white/10 transition-all text-sm">
                Batal
            </button>
            <button type="button" onclick="submitReject()" id="btnRejectConfirm"
                class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 font-bold shadow-sm transition-all active:scale-95 text-sm flex items-center gap-2">
                <span id="rejectText">Ya, Tolak Pengajuan</span>
                <div id="rejectSpinner"
                    class="hidden animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></div>
            </button>
        </div>
    </x-modal>
@endsection