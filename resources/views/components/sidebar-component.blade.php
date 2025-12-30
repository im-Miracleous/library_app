<aside id="sidebar"
    class="fixed lg:static inset-y-0 left-0 w-72 h-full bg-surface dark:bg-surface-dark border-r border-primary/20 dark:border-border-dark flex flex-col z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none overflow-hidden">

    <button id="close-sidebar"
        class="lg:hidden absolute top-4 right-4 text-primary-dark dark:text-white/60 hover:text-primary dark:hover:text-white cursor-pointer transition-colors z-50">
        <span class="material-symbols-outlined">close</span>
    </button>

    <!-- Fixed Header -->
    <div class="flex-none flex items-center px-6 py-5 border-b border-primary/20 dark:border-border-dark gap-3">
        @if(isset($pengaturan) && !empty($pengaturan->logo_path))
            <div
                class="size-10 flex items-center justify-center rounded-xl overflow-hidden bg-white dark:bg-surface-dark border border-primary/20 cursor-default">
                <img src="{{ asset('storage/' . $pengaturan->logo_path) }}" alt="Logo"
                    class="w-full h-full object-contain p-1">
            </div>
        @else
            <div
                class="bg-primary/20 dark:bg-accent/20 flex items-center justify-center rounded-full size-10 flex-shrink-0 cursor-default">
                <span class="material-symbols-outlined text-primary-dark dark:text-accent"
                    style="font-size: 24px;">local_library</span>
            </div>
        @endif
        <div class="flex flex-col cursor-default">
            <h1 class="text-primary-dark dark:text-white text-base font-bold leading-tight">
                {{ optional($pengaturan)->nama_perpustakaan ?? 'Library App' }}
            </h1>
            <p class="text-primary-mid dark:text-white/60 text-[10px] font-medium uppercase tracking-wider">
                Panel Manajemen</p>
        </div>
    </div>

    <!-- Scrollable Content -->
    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto flex flex-col justify-between px-4 py-4">
        <nav class="flex flex-col gap-2">

            <!-- Menu Utama (Dashboard) -->
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard')
    ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
    : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'filled' : '' }}"
                    style="{{ request()->routeIs('dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">dashboard</span>
                <p class="text-sm {{ request()->routeIs('dashboard') ? 'font-bold' : 'font-medium' }}">Dashboard</p>
            </a>

            @if(Auth::user()->peran == 'admin')
                    <div
                        class="mt-4 mb-2 px-4 text-xs font-bold text-primary-mid/60 dark:text-white/40 uppercase tracking-widest select-none">
                        Administrator</div>

                    <a href="{{ route('anggota.index') }}"
                        class="{{ request()->routeIs('anggota*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('anggota*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">group</span>
                        <p class="text-sm {{ request()->routeIs('anggota*') ? 'font-bold' : 'font-medium' }}">Data Anggota
                        </p>
                    </a>

                    <a href="{{ route('kepegawaian.index') }}"
                        class="{{ request()->routeIs('kepegawaian*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('kepegawaian*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">badge</span>
                        <p class="text-sm {{ request()->routeIs('kepegawaian*') ? 'font-bold' : 'font-medium' }}">Data
                            Kepegawaian
                        </p>
                    </a>

                    <a href="{{ route('buku.index') }}"
                        class="{{ request()->routeIs('buku*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('buku*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">library_books</span>
                        <p class="text-sm {{ request()->routeIs('buku*') ? 'font-bold' : 'font-medium' }}">Data Buku</p>
                    </a>

                    <a href="{{ route('kategori.index') }}"
                        class="{{ request()->routeIs('kategori*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('kategori*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">category</span>
                        <p class="text-sm {{ request()->routeIs('kategori*') ? 'font-bold' : 'font-medium' }}">Data Kategori</p>
                    </a>

                    <a href="{{ route('pengaturan.index') }}"
                        class="{{ request()->routeIs('pengaturan*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('pengaturan*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">settings</span>
                        <p class="text-sm {{ request()->routeIs('pengaturan*') ? 'font-bold' : 'font-medium' }}">Pengaturan</p>
                    </a>

                    <div class="mt-2 mb-1 px-4 py-2 border-t border-primary/10 dark:border-white/5"></div>
                    <div
                        class="px-4 text-xs font-bold text-primary-mid/60 dark:text-white/40 uppercase tracking-widest select-none mb-2">
                        Laporan</div>

                    <a href="{{ route('laporan.peminjaman') }}"
                        class="{{ request()->routeIs('laporan.peminjaman')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('laporan.peminjaman') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">description</span>
                        <p class="text-sm {{ request()->routeIs('laporan.peminjaman') ? 'font-bold' : 'font-medium' }}">Laporan
                            Transaksi</p>
                    </a>

                    <a href="{{ route('laporan.denda') }}"
                        class="{{ request()->routeIs('laporan.denda')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('laporan.denda') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">payments</span>
                        <p class="text-sm {{ request()->routeIs('laporan.denda') ? 'font-bold' : 'font-medium' }}">Laporan Denda
                        </p>
                    </a>

            @endif

            @if(Auth::user()->peran == 'admin' || Auth::user()->peran == 'petugas')
                    <div
                        class="mt-4 mb-2 px-4 text-xs font-bold text-primary-mid/60 dark:text-white/40 uppercase tracking-widest select-none">
                        Sirkulasi</div>

                    <a href="{{ route('peminjaman.index') }}"
                        class="{{ request()->routeIs('peminjaman*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('peminjaman*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">sync_alt</span>
                        <p class="text-sm {{ request()->routeIs('peminjaman*') ? 'font-bold' : 'font-medium' }}">Transaksi
                            Peminjaman</p>
                    </a>

                    <a href="{{ route('pengembalian.index') }}"
                        class="{{ request()->routeIs('pengembalian*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('pengembalian*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">assignment_return</span>
                        <p class="text-sm {{ request()->routeIs('pengembalian*') ? 'font-bold' : 'font-medium' }}">Pengembalian
                            & Denda</p>
                    </a>

                    <a href="{{ route('pengunjung.index') }}"
                        class="{{ request()->routeIs('pengunjung*')
                ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/20 dark:bg-accent text-primary-dark dark:text-primary-dark transition-all hover:brightness-110 hover:shadow-md cursor-pointer shadow-sm dark:shadow-[0_0_15px_rgba(236,177,118,0.3)]'
                : 'flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group' }}">
                        <span
                            class="material-symbols-outlined {{ request()->routeIs('pengunjung*') ? 'filled' : 'group-hover:text-primary dark:group-hover:text-accent transition-colors' }}">groups_3</span>
                        <p class="text-sm {{ request()->routeIs('pengunjung*') ? 'font-bold' : 'font-medium' }}">Data Pengunjung
                        </p>
                    </a>
            @endif

            @if(Auth::user()->peran == 'anggota')
                <div
                    class="mt-4 mb-2 px-4 text-xs font-bold text-primary-mid/60 dark:text-white/40 uppercase tracking-widest select-none">
                    Menu Anggota</div>
                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group">
                    <span
                        class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">search</span>
                    <p class="text-sm font-medium">Cari Buku</p>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-dark/80 dark:text-white/70 hover:bg-white dark:hover:bg-primary/20 hover:text-primary-dark dark:hover:text-white transition-all cursor-pointer group">
                    <span
                        class="material-symbols-outlined group-hover:text-primary dark:group-hover:text-accent transition-colors">history</span>
                    <p class="text-sm font-medium">Riwayat Saya</p>
                </a>
            @endif

        </nav>

        <div
            class="flex flex-col gap-4 flex-shrink-0 pt-4 pb-4 border-t border-primary/20 dark:border-border-dark mt-4">
            <div
                class="bg-white/50 dark:bg-surface-dark p-4 rounded-xl flex items-center gap-3 border border-primary/10 dark:border-border-dark cursor-default transition-colors">
                <div
                    class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold">
                    {{ substr(Auth::user()->nama, 0, 1) }}
                </div>
                <div class="flex flex-col overflow-hidden">
                    <p class="text-sm font-bold truncate text-primary-dark dark:text-white">
                        {{ Auth::user()->nama }}
                    </p>
                    <p class="text-xs text-primary-mid dark:text-white/60 truncate capitalize">
                        {{ Auth::user()->peran }}
                    </p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="w-full form-logout">
                @csrf
                <button type="submit"
                    class="flex w-full cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-full h-12 bg-white dark:bg-[#36271F] border border-primary/20 dark:border-transparent hover:bg-red-50 dark:hover:bg-[#4D3A2F] text-primary-dark dark:text-white hover:text-red-600 dark:hover:text-white text-sm font-bold transition-all active:scale-95 shadow-sm hover:shadow-md">
                    <span class="material-symbols-outlined" style="font-size: 20px;">logout</span>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebarContent = document.querySelector("#sidebar .overflow-y-auto");
        const sidebarScrollKey = "sidebarScrollPos";

        if (sidebarContent) {
            // Restore scroll position
            const savedScrollPos = localStorage.getItem(sidebarScrollKey);
            if (savedScrollPos) {
                sidebarContent.scrollTop = savedScrollPos;
            }

            // Save scroll position on scroll
            sidebarContent.addEventListener("scroll", function () {
                localStorage.setItem(sidebarScrollKey, sidebarContent.scrollTop);
            });

            // Optional: reset scroll on logout or specific actions if needed, 
            // but usually persistent scroll is desired behavior for navigation.
        }
    });
</script>