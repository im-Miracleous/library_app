<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Lupa Password - Library App</title>
    <link rel="icon" type="image/png" href="https://laravel.com/img/favicon/favicon-32x32.png">

    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        /* Standalone minimal CSS reset if Vite not running properly */
        .animate-enter {
            animation: enter 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        @keyframes enter {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display antialiased min-h-screen flex flex-col transition-colors duration-300">
    <div class="flex flex-1 w-full h-screen overflow-hidden">

        <!-- Left Side -->
        <div
            class="hidden lg:flex w-1/2 relative flex-col justify-between bg-background-dark p-12 text-white animate-fade">
            <div class="absolute inset-0 z-0 overflow-hidden">
                <img class="h-full w-full object-cover opacity-30"
                    src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2228&auto=format&fit=crop"
                    alt="Library Background" />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/80 to-background-dark/40">
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
                    Reset Password
                </h1>
                <p class="text-lg text-gray-400 font-light leading-relaxed">
                    Jangan khawatir, kembalikan akses akun Anda dengan mudah.
                </p>
            </div>
            <div class="relative z-10 text-xs text-gray-500 font-mono animate-enter delay-300">
                © 2025 Library App System
            </div>
        </div>

        <!-- Right Side (Form) -->
        <div
            class="flex w-full lg:w-1/2 flex-col justify-center items-center px-6 py-12 relative bg-background-light dark:bg-background-dark transition-colors duration-300">

            <!-- Theme Toggle -->
            <div class="absolute top-6 right-6">
                <button onclick="toggleTheme()"
                    class="cursor-pointer flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-slate-600 dark:text-white hover:bg-gray-100 dark:hover:bg-[#36271F] transition-all shadow-sm border border-gray-200 dark:border-transparent">
                    <span id="theme-icon" class="material-symbols-outlined text-[20px]">dark_mode</span>
                </button>
            </div>

            <div class="w-full max-w-[420px] flex flex-col">
                <div class="mb-10 text-center lg:text-left animate-enter delay-100">
                    <h2
                        class="text-3xl lg:text-[32px] font-bold leading-tight tracking-tight dark:text-white text-slate-900 mb-3">
                        Lupa Password?
                    </h2>
                    <p class="text-base font-normal leading-normal text-slate-500 dark:text-gray-400">
                        Masukkan email terdaftar, kami akan kirimkan link reset.
                    </p>
                </div>

                @if (session('status'))
                    <div
                        class="mb-4 p-4 text-sm text-green-700 dark:text-green-800 bg-green-100 dark:bg-green-200 rounded-lg animate-enter">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-4 p-4 text-sm text-red-700 dark:text-red-800 bg-red-100 dark:bg-red-200 rounded-lg animate-enter">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}"
                    class="flex flex-col gap-5 animate-enter delay-200">
                    @csrf
                    <!-- Email -->
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" required autofocus
                            class="w-full px-4 py-3 rounded-xl bg-white dark:bg-surface-dark border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white placeholder-slate-400 focus:border-accent focus:ring-1 focus:ring-accent transition-all outline-none"
                            placeholder="nama@email.com" />
                    </div>

                    <button type="submit"
                        class="cursor-pointer mt-2 flex w-full items-center justify-center rounded-full bg-primary-dark dark:bg-primary h-14 text-white dark:text-primary-dark text-base font-bold leading-normal hover:bg-[#563b2a] dark:hover:brightness-110 dark:hover:text-white active:scale-[0.95] transition-all duration-200 shadow-md hover:shadow-lg">
                        Kirim Link Reset
                        <span class="material-symbols-outlined ml-2 text-xl">send</span>
                    </button>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold text-slate-500 hover:text-accent dark:text-gray-400 dark:hover:text-white transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>