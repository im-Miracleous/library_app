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
                                    <div
                                        class="p-2 bg-background-light dark:bg-background-dark rounded-xl border border-primary/20 dark:border-white/10 w-fit">
                                        <img src="{{ asset('storage/' . $pengaturan->logo_path) }}" alt="Current Logo"
                                            class="h-16 w-auto object-contain">
                                    </div>
                                </div>
                            @endif

                            <div class="relative group">
                                <input
                                    class="w-full p-3 rounded-xl bg-background-light dark:bg-[#261C16] border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:border-transparent transition-all outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent dark:hover:file:bg-accent/20 cursor-pointer"
                                    id="logo" name="logo" type="file" accept="image/*" />
                            </div>
                            <p class="text-xs text-primary-mid dark:text-white/40">Format: PNG, JPG, JPEG. Maks: 2MB.
                                Biarkan kosong jika tidak ingin mengubah.</p>
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

                        <div class="flex justify-end pt-4">
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
    <!-- Crop Modal -->
    <div id="cropModal"
        class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-surface-dark rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div
                class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-[#1A1410]">
                <h3 class="font-bold text-lg text-primary-dark dark:text-white">Sesuaikan Logo Perpustakaan</h3>
                <button type="button" onclick="closeCropModal()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 p-4 bg-black/50 overflow-hidden flex items-center justify-center relative min-h-[300px]">
                <img id="imageToCrop" src="" alt="Crop Preview" class="max-w-full max-h-[60vh] block">
            </div>

            <div
                class="p-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 bg-white dark:bg-surface-dark">
                <button type="button" onclick="closeCropModal()"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    Batal
                </button>
                <button type="button" id="cropButton"
                    class="px-5 py-2.5 rounded-xl bg-primary dark:bg-accent text-white dark:text-primary-dark font-bold text-sm hover:brightness-110 shadow-lg shadow-primary/20 dark:shadow-accent/20 transition-all transform active:scale-95">
                    Potong & Simpan
                </button>
            </div>
        </div>
    </div>

    <script>
        let cropper;
        const inputImage = document.getElementById('logo');
        const modal = document.getElementById('cropModal');
        const imageToCrop = document.getElementById('imageToCrop');
        const cropButton = document.getElementById('cropButton');
        // Preview container is the div with border that contains the img
        // Since it's inside an if block, it might not exist initially if no logo.
        // But we can check if it exists or create/update a preview area.
        // For simplicity, let's look for the img tag inside the existing preview div if it exists.

        function openCropModal() {
            modal.classList.remove('hidden');
        }

        function closeCropModal() {
            modal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }

        inputImage.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();

                reader.onload = function (event) {
                    imageToCrop.src = event.target.result;
                    openCropModal();

                    // Init Cropper
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(imageToCrop, {
                        // For Logo, maybe we don't force 1:1? Or maybe yes. 
                        // Usually logos are flexible. Let's keep it free ratio or maybe 1:1 if requested "cropping".
                        // User just said "Crop", didn't specify ratio. But profile was 1:1. 
                        // Let's use NaN (Free) or 16:9? Let's use standard free crop since logos vary.
                        // But wait, user asked "implementasi SAAT saya mengupload...".
                        // Let's assume user wants to crop useful parts.
                        // I'll leave aspectRatio: NaN (Free) which is default if not set? 
                        // No, let's set it to NaN explicitly to be sure.
                        aspectRatio: NaN,
                        viewMode: 1,
                        autoCropArea: 0.8,
                        responsive: true,
                    });
                };

                reader.readAsDataURL(file);
            }
        });

        cropButton.addEventListener('click', function () {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    // maxWidth: 1024,
                    // maxHeight: 1024,
                });

                canvas.toBlob(function (blob) {
                    // Update File Input with Cropped Blob
                    const dataTransfer = new DataTransfer();
                    const file = new File([blob], "logo_cropped.png", { type: "image/png" });
                    dataTransfer.items.add(file);
                    inputImage.files = dataTransfer.files;

                    // Update UI Preview
                    // Find or Create preview container
                    let previewDiv = document.querySelector('img[alt="Current Logo"]')?.parentElement;

                    if (!previewDiv) {
                        // If no existing preview (first time upload), we might need to insert it.
                        // Looking at the blade, the preview div is inside an if($pengaturan->logo_path).
                        // If that's false, the div is not there.
                        // We can create a temporary preview if needed, or just rely on the filename in input (default browser behavior).
                        // But seeing the result is nice.
                        // Let's just create a dynamic preview below the label if needed.
                        // But for now, ensuring the file input has the cropped file is the most important part.
                    } else {
                        // If existing, update the src
                        const img = previewDiv.querySelector('img');
                        if (img) img.src = canvas.toDataURL();
                    }

                    closeCropModal();
                }, 'image/png');
            }
        });
    </script>
</body>

</html>