<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>403 Forbidden - Library App</title>
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
    class="bg-background-light dark:bg-background-dark font-display text-slate-700 dark:text-gray-200 h-screen w-full flex flex-col items-center justify-center p-6 text-center overflow-hidden">

    <div class="animate-enter flex flex-col items-center max-w-md w-full">
        <!-- Icon/Illustration -->
        <!-- Menggunakan inline style untuk memaksa bentuk lingkaran (override issue CSS lainnya) -->
        <div style="width: 80px; height: 80px;"
            class="rounded-full flex items-center justify-center mb-6 text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/20 border border-red-200 dark:border-red-900/50 shadow-sm shrink-0">
            <span class="material-symbols-outlined text-6xl">lock_person</span>
        </div>

        <h1 class="text-6xl font-bold text-slate-900 dark:text-white mb-2 tracking-tighter">403</h1>
        <h2 class="text-xl font-semibold mb-4 text-slate-800 dark:text-white">Akses Ditolak</h2>

        <p class="text-slate-500 dark:text-gray-400 leading-relaxed mb-8">
            Error 403 - You don't have permission to access this page or resource.
        </p>

        <!-- Actions -->
        <div class="flex justify-center w-full">
            <a href="{{ route('dashboard') }}"
                class="w-full sm:w-64 py-4 rounded-xl bg-primary hover:bg-primary-dark text-white transition-all duration-200 font-bold text-base shadow-lg shadow-primary/25 hover:shadow-primary/40 flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                Ke Dashboard
            </a>
        </div>
    </div>

</body>

</html>