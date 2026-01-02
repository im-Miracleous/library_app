@extends('layouts.app')

@section('title', 'Data Anggota - Library App')
@section('header-title', 'Data Anggota')

@push('scripts')
    @vite(['resources/js/live-search/live-search-anggota.js', 'resources/js/logic/modals/anggota-modal.js'])
    <script>
        window.currentUserId = "{{ auth()->user()->id_pengguna }}";
    </script>
@endpush

@section('content')
    <div class="flex flex-col">
        <x-breadcrumb-component parent="Administrator" current="Anggota" class="mb-6 animate-enter" />

        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
            <!-- Tombol Tambah -->
            <button onclick="openModal('createModal')"
                class="flex items-center gap-2 px-5 py-2.5 bg-primary dark:bg-accent text-white dark:text-primary-dark rounded-xl font-bold text-sm shadow-sm dark:shadow-lg dark:shadow-accent/10 transition-all hover:scale-105 active:scale-95 duration-200 cursor-pointer">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah Anggota
            </button>

            <!-- Statistik Bar -->
            <div class="flex flex-wrap gap-3">
                <div
                    class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg">
                    <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Total:</span>
                    <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $totalAnggota }}</span>
                </div>
                <div
                    class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                    <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-green-700 dark:text-green-400">Aktif:</span>
                    <span class="text-sm font-bold text-green-700 dark:text-green-400">{{ $totalAktif }}</span>
                </div>
                <div
                    class="flex items-center gap-2 px-3 py-1.5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg">
                    <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-red-700 dark:text-red-400">Nonaktif:</span>
                    <span class="text-sm font-bold text-red-700 dark:text-red-400">{{ $totalNonaktif }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <x-datatable :data="$pengguna" search-placeholder="Cari ID, nama, atau email..." search-id="searchAnggotaInput"
            :search-value="request('search')">
            <x-slot:filters>
                <div class="flex bg-slate-100 dark:bg-black/20 rounded-lg p-1">
                    <a href="#" data-filter-status=""
                        class="px-3 py-1 text-xs font-bold rounded-md {{ !request('status') ? 'bg-white shadow-sm text-primary' : 'text-slate-500' }}">Semua</a>
                    <a href="#" data-filter-status="aktif"
                        class="px-3 py-1 text-xs font-bold rounded-md {{ request('status') == 'aktif' ? 'bg-green-100 text-green-700 shadow-sm' : 'text-slate-500' }}">Aktif</a>
                    <a href="#" data-filter-status="nonaktif"
                        class="px-3 py-1 text-xs font-bold rounded-md {{ request('status') == 'nonaktif' ? 'bg-red-100 text-red-700 shadow-sm' : 'text-slate-500' }}">Nonaktif</a>
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
                        Nama Anggota
                        @if(request('sort') == 'nama')
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
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
                @forelse($pengguna as $user)
                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold">
                            {{ $user->id_pengguna }}
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <!-- Avatar Initials -->
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
                        <td class="p-4">{{ $user->telepon ?? '-' }}</td>
                        <td class="p-4 max-w-[200px] truncate" title="{{ $user->alamat ?? '-' }}">
                            {{ $user->alamat ?? '-' }}
                        </td>
                        <td class="p-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold {{ $user->status == 'aktif' ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-500' : 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-500' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="p-4 pr-6 text-right flex justify-end gap-2">
                            <button onclick="openEditAnggota('{{ $user->id_pengguna }}')"
                                class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                                title="Edit">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </button>
                            @if($user->peran === 'admin')
                                <button disabled class="p-2 rounded-lg text-red-300 dark:text-red-800 cursor-not-allowed opacity-70"
                                    title="Hapus (Dilindungi)">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            @else
                                <form action="{{ route('anggota.destroy', $user->id_pengguna) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 transition-colors"
                                        title="Hapus">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 dark:text-white/40">Belum ada data.</td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </div>

    <!-- MODAL TAMBAH -->
    <x-modal id="createModal" title="Tambah Anggota" maxWidth="2xl">
        <form action="{{ route('anggota.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
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
    <x-modal id="editModal" title="Edit Anggota" maxWidth="2xl">
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
                <x-input id="edit_telepon" name="telepon" label="Telepon" />
            </div>

            <x-textarea id="edit_alamat" name="alamat" label="Alamat" rows="2" />

            <div class="flex flex-col gap-2">
                <x-select id="edit_status" name="status" label="Status" placeholder="">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </x-select>
            </div>

            <!-- UNLOCK ACCOUNT -->
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

            <!-- CHANGE PASSWORD -->
            <div
                class="p-3 bg-yellow-50 dark:bg-yellow-500/5 rounded-lg border border-yellow-200 dark:border-yellow-500/10 mt-2">
                <p class="text-[10px] text-yellow-600 dark:text-yellow-500 mb-2 font-bold uppercase tracking-wider">
                    Ubah Password (Opsional)
                </p>
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