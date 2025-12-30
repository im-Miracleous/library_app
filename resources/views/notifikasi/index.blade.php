<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifikasi - Library App</title>
    <link rel="icon" type="image/png" href="https://laravel.com/img/favicon/favicon-32x32.png">
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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">

            <x-header-component title="Notifikasi" />

            <div class="p-4 sm:p-8 max-w-4xl mx-auto w-full">

                <!-- Header Section -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-primary-dark dark:text-white">Semua Notifikasi</h2>

                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifikasi.readAll') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-surface-dark border border-gray-200 dark:border-gray-700 rounded-xl text-primary font-bold text-xs hover:bg-gray-50 dark:hover:bg-white/5 transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-lg">done_all</span>
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif
                </div>

                @if(session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/10 dark:border-border-dark overflow-hidden shadow-sm">
                    @forelse($notifications as $notification)
                        <div
                            class="p-4 border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors flex gap-4 {{ $notification->read_at ? 'opacity-70' : 'bg-blue-50/50 dark:bg-blue-900/10' }}">
                            <div class="shrink-0 mt-1">
                                @if($notification->data['type'] == 'warning')
                                    <div
                                        class="size-10 rounded-full bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                        <span class="material-symbols-outlined">warning</span>
                                    </div>
                                @elseif($notification->data['type'] == 'info')
                                    <div
                                        class="size-10 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <span class="material-symbols-outlined">info</span>
                                    </div>
                                @else
                                    <div
                                        class="size-10 rounded-full bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center text-gray-600 dark:text-gray-400">
                                        <span class="material-symbols-outlined">notifications</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-gray-900 dark:text-white mb-1">
                                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                                        @if(!$notification->read_at)
                                            <span class="ml-2 inline-block size-2 bg-red-500 rounded-full"></span>
                                        @endif
                                    </h4>
                                    <span
                                        class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 leading-relaxed">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>

                                <div class="flex gap-3">
                                    @if(isset($notification->data['link']))
                                        <form action="{{ route('notifikasi.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs font-bold text-primary hover:text-primary-dark dark:text-accent dark:hover:text-white flex items-center gap-1">
                                                Lihat Detail
                                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifikasi.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div
                                class="inline-flex items-center justify-center size-16 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 mb-4">
                                <span class="material-symbols-outlined text-3xl">notifications_off</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Tidak ada notifikasi</h3>
                            <p class="text-gray-500 dark:text-gray-400">Anda belum memiliki notifikasi apapun saat ini.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>

            </div>
        </main>
    </div>
</body>

</html>