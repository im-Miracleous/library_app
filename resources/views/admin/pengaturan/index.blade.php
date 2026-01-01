<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pengaturan - Library App</title>
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

    <link rel="icon" type="image/png"
        href="{{ !empty($pengaturan->logo_path) ? asset('storage/' . $pengaturan->logo_path) : 'https://laravel.com/img/favicon/favicon-32x32.png' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">

        <div id="mobile-overlay"
            class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Pengaturan" />

            <div class="p-4 sm:p-8 flex flex-col gap-6 max-w-[1000px] mx-auto w-full">
                <x-breadcrumb-component parent="Administrator" current="Pengaturan" class="animate-enter" />

                <div class="animate-enter">
                    <h1 class="text-2xl sm:text-3xl font-bold text-primary-dark dark:text-white">Pengaturan Sistem</h1>
                    <p class="text-primary-mid dark:text-white/60 mt-1">Kelola konfigurasi dasar aplikasi perpustakaan.
                    </p>
                </div>

                @if (session('success'))
                    <div class="p-4 text-sm text-green-700 dark:text-green-800 bg-green-100 dark:bg-green-200 rounded-lg animate-enter"
                        role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 text-sm text-red-700 dark:text-red-800 bg-red-100 dark:bg-red-200 rounded-lg animate-enter"
                        role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 sm:p-8 shadow-sm animate-enter delay-100">
                    <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data"
                        class="flex flex-col gap-6">
                        @csrf
                        @method('PUT')

                        <!-- Logo -->
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-primary-dark dark:text-white" for="logo">
                                Logo Perpustakaan
                            </label>

                            @if($pengaturan->logo_path)
                                <div class="mb-2">
                                    <p class="text-xs text-primary-mid dark:text-white/60 mb-2">Logo Saat Ini:</p>
                                    <div class="p-2 bg-background-light dark:bg-background-dark rounded-xl border border-primary/20 dark:border-white/10 w-fit cursor-pointer relative group/logo shadow-sm hover:shadow-md transition-all"
                                        onclick="openZoom()">
                                        <img id="logo_preview_img" src="{{ asset('storage/' . $pengaturan->logo_path) }}"
                                            data-initial-src="{{ asset('storage/' . $pengaturan->logo_path) }}"
                                            alt="Current Logo"
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
                                    class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent dark:hover:file:bg-accent/20 cursor-pointer"
                                    id="logo" name="logo" type="file" accept="image/*" />
                                <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                            </div>
                            <p
                                class="text-xs text-primary-mid dark:text-white/40 mt-1 flex flex-wrap items-center gap-3">
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
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-primary-dark dark:text-white" for="nama_perpustakaan">
                                Nama Perpustakaan
                            </label>
                            <input
                                class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none"
                                id="nama_perpustakaan" name="nama_perpustakaan" type="text"
                                value="{{ old('nama_perpustakaan', $pengaturan->nama_perpustakaan) }}" required />
                            <p class="text-xs text-primary-mid dark:text-white/40">Nama ini akan ditampilkan di halaman
                                login dan judul aplikasi.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <!-- Denda -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-dark dark:text-white" for="denda_per_hari">
                                    Denda Per Hari (Rp)
                                </label>
                                <input
                                    class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none"
                                    id="denda_per_hari" name="denda_per_hari" type="number" min="0" step="0.01"
                                    value="{{ old('denda_per_hari', $pengaturan->denda_per_hari) }}" required />
                            </div>

                            <!-- Denda Rusak -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-dark dark:text-white" for="denda_rusak">
                                    Denda Buku Rusak (Rp)
                                </label>
                                <input
                                    class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none"
                                    id="denda_rusak" name="denda_rusak" type="number" min="0" step="0.01"
                                    value="{{ old('denda_rusak', $pengaturan->denda_rusak) }}" required />
                            </div>

                            <!-- Denda Hilang -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-dark dark:text-white" for="denda_hilang">
                                    Denda Buku Hilang (Rp)
                                </label>
                                <input
                                    class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none"
                                    id="denda_hilang" name="denda_hilang" type="number" min="0" step="0.01"
                                    value="{{ old('denda_hilang', $pengaturan->denda_hilang) }}" required />
                            </div>

                            <!-- Batas Hari -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-dark dark:text-white"
                                    for="batas_peminjaman_hari">
                                    Batas Peminjaman (Hari)
                                </label>
                                <input
                                    class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none"
                                    id="batas_peminjaman_hari" name="batas_peminjaman_hari" type="number" min="1"
                                    value="{{ old('batas_peminjaman_hari', $pengaturan->batas_peminjaman_hari) }}"
                                    required />
                            </div>

                            <!-- Max Buku -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-dark dark:text-white"
                                    for="maksimal_buku_pinjam">
                                    Maks. Buku Dipinjam
                                </label>
                                <input
                                    class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none"
                                    id="maksimal_buku_pinjam" name="maksimal_buku_pinjam" type="number" min="1"
                                    value="{{ old('maksimal_buku_pinjam', $pengaturan->maksimal_buku_pinjam) }}"
                                    required />
                            </div>
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
        </main>
    </div>


    <x-image-preview-modal />

    <x-image-zoom-modal />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Init Image Preview
            if (typeof initImagePreview === 'function') {
                initImagePreview('#logo', '#logo_preview_img');
            }     // Observer to show preview container and zoom overlay for NEW images
            const updateCancelBtnVisibility = () => {
                const img = document.getElementById('logo_preview_img');
                const input = document.getElementById('logo');
                const cancelBtn = document.getElementById('logo_cancel_btn');
                const previewContainer = document.getElementById('logo_preview_container');

                const deleteBtn = document.getElementById('logo_delete_btn');
                const restoreBtn = document.getElementById('logo_restore_btn');
                const removeInput = document.getElementById('remove_logo');
                const isRemoved = removeInput?.value === '1';

                const newSrc = img.getAttribute('src') || '';
                const initialSrc = img.dataset.initialSrc || '';

                // A "new draft" is when we HAVE a file selected AND a data/blob URL 
                // AND it's DIFFERENT from the initial one.
                const isNewDraft = newSrc &&
                    newSrc.length > 0 &&
                    newSrc !== initialSrc &&
                    (newSrc.startsWith('data:') || newSrc.startsWith('blob:'));

                if (isNewDraft) {
                    cancelBtn.classList.remove('hidden');
                    cancelBtn.style.display = 'inline-flex';
                    if (previewContainer) previewContainer.classList.remove('hidden');

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
                            if (previewContainer) previewContainer.classList.add('hidden');
                        } else {
                            if (deleteBtn) {
                                deleteBtn.classList.remove('hidden');
                                deleteBtn.style.display = 'inline-flex';
                            }
                            if (restoreBtn) {
                                restoreBtn.classList.add('hidden');
                                restoreBtn.style.display = 'none';
                            }
                            if (previewContainer) previewContainer.classList.remove('hidden');
                        }
                    } else {
                        // No initial image and no draft
                        if (previewContainer) previewContainer.classList.add('hidden');
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

            // Add listener to input change as well, for double safety
            const logoInput = document.getElementById('logo');
            if (logoInput) {
                logoInput.addEventListener('change', updateCancelBtnVisibility);
                logoInput.addEventListener('input', updateCancelBtnVisibility);
            }

            // Observer to show preview container and zoom overlay for NEW images
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                        updateCancelBtnVisibility();
                    }
                });
            });

            const targetImg = document.getElementById('logo_preview_img');
            if (targetImg) {
                observer.observe(targetImg, { attributes: true });
                // Initial check on load
                updateCancelBtnVisibility();
            }

            // Delete (Draft) Selection Logic
            const deleteBtn = document.getElementById('logo_delete_btn');
            const restoreBtn = document.getElementById('logo_restore_btn');
            const removeInput = document.getElementById('remove_logo');

            if (deleteBtn && removeInput) {
                deleteBtn.addEventListener('click', function () {
                    const preview = document.getElementById('logo_preview_img');
                    const previewContainer = document.getElementById('logo_preview_container');

                    // Hide current logo elements
                    preview.closest('.mb-2').classList.add('hidden');

                    deleteBtn.classList.add('hidden');
                    deleteBtn.style.display = 'none';
                    if (restoreBtn) {
                        restoreBtn.classList.remove('hidden');
                        restoreBtn.style.display = 'inline-flex';
                    }
                    removeInput.value = '1';

                    // Clear file input if any
                    document.getElementById('logo').value = '';
                    document.getElementById('logo_cancel_btn').classList.add('hidden');
                    if (previewContainer) previewContainer.classList.add('hidden');
                });
            }

            if (restoreBtn) {
                restoreBtn.addEventListener('click', function () {
                    const preview = document.getElementById('logo_preview_img');

                    // Show current logo elements
                    preview.closest('.mb-2').classList.remove('hidden');

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

            // Cancel Button Logic
            const cancelBtn = document.getElementById('logo_cancel_btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    const input = document.getElementById('logo');
                    const preview = document.getElementById('logo_preview_img');
                    const initialSrc = preview.dataset.initialSrc || '';

                    input.value = '';
                    preview.src = initialSrc;

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
                    this.classList.add('hidden');
                });
            }
        }
        });


    </script>
</body>

</html>