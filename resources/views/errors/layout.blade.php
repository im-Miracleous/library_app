<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title') - Library App</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    <!-- Scripts & Styles -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-slate-50 dark:bg-[#0f172a] font-display text-slate-700 dark:text-gray-200 h-screen w-full flex flex-col items-center justify-center p-6 text-center overflow-hidden relative selection:bg-rose-500/30">

    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-blue-500/10 blur-3xl dark:bg-blue-500/5"></div>
        <div class="absolute top-[40%] -left-[10%] w-[40%] h-[40%] rounded-full bg-purple-500/10 blur-3xl dark:bg-purple-500/5"></div>
        <div class="absolute -bottom-[20%] right-[20%] w-[40%] h-[40%] rounded-full bg-rose-500/10 blur-3xl dark:bg-rose-500/5"></div>
    </div>

    <div class="relative z-10 animate-enter flex flex-col items-center max-w-lg w-full">
        <!-- Icon/Illustration -->
        <div class="mb-8 relative group">
            <div class="absolute inset-0 bg-gradient-to-tr from-rose-500 to-orange-500 rounded-full blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-500"></div>
            @yield('image')
        </div>

        <!-- Error Code -->
        <h1 class="text-7xl font-bold bg-gradient-to-r from-slate-900 via-slate-700 to-slate-900 dark:from-white dark:via-slate-200 dark:to-white bg-clip-text text-transparent mb-2 tracking-tighter">
            @yield('code')
        </h1>

        <!-- Title -->
        <h2 class="text-2xl font-bold mb-4 text-slate-800 dark:text-white">
            @yield('message_title')
        </h2>

        <!-- Description -->
        <p class="text-slate-500 dark:text-gray-400 leading-relaxed mb-10 max-w-md mx-auto">
            @yield('message')
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
            <a href="{{ url()->previous() }}"
                class="px-8 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition-all duration-200 font-semibold text-sm flex items-center justify-center gap-2 group">
                <span class="material-symbols-outlined text-[20px] group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                Kembali
            </a>

            <a href="{{ route('dashboard') }}"
                class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-orange-600 hover:from-rose-500 hover:to-orange-500 text-white transition-all duration-200 font-semibold text-sm shadow-lg shadow-rose-500/25 hover:shadow-rose-500/40 flex items-center justify-center gap-2 hover:-translate-y-0.5">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                Ke Dashboard
            </a>
        </div>
    </div>

</body>

</html>
