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
                                    @if($user->foto_profil)
                                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Profile Photo"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover/image:scale-110">
                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover/image:bg-black/20 transition-colors flex items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-white opacity-0 group-hover/image:opacity-100 transition-opacity">zoom_in</span>
                                        </div>
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-primary/10 dark:bg-accent/10 text-primary dark:text-accent font-bold text-4xl">
                                            {{ substr($user->nama, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <label for="foto_profil"
                                    class="absolute bottom-0 right-0 p-2 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-full cursor-pointer hover:scale-110 transition-transform shadow-md"
                                    title="Ubah Foto">
                                    <span class="material-symbols-outlined text-lg">photo_camera</span>
                                </label>
                                <input type="file" id="foto_profil" name="foto_profil" class="hidden" accept="image/*"
                                    onchange="previewImage(this)">
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="font-bold text-primary-dark dark:text-white text-lg">{{ $user->nama }}</h3>
                                <p class="text-sm text-slate-500 dark:text-white/50 mb-3">{{ $user->email }}</p>
                                <p class="text-xs text-slate-400 dark:text-white/40 italic">
                                    Role: <span class="uppercase font-semibold tracking-wide">{{ $user->peran }}</span>
                                </p>
                                <p class="text-xs text-slate-400 dark:text-white/40 mt-2">
                                    Upload foto JPG, PNG, atau GIF. Maksimal 2MB.
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

    <!-- Zoom Modal -->
    <div id="zoomModal" onclick="closeZoom()"
        class="fixed inset-0 z-[60] hidden bg-black/90 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none">
        <div class="relative max-w-4xl max-h-[90vh] w-full flex items-center justify-center">
            <button class="absolute -top-12 right-0 text-white/70 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
            <img id="zoomedImage" src=""
                class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300">
        </div>
    </div>

    <!-- Crop Modal -->
    <div id="cropModal"
        class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-surface-dark rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div
                class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-[#1A1410]">
                <h3 class="font-bold text-lg text-primary-dark dark:text-white">Sesuaikan Foto Profil</h3>
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
        const inputImage = document.getElementById('foto_profil');
        const modal = document.getElementById('cropModal');
        const imageToCrop = document.getElementById('imageToCrop');
        const cropButton = document.getElementById('cropButton');
        const previewContainer = document.querySelector('.relative.group .size-24'); // Target wrapper image preview

        function openCropModal() {
            modal.classList.remove('hidden');
        }

        function closeCropModal() {
            modal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            // Reset input if cancelled (optional, but good UX if user cancels crop they might want to re-select)
            // But if we reset, we lose the file selection. Let's keep it but maybe not reset.
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
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                    });
                };

                reader.readAsDataURL(file);
            }
        });

        cropButton.addEventListener('click', function () {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                });

                canvas.toBlob(function (blob) {
                    // Update File Input with Cropped Blob
                    const dataTransfer = new DataTransfer();
                    const file = new File([blob], "profile_cropped.jpg", { type: "image/jpeg" });
                    dataTransfer.items.add(file);
                    inputImage.files = dataTransfer.files;

                    // Update UI Preview
                    // Remove old existing content in preview container
                    previewContainer.innerHTML = '';
                    const imgPreview = document.createElement('img');
                    imgPreview.src = canvas.toDataURL(); // Use dataURL for immediate preview
                    imgPreview.className = 'w-full h-full object-cover';

                    // Re-add hover effect structure if needed, or just simpify for now. 
                    // The user wants zoom on the NEW image too? Yes.
                    // So we should reconstruct the hover/zoom structure dynamically?
                    // Or just replacing innerHTML clears the 'zoom_in' icon.
                    // Let's bring back the structure:
                    // <img ...>
                    // <div absolute ...><span>zoom_in</span></div>

                    const hoverDiv = document.createElement('div');
                    hoverDiv.className = 'absolute inset-0 bg-black/0 group-hover/image:bg-black/20 transition-colors flex items-center justify-center';
                    hoverDiv.innerHTML = '<span class="material-symbols-outlined text-white opacity-0 group-hover/image:opacity-100 transition-opacity">zoom_in</span>';

                    previewContainer.appendChild(imgPreview);
                    previewContainer.appendChild(hoverDiv);

                    closeCropModal();
                }, 'image/jpeg');
            }
        });

        // Zoom Logic
        const zoomModal = document.getElementById('zoomModal');
        const zoomedImage = document.getElementById('zoomedImage');

        function openZoom() {
            // Find current image src
            const currentImg = previewContainer.querySelector('img');
            if (currentImg && currentImg.src) {
                zoomedImage.src = currentImg.src;
                zoomModal.classList.remove('hidden');
                // Small delay to allow display flex to apply before opacity transition
                setTimeout(() => {
                    zoomModal.classList.remove('opacity-0', 'pointer-events-none');
                    zoomedImage.classList.remove('scale-95');
                    zoomedImage.classList.add('scale-100');
                }, 10);
            }
        }

        function closeZoom() {
            zoomModal.classList.add('opacity-0', 'pointer-events-none');
            zoomedImage.classList.remove('scale-100');
            zoomedImage.classList.add('scale-95');
            setTimeout(() => {
                zoomModal.classList.add('hidden');
            }, 300);
        }
    </script>
</body>

</html>