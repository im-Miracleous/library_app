<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Kepegawaian - Library App</title>
    <link rel="icon" type="image/png" href="https://laravel.com/img/favicon/favicon-32x32.png">
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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js', 'resources/js/live-search-kepegawaian.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">

        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">

            <x-header-component title="Data Kepegawaian" />

            <div class="p-4 sm:p-8">
                <x-breadcrumb-component parent="Administrator" current="Kepegawaian" class="mb-6 animate-enter" />
                <!-- Action Bar & Stats -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <!-- Tombol Tambah (Left Aligned below Title) -->
                    <button onclick="openModal('createModal')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-xl font-bold text-sm shadow-sm dark:shadow-lg dark:shadow-accent/10 transition-all hover:scale-105 active:scale-95 duration-200 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Pegawai
                    </button>

                    <!-- Statistik Bar (Right Aligned) -->
                    <div class="flex flex-wrap gap-3">
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Total:</span>
                            <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $totalPegawai }}</span>
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-purple-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-purple-700 dark:text-purple-400">Administrator:</span>
                            <span
                                class="text-sm font-bold text-purple-700 dark:text-purple-400">{{ $totalAdmin }}</span>
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-orange-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-orange-700 dark:text-orange-400">Petugas:</span>
                            <span
                                class="text-sm font-bold text-orange-700 dark:text-orange-400">{{ $totalPetugas }}</span>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex flex-col gap-1 animate-enter">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span>
                                {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <x-datatable :data="$pegawai" search-placeholder="Cari ID, nama, atau email..."
                    search-id="searchPegawaiInput" :search-value="request('search')">
                    <x-slot:filters>
                        <div class="flex bg-slate-100 dark:bg-black/20 rounded-lg p-1">
                            <a href="#" data-filter-peran=""
                                class="px-3 py-1 text-xs font-bold rounded-md {{ !request('peran') ? 'bg-white shadow-sm text-primary' : 'text-slate-500' }}">Semua</a>
                            <a href="#" data-filter-peran="admin"
                                class="px-3 py-1 text-xs font-bold rounded-md {{ request('peran') == 'admin' ? 'bg-purple-100 text-purple-700 shadow-sm' : 'text-slate-500' }}">Admin</a>
                            <a href="#" data-filter-peran="petugas"
                                class="px-3 py-1 text-xs font-bold rounded-md {{ request('peran') == 'petugas' ? 'bg-orange-100 text-orange-700 shadow-sm' : 'text-slate-500' }}">Petugas</a>
                        </div>
                    </x-slot:filters>

                    <x-slot:header>
                        <th class="p-4 pl-6 font-medium cursor-pointer hover:text-primary transition-colors"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_pengguna', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                ID
                                @if(request('sort') == 'id_pengguna')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Nama Pegawai
                                @if(request('sort') == 'nama')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 font-medium">Role</th>
                        <th class="p-4 font-medium">Telepon</th>
                        <th class="p-4 font-medium">Alamat</th>
                        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors"
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                            <div class="flex items-center gap-1">
                                Status
                                @if(request('sort') == 'status')
                                    <span
                                        class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @else
                                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                                @endif
                            </div>
                        </th>
                        <th class="p-4 pr-6 font-medium text-right">Aksi</th>
                    </x-slot:header>

                    <x-slot:body>
                        @forelse($pegawai as $user)
                            <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                                <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold">
                                    {{ $user->id_pengguna }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold flex-shrink-0 overflow-hidden">
                                            @if($user->foto_profil)
                                                <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->nama }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                {{ substr($user->nama, 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="flex flex-col max-w-[220px]">
                                            <span
                                                class="font-bold text-slate-800 dark:text-white line-clamp-2 text-sm leading-tight"
                                                title="{{ $user->nama }}">
                                                {{ $user->nama }}
                                            </span>
                                            <span class="text-xs text-slate-500 dark:text-white/60 truncate"
                                                title="{{ $user->email }}">
                                                {{ $user->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if ($user->peran === 'admin')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary-dark dark:bg-accent/10 dark:text-accent border border-primary/20 dark:border-accent/20">
                                            Administrator
                                        </span>
                                    @elseif ($user->peran === 'owner')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-900 border border-gray-600 dark:border-gray-400">
                                            Owner
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                            Petugas
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">{{ $user->telepon ?? '-' }}</td>
                                <td class="p-4 max-w-[200px] truncate" title="{{ $user->alamat ?? '-' }}">
                                    {{ $user->alamat ?? '-' }}
                                </td>
                                <td class="p-4">
                                    @if ($user->status === 'aktif')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-500 border border-green-200 dark:border-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-500 border border-red-200 dark:border-red-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                    
                                    @if ($user->is_locked)
                                        <span class="ml-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white border border-red-700">
                                            LOCKED
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 pr-6 text-right flex justify-end gap-2">
                                    {{-- LOGIC TOMBOL EDIT --}}
                                        @php
                                            $canEdit = true;
                                            $currentUser = auth()->user();
                                            
                                            // 1. Admin/Petugas tidak bisa edit Owner
                                            if ($user->peran === 'owner' && $currentUser->peran !== 'owner') {
                                                $canEdit = false;
                                            }
                                            // 2. Admin tidak bisa edit sesama Admin (kecuali diri sendiri)
                                            // Owner BISA edit Admin
                                            if ($currentUser->peran === 'admin' && $user->peran === 'admin' && $user->id_pengguna !== $currentUser->id_pengguna) {
                                                $canEdit = false;
                                            }
                                        @endphp

                                        @if ($canEdit)
                                            <button onclick="openEditPegawai({{ $user->toJson() }})"
                                                class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                        @else
                                            <button disabled
                                                class="p-2 rounded-lg text-blue-300 dark:text-blue-800 cursor-not-allowed opacity-70"
                                                title="Edit (Dilindungi)">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                        @endif

                                        {{-- LOGIC TOMBOL DELETE --}}
                                        @php
                                            $canDelete = true;
                                            
                                            // 1. Owner tidak bisa dihapus oleh siapapun
                                            if ($user->peran === 'owner') {
                                                $canDelete = false;
                                            }
                                            // 2. Admin tidak bisa hapus Admin
                                            // Owner BISA hapus Admin
                                            if ($currentUser->peran === 'admin' && $user->peran === 'admin') {
                                                $canDelete = false;
                                            }
                                            // 3. User tidak bisa hapus diri sendiri (UI only, backend protected too)
                                            if ($user->id_pengguna === $currentUser->id_pengguna) {
                                                $canDelete = false;
                                            }
                                        @endphp
                                        
                                        @if ($canDelete)
                                            <form action="{{ route('kepegawaian.destroy', $user->id_pengguna) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 transition-colors"
                                                    title="Hapus">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        @else
                                            <button disabled
                                                class="p-2 rounded-lg text-red-300 dark:text-red-800 cursor-not-allowed opacity-70"
                                                title="Hapus (Dilindungi)">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 dark:text-white/40">Belum ada
                                    data pegawai.</td>
                            </tr>
                        @endforelse
                    </x-slot:body>
                </x-datatable>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH -->
    <div id="createModal" class="fixed inset-0 z-50 transition-all duration-200 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 dark:bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('createModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <!-- Panel Modal -->
                <div id="createModalPanel"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-left shadow-2xl transition-all duration-300 scale-95 sm:w-full sm:max-w-2xl">

                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-border-dark flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary dark:text-accent">person_add</span>
                            Tambah Pegawai
                        </h3>
                        <button onclick="closeModal('createModal')"
                            class="text-slate-500 dark:text-white/60 hover:text-slate-800 dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('kepegawaian.store') }}" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Lengkap</label>
                            <input type="text" name="nama"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-colors"
                                required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Email</label>
                                <input type="email" name="email"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Role</label>
                                <select name="peran" required
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer">
                                    <option value="" disabled selected>Pilih Role...</option>
                                    <option value="petugas">Petugas</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Telepon</label>
                                <input type="text" name="telepon"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Alamat</label>
                            <textarea name="alamat" rows="2"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Password</label>
                                <input type="password" name="password"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Konfirmasi
                                    Password</label>
                                <input type="password" name="password_confirmation"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                        </div>

                        <div
                            class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                            <button type="button" onclick="closeModal('createModal')"
                                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold transition-all shadow-sm dark:shadow-md hover:scale-105 active:scale-95 duration-200">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div id="editModal" class="fixed inset-0 z-50 transition-all duration-200 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 dark:bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div id="editModalPanel"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-2xl">
                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-border-dark flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">edit</span>
                            Edit Pegawai
                        </h3>
                        <button onclick="closeModal('editModal')"
                            class="text-slate-500 dark:text-white/60 hover:text-slate-800 dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <form id="editForm" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf @method('PUT')
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Lengkap</label>
                            <input type="text" id="edit_nama" name="nama"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Email</label>
                                <input type="email" id="edit_email" name="email"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Role</label>
                                <div id="edit_peran_container">
                                    <select id="edit_peran" name="peran"
                                        class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer">
                                        <option value="petugas">Petugas</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                    <div id="edit_peran_readonly" class="hidden">
                                        <div class="px-4 py-3 rounded-lg bg-gray-100 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-bold text-sm flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">shield_person</span>
                                            Root / Owner
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Telepon</label>
                                <input type="text" id="edit_telepon" name="telepon"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Status</label>
                                <select id="edit_status" name="status"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Alamat</label>
                            <textarea id="edit_alamat" name="alamat" rows="2"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none"></textarea>
                        </div>

                        <div id="unlockContainer"
                            class="hidden p-3 bg-red-50 dark:bg-red-500/10 rounded-lg border border-red-200 dark:border-red-500/20 mt-2 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                                <span class="material-symbols-outlined text-xl">lock</span>
                                <span class="text-xs font-bold uppercase">Akun Terkunci Permanen</span>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="unlock_account" id="unlock_account" value="1"
                                    class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                                <span class="text-sm font-bold text-slate-700 dark:text-white">Buka Kunci</span>
                            </label>
                        </div>
                        <div
                            class="p-3 bg-yellow-50 dark:bg-yellow-500/5 rounded-lg border border-yellow-200 dark:border-yellow-500/10 mt-2">
                            <p
                                class="text-[10px] text-yellow-600 dark:text-yellow-500 mb-2 font-bold uppercase tracking-wider">
                                Ubah Password (Opsional)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <input type="password" name="password"
                                    class="bg-white dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    placeholder="Password Baru">
                                <input type="password" name="password_confirmation"
                                    class="bg-white dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    placeholder="Konfirmasi">
                            </div>
                        </div>
                        <div
                            class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                            <button type="button" onclick="closeModal('editModal')"
                                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold transition-all shadow-sm dark:shadow-md hover:scale-105 active:scale-95 duration-200">Update
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditPegawai(user) {
            document.getElementById('edit_nama').value = user.nama;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_telepon').value = user.telepon;
            document.getElementById('edit_alamat').value = user.alamat;
            document.getElementById('edit_status').value = user.status;
            document.getElementById('edit_peran').value = user.peran;

            // PROTEKSI ROLE & STATUS
            const currentUserId = '{{ auth()->user()->id_pengguna }}';
            const statusSelect = document.getElementById('edit_status');
            const peranSelect = document.getElementById('edit_peran');
            const peranReadonly = document.getElementById('edit_peran_readonly');

            if (user.peran === 'owner') {
                // Akun Owner: Role tidak bisa diubah sama sekali (tetap Owner)
                peranSelect.classList.add('hidden');
                peranSelect.disabled = true;
                peranReadonly.classList.remove('hidden');
                
                // Status juga tidak bisa diubah untuk Owner (Safety)
                statusSelect.disabled = true;
                statusSelect.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
            } else if (user.id_pengguna === currentUserId) {
                // Edit diri sendiri (Admin/Petugas): Gak boleh ganti Role & Status sendiri
                peranSelect.classList.remove('hidden');
                peranSelect.disabled = true;
                peranReadonly.classList.add('hidden');
                peranSelect.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');

                statusSelect.disabled = true;
                statusSelect.classList.add('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
            } else {
                // Edit orang lain (dan bukan Owner): Normal
                peranSelect.classList.remove('hidden');
                peranSelect.disabled = false;
                peranReadonly.classList.add('hidden');
                peranSelect.classList.remove('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');

                statusSelect.disabled = false;
                statusSelect.classList.remove('bg-slate-100', 'dark:bg-slate-800/50', 'cursor-not-allowed');
            }

            // UNLOCK ACCOUNT UI
            const unlockContainer = document.getElementById('unlockContainer');
            if (user.is_locked) {
                unlockContainer.classList.remove('hidden');
            } else {
                unlockContainer.classList.add('hidden');
                document.getElementById('unlock_account').checked = false;
            }

            // Set action url
            document.getElementById('editForm').action = `{{ url('kepegawaian') }}/${user.id_pengguna}`;

            openModal('editModal');
        }
    </script>
</body>

</html>