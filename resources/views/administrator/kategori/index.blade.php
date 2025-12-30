<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Kategori - Library App</title>
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

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/live-search-kategori.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display">
    <div class="flex h-screen w-full relative">

        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Data Kategori" />

            <div class="p-4 sm:p-8">
                <x-breadcrumb-component parent="Administrator" current="Kategori" class="mb-6 animate-enter" />
                <!-- Header & Action -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">

                    <!-- Tombol Tambah (Left Aligned) -->
                    <button onclick="openModal('createModal')"
                        class="cursor-pointer flex items-center gap-2 px-5 py-2.5 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-xl font-bold text-sm transition-all shadow-sm dark:shadow-lg shadow-accent/10 w-full sm:w-auto justify-center hover:scale-105 active:scale-95 duration-200">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Kategori
                    </button>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-3">
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Total
                                Kategori:</span>
                            <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $totalKategori }}</span>
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-green-700 dark:text-green-400">Baru Bulan
                                Ini:</span>
                            <span
                                class="text-sm font-bold text-green-700 dark:text-green-400">{{ $kategoriBaru }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pesan Sukses / Error -->
                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Tabel Data -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-[#36271F] overflow-hidden animate-enter delay-100 shadow-sm dark:shadow-none">

                    <!-- Table Controls -->
                    <div
                        class="p-4 border-b border-primary/20 dark:border-[#36271F] flex flex-col sm:flex-row justify-between items-center gap-4 bg-surface dark:bg-[#1A1410]">

                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-500 dark:text-white/60">Show</label>
                            <select onchange="window.location.href = this.value"
                                class="bg-white dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] text-xs rounded-lg p-2 focus:outline-none focus:ring-1 focus:ring-primary dark:focus:ring-accent dark:text-white">
                                @foreach([10, 25, 50, 100] as $limit)
                                    <option value="{{ request()->fullUrlWithQuery(['limit' => $limit]) }}" {{ request('limit') == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                                @endforeach
                            </select>
                            <label class="text-xs font-bold text-slate-500 dark:text-white/60">entries</label>
                        </div>

                        <!-- Search Bar Sederhana -->
                        <div class="relative w-full sm:w-64">
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 dark:text-white/40 text-lg">search</span>
                            <input type="text" id="searchKategoriInput" value="{{ request('search') }}"
                                placeholder="Cari ID atau kategori..."
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg pl-10 pr-4 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none placeholder-primary-mid/60 dark:placeholder-white/40">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr
                                    class="border-b border-primary/20 dark:border-[#36271F] text-slate-500 dark:text-white/40 text-xs uppercase tracking-wider bg-surface dark:bg-[#1A1410]">
                                    <th class="p-4 pl-6 font-medium w-20 cursor-pointer hover:text-primary transition-colors"
                                        onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_kategori', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                                        <div class="flex items-center gap-1">
                                            ID
                                            @if(request('sort') == 'id_kategori')
                                                <span
                                                    class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                            @else
                                                <span
                                                    class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="p-4 font-medium w-1/4 cursor-pointer hover:text-primary transition-colors"
                                        onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_kategori', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                                        <div class="flex items-center gap-1">
                                            Nama Kategori
                                            @if(request('sort') == 'nama_kategori')
                                                <span
                                                    class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                            @else
                                                <span
                                                    class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="p-4 font-medium">Deskripsi</th>
                                    <th class="p-4 pr-6 font-medium text-right w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-[#36271F] text-sm text-slate-600 dark:text-white/80">
                                @forelse($kategori as $item)
                                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                                        <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold">
                                            #{{ $item->id_kategori }}</td>
                                        <td
                                            class="p-4 font-bold text-slate-800 dark:text-white group-hover:text-primary dark:group-hover:text-accent">
                                            {{ $item->nama_kategori }}
                                        </td>
                                        <td class="p-4 text-slate-500 dark:text-white/60 truncate max-w-xs">
                                            {{ $item->deskripsi ?? '-' }}
                                        </td>
                                        <td class="p-4 pr-6 text-right flex justify-end gap-2">
                                            <button onclick="openEditKategori('{{ $item->id_kategori }}')"
                                                class="cursor-pointer p-2 rounded-lg hover:bg-blue-500/20 text-blue-400 transition-colors"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <form action="{{ route('kategori.destroy', $item->id_kategori) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus kategori ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="cursor-pointer p-2 rounded-lg hover:bg-red-500/20 text-red-400 transition-colors"
                                                    title="Hapus">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-500 dark:text-white/40">Belum ada
                                            kategori buku.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Pagination & Info -->
                    <div
                        class="p-4 border-t border-slate-200 dark:border-border-dark flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-xs text-slate-500 dark:text-white/60">
                            Showing <span class="font-bold">{{ $kategori->firstItem() ?? 0 }}</span> to <span
                                class="font-bold">{{ $kategori->lastItem() ?? 0 }}</span> of <span
                                class="font-bold">{{ $kategori->total() }}</span> entries
                        </div>
                        <div class="flex gap-2">
                            @if($kategori->onFirstPage())
                                <button disabled
                                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-400 cursor-not-allowed">Previous</button>
                            @else
                                <a href="{{ $kategori->previousPageUrl() }}"
                                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-primary hover:bg-primary/5 transition-colors">Previous</a>
                            @endif

                            @if($kategori->hasMorePages())
                                <a href="{{ $kategori->nextPageUrl() }}"
                                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-primary hover:bg-primary/5 transition-colors">Next</a>
                            @else
                                <button disabled
                                    class="px-3 py-1 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-400 cursor-not-allowed">Next</button>
                            @endif
                        </div>
                    </div>
                </div>
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
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-primary/20 dark:border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-lg">
                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-[#36271F] flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary dark:text-accent">add_circle</span>
                            Tambah Kategori
                        </h3>
                        <button onclick="closeModal('createModal')"
                            class="cursor-pointer text-slate-500 dark:text-white/60 hover:text-slate-700 dark:hover:text-white transition-colors"><span
                                class="material-symbols-outlined">close</span></button>
                    </div>
                    <form action="{{ route('kategori.store') }}" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Kategori</label>
                            <input type="text" name="nama_kategori"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                placeholder="Misal: Fiksi Ilmiah" required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Deskripsi</label>
                            <textarea name="deskripsi" rows="3"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none"
                                placeholder="Keterangan singkat..."></textarea>
                        </div>
                        <div class="mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                            <button type="button" onclick="closeModal('createModal')"
                                class="cursor-pointer px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit"
                                class="cursor-pointer px-4 py-2 rounded-lg bg-primary/20 dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div id="editModal" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-primary/20 dark:border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-lg">
                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-[#36271F] flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500 dark:text-blue-400">edit</span> Edit
                            Kategori
                        </h3>
                        <button onclick="closeModal('editModal')"
                            class="cursor-pointer text-slate-500 dark:text-white/60 hover:text-slate-700 dark:hover:text-white transition-colors"><span
                                class="material-symbols-outlined">close</span></button>
                    </div>
                    <form id="editForm" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf @method('PUT')
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Kategori</label>
                            <input type="text" id="edit_nama" name="nama_kategori"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Deskripsi</label>
                            <textarea id="edit_deskripsi" name="deskripsi" rows="3"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none"></textarea>
                        </div>
                        <div class="mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                            <button type="button" onclick="closeModal('editModal')"
                                class="cursor-pointer px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit"
                                class="cursor-pointer px-4 py-2 rounded-lg bg-primary/20 dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>