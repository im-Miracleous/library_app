@extends('layouts.app')

@section('title', 'Profil Saya - Library App')
@section('header-title', 'Profil Saya')

@push('scripts')
    @vite(['resources/js/logic/special-pages/profile.js'])
@endpush

@section('content')
    <div class="flex flex-col">
        <div class="p-4 sm:p-8 max-w-4xl mx-auto w-full">

            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 animate-enter">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div
                class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark shadow-sm dark:shadow-none overflow-hidden animate-enter">

                <div class="p-6 border-b border-primary/10 dark:border-white/5 bg-surface dark:bg-[#1A1410]">
                    <h2 class="text-lg font-bold text-primary-dark dark:text-white">Edit Informasi Profil</h2>
                    <p class="text-sm text-slate-500 dark:text-white/60">Perbarui informasi pribadi dan foto profil Anda.
                    </p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 sm:p-8 space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Foto Profil Section -->
                    <div
                        class="flex flex-col sm:flex-row items-center sm:items-start gap-6 pb-8 border-b border-gray-100 dark:border-gray-800">
                        <div class="relative group">
                            <div onclick="openZoom()"
                                class="size-24 sm:size-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 border-4 border-white dark:border-[#2C2420] shadow-md cursor-pointer relative group/image">
                                <div id="profile_initials"
                                    class="w-full h-full flex items-center justify-center bg-primary/10 dark:bg-accent/10 text-primary dark:text-accent font-bold text-4xl {{ $user->foto_profil ? 'hidden' : '' }}">
                                    {{ substr($user->nama, 0, 1) }}
                                </div>
                                <img id="profile_preview_img"
                                    src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : '' }}"
                                    data-initial-src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : '' }}"
                                    alt="Profile Photo"
                                    class="w-full h-full object-cover {{ $user->foto_profil ? '' : 'hidden' }} transition-transform duration-500 group-hover/image:scale-110">
                                <div id="profile_zoom_overlay"
                                    class="absolute inset-0 bg-black/0 group-hover/image:bg-black/20 transition-colors flex items-center justify-center {{ $user->foto_profil ? '' : 'hidden' }}">
                                    <span
                                        class="material-symbols-outlined text-white opacity-0 group-hover/image:opacity-100 transition-opacity">zoom_in</span>
                                </div>
                            </div>
                            <label for="foto_profil"
                                class="absolute bottom-0 right-0 p-2 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-full cursor-pointer hover:scale-110 transition-transform shadow-md"
                                title="Ubah Foto">
                                <span class="material-symbols-outlined text-lg">photo_camera</span>
                            </label>

                            <input type="file" id="foto_profil" name="foto_profil" class="hidden" accept="image/*">
                            <input type="hidden" name="remove_foto_profil" id="remove_foto_profil" value="0">
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <h3 class="font-bold text-primary-dark dark:text-white text-lg">{{ $user->nama }}</h3>
                            <p class="text-sm text-slate-500 dark:text-white/50 mb-3">{{ $user->email }}</p>
                            <p class="text-xs text-slate-400 dark:text-white/40 italic">
                                Role: <span class="uppercase font-semibold tracking-wide">{{ $user->peran }}</span>
                            </p>
                            <p class="text-xs text-slate-400 dark:text-white/40 mt-2 flex flex-wrap items-center gap-3">
                                <span>Upload foto JPG, PNG, atau GIF. Maksimal 2MB.</span>
                                <button type="button" id="profile_delete_btn"
                                    style="{{ $user->foto_profil ? '' : 'display: none' }}"
                                    class="text-red-500 hover:text-red-600 font-medium transition-colors inline-flex items-center gap-1 {{ $user->foto_profil ? '' : 'hidden' }}">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Hapus Foto Profil
                                </button>
                                <button type="button" id="profile_restore_btn" style="display: none"
                                    class="text-blue-500 hover:text-blue-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">settings_backup_restore</span>
                                    Batalkan Penghapusan
                                </button>
                                <button type="button" id="profile_cancel_btn"
                                    class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Hapus Gambar
                                </button>
                            </p>
                        </div>
                    </div>

                    <!-- Form Input Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input id="nama" name="nama" label="Nama Lengkap" :value="old('nama', $user->nama)" required />
                        <x-input id="email" name="email" label="Alamat Email" type="email" :value="old('email', $user->email)" required />
                        <x-input id="telepon" name="telepon" label="Nomor Telepon" :value="old('telepon', $user->telepon)" />
                        <x-textarea id="alamat" name="alamat" label="Alamat Lengkap"
                            rows="3">{{ old('alamat', $user->alamat) }}</x-textarea>
                    </div>

                    <div class="flex justify-end pt-4 gap-3">
                        <button type="button" onclick="history.back()"
                            class="px-6 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-white/70 font-bold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            Kembali
                        </button>
                        <button type="submit"
                            class="flex items-center gap-2 px-6 py-3 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-xl font-bold font-display shadow-lg shadow-primary/20 dark:shadow-accent/20 hover:scale-105 active:scale-95 transition-all duration-200">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <x-image-preview-modal />
    <x-image-zoom-modal />
@endsection