@props(['title' => 'Overview'])

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
                <input
                    class="block w-full p-3 pl-12 text-sm text-primary-dark dark:text-white bg-white dark:bg-surface-dark border-none rounded-full placeholder-primary-mid/60 dark:placeholder-white/40 focus:ring-2 focus:ring-primary dark:focus:ring-accent focus:bg-white dark:focus:bg-[#36271F] transition-all shadow-sm dark:shadow-none"
                    placeholder="Cari buku, ISBN, atau anggota..." type="text" />
            </div>
        @endif
    </div>

    <div class="flex items-center justify-end gap-3 sm:gap-4 w-auto xl:w-[280px] shrink-0 pl-2">

        <button onclick="toggleTheme()"
            class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] shadow-sm border border-primary/20 dark:border-transparent cursor-pointer transition-all shrink-0">
            <span id="theme-icon" class="material-symbols-outlined text-[20px]">dark_mode</span>
        </button>

        <button
            class="flex items-center justify-center size-10 rounded-full bg-white dark:bg-surface-dark text-primary-dark dark:text-white hover:bg-primary/10 dark:hover:bg-[#36271F] transition-all hover:rotate-12 relative shadow-sm border border-primary/20 dark:border-transparent cursor-pointer shrink-0">
            <span class="material-symbols-outlined">notifications</span>
            <span
                class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-white dark:border-surface-dark animate-pulse"></span>
        </button>

        <!-- Profile Shortcut Button -->
        <button
            class="flex items-center gap-3 p-1.5 sm:pr-4 sm:pl-1.5 rounded-full bg-white dark:bg-surface-dark border border-primary/20 dark:border-transparent hover:bg-primary/5 dark:hover:bg-[#36271F] transition-all cursor-pointer group shadow-sm shrink-0">
            <div
                class="size-8 rounded-full bg-primary/10 dark:bg-accent/10 flex items-center justify-center text-primary dark:text-accent font-bold group-hover:bg-primary/20 dark:group-hover:bg-accent/20 transition-colors">
                <!-- Fallback to Initial if no image (assuming no image field for now based on context) -->
                {{ substr(Auth::user()->nama, 0, 1) }}
            </div>
            <div class="hidden sm:flex flex-col items-start text-left">
                <span
                    class="text-primary-dark dark:text-white text-xs font-bold leading-tight truncate max-w-[120px]">{{ Auth::user()->nama }}</span>
                <span
                    class="text-primary/70 dark:text-accent/70 text-[10px] uppercase tracking-wider font-semibold">{{ Auth::user()->peran }}</span>
            </div>
        </button>
    </div>
</header>