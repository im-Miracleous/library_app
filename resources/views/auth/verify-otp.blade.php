<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Verifikasi OTP - {{ $pengaturan->nama_perpustakaan ?? 'Perpustakaan' }}</title>

    <link rel="icon" type="image/png"
        href="{{ !empty($pengaturan->logo_path) ? asset('storage/' . $pengaturan->logo_path) : 'https://laravel.com/img/favicon/favicon-32x32.png' }}">

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

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display antialiased min-h-screen flex flex-col transition-colors duration-300">
    <div class="flex flex-1 w-full h-screen overflow-hidden">

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
                @if(!empty($pengaturan->logo_path))
                    <div
                        class="h-10 w-10 flex items-center justify-center rounded-xl bg-white overflow-hidden border border-white/20 shadow-sm">
                        <img src="{{ asset('storage/' . $pengaturan->logo_path) }}" alt="Logo"
                            class="w-full h-full object-contain p-1">
                    </div>
                @else
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-xl bg-accent/20 text-accent border border-accent/30">
                        <span class="material-symbols-outlined text-2xl">local_library</span>
                    </div>
                @endif
                <h2 class="text-xl font-bold tracking-wide">{{ $pengaturan->nama_perpustakaan ?? 'Library App' }}</h2>
            </div>

            <div class="relative z-10 max-w-lg mb-12 animate-enter delay-200">
                <h1 class="text-5xl font-bold leading-tight mb-6 tracking-tight">
                    Keamanan Akun <br />
                    <span class="text-accent">Terjamin & Aman.</span>
                </h1>
                <p class="text-lg text-gray-400 font-light leading-relaxed">
                    Kami memastikan data Anda tetap aman. Verifikasi identitas Anda untuk melanjutkan akses.
                </p>
            </div>
            <div class="relative z-10 text-xs text-gray-500 font-mono animate-enter delay-300">
                © 2025 {{ $pengaturan->nama_perpustakaan ?? 'Library App System' }} v1.0
            </div>
        </div>

        <div
            class="flex w-full lg:w-1/2 flex-col justify-center items-center px-6 py-12 relative bg-background-light dark:bg-background-dark transition-colors duration-300">

            <div class="absolute top-6 right-6">
                <button onclick="toggleTheme()"
                    class="cursor-pointer flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-slate-600 dark:text-white hover:bg-gray-100 dark:hover:bg-[#36271F] transition-all shadow-sm border border-gray-200 dark:border-transparent">
                    <span id="theme-icon" class="material-symbols-outlined text-[20px]">dark_mode</span>
                </button>
            </div>

            <div class="w-full max-w-[420px] flex flex-col">
                <div class="mb-8 text-center animate-enter delay-100">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 dark:bg-accent/10 mb-6 text-primary dark:text-accent">
                        <span class="material-symbols-outlined text-3xl">lock_open</span>
                    </div>
                    <h2
                        class="text-3xl lg:text-[32px] font-bold leading-tight tracking-tight dark:text-white text-slate-900 mb-3">
                        Verifikasi OTP
                    </h2>
                    <p class="text-base font-normal leading-normal text-slate-500 dark:text-gray-400">
                        Kode 6 digit telah dikirim ke email Anda.<br>Silakan masukkan kode tersebut di bawah ini.
                    </p>
                </div>

                @if (session('error'))
                    <div class="mb-6 p-4 flex items-center gap-3 text-sm text-red-700 dark:text-red-200 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl animate-enter"
                        role="alert">
                        <span class="material-symbols-outlined text-xl">error</span>
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="mb-6 p-4 flex items-center gap-3 text-sm text-green-700 dark:text-green-200 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl animate-enter"
                        role="alert">
                        <span class="material-symbols-outlined text-xl">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 p-4 flex items-center gap-3 text-sm text-red-700 dark:text-red-200 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl animate-enter"
                        role="alert">
                        <span class="material-symbols-outlined text-xl">error</span>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="otpForm" action="{{ route('otp.action', $id) }}" method="POST"
                    class="flex flex-col gap-6 animate-enter delay-200" onsubmit="return handleOtpSubmit(this)">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label class="text-base font-medium leading-normal dark:text-white text-slate-900 text-center"
                            for="otp">
                            Kode OTP (6 Digit)
                        </label>
                        <div class="relative group input-focus-effect">
                            <input
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl h-16 p-[15px] text-center text-2xl font-bold tracking-[0.5em] bg-white dark:bg-input-dark text-slate-900 dark:text-white placeholder:text-slate-300 dark:placeholder:text-slate-600 border border-gray-200 dark:border-border-dark focus:outline-0 focus:ring-2 focus:ring-primary dark:focus:ring-primary/50 focus:border-primary transition-all duration-200"
                                id="otp" name="otp" placeholder="••••••" type="text" inputmode="numeric"
                                pattern="[0-9]*" maxlength="6" required autofocus autocomplete="one-time-code"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />

                            <div
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 pointer-events-none transition-colors group-focus-within:text-primary">
                                <span class="material-symbols-outlined">key</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="cursor-pointer mt-2 flex w-full items-center justify-center rounded-full bg-primary-dark dark:bg-primary h-14 text-white dark:text-primary-dark text-base font-bold leading-normal hover:bg-[#563b2a] dark:hover:brightness-110 dark:hover:text-white active:scale-[0.95] transition-all duration-200 shadow-md hover:shadow-lg">
                        Verifikasi
                        <span class="material-symbols-outlined ml-2 text-xl">check</span>
                    </button>
                </form>

                <div
                    class="mt-8 pt-6 border-t border-gray-200 dark:border-border-dark flex flex-col items-center gap-4 w-full animate-enter delay-300">
                    <p class="text-slate-500 dark:text-gray-400 text-sm">Tidak menerima kode?</p>

                    <form action="{{ route('otp.resend', $id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="text-primary hover:text-primary-dark dark:text-accent dark:hover:text-accent/80 font-semibold text-sm transition-colors hover:underline">
                            Kirim Ulang OTP
                        </button>
                    </form>

                    <a href="{{ route('login') }}"
                        class="mt-2 flex items-center text-sm text-slate-500 dark:text-gray-400 hover:text-slate-800 dark:hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-lg mr-1">arrow_back</span>
                        Kembali ke Login
                    </a>
                </div>

            </div>
        </div>
    </div>
    <script>
        function handleOtpSubmit(form) {
            const btn = form.querySelector('button[type="submit"]');

            // Prevent double submit if already disabled
            if (btn.disabled) return false;

            // Apply loading state
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');

            // Replace content with Spinner + Text
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="inline-block">Memproses...</span>
            `;

            return true;
        }
    </script>
</body>

</html>