@extends('layouts.app')

@section('title', 'Pengaturan - Library App')
@section('header-title', 'Pengaturan')

@push('scripts')
    @vite(['resources/js/logic/special-pages/pengaturan.js'])
@endpush

@section('content')
    <div class="flex flex-col gap-6 max-w-[1000px] mx-auto w-full">
        <x-breadcrumb-component parent="Administrator" current="Pengaturan" class="animate-enter" />

        <div class="animate-enter">
            <h1 class="text-2xl sm:text-3xl font-bold text-primary-dark dark:text-white">Pengaturan Sistem</h1>
            <p class="text-primary-mid dark:text-white/60 mt-1">Kelola konfigurasi dasar aplikasi perpustakaan.</p>
        </div>

        <div
            class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 sm:p-8 shadow-sm animate-enter delay-100">
            <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                <!-- Logo -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider" for="logo">
                        Logo Perpustakaan
                    </label>

                    @if($pengaturan->logo_path)
                        <div class="mb-2">
                            <p class="text-xs text-primary-mid dark:text-white/60 mb-2">Logo Saat Ini:</p>
                            <div class="p-2 bg-background-light dark:bg-background-dark rounded-xl border border-primary/20 dark:border-white/10 w-fit cursor-pointer relative group/logo shadow-sm hover:shadow-md transition-all"
                                onclick="openZoom()">
                                <img id="logo_preview_img" src="{{ asset('storage/' . $pengaturan->logo_path) }}"
                                    data-initial-src="{{ asset('storage/' . $pengaturan->logo_path) }}" alt="Current Logo"
                                    class="h-16 w-auto object-contain transition-transform duration-300 group-hover/logo:scale-105">
                                <div
                                    class="absolute inset-0 bg-black/0 group-hover/logo:bg-black/40 transition-colors flex items-center justify-center rounded-xl overflow-hidden">
                                    <span
                                        class="material-symbols-outlined text-white opacity-0 group-hover/logo:opacity-100 transition-opacity">zoom_in</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div id="logo_preview_container" class="mb-2 hidden">
                            <p class="text-xs text-primary-mid dark:text-white/60 mb-2">Pratinjau Logo Baru:</p>
                            <div class="p-2 bg-background-light dark:bg-background-dark rounded-xl border border-primary/20 dark:border-white/10 w-fit cursor-pointer relative group/logo shadow-sm hover:shadow-md transition-all"
                                onclick="openZoom()">
                                <img id="logo_preview_img" src="" data-initial-src="" alt="Logo Preview"
                                    class="h-16 w-auto object-contain transition-transform duration-300 group-hover/logo:scale-105">
                                <div id="logo_zoom_overlay"
                                    class="absolute inset-0 bg-black/0 group-hover/logo:bg-black/40 transition-colors flex items-center justify-center rounded-xl overflow-hidden">
                                    <span
                                        class="material-symbols-outlined text-white opacity-0 group-hover/logo:opacity-100 transition-opacity">zoom_in</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="relative group">
                        <input
                            class="w-full p-3 rounded-lg bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent dark:hover:file:bg-accent/20 cursor-pointer text-sm"
                            id="logo" name="logo" type="file" accept="image/*" />
                        <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                    </div>
                    <p class="text-xs text-primary-mid dark:text-white/40 mt-1 flex flex-wrap items-center gap-3">
                        <span>Format: PNG, JPG, JPEG. Maks: 2MB.</span>
                        @if($pengaturan->logo_path)
                            <button type="button" id="logo_delete_btn"
                                style="{{ $pengaturan->logo_path ? '' : 'display: none' }}"
                                class="text-red-500 hover:text-red-600 font-medium transition-colors inline-flex items-center gap-1 {{ $pengaturan->logo_path ? '' : 'hidden' }}">
                                <span class="material-symbols-outlined text-sm">delete</span>
                                Hapus Logo Perpustakaan
                            </button>
                            <button type="button" id="logo_restore_btn" style="display: none"
                                class="text-blue-500 hover:text-blue-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">settings_backup_restore</span>
                                Batalkan Penghapusan
                            </button>
                        @endif
                        <button type="button" id="logo_cancel_btn" style="display: none"
                            class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            Hapus Gambar
                        </button>
                    </p>
                </div>

                <!-- Nama Perpustakaan -->
                <div class="flex flex-col gap-1">
                    <x-input id="nama_perpustakaan" name="nama_perpustakaan" label="Nama Perpustakaan"
                        value="{{ old('nama_perpustakaan', $pengaturan->nama_perpustakaan) }}" required />
                    <p class="text-[10px] text-primary-mid dark:text-white/40">Nama ini akan ditampilkan di halaman login
                        dan judul aplikasi.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <x-input id="denda_per_hari" name="denda_per_hari" label="Denda Per Hari (Rp)" type="number" step="0.01"
                        min="0" value="{{ old('denda_per_hari', $pengaturan->denda_per_hari) }}" required />

                    <x-input id="denda_rusak" name="denda_rusak" label="Denda Buku Rusak (Rp)" type="number" step="0.01"
                        min="0" value="{{ old('denda_rusak', $pengaturan->denda_rusak) }}" required />

                    <x-input id="denda_hilang" name="denda_hilang" label="Denda Buku Hilang (Rp)" type="number" step="0.01"
                        min="0" value="{{ old('denda_hilang', $pengaturan->denda_hilang) }}" required />

                    <x-input id="batas_peminjaman_hari" name="batas_peminjaman_hari" label="Batas Peminjaman (Hari)"
                        type="number" min="1" value="{{ old('batas_peminjaman_hari', $pengaturan->batas_peminjaman_hari) }}"
                        required />

                    <x-input id="maksimal_buku_pinjam" name="maksimal_buku_pinjam" label="Maks. Buku Dipinjam" type="number"
                        min="1" value="{{ old('maksimal_buku_pinjam', $pengaturan->maksimal_buku_pinjam) }}" required />
                </div>

                <div class="flex justify-end pt-4 gap-3">
                    <button type="button" onclick="history.back()"
                        class="px-6 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white/70 font-bold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        Kembali
                    </button>
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-primary dark:bg-accent text-white dark:text-primary-dark font-bold hover:brightness-110 active:scale-95 transition-all shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-image-preview-modal />

    <x-image-zoom-modal />
@endsection