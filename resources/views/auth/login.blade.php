<!DOCTYPE html>
<html class="dark" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login - Library App</title>

    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body
    class="bg-background-light dark:bg-background-dark font-display antialiased min-h-screen flex flex-col transition-colors duration-300">
    <div class="flex flex-1 w-full h-screen overflow-hidden">

        <!-- Bagian Kiri: Gambar -->
        <div
            class="hidden lg:flex w-1/2 relative flex-col justify-between bg-background-dark p-12 text-white animate-fade">
            <div class="absolute inset-0 z-0 overflow-hidden">
                <img class="h-full w-full object-cover opacity-30 hover:scale-105 transition-transform duration-[2000ms]"
                    src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2228&auto=format&fit=crop" 
                    alt="Library Background" />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/80 to-background-dark/40">
                </div>
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-accent/20 blur-[120px] rounded-full pointer-events-none animate-pulse">
                </div>
            </div>

            <div class="relative z-10 flex items-center gap-3 animate-enter delay-100">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-accent/20 text-accent border border-accent/30">
                    <span class="material-symbols-outlined text-2xl">local_library</span>
                </div>
                <h2 class="text-xl font-bold tracking-wide">Library App</h2>
            </div>

            <div class="relative z-10 max-w-lg mb-12 animate-enter delay-200">
                <h1 class="text-5xl font-bold leading-tight mb-6 tracking-tight">
                    Sistem Manajemen <br />
                    <span class="text-accent">Perpustakaan Modern.</span>
                </h1>
                <p class="text-lg text-gray-400 font-light leading-relaxed">
                    Akses katalog lengkap, kelola keanggotaan, dan analisis tren peminjaman.
                </p>
            </div>
            <div class="relative z-10 text-xs text-gray-500 font-mono animate-enter delay-300">
                © 2025 Library App System v1.0
            </div>
        </div>

        <!-- Bagian Kanan: Form Login -->
        <div
            class="flex w-full lg:w-1/2 flex-col justify-center items-center px-6 py-12 relative bg-background-light dark:bg-background-dark">
            <div class="w-full max-w-[420px] flex flex-col">
                <div class="mb-10 text-center lg:text-left animate-enter delay-100">
                    <h2
                        class="text-3xl lg:text-[32px] font-bold leading-tight tracking-tight dark:text-white text-slate-900 mb-3">
                        Selamat Datang
                    </h2>
                    <p class="text-base font-normal leading-normal text-slate-500 dark:text-gray-400">
                        Silakan masuk menggunakan akun Anda.
                    </p>
                </div>

                <!-- Pesan Error Global -->
                @if (session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800 animate-enter"
                        role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800 animate-enter"
                        role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-5 animate-enter delay-200">
                    @csrf

                    <!-- Input Email -->
                    <div class="flex flex-col gap-2">
                        <label class="text-base font-medium leading-normal dark:text-white text-slate-900"
                            for="email">Email Address</label>
                        <div class="relative group input-focus-effect">
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl h-14 p-[15px] text-base font-normal leading-normal bg-white dark:bg-input-dark text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200 dark:border-border-dark' }} focus:outline-0 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                                id="email" name="email" placeholder="nama@email.com" type="email"
                                value="{{ old('email') }}" required autofocus />
                            <div
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 pointer-events-none transition-colors group-focus-within:text-primary">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div class="flex flex-col gap-2">
                        <label class="text-base font-medium leading-normal dark:text-white text-slate-900"
                            for="password">Password</label>
                        <div class="relative group input-focus-effect">
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl h-14 p-[15px] pr-12 text-base font-normal leading-normal bg-white dark:bg-input-dark text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 border border-gray-200 dark:border-border-dark focus:outline-0 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                                id="password" name="password" placeholder="••••••••" type="password" required />

                            <!-- Toggle Button (Class toggle-password ditangani script.js) -->
                            <button type="button"
                                class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 hover:text-primary transition-colors cursor-pointer">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        <div class="flex justify-end mt-1">
                            <a class="text-sm font-semibold text-primary hover:text-primary/80 dark:text-accent dark:hover:text-accent/80 transition-colors"
                                href="#">
                                Lupa Password?
                            </a>
                        </div>
                    </div>

                    <button type="submit"
                        class="cursor-pointer mt-6 flex w-full items-center justify-center rounded-full bg-primary h-14 text-white text-base font-bold leading-normal hover:bg-[#563b2a] active:scale-[0.95] transition-all duration-200 shadow-[0_4px_14px_0_rgba(111,78,55,0.39)] hover:shadow-[0_6px_20px_rgba(111,78,55,0.23)]">
                        Masuk
                        <span class="material-symbols-outlined ml-2 text-xl">arrow_forward</span>
                    </button>
                </form>

                <div
                    class="mt-10 pt-6 border-t border-slate-200 dark:border-border-dark flex flex-col items-center gap-4 w-full animate-enter delay-300">
                    <p class="text-slate-500 dark:text-gray-400 text-sm">Belum memiliki akun?</p>
                    <a href="#"
                        class="px-6 py-2.5 rounded-full border border-slate-300 dark:border-border-dark text-slate-700 dark:text-white text-sm font-medium hover:bg-slate-50 dark:hover:bg-input-dark transition-colors w-full text-center sm:w-auto hover:scale-105 active:scale-95 duration-200">
                        Hubungi Administrator
                    </a>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
