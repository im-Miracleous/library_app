<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Kategori - Library App</title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 w-72 h-full bg-background-dark border-r border-[#36271F] p-6 flex flex-col justify-between z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none overflow-y-auto">
            <button id="close-sidebar" class="cursor-pointer lg:hidden absolute top-4 right-4 text-white/60 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>

            <div class="flex flex-col gap-8">
                <div class="flex items-center gap-3 px-2">
                    <div class="bg-accent/20 flex items-center justify-center rounded-full size-12">
                        <span class="material-symbols-outlined text-accent" style="font-size: 28px;">local_library</span>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-white text-lg font-bold leading-tight">Library App</h1>
                        <p class="text-white/60 text-xs font-medium">Panel Manajemen</p>
                    </div>
                </div>

                <nav class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/70 hover:bg-[#36271F] hover:text-white transition-colors">
                            <span class="material-symbols-outlined">arrow_back</span>
                            <p class="text-sm font-bold">Dashboard</p>
                        </a>
                        
                        <div class="px-4 py-3 rounded-xl bg-accent text-primary-dark shadow-[0_0_15px_rgba(236,177,118,0.3)] flex items-center gap-3">
                            <span class="material-symbols-outlined filled">category</span>
                            <p class="text-sm font-bold">Kategori Buku</p>
                        </div>
                    </div>

                    <!-- STATISTIK -->
                    <div class="bg-[#1A1410] rounded-2xl p-5 border border-[#36271F] space-y-4">
                        <h3 class="text-xs font-bold text-white/40 uppercase tracking-widest">Statistik</h3>
                        
                        <div class="flex justify-between items-center pb-3 border-b border-[#36271F]">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-blue-500"></span>
                                <span class="text-sm text-white/80">Total Kategori</span>
                            </div>
                            <span class="text-xs font-bold text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded">{{ $totalKategori }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-green-500"></span>
                                <span class="text-sm text-white/80">Baru Bulan Ini</span>
                            </div>
                            <span class="text-xs font-bold text-green-500 bg-green-500/10 px-2 py-0.5 rounded">{{ $kategoriBaru }}</span>
                        </div>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <header class="flex items-center justify-between sticky top-0 bg-background-dark/95 backdrop-blur-sm z-30 px-4 py-4 border-b border-[#36271F] lg:hidden">
                <div class="flex items-center gap-4">
                    <button id="open-sidebar" class="cursor-pointer text-white hover:text-accent transition-colors">
                        <span class="material-symbols-outlined text-3xl">menu</span>
                    </button>
                    <h2 class="text-white text-lg font-bold">Kategori</h2>
                </div>
            </header>

            <div class="p-4 sm:p-8">
                <!-- Header & Action -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 animate-enter">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-accent hidden sm:block">category</span>
                            Kategori Buku
                        </h1>
                        <p class="text-white/60 mt-1">Klasifikasi koleksi buku perpustakaan.</p>
                    </div>
                    <button onclick="openModal('createModal')" class="cursor-pointer flex items-center gap-2 px-5 py-2.5 bg-accent text-primary-dark rounded-xl font-bold text-sm hover:brightness-110 transition-all shadow-lg shadow-accent/10 w-full sm:w-auto justify-center hover:scale-105 active:scale-95 duration-200">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Kategori
                    </button>
                </div>

                <!-- Pesan Sukses / Error -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Tabel Data -->
                <div class="bg-surface-dark rounded-2xl border border-[#36271F] overflow-hidden animate-enter delay-100">
                    <!-- Search Bar Sederhana -->
                    <div class="p-4 border-b border-[#36271F] flex justify-end">
                        <form action="{{ route('kategori.index') }}" method="GET" class="relative w-full sm:w-64">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-white/40 text-lg">search</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..." class="w-full bg-[#120C0A] border border-[#36271F] rounded-lg pl-10 pr-4 py-2 text-white text-sm focus:ring-1 focus:ring-accent outline-none">
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr class="border-b border-[#36271F] text-white/40 text-xs uppercase tracking-wider bg-[#1A1410]">
                                    <th class="p-4 pl-6 font-medium w-20">ID</th>
                                    <th class="p-4 font-medium w-1/4">Nama Kategori</th>
                                    <th class="p-4 font-medium">Deskripsi</th>
                                    <th class="p-4 pr-6 font-medium text-right w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#36271F] text-sm text-white/80">
                                @forelse($kategori as $item)
                                    <tr class="hover:bg-white/5 transition-colors group">
                                        <td class="p-4 pl-6 font-mono text-accent">#{{ $item->id_kategori }}</td>
                                        <td class="p-4 font-bold text-white">{{ $item->nama_kategori }}</td>
                                        <td class="p-4 text-white/60 truncate max-w-xs">{{ $item->deskripsi ?? '-' }}</td>
                                        <td class="p-4 pr-6 text-right flex justify-end gap-2">
                                            <button onclick="openEditKategori('{{ $item->id_kategori }}')" class="cursor-pointer p-2 rounded-lg hover:bg-blue-500/20 text-blue-400 transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <form action="{{ route('kategori.destroy', $item->id_kategori) }}" method="POST" onsubmit="return confirm('Yakin hapus kategori ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="cursor-pointer p-2 rounded-lg hover:bg-red-500/20 text-red-400 transition-colors" title="Hapus">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-8 text-center text-white/40">Belum ada kategori buku.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-[#36271F]">{{ $kategori->links() }}</div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH -->
    <div id="createModal" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal('createModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface-dark border border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="px-6 py-4 border-b border-[#36271F] flex justify-between items-center bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-accent">add_circle</span> Tambah Kategori
                        </h3>
                        <button onclick="closeModal('createModal')" class="cursor-pointer text-white/60 hover:text-white transition-colors"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <form action="{{ route('kategori.store') }}" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" placeholder="Misal: Fiksi Ilmiah" required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none resize-none" placeholder="Keterangan singkat..."></textarea>
                        </div>
                        <div class="mt-2 flex justify-end gap-3 pt-4 border-t border-[#36271F]">
                            <button type="button" onclick="closeModal('createModal')" class="cursor-pointer px-4 py-2 rounded-lg border border-[#36271F] text-white/70 hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit" class="cursor-pointer px-4 py-2 rounded-lg bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div id="editModal" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-surface-dark border border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="px-6 py-4 border-b border-[#36271F] flex justify-between items-center bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-400">edit</span> Edit Kategori
                        </h3>
                        <button onclick="closeModal('editModal')" class="text-white/60 hover:text-white transition-colors"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <form id="editForm" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf @method('PUT')
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Nama Kategori</label>
                            <input type="text" id="edit_nama" name="nama_kategori" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Deskripsi</label>
                            <textarea id="edit_deskripsi" name="deskripsi" rows="3" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none resize-none"></textarea>
                        </div>
                        <div class="mt-2 flex justify-end gap-3 pt-4 border-t border-[#36271F]">
                            <button type="button" onclick="closeModal('editModal')" class="cursor-pointer px-4 py-2 rounded-lg border border-[#36271F] text-white/70 hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit" class="cursor-pointer px-4 py-2 rounded-lg bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>