<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $buku->judul }} - Library App</title>
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

    <style>
        @keyframes spin-custom {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spin-custom 2s linear infinite;
        }

        .animate-spin-fast {
            animation: spin-custom 0.8s linear infinite;
        }

        /* Loading States for Buttons */
        .btn-loading-state {
            position: relative;
            transition: all 0.3s ease;
        }

        .btn-loading-state.loading {
            pointer-events: none;
        }

        /* Light Mode Loading */
        .btn-loading-state.loading.bg-white,
        .btn-loading-state.loading.dark\:bg-primary\/5 {
            background-color: #f1f5f9 !important;
            /* slate-100 */
            border-color: #cbd5e1 !important;
            /* slate-300 */
            color: #64748b !important;
            /* slate-500 */
            opacity: 0.8;
        }

        .dark .btn-loading-state.loading.bg-white,
        .dark .btn-loading-state.loading.dark\:bg-primary\/5 {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: rgba(255, 255, 255, 0.3) !important;
        }

        .btn-loading-state.loading.bg-primary {
            background-color: #92400e !important;
            /* orange-800 - darker than primary */
            opacity: 0.9;
        }

        /* Dark Mode Loading */
        .dark .btn-loading-state.loading.bg-primary {
            background-color: #451a03 !important;
            /* very dark brown */
            opacity: 0.8;
        }

        .loading-spinner {
            display: none;
        }

        .loading .loading-spinner {
            display: block;
        }

        .loading .default-icon {
            display: none;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display overflow-hidden">
    <div class="flex h-screen w-full relative">

        <x-sidebar-component />

        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Detail Buku" />

            <div class="p-4 sm:p-8 max-w-5xl mx-auto w-full animate-enter">

                <!-- Back Button -->
                <a href="javascript:history.back()"
                    class="inline-flex items-center gap-2 text-sm font-bold text-primary dark:text-accent mb-6 px-3 py-2 rounded-xl hover:bg-primary/10 transition-all group">
                    <span
                        class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Kembali
                </a>

                <div
                    class="bg-white dark:bg-surface-dark rounded-3xl shadow-sm border border-primary/10 dark:border-white/5 overflow-hidden flex flex-col md:flex-row">

                    <!-- Cover Section -->
                    <div
                        class="w-full md:w-1/3 relative bg-slate-100 dark:bg-white/5 p-10 md:p-8 flex items-center justify-center">
                        @php
                            $colors = ['from-blue-400 to-indigo-500', 'from-emerald-400 to-teal-500', 'from-orange-400 to-red-500', 'from-purple-400 to-pink-500', 'from-cyan-400 to-blue-500', 'from-rose-400 to-orange-500'];
                            $colorIndex = abs(crc32($buku->id_buku)) % count($colors);
                            $randomColor = $colors[$colorIndex]; 
                        @endphp
                        <div
                            class="w-full max-w-[240px] md:max-w-none aspect-[2/3] md:h-full md:max-h-[500px] shadow-2xl rounded-tr-2xl rounded-br-2xl bg-gradient-to-br {{ $randomColor }} relative flex items-center justify-center text-white p-6">
                            <div class="absolute inset-y-0 left-0 w-3 bg-white/20"></div>
                            <div class="text-center">
                                <span
                                    class="material-symbols-outlined text-[80px] drop-shadow-xl mb-4">auto_stories</span>
                                <h2
                                    class="text-xl font-black uppercase tracking-widest drop-shadow-md border-t-2 border-white/40 pt-4">
                                    {{ $buku->judul }}
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="w-full md:w-2/3 p-8 flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span
                                    class="inline-block px-3 py-1 bg-primary/10 dark:bg-accent/10 text-primary dark:text-accent text-xs font-bold uppercase tracking-wider rounded-full mb-2">
                                    {{ $buku->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                                <h1 class="text-3xl font-bold text-primary-dark dark:text-white mb-2 leading-tight">
                                    {{ $buku->judul }}
                                </h1>
                                <p class="text-lg text-primary-mid dark:text-white/60 font-medium">{{ $buku->penulis }}
                                </p>
                            </div>

                            <!-- BUtton Bookmark -->
                            <button onclick="toggleBookmark('{{ $buku->id_buku }}')" id="btn-bookmark"
                                class="size-12 rounded-full flex items-center justify-center transition-all duration-300 {{ $isBookmarked ? 'bg-pink-100 dark:bg-pink-500/20 text-pink-600 dark:text-pink-400 hover:bg-pink-200 dark:hover:bg-pink-500/30' : 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-white/50 hover:bg-slate-200 dark:hover:bg-white/20' }}"
                                title="Simpan ke Koleksi">
                                <span class="material-symbols-outlined {{ $isBookmarked ? 'filled' : '' }}"
                                    style="{{ $isBookmarked ? 'font-variation-settings: \'FILL\' 1;' : '' }}">favorite</span>
                            </button>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-6 my-6 p-6 bg-primary/5 dark:bg-white/5 rounded-2xl border border-primary/5 dark:border-white/5">
                            <div>
                                <p
                                    class="text-xs text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                                    Penerbit</p>
                                <p class="font-bold text-primary-dark dark:text-white">{{ $buku->penerbit }}</p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                                    Tahun Terbit</p>
                                <p class="font-bold text-primary-dark dark:text-white">{{ $buku->tahun_terbit }}</p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                                    ISBN</p>
                                <p class="font-mono text-sm font-bold text-primary-dark dark:text-white">
                                    {{ $buku->isbn }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                                    Stok Tersedia</p>
                                <p class="font-bold {{ $buku->stok_tersedia > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $buku->stok_tersedia }} <span
                                        class="text-xs font-normal text-primary-mid/60 dark:text-white/40">Exemplar</span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-2">Deskripsi</h3>
                            <p class="text-primary-mid dark:text-white/70 leading-relaxed text-sm">
                                {{ $buku->deskripsi ?? 'Tidak ada deskripsi tersedia untuk buku ini.' }}
                            </p>
                        </div>

                        <div class="mt-auto flex flex-col sm:flex-row gap-4">
                            @if($buku->stok_tersedia > 0)
                                <button onclick="addToCart('{{ $buku->id_buku }}')" id="btn-add-cart"
                                    class="flex-1 bg-white dark:bg-primary/5 border-2 border-primary text-primary font-bold py-3 px-6 rounded-xl hover:bg-primary/5 dark:hover:bg-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 btn-loading-state disabled:opacity-70 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined default-icon">add_shopping_cart</span>
                                    <span
                                        class="material-symbols-outlined loading-spinner animate-spin-fast">progress_activity</span>
                                    <span class="btn-text">Tambah Ke Keranjang</span>
                                </button>
                                <button onclick="loanNow('{{ $buku->id_buku }}')" id="btn-loan-now"
                                    class="flex-1 bg-primary text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-dark hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2 btn-loading-state disabled:opacity-70 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined default-icon">assignment_add</span>
                                    <span
                                        class="material-symbols-outlined loading-spinner animate-spin-fast">progress_activity</span>
                                    <span class="btn-text">Ajukan Peminjaman</span>
                                </button>
                            @else
                                <button disabled
                                    class="w-full bg-slate-200 dark:bg-white/10 text-slate-400 font-bold py-3 px-6 rounded-xl cursor-not-allowed">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        async function toggleBookmark(id) {
            const btn = document.getElementById('btn-bookmark');
            const icon = btn.querySelector('span');

            try {
                const response = await fetch(`/member/buku/${id}/bookmark`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.status === 'added') {
                    icon.classList.add('filled');
                    icon.style.fontVariationSettings = "'FILL' 1";
                    btn.className = "size-12 rounded-full flex items-center justify-center transition-all duration-300 bg-pink-100 dark:bg-pink-500/20 text-pink-600 dark:text-pink-400 hover:bg-pink-200 dark:hover:bg-pink-500/30";
                } else {
                    icon.classList.remove('filled');
                    icon.style.fontVariationSettings = "'FILL' 0";
                    btn.className = "size-12 rounded-full flex items-center justify-center transition-all duration-300 bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-white/50 hover:bg-slate-200 dark:hover:bg-white/20";
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengubah status koleksi');
            }
        }

        async function addToCart(id, isQuickLoan = false) {
            const btn = isQuickLoan ? document.getElementById('btn-loan-now') : document.getElementById('btn-add-cart');
            const originalText = isQuickLoan ? 'Ajukan Peminjaman' : 'Tambah Ke Keranjang';

            if (btn.classList.contains('loading')) return;

            // Start Loading
            btn.classList.add('loading');
            btn.disabled = true;
            btn.querySelector('.btn-text').textContent = 'Memproses...';

            try {
                const response = await fetch("{{ route('member.keranjang.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id_buku: id })
                });
                const data = await response.json();

                if (data.status === 'success') {
                    if (!isQuickLoan) {
                        alert('Berhasil: ' + data.message);
                        window.location.href = "{{ route('member.keranjang.index') }}";
                    }
                    return true;
                } else {
                    alert('Gagal: ' + data.message);
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan ke keranjang');
                return false;
            } finally {
                // Stop Loading
                btn.classList.remove('loading');
                btn.disabled = false;
                btn.querySelector('.btn-text').textContent = originalText;
            }
        }

        async function loanNow(id) {
            const success = await addToCart(id, true);
            if (success) {
                window.location.href = "{{ route('member.keranjang.index') }}";
            }
        }
    </script>
</body>

</html>