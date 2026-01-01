<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Buku - Library App</title>
    <link rel="icon" type="image/png" href="https://laravel.com/img/favicon/favicon-32x32.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/live-search-buku.js'])
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap"
        rel="stylesheet" />
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display">
    <div class="flex h-screen w-full relative">

        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Data Buku" />

            <div class="p-4 sm:p-8">
                <x-breadcrumb-component parent="Administrator" current="Buku" class="mb-6 animate-enter" />
                <!-- Action Bar & Stats -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <!-- Tombol Tambah (Left Aligned below Title) -->
                    <button onclick="openModal('createModal')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-xl font-bold text-sm shadow-sm dark:shadow-lg dark:shadow-accent/10 transition-all hover:scale-105 active:scale-95 duration-200 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Buku
                    </button>

                    <!-- Indikator Statistik Compact (Right Aligned) -->
                    <div class="flex flex-wrap gap-3">
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Total Judul:</span>
                            <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $totalBuku }}</span>
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-green-700 dark:text-green-400">Total Stok:</span>
                            <span class="text-sm font-bold text-green-700 dark:text-green-400">{{ $totalStok }}</span>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex flex-col gap-1 animate-enter shadow-sm text-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">error</span>
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Tabel Data -->
                <x-datatable :data="$buku" search-placeholder="Cari ID, judul, atau penulis..." search-id="searchInput"
                    :search-value="request('search')">
                    <x-slot:header>
                        <th class="p-4 pl-6 font-medium w-24 cursor-pointer hover:text-primary transition-colors"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_buku', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                ID
                                @if(request('sort') == 'id_buku')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium w-1/3 cursor-pointer hover:text-primary transition-colors"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'judul', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Judul Buku
                                @if(request('sort') == 'judul')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'kategori', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Kategori
                                @if(request('sort') == 'kategori')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'penulis', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Penulis
                                @if(request('sort') == 'penulis')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium text-center">Stok</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 pr-6 font-medium text-right">Aksi</th>
                    </x-slot:header>

                    <x-slot:body>
                        @forelse($buku as $item)
                            <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                                <td class="p-4 pl-6 font-mono text-primary dark:text-accent text-xs font-bold">
                                    {{ $item->id_buku }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Cover Image -->
                                        @if($item->gambar_sampul)
                                            <div class="w-10 h-14 rounded overflow-hidden shadow-sm shrink-0 border border-slate-200 dark:border-white/10 relative group cursor-pointer"
                                                onclick="openImageModal('{{ asset('storage/' . $item->gambar_sampul) }}', '{{ $item->judul }}')">
                                                <img src="{{ asset('storage/' . $item->gambar_sampul) }}"
                                                    alt="{{ $item->judul }}"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                                <div
                                                    class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                                    <span
                                                        class="material-symbols-outlined text-white text-sm opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-md">zoom_in</span>
                                                </div>
                                            </div>
                                        @else
                                            <div
                                                class="w-10 h-14 rounded bg-slate-100 dark:bg-white/5 flex items-center justify-center shrink-0 border border-slate-200 dark:border-white/10">
                                                <span
                                                    class="material-symbols-outlined text-slate-300 dark:text-white/20 text-xl">book</span>
                                            </div>
                                        @endif

                                        <!-- Title & ISBN -->
                                        <div class="flex flex-col">
                                            <div
                                                class="font-bold text-slate-800 dark:text-white group-hover:text-primary dark:group-hover:text-accent">
                                                {{ $item->judul }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-white/40 font-mono mt-0.5">
                                                {{ $item->isbn ?? 'No ISBN' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4"><span
                                        class="px-2 py-1 bg-primary/10 dark:bg-white/5 rounded text-xs font-semibold text-primary-dark dark:text-white/80">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                                </td>
                                <td class="p-4">{{ $item->penulis }}</td>
                                <td class="p-4 text-center">
                                    <div class="flex flex-col items-center">
                                        <div>
                                            <span
                                                class="font-bold {{ $item->stok_tersedia > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $item->stok_tersedia }}</span>
                                            <span
                                                class="text-slate-400 dark:text-white/30 text-xs">/{{ $item->stok_total }}</span>
                                        </div>
                                        <div class="text-[10px] flex gap-2 mt-0.5">
                                            @if($item->stok_rusak > 0) <span class="text-amber-600 dark:text-amber-400"
                                            title="Rusak">R:{{$item->stok_rusak}}</span> @endif
                                            @if($item->stok_hilang > 0) <span class="text-red-600 dark:text-red-400"
                                            title="Hilang">H:{{$item->stok_hilang}}</span> @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4"><span
                                        class="px-2 py-1 rounded text-xs font-bold uppercase {{ $item->status == 'tersedia' ? 'text-green-600 dark:text-green-500 bg-green-50 dark:bg-green-500/10' : 'text-red-600 dark:text-red-500 bg-red-50 dark:bg-red-500/10' }}">
                                        {{ $item->status == 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-right flex justify-end gap-2">
                                    <button onclick="openEditBuku('{{ $item->id_buku }}')"
                                        class="p-2 rounded-lg hover:bg-blue-500/20 text-blue-600 transition-colors"
                                        title="Edit"><span class="material-symbols-outlined text-lg">edit</span></button>
                                    <form action="{{ route('buku.destroy', $item->id_buku) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus buku ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 rounded-lg hover:bg-red-500/20 text-red-600 transition-colors"
                                            title="Hapus"><span
                                                class="material-symbols-outlined text-lg">delete</span></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500 dark:text-white/40">Belum ada data
                                    buku.</td>
                            </tr>
                        @endforelse
                    </x-slot:body>
                </x-datatable>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH -->
    <div id="createModal" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('createModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-primary/20 dark:border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-3xl">
                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-[#36271F] flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2"><span
                                class="material-symbols-outlined text-primary dark:text-accent">add_circle</span> Tambah
                            Buku</h3>
                        <button onclick="closeModal('createModal')"
                            class="text-slate-500 dark:text-white/60 hover:text-slate-700 dark:hover:text-white transition-colors"><span
                                class="material-symbols-outlined">close</span></button>
                    </div>
                    <form action="{{ route('buku.store') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        @csrf
                        <!-- Kiri -->
                        <div class="flex flex-col gap-4">
                            <!-- Input Gambar Sampul -->
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Gambar
                                    Sampul</label>
                                <div id="create_preview_container"
                                    class="hidden mb-2 relative group w-24 h-36 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10">
                                    <img id="create_preview_img" src="" alt="Preview Sampul"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <p class="text-[10px] text-white font-bold text-center px-1">Ganti gambar</p>
                                    </div>
                                </div>
                                <input type="file" id="create_gambar_sampul" name="gambar_sampul" accept="image/*"
                                    class="block w-full text-sm text-slate-500 dark:text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent cursor-pointer">
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-[10px] text-slate-400 dark:text-white/40">*Max 2MB (JPG, PNG)</p>
                                    <button type="button" id="create_cover_cancel_btn" style="display: none"
                                        class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1 text-[10px]">
                                        <span class="material-symbols-outlined text-xs">delete</span> Hapus Sampul
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Judul
                                    Buku</label>
                                <input type="text" name="judul"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none font-normal"
                                    required>
                            </div>

                            <!-- BAGIAN KATEGORI (UPDATED) -->
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Kategori</label>
                                <div class="relative">
                                    <select name="id_kategori"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 pr-10 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none appearance-none w-full cursor-pointer"
                                        required>
                                        <option value="" disabled selected>Pilih Kategori...</option>
                                        @foreach($kategoriList as $kat)
                                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                    <!-- Ikon Panah -->
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500 dark:text-white/60">
                                        <span class="material-symbols-outlined">expand_more</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Penulis</label>
                                <input type="text" name="penulis"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none font-normal"
                                    required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Penerbit</label>
                                <input type="text" name="penerbit"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                            </div>
                        </div>
                        <!-- Kanan -->
                        <div class="flex flex-col gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Tahun
                                        Terbit</label>
                                    <input type="number" name="tahun_terbit"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                        required>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Stok
                                        Total</label>
                                    <input type="number" name="stok_total"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                        required>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">ISBN</label>
                                <input type="text" name="isbn"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Kode
                                    Dewey
                                </label>
                                <input type="text" name="kode_dewey"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Deskripsi
                                    Singkat</label>
                                <textarea name="deskripsi" rows="2"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none font-normal"></textarea>
                            </div>
                        </div>

                        <div
                            class="md:col-span-2 mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                            <button type="button" onclick="closeModal('createModal')"
                                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110 transition-all shadow-sm dark:shadow-md">Simpan
                                Buku</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-image-zoom-modal />

    <!-- MODAL EDIT -->
    <div id="editModal" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-primary/20 dark:border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-3xl">
                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-[#36271F] flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2"><span
                                class="material-symbols-outlined text-blue-500 dark:text-blue-400">edit</span> Edit Buku
                        </h3>
                        <button onclick="closeModal('editModal')"
                            class="text-slate-500 dark:text-white/60 hover:text-slate-700 dark:hover:text-white transition-colors"><span
                                class="material-symbols-outlined">close</span></button>
                    </div>
                    <form id="editForm" method="POST" enctype="multipart/form-data"
                        class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        @csrf @method('PUT')

                        <!-- BARIS 1, KOLOM 1: INFO UTAMA -->
                        <div class="flex flex-col gap-4">
                            <!-- Input Gambar Sampul -->
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Gambar
                                    Sampul</label>
                                <div id="edit_preview_container"
                                    class="hidden mb-2 relative group w-24 h-36 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10">
                                    <img id="edit_preview_img" src="" alt="Preview Sampul"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <p class="text-[10px] text-white font-bold text-center px-1">Ganti gambar di
                                            bawah</p>
                                    </div>
                                </div>
                                <input type="file" id="edit_gambar_sampul" name="gambar_sampul" accept="image/*"
                                    class="block w-full text-sm text-slate-500 dark:text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent cursor-pointer">
                                <input type="hidden" name="remove_gambar_sampul" id="edit_remove_gambar_sampul"
                                    value="0">
                                <p
                                    class="text-[10px] text-slate-400 dark:text-white/40 mt-1 flex flex-wrap items-center gap-2">
                                    <span id="edit_cover_helper_text">*Max 2MB. Kosongkan jika tidak diubah.</span>

                                    <button type="button" id="edit_cover_delete_btn" style="display: none"
                                        class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">delete</span> Hapus Sampul
                                    </button>

                                    <button type="button" id="edit_cover_restore_btn" style="display: none"
                                        class="text-blue-500 hover:text-blue-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">settings_backup_restore</span>
                                        Batal Hapus
                                    </button>

                                    <button type="button" id="edit_cover_cancel_btn" style="display: none"
                                        class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">delete</span> Hapus Gambar
                                    </button>
                                </p>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Judul
                                    Buku</label>
                                <input type="text" id="edit_judul" name="judul"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none font-normal"
                                    required>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Penulis</label>
                                <input type="text" id="edit_penulis" name="penulis"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none font-normal"
                                    required>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Kategori</label>
                                <div class="relative">
                                    <select id="edit_kategori" name="id_kategori"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 pr-10 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none appearance-none w-full cursor-pointer"
                                        required>
                                        @foreach($kategoriList as $kat)
                                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500 dark:text-white/60">
                                        <span class="material-symbols-outlined">expand_more</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BARIS 1, KOLOM 2: DETAIL & DESKRIPSI -->
                        <div class="flex flex-col gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Penerbit</label>
                                    <input type="text" id="edit_penerbit" name="penerbit"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Tahun</label>
                                    <input type="number" id="edit_tahun" name="tahun_terbit"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                        required>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">ISBN</label>
                                    <input type="text" id="edit_isbn" name="isbn"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Dewey</label>
                                    <input type="text" id="edit_dewey" name="kode_dewey"
                                        class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm">
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 h-full">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Deskripsi
                                    Singkat</label>
                                <textarea id="edit_deskripsi" name="deskripsi" rows="6"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none h-full text-sm font-normal"></textarea>
                            </div>
                        </div>

                        <!-- BARIS SEJAJAR: STOK & INFO (UNIFIED SEGMENT) -->
                        <div
                            class="md:col-span-2 pt-4 mt-2 border-t border-dashed border-primary/20 dark:border-white/10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Group: Input Stok -->
                                <div class="flex flex-col gap-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2">
                                            <label
                                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Status</label>
                                            <div class="relative">
                                                <select id="edit_status" name="status"
                                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-2.5 pr-10 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none appearance-none w-full cursor-pointer truncate text-sm">
                                                    <option value="tersedia">Tersedia</option>
                                                    <option value="tidak_tersedia">Tidak Tersedia</option>
                                                </select>
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500 dark:text-white/60">
                                                    <span class="material-symbols-outlined text-sm">expand_more</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label
                                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Total
                                                Stok</label>
                                            <input type="number" id="edit_stok" name="stok_total"
                                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm"
                                                required>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label
                                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider text-amber-600 dark:text-amber-500">Rusak</label>
                                            <input type="number" id="edit_rusak" name="stok_rusak"
                                                class="bg-background-light dark:bg-[#120C0A] border border-amber-200 dark:border-amber-900/30 rounded-lg px-4 py-2.5 text-amber-700 dark:text-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-sm"
                                                min="0">
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label
                                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider text-red-600 dark:text-red-500">Hilang</label>
                                            <input type="number" id="edit_hilang" name="stok_hilang"
                                                class="bg-background-light dark:bg-[#120C0A] border border-red-200 dark:border-red-900/30 rounded-lg px-4 py-2.5 text-red-700 dark:text-red-500 focus:ring-1 focus:ring-red-500 outline-none text-sm"
                                                min="0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Group: Info Box -->
                                <div class="flex flex-col justify-center">
                                    <div
                                        class="p-4 bg-blue-50/50 dark:bg-blue-900/10 rounded-xl border border-blue-200/50 dark:border-blue-900/30 text-[11px] leading-relaxed text-blue-700 dark:text-blue-300 h-full flex flex-col justify-center">
                                        <div
                                            class="font-bold mb-2 flex items-center gap-2 text-blue-800 dark:text-blue-200 text-xs">
                                            <span class="material-symbols-outlined text-sm">info</span>
                                            Manajemen Stok
                                        </div>
                                        <ul class="space-y-1.5 opacity-90">
                                            <li class="flex items-start gap-2">
                                                <span class="mt-1 w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>
                                                <span>Total = Tersedia + Pinjam + Rusak + Hilang</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="mt-1 w-1 h-1 rounded-full bg-blue-400 shrink-0"></span>
                                                <span>Perubahan angka di atas akan mengubah Stok Tersedia.</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI -->
                        <div
                            class="md:col-span-2 mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                            <button type="button" onclick="closeModal('editModal')"
                                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110 transition-all shadow-sm">Update
                                Buku</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-image-preview-modal />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                openModal('createModal');

                // Clean URL without reloading
                const newUrl = window.location.pathname + window.location.search.replace(/[\?&]action=create/, '') + window.location.hash;
                window.history.replaceState({}, '', newUrl);
            }



            // Init Image Preview
            if (typeof initImagePreview === 'function') {
                initImagePreview('#create_gambar_sampul', '#create_preview_img');
                initImagePreview('#edit_gambar_sampul', '#edit_preview_img');
            }

            const editPreviewImg = document.getElementById('edit_preview_img');

            // Book Management Draft Delete & Preview Logic
            const updateCancelBtnVisibility = () => {
                const img = document.getElementById('edit_preview_img');
                const cancelBtn = document.getElementById('edit_cover_cancel_btn');
                const deleteBtn = document.getElementById('edit_cover_delete_btn');
                const restoreBtn = document.getElementById('edit_cover_restore_btn');
                const removeInput = document.getElementById('edit_remove_gambar_sampul');
                const previewContainer = document.getElementById('edit_preview_container');

                if (!img || !cancelBtn) return;

                const newSrc = img.getAttribute('src') || '';
                const initialSrc = img.dataset.initialSrc || '';
                const isRemoved = removeInput?.value === '1';

                const isNewDraft = newSrc &&
                    newSrc.length > 0 &&
                    newSrc !== initialSrc &&
                    (newSrc.startsWith('data:') || newSrc.startsWith('blob:'));

                if (isNewDraft) {
                    cancelBtn.classList.remove('hidden');
                    cancelBtn.style.display = 'inline-flex';
                    if (previewContainer) previewContainer.classList.remove('hidden');

                    if (deleteBtn) {
                        deleteBtn.classList.add('hidden');
                        deleteBtn.style.display = 'none';
                    }
                    if (restoreBtn) {
                        restoreBtn.classList.add('hidden');
                        restoreBtn.style.display = 'none';
                    }
                } else {
                    cancelBtn.classList.add('hidden');
                    cancelBtn.style.display = 'none';

                    if (initialSrc && initialSrc.length > 0) {
                        if (isRemoved) {
                            if (deleteBtn) {
                                deleteBtn.classList.add('hidden');
                                deleteBtn.style.display = 'none';
                            }
                            if (restoreBtn) {
                                restoreBtn.classList.remove('hidden');
                                restoreBtn.style.display = 'inline-flex';
                            }
                            if (previewContainer) previewContainer.classList.add('hidden');
                        } else {
                            if (deleteBtn) {
                                deleteBtn.classList.remove('hidden');
                                deleteBtn.style.display = 'inline-flex';
                            }
                            if (restoreBtn) {
                                restoreBtn.classList.add('hidden');
                                restoreBtn.style.display = 'none';
                            }
                            if (previewContainer) previewContainer.classList.remove('hidden');
                        }
                    } else {
                        if (previewContainer) previewContainer.classList.add('hidden');
                        if (deleteBtn) {
                            deleteBtn.classList.add('hidden');
                            deleteBtn.style.display = 'none';
                        }
                        if (restoreBtn) {
                            restoreBtn.classList.add('hidden');
                            restoreBtn.style.display = 'none';
                        }
                    }
                }
            };

            const editPreviewObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                        updateCancelBtnVisibility();
                    }
                });
            });

            if (editPreviewImg) {
                editPreviewObserver.observe(editPreviewImg, { attributes: true });
                // Initial check
                updateCancelBtnVisibility();
            }

            // Edit Cover Delete draft logic
            const editDeleteBtn = document.getElementById('edit_cover_delete_btn');
            const editRestoreBtn = document.getElementById('edit_cover_restore_btn');
            const editRemoveInput = document.getElementById('edit_remove_gambar_sampul');

            if (editDeleteBtn) {
                editDeleteBtn.addEventListener('click', function () {
                    const previewContainer = document.getElementById('edit_preview_container');
                    const restoreBtn = document.getElementById('edit_cover_restore_btn');
                    const removeInput = document.getElementById('edit_remove_gambar_sampul');
                    const cancelBtn = document.getElementById('edit_cover_cancel_btn');

                    if (previewContainer) previewContainer.classList.add('hidden');

                    this.classList.add('hidden');
                    this.style.display = 'none';

                    if (restoreBtn) {
                        restoreBtn.classList.remove('hidden');
                        restoreBtn.style.display = 'inline-flex';
                    }
                    if (removeInput) removeInput.value = '1';

                    // Clear file input if any
                    const input = document.getElementById('edit_gambar_sampul');
                    if (input) input.value = '';

                    if (cancelBtn) {
                        cancelBtn.classList.add('hidden');
                        cancelBtn.style.display = 'none';
                    }
                });
            }

            if (editRestoreBtn) {
                editRestoreBtn.addEventListener('click', function () {
                    const previewContainer = document.getElementById('edit_preview_container');
                    const previewImg = document.getElementById('edit_preview_img');
                    const deleteBtn = document.getElementById('edit_cover_delete_btn');
                    const removeInput = document.getElementById('edit_remove_gambar_sampul');

                    if (previewContainer && previewImg && previewImg.dataset.initialSrc) {
                        previewImg.src = previewImg.dataset.initialSrc;
                        previewContainer.classList.remove('hidden');
                    }

                    if (deleteBtn) {
                        deleteBtn.classList.remove('hidden');
                        deleteBtn.style.display = 'inline-flex';
                    }

                    this.classList.add('hidden');
                    this.style.display = 'none';

                    if (removeInput) removeInput.value = '0';
                });
            }

            // Edit Cover Cancel selection
            const editCancelBtn = document.getElementById('edit_cover_cancel_btn');
            if (editCancelBtn) {
                editCancelBtn.addEventListener('click', function () {
                    const input = document.getElementById('edit_gambar_sampul');
                    const previewImg = document.getElementById('edit_preview_img');
                    const previewContainer = document.getElementById('edit_preview_container');
                    const initialSrc = previewImg.dataset.initialSrc || '';

                    input.value = '';
                    previewImg.src = initialSrc;

                    if (!initialSrc || initialSrc === '') {
                        if (previewContainer) previewContainer.classList.add('hidden');
                    } else {
                        if (previewContainer) previewContainer.classList.remove('hidden');
                        // If we reverted to an existing image, make sure delete btn is visible
                        const removeInput = document.getElementById('edit_remove_gambar_sampul');
                        const deleteBtn = document.getElementById('edit_cover_delete_btn');
                        const restoreBtn = document.getElementById('edit_cover_restore_btn');
                        if (removeInput?.value === '0') {
                            if (deleteBtn) {
                                deleteBtn.classList.remove('hidden');
                                deleteBtn.style.display = 'inline-flex';
                            }
                            if (restoreBtn) {
                                restoreBtn.classList.add('hidden');
                                restoreBtn.style.display = 'none';
                            }
                        }
                    }
                    this.classList.add('hidden');
                    this.style.display = 'none';
                });
            }
            // Create Modal Cover Logic
            const createPreviewImg = document.getElementById('create_preview_img');
            const createCancelBtn = document.getElementById('create_cover_cancel_btn');

            if (createPreviewImg) {
                const createObserver = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                            const src = createPreviewImg.getAttribute('src');
                            if (src && src !== '') {
                                createCancelBtn.classList.remove('hidden');
                                createCancelBtn.style.display = 'inline-flex';
                            } else {
                                createCancelBtn.classList.add('hidden');
                                createCancelBtn.style.display = 'none';
                            }
                        }
                    });
                });
                createObserver.observe(createPreviewImg, { attributes: true });
            }

            if (createCancelBtn) {
                createCancelBtn.addEventListener('click', function () {
                    const input = document.getElementById('create_gambar_sampul');
                    const previewContainer = document.getElementById('create_preview_container');
                    const previewImg = document.getElementById('create_preview_img');

                    if (input) input.value = '';
                    if (previewImg) previewImg.src = '';
                    if (previewContainer) previewContainer.classList.add('hidden');

                    this.classList.add('hidden');
                    this.style.display = 'none';
                });
            }
        });
    </script>
</body>

</html>