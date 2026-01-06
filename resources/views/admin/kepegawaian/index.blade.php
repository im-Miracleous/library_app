@extends('layouts.app')

@section('title', 'Data Kepegawaian - Library App')
@section('header-title', 'Data Kepegawaian')

@push('scripts')
    @vite(['resources/js/live-search/live-search-kepegawaian.js', 'resources/js/logic/modals/kepegawaian-modal.js'])
    <script>
        window.currentUserId = "{{ auth()->user()->id_pengguna }}";
        window.currentUserRole = "{{ auth()->user()->peran }}";
    </script>
@endpush

@section('content')
    <div class="flex flex-col">
        <x-breadcrumb-component parent="Administrator" current="Kepegawaian" class="mb-6 animate-enter" />

        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
            <!-- Tombol Tambah -->
            <button onclick="openModal('createModal')"
                class="flex items-center gap-2 px-5 py-2.5 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-xl font-bold text-sm shadow-sm dark:shadow-lg dark:shadow-accent/10 transition-all hover:scale-105 active:scale-95 duration-200 cursor-pointer">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah Pegawai
            </button>

            <!-- Statistik Bar -->
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
                    <span class="text-sm font-bold text-purple-700 dark:text-purple-400">{{ $totalAdmin }}</span>
                </div>
                <div
                    class="flex items-center gap-2 px-3 py-1.5 bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/20 rounded-lg">
                    <span class="size-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-orange-700 dark:text-orange-400">Petugas:</span>
                    <span class="text-sm font-bold text-orange-700 dark:text-orange-400">{{ $totalPetugas }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <x-datatable :data="$pegawai" search-placeholder="Cari ID, nama, atau email..." search-id="searchPegawaiInput"
            :search-value="request('search')">
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
                                <div class="size-10 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-primary-dark dark:text-accent font-bold flex-shrink-0 overflow-hidden {{ $user->foto_profil ? 'cursor-pointer group relative' : '' }}"
                                    @if($user->foto_profil)
                                        onclick="openImageModal('{{ asset('storage/' . $user->foto_profil) }}', '{{ $user->nama }}')"
                                    @endif>
                                    @if($user->foto_profil)
                                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->nama }}"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-md">zoom_in</span>
                                        </div>
                                    @else
                                        {{ substr($user->nama, 0, 1) }}
                                    @endif
                                </div>
                                <div class="flex flex-col max-w-[220px]">
                                    <span class="font-bold text-slate-800 dark:text-white line-clamp-2 text-sm leading-tight"
                                        title="{{ $user->nama }}">
                                        {{ $user->nama }}
                                    </span>
                                    <span class="text-xs text-slate-500 dark:text-white/60 truncate" title="{{ $user->email }}">
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
                                <span
                                    class="ml-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white border border-red-700">
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
                                    // PENGECUALIAN: Jika akun terkunci, Admin boleh akses tombol ini (untuk membuka kunci)
                                    if (!$user->is_locked) {
                                        $canEdit = false;
                                    }
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
                                <button disabled class="p-2 rounded-lg text-red-300 dark:text-red-800 cursor-not-allowed opacity-70"
                                    title="Hapus (Dilindungi)">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400 dark:text-white/40">Belum ada data pegawai.</td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </div>

    <!-- MODAL TAMBAH -->
    <x-modal id="createModal" title="Tambah Pegawai" maxWidth="2xl">
        <form action="{{ route('kepegawaian.store') }}" method="POST" enctype="multipart/form-data"
            class="flex flex-col gap-5">
            @csrf

            <x-input name="nama" label="Nama Lengkap" required />

            <!-- Upload Foto Profil (Create) -->
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Foto
                    Profil</label>
                <div class="flex items-center gap-4">
                    <div id="create_preview_container"
                        class="hidden size-16 rounded-full overflow-hidden bg-slate-100 border border-slate-200 dark:border-white/10 shrink-0 relative">
                        <img id="create_preview_img" src="" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" id="create_foto_profil" name="foto_profil" accept="image/*"
                            class="block w-full text-sm text-slate-500 dark:text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent cursor-pointer">
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-[10px] text-slate-400 dark:text-white/40">*Max 2MB (JPG, PNG)</p>
                            <button type="button" id="create_cancel_btn" style="display: none"
                                class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1 text-[10px]">
                                <span class="material-symbols-outlined text-xs">delete</span> Hapus Foto
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input name="email" label="Email" type="email" required />
                <x-select name="peran" label="Role" placeholder="Pilih Role..." required>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Administrator</option>
                </x-select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input name="telepon" label="Telepon" />
            </div>

            <x-textarea name="alamat" label="Alamat" rows="2" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input name="password" label="Password" type="password" required />
                <x-input name="password_confirmation" label="Konfirmasi Password" type="password" required />
            </div>

            <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                <button type="button" onclick="closeModal('createModal')"
                    class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold transition-all shadow-sm dark:shadow-md hover:scale-105 active:scale-95 duration-200">Simpan</button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT -->
    <x-modal id="editModal" title="Edit Pegawai" maxWidth="2xl">
        <form id="editForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf @method('PUT')

            <x-input id="edit_nama" name="nama" label="Nama Lengkap" required />

            <!-- Upload Foto Profil (Edit) -->
            <input type="hidden" name="remove_foto_profil" id="edit_remove_foto_profil" value="0">
            <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Foto
                    Profil</label>
                <div class="flex items-center gap-4">
                    <div id="edit_preview_container"
                        class="hidden size-16 rounded-full overflow-hidden bg-slate-100 border border-slate-200 dark:border-white/10 shrink-0 relative">
                        <img id="edit_preview_img" src="" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" id="edit_foto_profil" name="foto_profil" accept="image/*"
                            class="block w-full text-sm text-slate-500 dark:text-white/60 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 dark:file:bg-accent/10 dark:file:text-accent cursor-pointer">
                        <div class="flex items-center gap-3 mt-1 h-5">
                            <p class="text-[10px] text-slate-400 dark:text-white/40">*Max 2MB. Kosongkan jika tidak diubah.
                            </p>

                            <!-- Buttons -->
                            <button type="button" id="edit_delete_btn" style="display: none"
                                class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1 text-[10px]">
                                <span class="material-symbols-outlined text-xs">delete</span> Hapus Foto
                            </button>
                            <button type="button" id="edit_restore_btn" style="display: none"
                                class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white font-medium transition-colors hidden inline-flex items-center gap-1 text-[10px]">
                                <span class="material-symbols-outlined text-xs">undo</span> Batal Hapus
                            </button>
                            <button type="button" id="edit_cancel_btn" style="display: none"
                                class="text-red-500 hover:text-red-600 font-medium transition-colors hidden inline-flex items-center gap-1 text-[10px]">
                                <span class="material-symbols-outlined text-xs">delete</span> Hapus Foto
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input id="edit_email" name="email" label="Email" type="email" required />

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Role</label>
                    <div id="edit_peran_container">
                        <select id="edit_peran" name="peran"
                            class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer">
                            <option value="petugas">Petugas</option>
                            <option value="admin">Administrator</option>
                        </select>
                        <div id="edit_peran_readonly" class="hidden">
                            <div
                                class="px-4 py-3 rounded-lg bg-gray-100 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-bold text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">shield_person</span>
                                Root / Owner
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input id="edit_telepon" name="telepon" label="Telepon" />
                <x-select id="edit_status" name="status" label="Status" placeholder="">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </x-select>
            </div>

            <x-textarea id="edit_alamat" name="alamat" label="Alamat" rows="2" />

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
                <p class="text-[10px] text-yellow-600 dark:text-yellow-500 mb-2 font-bold uppercase tracking-wider">
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

            <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                <button type="button" onclick="closeModal('editModal')"
                    class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold transition-all shadow-sm dark:shadow-md hover:scale-105 active:scale-95 duration-200">Update
                    Data</button>
            </div>
        </form>
    </x-modal>

    <x-image-zoom-modal />
@endsection