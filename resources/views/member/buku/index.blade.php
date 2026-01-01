<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Katalog Buku - Library App</title>
    <link rel="icon" type="image/png"
        href="{{ !empty($pengaturan->logo_path) ? asset('storage/' . $pengaturan->logo_path) : 'https://laravel.com/img/favicon/favicon-32x32.png' }}">
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

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Katalog Buku" />

            <div class="p-4 sm:p-8 flex flex-col gap-6 max-w-[1600px] mx-auto w-full">

                <!-- Header & Filter Section -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 animate-enter">
                    <div>
                        @if(request('filter') === 'bookmarks')
                            <h1 class="text-2xl font-bold text-primary-dark dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-pink-500">favorite</span>
                                Koleksi Tersimpan
                            </h1>
                            <p class="text-primary-mid dark:text-white/60 mt-1">Buku-buku yang telah Anda simpan</p>
                        @else
                            <h1 class="text-2xl font-bold text-primary-dark dark:text-white">Jelajahi Koleksi</h1>
                            <p class="text-primary-mid dark:text-white/60 mt-1">Temukan buku favorit Anda di sini</p>
                        @endif
                    </div>

                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('member.buku.index') }}"
                        class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <!-- Filter Kategori -->
                        <select name="kategori" onchange="this.form.submit()"
                            class="px-4 py-2 rounded-xl bg-white dark:bg-white/5 border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach($kategori_list as $kat)
                                <option value="{{ $kat->id_kategori }}" {{ request('kategori') == $kat->id_kategori ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Search Input -->
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary-mid/60 text-[20px]">search</span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari judul atau penulis..."
                                class="pl-10 pr-4 py-2 rounded-xl bg-white dark:bg-white/5 border border-primary/20 dark:border-white/10 text-primary-dark dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 w-full sm:w-64">
                        </div>
                    </form>
                </div>

                <!-- Grid Buku -->
                <!-- Grid Buku -->
                <div
                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 animate-enter delay-100 pb-10">
                    @forelse($buku as $item)
                        <!-- Card Buku -->
                        <div class="group bg-white dark:bg-surface-dark rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-primary/10 dark:border-white/5 overflow-hidden flex flex-col h-full relative cursor-pointer"
                            onclick="if(!event.target.closest('.bookmark-btn')) window.location.href='{{ route('member.buku.show', $item->id_buku) }}'">

                            <!-- Cover Image Area -->
                            <div class="aspect-[2/3] relative overflow-hidden bg-slate-100 dark:bg-white/5">
                                <!-- Dynamic Gradient Placeholder -->
                                @php
                                    $colors = [
                                        'from-blue-400 to-indigo-500',
                                        'from-emerald-400 to-teal-500',
                                        'from-orange-400 to-red-500',
                                        'from-purple-400 to-pink-500',
                                        'from-cyan-400 to-blue-500',
                                        'from-rose-400 to-orange-500'
                                    ];
                                    $colorIndex = abs(crc32($item->id_buku)) % count($colors);
                                    $randomColor = $colors[$colorIndex];
                                @endphp

                                <div
                                    class="w-full h-full bg-gradient-to-br {{ $randomColor }} opacity-80 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center p-6">
                                    <div
                                        class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-repeat">
                                    </div>

                                    <div
                                        class="text-center text-white p-4 items-center flex flex-col gap-2 transform group-hover:scale-105 transition-transform duration-300">
                                        <span
                                            class="material-symbols-outlined text-[48px] drop-shadow-md">auto_stories</span>
                                        <p
                                            class="text-[10px] sm:text-xs font-black uppercase tracking-widest border-t-2 border-white/50 pt-2 mt-2 line-clamp-3 leading-tight drop-shadow-lg text-white">
                                            {{ $item->judul }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Overlay Actions -->
                                <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3 backdrop-blur-[2px]">
                                    <div
                                        class="size-10 rounded-full bg-white text-primary-dark hover:bg-primary hover:text-white transition-colors shadow-lg flex items-center justify-center transform translate-y-4 group-hover:translate-y-0 duration-300 delay-75">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </div>
                                </div>

                                @if(in_array($item->id_buku, $cartItemIds ?? []))
                                    <div class="absolute top-2 right-2 z-20">
                                        <div class="bg-primary/90 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-md flex items-center gap-1 border border-white/20"
                                            title="Sudah di keranjang">
                                            <span class="material-symbols-outlined text-[14px]">shopping_cart</span>
                                            <span class="hidden sm:inline">Di Keranjang</span>
                                        </div>
                                    </div>
                                @elseif(in_array($item->id_buku, $borrowedBookIds ?? []))
                                    <div class="absolute top-2 right-2 z-20">
                                        <div class="bg-emerald-600/90 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-md flex items-center gap-1 border border-white/20"
                                            title="Buku sedang Anda pinjam">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            <span class="hidden sm:inline">Sedang Dipinjam</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer Info -->
                            <div class="p-5 flex flex-col flex-1 bg-white dark:bg-surface-dark relative">
                                <!-- Category Tag -->
                                <div class="absolute -top-3 left-4">
                                    <span
                                        class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-white dark:bg-[#2A201C] text-primary-dark dark:text-accent shadow-sm border border-primary/10 dark:border-primary/20">
                                        {{ $item->kategori->nama_kategori ?? 'Umum' }}
                                    </span>
                                </div>


                                <div class="mt-2 flex-1">
                                    <div class="flex justify-between items-start gap-2">
                                        <h3 class="text-base font-bold text-primary-dark dark:text-white leading-tight mb-1 line-clamp-2 group-hover:text-primary transition-colors flex-1"
                                            title="{{ $item->judul }}">
                                            {{ $item->judul }}
                                        </h3>
                                        <button onclick="toggleBookmark(event, '{{ $item->id_buku }}')"
                                            class="bookmark-btn text-pink-500 hover:scale-125 transition-transform"
                                            id="bookmark-{{ $item->id_buku }}">
                                            <span
                                                class="material-symbols-outlined {{ in_array($item->id_buku, $bookmarkedIds) ? 'filled' : '' }}"
                                                style="{{ in_array($item->id_buku, $bookmarkedIds) ? 'font-variation-settings: \'FILL\' 1;' : '' }}">
                                                favorite
                                            </span>
                                        </button>
                                    </div>
                                    <p class="text-sm text-primary-mid dark:text-white/60 mb-2 truncate">
                                        {{ $item->penulis }}
                                    </p>
                                </div>

                                <div
                                    class="pt-3 mt-3 border-t border-dashed border-primary/10 dark:border-white/10 flex items-center justify-between text-xs text-primary-mid/80 dark:text-white/40 font-medium">
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                                        {{ $item->tahun_terbit }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @if($item->stok_tersedia > 0)
                                            <span class="text-green-600 dark:text-green-400 font-bold">Tersedia
                                                ({{ $item->stok_tersedia }})</span>
                                        @else
                                            <span class="text-red-500 font-bold">Habis</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                            <div class="bg-primary/5 dark:bg-white/5 p-8 rounded-full mb-4 animate-pulse">
                                <span
                                    class="material-symbols-outlined text-[48px] text-primary-mid dark:text-white/40">library_books</span>
                            </div>
                            <h3 class="text-xl font-bold text-primary-dark dark:text-white mb-2">Belum ada buku</h3>
                            <p class="text-primary-mid dark:text-white/60 max-w-md mx-auto">
                                Coba ubah filter kategori atau kata kunci pencarian Anda.
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $buku->withQueryString()->links() }}
                </div>

            </div>
        </main>
    </div>
    <script>
        async function toggleBookmark(event, id) {
            event.stopPropagation();
            const btn = document.getElementById(`bookmark-${id}`);
            const icon = btn.querySelector('span');

            try {
                const response = await fetch(`{{ url('/member/buku') }}/${id}/bookmark`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.status === 'added') {
                    icon.classList.add('filled');
                    icon.style.fontVariationSettings = "'FILL' 1";
                } else {
                    icon.classList.remove('filled');
                    icon.style.fontVariationSettings = "'FILL' 0";

                    // If we are on the bookmarks filter page, we might want to hide the card
                    if (window.location.search.includes('filter=bookmarks')) {
                        const card = btn.closest('.group');
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                }
            } catch (error) {
                console.error('Error toggling bookmark:', error);
            }
        }
    </script>
</body>

</html>