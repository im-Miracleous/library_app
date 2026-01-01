@props(['title' => 'Overview'])

@php
    $pendingVerificationCount = 0;
    if (Auth::check() && in_array(Auth::user()->peran, ['admin', 'petugas', 'owner'])) {
        $pendingVerificationCount = \App\Models\Peminjaman::where('status_transaksi', 'menunggu_verifikasi')->count();
    }
@endphp

<header
    class="animate-enter flex items-center justify-between gap-4 lg:gap-8 sticky top-0 bg-surface/90 dark:bg-background-dark/95 backdrop-blur-sm z-30 px-4 sm:px-8 py-4 border-b border-primary/20 dark:border-border-dark">

    <div class="flex items-center w-auto xl:w-[280px] min-w-0 shrink">

        <!-- Mobile Actions Group -->
        <div class="flex items-center gap-3 mr-6 pt-1 lg:hidden flex-shrink-0">
            <button id="open-sidebar"
                class="flex items-center justify-center text-primary-dark dark:text-white hover:text-primary dark:hover:text-accent transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-3xl leading-none">menu</span>
            </button>

            <button
                class="md:hidden flex items-center justify-center text-primary-dark dark:text-white hover:text-primary dark:hover:text-accent transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-2xl leading-none">search</span>
            </button>
        </div>

        <h2 class="text-primary-dark dark:text-white text-xl sm:text-2xl font-bold tracking-tight truncate">{{ $title }}
        </h2>
    </div>

    <div class="flex-1 max-w-xl px-4 lg:px-8 mx-4 hidden md:flex justify-end lg:justify-center min-w-[320px]">
        @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center text-sm font-medium text-slate-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white">
                            <span class="material-symbols-outlined text-lg mr-1">home</span>
                            Beranda
                        </a>
                    </li>
                    @foreach($breadcrumbs as $crumb)
                        <li>
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-slate-400 text-lg">chevron_right</span>
                                <span
                                    class="ml-1 text-sm font-medium text-slate-500 dark:text-gray-400 md:ml-2">{{ $crumb }}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </nav>
        @else
            <div class="relative group input-focus-effect w-full">
                <div
                    class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-primary-mid dark:text-accent">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input id="global-search-input"
                    class="block w-full p-3 pl-12 text-sm text-primary-dark dark:text-white bg-white dark:bg-surface-dark border-none rounded-full placeholder-primary-mid/60 dark:placeholder-white/40 focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:bg-white dark:focus:bg-[#36271F] transition-all shadow-sm dark:shadow-none"
                    placeholder="Cari buku, ISBN, atau anggota..." type="text" autocomplete="off" />

                <!-- Global Search Results Container -->
                <div id="global-search-results"
                    class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-surface-dark rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden z-50 hidden max-h-[400px] overflow-y-auto">
                    <!-- Results injected via JS -->
                </div>
            </div>
        @endif
    </div>

    <div class="flex items-center justify-end gap-3 sm:gap-4 w-auto xl:w-[280px] shrink-0 pl-2">

        <button onclick="toggleTheme()"
            class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] shadow-sm border border-primary/20 dark:border-transparent cursor-pointer transition-all shrink-0">
            <span id="theme-icon" class="material-symbols-outlined text-[20px]">dark_mode</span>
        </button>

        <!-- Notification Dropdown -->
        <div class="relative" id="notification-container">
            <button onclick="toggleNotificationDropdown()"
                class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] transition-all duration-500 relative shadow-sm border border-primary/20 dark:border-transparent cursor-pointer shrink-0">
                <span class="material-symbols-outlined">notifications</span>

                @if(auth()->user()->unreadNotifications->count() > 0 || $pendingVerificationCount > 0)
                    <span
                        class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-white dark:border-surface-dark {{ $pendingVerificationCount > 0 ? 'animate-ping' : 'animate-pulse' }}"></span>
                    @if($pendingVerificationCount > 0)
                        <span
                            class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-white dark:border-surface-dark"></span>
                    @endif
                @endif
            </button>

            <!-- Dropdown Menu -->
            <div id="notification-dropdown"
                class="absolute top-full right-0 mt-3 w-80 bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50 hidden origin-top-right transform transition-all duration-200">

                <div
                    class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-surface-dark/50">
                    <h3 class="font-bold text-gray-800 dark:text-white text-sm">Notifikasi</h3>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifikasi.readAll') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="no-bounce text-xs text-primary hover:text-primary-dark dark:text-accent dark:hover:text-white font-medium transition-colors">
                                Tandai semua dibaca
                            </button>
                        </form>
                    @endif
                </div>

                <div class="max-h-[350px] overflow-y-auto">
                    {{-- Verification Tasks (High Priority) --}}
                    @if($pendingVerificationCount > 0)
                        <a href="{{ route('peminjaman.index', ['status' => 'menunggu_verifikasi']) }}"
                            class="no-bounce block px-4 py-4 bg-blue-50/50 dark:bg-blue-400/5 hover:bg-blue-100/50 dark:hover:bg-blue-400/10 transition-all border-b border-blue-100 dark:border-blue-900/30 group">
                            <div class="flex gap-4">
                                <div class="shrink-0">
                                    <div
                                        class="size-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-[20px]">assignment_late</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <h4 class="text-xs font-bold text-blue-800 dark:text-blue-300">Tugas Verifikasi</h4>
                                        <div class="flex items-center gap-1">
                                            <span class="size-1.5 rounded-full bg-blue-500 animate-ping"></span>
                                            <span
                                                class="text-[10px] text-blue-600 dark:text-blue-400 font-bold uppercase">Penting</span>
                                        </div>
                                    </div>
                                    <p
                                        class="text-[11px] text-blue-700/70 dark:text-blue-400/50 font-medium leading-relaxed">
                                        Ada {{ $pendingVerificationCount }} pengajuan yang menunggu persetujuan Anda.
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endif

                    <div id="unread-notification-list">
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                            <form action="{{ route('notifikasi.read', $notification->id) }}" method="POST" class="block">
                                @csrf
                                <button type="submit"
                                    class="no-bounce w-full text-left px-4 py-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-200 border-b last:border-0 border-gray-50 dark:border-gray-800 flex gap-4 group relative">
                                    <div class="shrink-0">
                                        @php
                                            $ntype = $notification->data['type'] ?? '';
                                            $nicon = $notification->data['icon'] ?? ($ntype === 'warning' ? 'warning' : ($ntype === 'success' ? 'check_circle' : 'info'));
                                            $ncolor = match ($ntype) {
                                                'warning' => 'bg-orange-100 text-orange-600',
                                                'success' => 'bg-emerald-100 text-emerald-600',
                                                default => 'bg-blue-100 text-blue-600',
                                            };
                                        @endphp
                                        <div class="size-10 rounded-xl flex items-center justify-center 
                                                    {{ $ncolor }} 
                                                    dark:bg-white/5 group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined text-[20px]">
                                                {{ $nicon }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start mb-0.5">
                                            <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                                            </h4>
                                            <span class="text-[10px] text-gray-400 font-medium shrink-0 ml-2">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                    </div>
                                </button>
                            </form>
                        @empty
                            @if($pendingVerificationCount == 0)
                                <div class="p-8 text-center text-gray-400 dark:text-white/20">
                                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">notifications_off</span>
                                    <p class="text-sm">Tidak ada notifikasi baru</p>
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>

                <div
                    class="p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-surface-dark/50 text-center">
                    <a href="{{ route('notifikasi.index') }}"
                        class="block p-2 text-xs font-bold text-primary hover:text-primary-dark dark:text-accent dark:hover:text-white transition-colors rounded-lg hover:bg-primary/5 dark:hover:bg-white/10">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </div>
        </div>

        <script>
            function toggleNotificationDropdown() {
                const dropdown = document.getElementById('notification-dropdown');
                dropdown.classList.toggle('hidden');
            }

            // Close on click outside
            document.addEventListener('click', function (event) {
                const container = document.getElementById('notification-container');
                const dropdown = document.getElementById('notification-dropdown');
                if (!container.contains(event.target) && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>

        <!-- Profile Shortcut Button -->
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 p-1.5 sm:pr-4 sm:pl-1.5 rounded-full bg-white dark:bg-surface-dark border border-primary/20 dark:border-transparent hover:bg-primary/5 dark:hover:bg-[#36271F] transition-all cursor-pointer group shadow-sm shrink-0">
            <div
                class="size-8 rounded-full bg-primary/10 dark:bg-accent/10 flex items-center justify-center text-primary dark:text-accent font-bold group-hover:bg-primary/20 dark:group-hover:bg-accent/20 transition-colors overflow-hidden">
                @if(Auth::user()->foto_profil)
                    <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Profile"
                        class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->nama, 0, 1) }}
                @endif
            </div>
            <div class="hidden sm:flex flex-col items-start text-left">
                <span
                    class="text-primary-dark dark:text-white text-xs font-bold leading-tight truncate max-w-[120px]">{{ Auth::user()->nama }}</span>
                <span
                    class="text-primary/70 dark:text-accent/70 text-[10px] uppercase tracking-wider font-semibold">{{ Auth::user()->peran }}</span>
            </div>
        </a>
    </div>
</header>