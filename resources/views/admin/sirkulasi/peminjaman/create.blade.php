@extends('layouts.app')

@section('title', 'Transaksi Baru - Library App')
@section('header-title', 'Transaksi Baru')

@push('scripts')
    @vite('resources/js/transactions/peminjaman-create.js')
@endpush

@section('content')
    <div class="p-4 sm:p-0">
        <x-breadcrumb-component parent="Sirkulasi" middle="Peminjaman" :middleLink="route('peminjaman.index')"
            current="Baru" class="mb-6 animate-enter" />

        @if ($errors->any())
            <div
                class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex flex-col gap-1 animate-enter shadow-sm text-sm">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span>
                        {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3 animate-enter shadow-sm text-sm">
                <span class="material-symbols-outlined">error</span>
                {{ session('error') }}
            </div>
        @endif


        <!-- SETTINGS DATA -->
        <div id="loanSettings" data-max-books="{{ $pengaturan->maksimal_buku_pinjam ?? 3 }}"
            data-max-days="{{ $pengaturan->batas_peminjaman_hari ?? 7 }}">
        </div>

        <!-- DYNAMIC JS WARNING BANNER -->
        <div id="warningBanner"
            class="hidden mb-6 p-4 bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-200 dark:border-yellow-500/20 rounded-xl text-yellow-800 dark:text-yellow-400 flex items-start gap-3 animate-enter shadow-sm">
            <span class="material-symbols-outlined mt-0.5">warning</span>
            <div>
                <h4 class="font-bold mb-1">Perhatian</h4>
                <ul id="warningList" class="list-disc list-inside text-sm space-y-1">
                    <!-- JS will populate -->
                </ul>
            </div>
        </div>

        <form action="{{ route('peminjaman.store') }}" method="POST" id="peminjamanForm"
            class="flex flex-col gap-6 animate-enter delay-100">
            @csrf

            <!-- 1. IDENTITAS PEMINJAM -->
            <div
                class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <div class="size-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">person_search</span>
                    </div>
                    Identitas Peminjam
                </h3>

                <div class="relative">
                    <label
                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Cari
                        Anggota</label>
                    <input type="text" id="searchAnggota" placeholder="Ketik nama atau email anggota..."
                        class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg pl-3 pr-10 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-all">
                    <span class="material-symbols-outlined absolute right-3 top-[34px] text-slate-400">search</span>

                    <!-- Search Results Dropdown -->
                    <div id="anggotaResults"
                        class="absolute z-20 w-full mt-1 bg-white dark:bg-[#1A1410] border border-slate-200 dark:border-border-dark rounded-xl shadow-xl max-h-60 overflow-y-auto hidden">
                        <!-- JS will populate this -->
                    </div>
                </div>

                <!-- Selected Member Display -->
                <div id="selectedAnggota"
                    class="mt-4 p-4 bg-primary/5 dark:bg-white/5 rounded-xl border border-primary/10 dark:border-white/10 hidden items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold">
                            <span id="selectedAnggotaInitial">A</span>
                        </div>
                        <div>
                            <p id="selectedAnggotaName" class="font-bold text-slate-800 dark:text-white">Nama
                                Anggota</p>
                            <p id="selectedAnggotaEmail" class="text-xs text-slate-500 dark:text-white/60">
                                email@example.com</p>
                        </div>
                    </div>
                    <button type="button" id="removeAnggotaBtn" class="text-red-500 hover:text-red-700 p-2">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                    <input type="hidden" name="id_pengguna" id="id_pengguna_input">
                </div>
            </div>

            <!-- 2. PILIH BUKU -->
            <div
                class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <div class="size-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">bookmark_add</span>
                    </div>
                    Pilih Buku
                </h3>

                <div class="relative mb-4">
                    <label
                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Cari
                        Buku</label>
                    <input type="text" id="searchBuku" placeholder="Judul, Penulis, atau ISBN..."
                        class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg pl-3 pr-10 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-all">
                    <span class="material-symbols-outlined absolute right-3 top-[34px] text-slate-400">abc</span>

                    <!-- Search Results Dropdown -->
                    <div id="bukuResults"
                        class="absolute z-20 w-full mt-1 bg-white dark:bg-[#1A1410] border border-slate-200 dark:border-border-dark rounded-xl shadow-xl max-h-60 overflow-y-auto hidden">
                        <!-- JS will populate this -->
                    </div>
                </div>

                <!-- Selected Books Table -->
                <div class="overflow-x-auto border border-slate-200 dark:border-border-dark rounded-xl">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 uppercase text-xs">
                            <tr>
                                <th class="p-3 pl-4">Judul Buku</th>
                                <th class="p-3">Stok</th>
                                <th class="p-3 text-right pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="selectedBukuParams"
                            class="divide-y divide-slate-100 dark:divide-white/10 text-slate-600 dark:text-white/80">
                            <tr id="emptyBukuRow">
                                <td colspan="3" class="p-6 text-center text-slate-400 italic">Belum ada buku
                                    yang dipilih.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. DETAIL PEMINJAMAN -->
            <div
                class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <div class="size-8 rounded-lg bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">calendar_month</span>
                    </div>
                    Detail Transaksi
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input type="date" name="tanggal_pinjam" id="tanggal_pinjam" :value="date('Y-m-d')"
                        label="Tanggal Pinjam" required />
                    <x-input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" :value="date('Y-m-d', strtotime('+7 days'))" label="Tanggal Jatuh Tempo (+7 Hari)" required />
                </div>
                <div class="mt-4">
                    <x-textarea name="keterangan" id="keterangan" label="Keterangan (Opsional)"
                        placeholder="Catatan tambahan..." rows="2" />
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('peminjaman.index') }}"
                    class="px-6 py-3 rounded-xl border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 font-bold transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-primary text-white dark:bg-accent dark:text-primary-dark hover:brightness-110 font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Simpan Transaksi
                </button>
            </div>

        </form>
    </div>
@endsection