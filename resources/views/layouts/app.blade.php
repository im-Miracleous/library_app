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

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/global-search.js'])
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
                {{-- Flash Messages (Notification Toast) --}}
                @if (session('error'))
                    <x-notification type="error" :message="session('error')" class="top-24" />
                @endif

                @if (session('success'))
                    <x-notification type="success" :message="session('success')" :detailUrl="session('detail_url')" class="top-24" />
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        @php
                            // Stack errors below the top-24 (6rem). 
                            // If session error exists, we might overlap, but usually validation errors come from form submit without session error.
                            // We start at top-24 (6rem). Each card approx 5-6rem height + gap.
                            $top = 6 + ($loop->index * 7);
                        @endphp
                        <x-notification type="error" :message="$error" style="top: {{ $top }}rem;" />
                    @endforeach
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>