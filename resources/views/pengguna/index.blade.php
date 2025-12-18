<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Pengguna - Library App</title>
    
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

            <!-- Bagian Atas: Logo & Menu -->
            <div class="flex flex-col gap-8">
                <!-- Logo -->
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
                    <!-- Navigasi Utama -->
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/70 hover:bg-[#36271F] hover:text-white transition-colors">
                            <span class="material-symbols-outlined">arrow_back</span>
                            <p class="text-sm font-bold">Dashboard</p>
                        </a>
                        
                        <div class="px-4 py-3 rounded-xl bg-accent text-primary-dark shadow-[0_0_15px_rgba(236,177,118,0.3)] flex items-center gap-3">
                            <span class="material-symbols-outlined filled">group</span>
                            <p class="text-sm font-bold">Kelola Anggota</p>
                        </div>
                    </div>

                    <div class="bg-[#1A1410] rounded-2xl p-5 border border-[#36271F] space-y-4">
                        <h3 class="text-xs font-bold text-white/40 uppercase tracking-widest">Statistik Data</h3>
                        
                        <!-- Item Stat 1: Total Anggota -->
                        <div class="flex justify-between items-center pb-3 border-b border-[#36271F]">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-blue-500"></span> <!-- Dot Biru -->
                                <span class="text-sm text-white/80">Total Anggota</span>
                            </div>
                            <span class="text-xs font-bold text-blue-500 bg-blue-500/10 px-2 py-0.5 rounded">{{ $totalAnggota }}</span>
                        </div>
                        
                        <!-- Item Stat 2: Aktif -->
                        <div class="flex justify-between items-center pb-3 border-b border-[#36271F]">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-green-500"></span>
                                <span class="text-sm text-white/80">Aktif</span>
                            </div>
                            <span class="text-xs font-bold text-green-500 bg-green-500/10 px-2 py-0.5 rounded">{{ $totalAktif }}</span>
                        </div>

                        <!-- Item Stat 3: Nonaktif -->
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-red-500"></span>
                                <span class="text-sm text-white/80">Nonaktif</span>
                            </div>
                            <span class="text-xs font-bold text-red-500 bg-red-500/10 px-2 py-0.5 rounded">{{ $totalNonaktif }}</span>
                        </div>
                    </div>

                    <!-- WIDGET: Filter Cepat -->
                    <div>
                        <h3 class="px-4 text-xs font-bold text-white/40 uppercase tracking-widest mb-3">Filter Cepat</h3>
                        <div class="flex flex-col gap-1">
                            <a href="{{ route('pengguna.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ !request('status') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-[#36271F] hover:text-white' }} transition-colors">
                                <span class="material-symbols-outlined text-[18px]">list</span>
                                <span class="text-sm font-medium">Semua Data</span>
                            </a>
                            <a href="{{ route('pengguna.index', ['status' => 'aktif']) }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request('status') == 'aktif' ? 'bg-green-500/20 text-green-400' : 'text-white/60 hover:bg-[#36271F] hover:text-white' }} transition-colors">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                <span class="text-sm font-medium">Hanya Aktif</span>
                            </a>
                            <a href="{{ route('pengguna.index', ['status' => 'nonaktif']) }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request('status') == 'nonaktif' ? 'bg-red-500/20 text-red-400' : 'text-white/60 hover:bg-[#36271F] hover:text-white' }} transition-colors">
                                <span class="material-symbols-outlined text-[18px]">cancel</span>
                                <span class="text-sm font-medium">Hanya Nonaktif</span>
                            </a>
                        </div>
                    </div>

                </nav>
            </div>

            <!-- Bagian Bawah -->
            <div class="mt-auto pt-6 border-t border-[#36271F]">
                <a href="#" class="flex items-center gap-3 text-white/40 hover:text-accent transition-colors">
                    <span class="material-symbols-outlined text-[20px]">help</span>
                    <span class="text-sm font-medium">Bantuan & Panduan</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <header class="flex items-center justify-between sticky top-0 bg-background-dark/95 backdrop-blur-sm z-30 px-4 py-4 border-b border-[#36271F] lg:hidden">
                <div class="flex items-center gap-4">
                    <button id="open-sidebar" class="text-white hover:text-accent transition-colors">
                        <span class="material-symbols-outlined text-3xl">menu</span>
                    </button>
                    <h2 class="text-white text-lg font-bold">Kelola Pengguna</h2>
                </div>
            </header>

            <div class="p-4 sm:p-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 animate-enter">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-accent hidden sm:block">group</span>
                            Daftar Anggota
                        </h1>
                        <p class="text-white/60 mt-1">Kelola data anggota perpustakaan di sini.</p>
                    </div>
                    <button onclick="openModal('createModal')" class="cursor-pointer flex items-center gap-2 px-5 py-2.5 bg-accent text-primary-dark rounded-xl font-bold text-sm hover:brightness-110 transition-all shadow-lg shadow-accent/10 w-full sm:w-auto justify-center hover:scale-105 active:scale-95 duration-200">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Anggota
                    </button>
                </div>

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-surface-dark rounded-2xl border border-[#36271F] overflow-hidden animate-enter delay-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr class="border-b border-[#36271F] text-white/40 text-xs uppercase tracking-wider bg-[#1A1410]">
                                    <th class="p-4 pl-6 font-medium">ID</th>
                                    <th class="p-4 font-medium">Nama Anggota</th>
                                    <th class="p-4 font-medium">Email</th>
                                    <th class="p-4 font-medium">Telepon</th>
                                    <th class="p-4 font-medium">Status</th>
                                    <th class="p-4 pr-6 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#36271F] text-sm text-white/80">
                                @forelse($pengguna as $user)
                                    <tr class="hover:bg-white/5 transition-colors group">
                                        <td class="p-4 pl-6 font-mono text-accent">{{ $user->id_pengguna }}</td>
                                        <td class="p-4 font-bold text-white">{{ $user->nama }}</td>
                                        <td class="p-4">{{ $user->email }}</td>
                                        <td class="p-4">{{ $user->telepon ?? '-' }}</td>
                                        <td class="p-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->status == 'aktif' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td class="p-4 pr-6 text-right flex justify-end gap-2">
                                            <button onclick="openEditPengguna('{{ $user->id_pengguna }}')" class="cursor-pointer p-2 rounded-lg hover:bg-blue-500/20 text-blue-400 transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <form action="{{ route('pengguna.destroy', $user->id_pengguna) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus anggota ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="cursor-pointer p-2 rounded-lg hover:bg-red-500/20 text-red-400 transition-colors" title="Hapus">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="p-8 text-center text-white/40">Belum ada data anggota.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-[#36271F]">{{ $pengguna->links() }}</div>
                </div>
            </div>
        </main>
    </div>

    <!-- ================= MODAL TAMBAH ================= -->
    <div id="createModal" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop: Scale & Opacity transition -->
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal('createModal')"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal Panel: Transform scale transition -->
                <div id="createModalPanel" class="relative transform overflow-hidden rounded-2xl bg-surface-dark border border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-2xl">
                    
                    <div class="px-6 py-4 border-b border-[#36271F] flex justify-between items-center bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-accent">person_add</span>
                            Tambah Anggota Baru
                        </h3>
                        <button onclick="closeModal('createModal')" class="cursor-pointer text-white/60 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('pengguna.store') }}" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="nama" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Email</label>
                                <input type="email" name="email" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Telepon</label>
                                <input type="text" name="telepon" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Alamat</label>
                            <textarea name="alamat" rows="2" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Password</label>
                                <input type="password" name="password" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" required>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-[#36271F]">
                            <button type="button" onclick="closeModal('createModal')" class="cursor-pointer px-4 py-2 rounded-lg border border-[#36271F] text-white/70 hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit" class="cursor-pointer px-4 py-2 rounded-lg bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL EDIT ================= -->
    <div id="editModal" class="fixed inset-0 z-50 transition-all duration-300 opacity-0 pointer-events-none" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div id="editModalPanel" class="relative transform overflow-hidden rounded-2xl bg-surface-dark border border-[#36271F] text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-2xl">
                    
                    <div class="px-6 py-4 border-b border-[#36271F] flex justify-between items-center bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-400">edit</span>
                            Edit Anggota
                        </h3>
                        <button onclick="closeModal('editModal')" class="cursor-pointer text-white/60 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form id="editForm" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf @method('PUT')
                        
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" id="edit_nama" name="nama" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Email</label>
                                <input type="email" id="edit_email" name="email" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none" required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Telepon</label>
                                <input type="text" id="edit_telepon" name="telepon" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Alamat</label>
                            <textarea id="edit_alamat" name="alamat" rows="2" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none resize-none"></textarea>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-white/60 uppercase tracking-wider">Status</label>
                            <select id="edit_status" name="status" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-4 py-3 text-white focus:ring-1 focus:ring-accent outline-none">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>

                        <div class="p-3 bg-yellow-500/5 rounded-lg border border-yellow-500/10 mt-2">
                            <p class="text-[10px] text-yellow-500 mb-2 font-bold uppercase tracking-wider">Ubah Password (Opsional)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <input type="password" name="password" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-3 py-2 text-white text-sm focus:ring-1 focus:ring-accent outline-none" placeholder="Password Baru">
                                <input type="password" name="password_confirmation" class="bg-[#120C0A] border border-[#36271F] rounded-lg px-3 py-2 text-white text-sm focus:ring-1 focus:ring-accent outline-none" placeholder="Konfirmasi">
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-[#36271F]">
                            <button type="button" onclick="closeModal('editModal')" class="cursor-pointer px-4 py-2 rounded-lg border border-[#36271F] text-white/70 hover:bg-white/5 text-sm font-bold">Batal</button>
                            <button type="submit" class="cursor-pointer px-4 py-2 rounded-lg bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>