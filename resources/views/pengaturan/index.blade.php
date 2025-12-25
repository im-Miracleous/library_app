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
            <header
                class="animate-enter flex items-center justify-between sticky top-0 bg-surface/90 dark:bg-background-dark/95 backdrop-blur-sm z-30 px-4 sm:px-8 py-4 border-b border-primary/20 dark:border-border-dark">

                <div class="flex items-center gap-4">
                    <button id="open-sidebar"
                        class="lg:hidden text-primary-dark dark:text-white hover:text-primary dark:hover:text-accent transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-3xl">menu</span>
                    </button>

                    <h2 class="text-primary-dark dark:text-white text-xl sm:text-2xl font-bold tracking-tight">
                        Pengaturan
                    </h2>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <button onclick="toggleTheme()"
                        class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] shadow-sm border border-primary/20 dark:border-transparent cursor-pointer">
                        <span id="theme-icon" class="material-symbols-outlined text-[20px]">dark_mode</span>
                    </button>

                    <div class="hidden sm:flex flex-col items-end mr-2 cursor-default">
                        <span
                            class="text-primary-dark dark:text-white text-sm font-bold">{{ Auth::user()->nama }}</span>
                        <span
                            class="text-primary dark:text-accent text-xs uppercase tracking-wider font-bold">{{ Auth::user()->id_pengguna }}</span>
                    </div>

                    <button
                        class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] transition-all relative shadow-sm border border-primary/20 dark:border-transparent cursor-default">
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                </div>
            </header>

            <div class="p-4 sm:p-8 flex flex-col gap-8 max-w-[1000px] mx-auto w-full">

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
</body>

</html>