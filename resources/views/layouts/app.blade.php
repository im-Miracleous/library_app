<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Library App')</title>
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

    {{-- Favicon Logic --}}
    @php
        $pengaturan = \App\Models\Pengaturan::first();
    @endphp
    <link rel="icon" type="image/png"
        href="{{ !empty($pengaturan->logo_path) ? asset('storage/' . $pengaturan->logo_path) : 'https://laravel.com/img/favicon/favicon-32x32.png' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
    @stack('styles')
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">

        <div id="mobile-overlay"
            class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component :title="$__env->yieldContent('header-title', 'Dashboard')" />

            <div class="px-4 sm:px-8 pt-5 sm:pt-8 pb-8 flex flex-col gap-8 max-w-[1600px] mx-auto w-full flex-1">
                {{-- Flash Messages --}}
                @if (session('error'))
                    <div class="mb-4 lg:mb-6 p-4 flex items-center gap-3 text-sm font-medium text-red-800 dark:text-red-200 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl animate-enter"
                        role="alert">
                        <span class="material-symbols-outlined text-xl">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 lg:mb-6 p-4 flex items-center justify-between gap-3 text-sm font-medium text-emerald-800 dark:text-emerald-200 bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl animate-enter"
                        role="alert">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-xl">check_circle</span>
                            {{ session('success') }}
                        </div>
                        @if (session('detail_url'))
                            <a href="{{ session('detail_url') }}"
                                class="shrink-0 flex items-center gap-2 px-4 py-2 bg-emerald-200 dark:bg-emerald-800 text-emerald-900 dark:text-emerald-100 rounded-lg text-xs font-bold hover:bg-emerald-300 dark:hover:bg-emerald-700 transition-all active:scale-95 shadow-sm">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                Lihat Detail
                            </a>
                        @endif
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>