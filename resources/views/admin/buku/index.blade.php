@extends('layouts.app')

@section('title', 'Data Buku - Library App')
@section('header-title', 'Data Buku')

@push('scripts')
    @vite(['resources/js/live-search/live-search-buku.js', 'resources/js/logic/modals/buku-modal.js'])
@endpush

@section('content')
    <div class="flex flex-col">
        <x-breadcrumb-component parent="Administrator" current="Buku" class="mb-6 animate-enter" />

        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
            <!-- Tombol Tambah -->
            <div class="flex gap-3">
                <button onclick="openModal('createModal')"
                    class="flex items-center gap-2 px-5 py-2.5 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-xl font-bold text-sm shadow-sm dark:shadow-lg dark:shadow-accent/10 transition-all hover:scale-105 active:scale-95 duration-200 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Tambah Buku
                </button>
                <button onclick="openModal('googleSearchModal')"
                    class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 dark:bg-blue-500 text-white rounded-xl font-bold text-sm shadow-sm dark:shadow-lg dark:shadow-blue-500/10 transition-all hover:scale-105 active:scale-95 duration-200 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">search</span>
                    Import dari Google Books
                </button>
            </div>

            <!-- Indikator Statistik Compact -->
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg">
                    <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Total Judul:</span>
                    <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $totalBuku }}</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                    <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-green-700 dark:text-green-400">Total Stok:</span>
                    <span class="text-sm font-bold text-green-700 dark:text-green-400">{{ $totalStok }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <x-datatable :data="$buku" search-placeholder="Cari ID, judul, atau penulis..." search-id="searchInput"
            :search-value="request('search')">
            <x-slot:header>
                <th class="p-4 pl-6 font-medium w-24 cursor-pointer hover:text-primary transition-colors"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_buku', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        ID
                        @if(request('sort') == 'id_buku')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
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
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
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
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
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
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
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
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                            <span class="material-symbols-outlined text-white text-sm opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-md">zoom_in</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-10 h-14 rounded bg-slate-100 dark:bg-white/5 flex items-center justify-center shrink-0 border border-slate-200 dark:border-white/10">
                                        <span class="material-symbols-outlined text-slate-300 dark:text-white/20 text-xl">book</span>
                                    </div>
                                @endif

                                <!-- Title & ISBN -->
                                <div class="flex flex-col">
                                    <div class="font-bold text-slate-800 dark:text-white group-hover:text-primary dark:group-hover:text-accent">
                                        {{ $item->judul }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-white/40 font-mono mt-0.5">
                                        {{ $item->isbn ?? 'No ISBN' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4"><span class="px-2 py-1 bg-primary/10 dark:bg-white/5 rounded text-xs font-semibold text-primary-dark dark:text-white/80">{{ $item->nama_kategori ?? '-' }}</span></td>
                        <td class="p-4">{{ $item->penulis }}</td>
                        <td class="p-4 text-center">
                            <div class="flex flex-col items-center">
                                <div>
                                    <span class="font-bold {{ $item->stok_tersedia > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $item->stok_tersedia }}</span>
                                    <span class="text-slate-400 dark:text-white/30 text-xs">/{{ $item->stok_total }}</span>
                                </div>
                                <div class="text-[10px] flex gap-2 mt-0.5">
                                    @if($item->stok_rusak > 0) <span class="text-amber-600 dark:text-amber-400" title="Rusak">R:{{$item->stok_rusak}}</span> @endif
                                    @if($item->stok_hilang > 0) <span class="text-red-600 dark:text-red-400" title="Hilang">H:{{$item->stok_hilang}}</span> @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            @php
                                $statusLabel = 'Tidak Tersedia';
                                $statusClass = 'text-red-600 dark:text-red-500 bg-red-50 dark:bg-red-500/10';
                                
                                if ($item->status === 'tersedia') {
                                    $statusLabel = 'Tersedia';
                                    $statusClass = 'text-green-600 dark:text-green-500 bg-green-50 dark:bg-green-500/10';
                                } elseif ($item->status === 'habis') {
                                    $statusLabel = 'Habis';
                                    $statusClass = 'text-orange-600 dark:text-orange-500 bg-orange-50 dark:bg-orange-500/10';
                                }
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-bold uppercase {{ $statusClass }}">
                                {{ $statusLabel }}
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
                                    title="Hapus"><span class="material-symbols-outlined text-lg">delete</span></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500 dark:text-white/40">Belum ada data buku.</td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </div>

    <!-- MODAL GOOGLE BOOKS SEARCH -->
    <x-modal id="googleSearchModal" title="Import dari Google Books" maxWidth="4xl">
        <div class="flex flex-col gap-4">
            <!-- Search Input -->
            <div class="flex gap-3">
                <input type="text" id="googleSearchInput" placeholder="Cari judul buku, penulis, atau ISBN..."
                    class="flex-1 bg-background-light dark:bg-[#120C0A] border border-slate-200 dark:border-white/10 rounded-lg px-4 py-2.5 text-slate-700 dark:text-white/80 focus:ring-2 focus:ring-primary dark:focus:ring-accent outline-none text-sm"
                    onkeypress="if(event.key === 'Enter') searchGoogleBooks()">
                <button onclick="searchGoogleBooks()"
                    class="px-5 py-2.5 bg-blue-600 dark:bg-blue-500 text-white rounded-lg font-bold text-sm hover:bg-blue-700 dark:hover:bg-blue-600 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-lg">search</span>
                </button>
            </div>

            <!-- Loading State -->
            <div id="googleSearchLoading" class="hidden text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary dark:border-accent"></div>
                <p class="mt-3 text-sm text-slate-500 dark:text-white/60">Mencari buku...</p>
            </div>

            <!-- Results Container -->
            <div id="googleSearchResults" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto"></div>

            <!-- Empty State -->
            <div id="googleSearchEmpty" class="hidden text-center py-8 text-slate-500 dark:text-white/40">
                <span class="material-symbols-outlined text-4xl mb-2 opacity-30">search_off</span>
                <p class="text-sm">Tidak ada hasil ditemukan</p>
            </div>
        </div>
    </x-modal>

    <!-- MODAL TAMBAH -->
    <x-modal id="createModal" title="Tambah Buku" maxWidth="3xl">
        <form action="{{ route('buku.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @csrf
            <!-- Kiri -->
            <div class="flex flex-col gap-4">
                <!-- Input Gambar Sampul (Custom Layout) -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Gambar Sampul</label>
                    <div id="create_preview_container"
                        class="hidden mb-2 relative group w-24 h-36 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10">
                        <img id="create_preview_img" src="" alt="Preview Sampul" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
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

                <x-input name="judul" label="Judul Buku" required />
                
                <x-select name="id_kategori" label="Kategori" required>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </x-select>

                <x-input name="penulis" label="Penulis" required />
                <x-input name="penerbit" label="Penerbit" />
            </div>

            <!-- Kanan -->
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <x-input name="tahun_terbit" label="Tahun Terbit" type="number" required />
                    <x-input name="stok_total" label="Stok Total" type="number" required />
                </div>
                <x-input name="isbn" label="ISBN" />
                <x-input name="kode_dewey" label="Kode Dewey" />
                <x-textarea name="deskripsi" label="Deskripsi Singkat" rows="2" />
            </div>

            <div class="md:col-span-2 mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                <input type="hidden" id="create_gambar_sampul_url" name="gambar_sampul_url">
                <button type="button" onclick="closeModal('createModal')"
                    class="px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110 transition-all shadow-sm dark:shadow-md">Simpan Buku</button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT -->
    <x-modal id="editModal" title="Edit Buku" maxWidth="3xl">
        <form id="editForm" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            @csrf @method('PUT')

            <!-- BARIS 1, KOLOM 1: INFO UTAMA -->
            <div class="flex flex-col gap-4">
                <!-- Input Gambar Sampul (Custom Layout) -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Gambar Sampul</label>
                    <div id="edit_preview_container"
                        class="hidden mb-2 relative group w-24 h-36 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 dark:border-white/10">
                        <img id="edit_preview_img" src="" alt="Preview Sampul" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <p class="text-[10px] text-white font-bold text-center px-1">Ganti gambar di bawah</p>
                        </div>
                    </div>
                    <input type="file" id="edit_gambar_sampul" name="gambar_sampul" accept="image/*"
                        class="block w-full text-sm text-slate-500 dark:text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent cursor-pointer">
                    <input type="hidden" name="remove_gambar_sampul" id="edit_remove_gambar_sampul" value="0">
                    <p class="text-[10px] text-slate-400 dark:text-white/40 mt-1 flex flex-wrap items-center gap-2">
                        <span id="edit_cover_helper_text">*Max 2MB. Kosongkan jika tidak diubah.</span>
                        <button type="button" id="edit_cover_delete_btn" style="display: none"
                            class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">delete</span> Hapus Sampul
                        </button>
                        <button type="button" id="edit_cover_restore_btn" style="display: none"
                            class="text-blue-500 hover:text-blue-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">settings_backup_restore</span> Batal Hapus
                        </button>
                        <button type="button" id="edit_cover_cancel_btn" style="display: none"
                            class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">delete</span> Hapus Gambar
                        </button>
                    </p>
                </div>

                <x-input id="edit_judul" name="judul" label="Judul Buku" required />
                <x-input id="edit_penulis" name="penulis" label="Penulis" required />

                <x-select id="edit_kategori" name="id_kategori" label="Kategori" required>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </x-select>
            </div>

            <!-- BARIS 1, KOLOM 2: DETAIL & DESKRIPSI -->
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <x-input id="edit_penerbit" name="penerbit" label="Penerbit" />
                    <x-input id="edit_tahun" name="tahun_terbit" label="Tahun" type="number" required />
                    <x-input id="edit_isbn" name="isbn" label="ISBN" class="text-sm" />
                    <x-input id="edit_dewey" name="kode_dewey" label="Dewey" class="text-sm" />
                </div>

                <x-textarea id="edit_deskripsi" name="deskripsi" label="Deskripsi Singkat" rows="6" class="h-full" />
            </div>

            <!-- BARIS SEJAJAR: STOK & INFO (UNIFIED SEGMENT) -->
            <div class="md:col-span-2 pt-4 mt-2 border-t border-dashed border-primary/20 dark:border-white/10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Group: Input Stok -->
                    <div class="flex flex-col gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <x-select id="edit_status" name="status" label="Status" placeholder="">
                                <option value="tersedia">Tersedia</option>
                                <option value="habis">Habis</option>
                                <option value="tidak_tersedia">Tidak Tersedia</option>
                            </x-select>

                            <x-input id="edit_stok" name="stok_total" label="Total Stok" type="number" required class="text-sm" />
                            
                            <!-- Custom Warning Colors for Rusak/Hilang using classes/custom styling if not fully supported by x-input, but x-input is generic enough -->
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider text-amber-600 dark:text-amber-500">Rusak</label>
                                <input type="number" id="edit_rusak" name="stok_rusak"
                                    class="bg-background-light dark:bg-[#120C0A] border border-amber-200 dark:border-amber-900/30 rounded-lg px-4 py-2.5 text-amber-700 dark:text-amber-500 focus:ring-1 focus:ring-amber-500 outline-none text-sm w-full"
                                    min="0">
                            </div>
                             <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider text-red-600 dark:text-red-500">Hilang</label>
                                <input type="number" id="edit_hilang" name="stok_hilang"
                                    class="bg-background-light dark:bg-[#120C0A] border border-red-200 dark:border-red-900/30 rounded-lg px-4 py-2.5 text-red-700 dark:text-red-500 focus:ring-1 focus:ring-red-500 outline-none text-sm w-full"
                                    min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Group: Info Box -->
                    <div class="flex flex-col justify-center">
                        <div class="p-4 bg-blue-50/50 dark:bg-blue-900/10 rounded-xl border border-blue-200/50 dark:border-blue-900/30 text-[11px] leading-relaxed text-blue-700 dark:text-blue-300 h-full flex flex-col justify-center">
                            <div class="font-bold mb-2 flex items-center gap-2 text-blue-800 dark:text-blue-200 text-xs">
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
            <div class="md:col-span-2 mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                <button type="button" onclick="closeModal('editModal')"
                    class="px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110 transition-all shadow-sm">Update Buku</button>
            </div>
        </form>
    </x-modal>

    <x-image-preview-modal />
    <x-image-zoom-modal />

    <script>
        function searchGoogleBooks() {
            const query = document.getElementById('googleSearchInput').value.trim();
            if (!query) {
                alert('Masukkan kata kunci pencarian');
                return;
            }

            const loading = document.getElementById('googleSearchLoading');
            const results = document.getElementById('googleSearchResults');
            const empty = document.getElementById('googleSearchEmpty');

            loading.classList.remove('hidden');
            results.innerHTML = '';
            results.classList.add('hidden');
            empty.classList.add('hidden');

            fetch(`{{ route('buku.search-google') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');
                    
                    if (data.books && data.books.length > 0) {
                        results.classList.remove('hidden');
                        data.books.forEach(book => {
                            const card = document.createElement('div');
                            card.className = 'flex gap-3 p-3 bg-slate-50 dark:bg-white/5 rounded-lg border border-slate-200 dark:border-white/10 hover:border-primary dark:hover:border-accent cursor-pointer transition-all group';
                            card.onclick = () => selectBook(book);

                            const thumbnail = book.thumbnail ? 
                                `<img src="${book.thumbnail}" alt="${book.title}" class="w-16 h-24 object-cover rounded">` :
                                `<div class="w-16 h-24 bg-slate-200 dark:bg-white/10 rounded flex items-center justify-center"><span class="material-symbols-outlined text-slate-400 dark:text-white/20">book</span></div>`;

                            const year = book.publishedDate ? new Date(book.publishedDate).getFullYear() : 'N/A';

                            card.innerHTML = `
                                ${thumbnail}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-sm text-slate-800 dark:text-white group-hover:text-primary dark:group-hover:text-accent line-clamp-2">${book.title}</h4>
                                    <p class="text-xs text-slate-600 dark:text-white/60 mt-1">${book.authors}</p>
                                    <p class="text-xs text-slate-500 dark:text-white/40 mt-0.5">${book.publisher} (${year})</p>
                                    ${book.isbn ? `<p class="text-[10px] text-slate-400 dark:text-white/30 mt-1 font-mono">ISBN: ${book.isbn}</p>` : ''}
                                </div>
                            `;
                            results.appendChild(card);
                        });
                    } else {
                        empty.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    loading.classList.add('hidden');
                    alert('Terjadi kesalahan saat mencari buku');
                    console.error(error);
                });
        }

        function selectBook(book) {
            closeModal('googleSearchModal');
            
            // Reset search modal
            document.getElementById('googleSearchInput').value = '';
            document.getElementById('googleSearchResults').innerHTML = '';
            
            // Open create modal
            setTimeout(() => {
                openModal('createModal');
                
                // Populate form fields
                document.querySelector('[name="judul"]').value = book.title || '';
                document.querySelector('[name="penulis"]').value = book.authors || '';
                document.querySelector('[name="penerbit"]').value = book.publisher || '';
                
                // Extract year from publishedDate
                if (book.publishedDate) {
                    const year = new Date(book.publishedDate).getFullYear();
                    document.querySelector('[name="tahun_terbit"]').value = year;
                }
                
                document.querySelector('[name="isbn"]').value = book.isbn || '';
                document.querySelector('[name="deskripsi"]').value = book.description || '';
                
                // Set cover image
                if (book.thumbnail) {
                    const previewImg = document.getElementById('create_preview_img');
                    const previewContainer = document.getElementById('create_preview_container');
                    const imageUrlInput = document.getElementById('create_gambar_sampul_url');
                    
                    previewImg.src = book.thumbnail;
                    previewContainer.classList.remove('hidden');
                    imageUrlInput.value = book.thumbnail;
                }
            }, 300);
        }
    </script>
@endsection