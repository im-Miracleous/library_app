<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Saya - Library App</title>
    <link rel="icon" type="image/png" href="https://laravel.com/img/favicon/favicon-32x32.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap"
        rel="stylesheet" />
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display">
    <div class="flex h-screen w-full relative">

        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Profil Saya" />

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
                        <p class="text-sm text-slate-500 dark:text-white/60">Perbarui informasi pribadi dan foto profil
                            Anda.</p>
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
                                <p
                                    class="text-xs text-slate-400 dark:text-white/40 mt-2 flex flex-wrap items-center gap-3">
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

                            <div class="flex flex-col gap-2">
                                <label for="nama"
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                    Lengkap</label>
                                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-xl px-4 py-3 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent outline-none transition-all"
                                    required>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="email"
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Alamat
                                    Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-xl px-4 py-3 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent outline-none transition-all"
                                    required>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="telepon"
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nomor
                                    Telepon</label>
                                <input type="text" id="telepon" name="telepon"
                                    value="{{ old('telepon', $user->telepon) }}"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-xl px-4 py-3 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent outline-none transition-all">
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="alamat"
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Alamat
                                    Lengkap</label>
                                <textarea id="alamat" name="alamat" rows="3"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-xl px-4 py-3 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent outline-none transition-all resize-none">{{ old('alamat', $user->alamat) }}</textarea>
                            </div>

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
        </main>
    </div>



    <x-image-preview-modal />

    <x-image-zoom-modal />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Init Image Preview
            if (typeof initImagePreview === 'function') {
                initImagePreview('#foto_profil', '#profile_preview_img');
            }

            // Add observer or hack to show the zoom overlay if a new image is confirmed
            const updateCancelBtnVisibility = () => {
                const img = document.getElementById('profile_preview_img');
                const initials = document.getElementById('profile_initials');
                const zoomOverlay = document.getElementById('profile_zoom_overlay');
                const cancelBtn = document.getElementById('profile_cancel_btn');
                const deleteBtn = document.getElementById('profile_delete_btn');
                const removeInput = document.getElementById('remove_foto_profil');
                const input = document.getElementById('foto_profil');

                if (!cancelBtn) return;

                // FAIL-SAFE: If no input or no files selected, MUST HIDE EVERYTHING
                if (!input || !input.files || input.files.length === 0) {
                    cancelBtn.classList.add('hidden');
                    cancelBtn.style.display = 'none'; // FORCE HIDE

                    // Normal state restoration if needed (optional here as main logic handles display)
                    return;
                }

                if (!img) return;

                const newSrc = img.getAttribute('src') || '';
                const initialSrc = img.dataset.initialSrc || '';
                const isRemoved = removeInput?.value === '1';

                // A "new draft" is when we HAVE a data/blob URL 
                // and it's DIFFERENT from the initial one.
                const isNewDraft = newSrc &&
                    newSrc.length > 0 &&
                    newSrc !== initialSrc &&
                    (newSrc.startsWith('data:') || newSrc.startsWith('blob:'));

                if (isNewDraft) {
                    if (initials) initials.classList.add('hidden');
                    if (zoomOverlay) zoomOverlay.classList.remove('hidden');
                    if (img) img.classList.remove('hidden');

                    cancelBtn.classList.remove('hidden');
                    cancelBtn.style.display = 'inline-flex';

                    if (deleteBtn) {
                        deleteBtn.classList.add('hidden');
                        deleteBtn.style.display = 'none';
                    }
                    if (restoreBtn) {
                        restoreBtn.classList.add('hidden');
                        restoreBtn.style.display = 'none';
                    }
                } else {
                    cancelBtn.classList.add('hidden');
                    cancelBtn.style.display = 'none';

                    if (initialSrc && initialSrc.length > 0) {
                        if (isRemoved) {
                            if (deleteBtn) {
                                deleteBtn.classList.add('hidden');
                                deleteBtn.style.display = 'none';
                            }
                            if (restoreBtn) {
                                restoreBtn.classList.remove('hidden');
                                restoreBtn.style.display = 'inline-flex';
                            }
                            if (img) img.classList.add('hidden');
                            if (zoomOverlay) zoomOverlay.classList.add('hidden');
                            if (initials) initials.classList.remove('hidden');
                        } else {
                            if (deleteBtn) {
                                deleteBtn.classList.remove('hidden');
                                deleteBtn.style.display = 'inline-flex';
                            }
                            if (restoreBtn) {
                                restoreBtn.classList.add('hidden');
                                restoreBtn.style.display = 'none';
                            }
                            if (img) img.classList.remove('hidden');
                            if (zoomOverlay) zoomOverlay.classList.remove('hidden');
                            if (initials) initials.classList.add('hidden');
                        }
                    } else {
                        // No initial image and no draft
                        if (img) img.classList.add('hidden');
                        if (zoomOverlay) zoomOverlay.classList.add('hidden');
                        if (initials) initials.classList.remove('hidden');
                        if (deleteBtn) {
                            deleteBtn.classList.add('hidden');
                            deleteBtn.style.display = 'none';
                        }
                        if (restoreBtn) {
                            restoreBtn.classList.add('hidden');
                            restoreBtn.style.display = 'none';
                        }
                    }
                }
            };



            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                        updateCancelBtnVisibility();
                    }
                });
            });

            const targetImg = document.getElementById('profile_preview_img');
            if (targetImg) {
                observer.observe(targetImg, { attributes: true });
                // Initial check
                updateCancelBtnVisibility();
            }

        }

            const deleteBtn = document.getElementById('profile_delete_btn');
        const restoreBtn = document.getElementById('profile_restore_btn');
        const removeInput = document.getElementById('remove_foto_profil');

        if (deleteBtn && removeInput) {
            deleteBtn.addEventListener('click', function () {
                const preview = document.getElementById('profile_preview_img');
                const initials = document.getElementById('profile_initials');
                const zoomOverlay = document.getElementById('profile_zoom_overlay');

                preview.classList.add('hidden');
                initials?.classList.remove('hidden');
                zoomOverlay?.classList.add('hidden');

                deleteBtn.classList.add('hidden');
                deleteBtn.style.display = 'none';
                if (restoreBtn) {
                    restoreBtn.classList.remove('hidden');
                    restoreBtn.style.display = 'inline-flex';
                }
                removeInput.value = '1';

                // Clear file input if any
                const input = document.getElementById('foto_profil');
                const cancelBtn = document.getElementById('profile_cancel_btn');
                if (input) input.value = '';
                if (cancelBtn) {
                    cancelBtn.classList.add('hidden');
                    cancelBtn.style.display = 'none';
                }
            });
        }

        if (restoreBtn) {
            restoreBtn.addEventListener('click', function () {
                const preview = document.getElementById('profile_preview_img');
                const initials = document.getElementById('profile_initials');
                const zoomOverlay = document.getElementById('profile_zoom_overlay');
                const initialSrc = preview.dataset.initialSrc;

                if (initialSrc) {
                    preview.src = initialSrc;
                    preview.classList.remove('hidden');
                    initials?.classList.add('hidden');
                    zoomOverlay?.classList.remove('hidden');
                }

                if (deleteBtn) {
                    deleteBtn.classList.remove('hidden');
                    deleteBtn.style.display = 'inline-flex';
                }
                if (restoreBtn) {
                    restoreBtn.classList.add('hidden');
                    restoreBtn.style.display = 'none';
                }
                removeInput.value = '0';
            });
        }

        // Cancel Selection Logic
        const cancelBtn = document.getElementById('profile_cancel_btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                const input = document.getElementById('foto_profil');
                const preview = document.getElementById('profile_preview_img');
                const initialSrc = preview.dataset.initialSrc || '';

                input.value = '';
                preview.src = initialSrc;

                if (!initialSrc || initialSrc === '') {
                    preview.classList.add('hidden');
                    document.getElementById('profile_initials')?.classList.remove('hidden');
                    document.getElementById('profile_zoom_overlay')?.classList.add('hidden');
                } else {
                    // If we reverted to an existing image, make sure delete btn is visible
                    if (removeInput.value === '0') {
                        if (deleteBtn) {
                            deleteBtn.classList.remove('hidden');
                            deleteBtn.style.display = 'inline-flex';
                        }
                        if (restoreBtn) {
                            restoreBtn.classList.add('hidden');
                            restoreBtn.style.display = 'none';
                        }
                    }
                }
                this.classList.add('hidden');
                this.style.display = 'none';
            });
        }
        });


    </script>
</body>

</html>