@extends('layouts.app')

@section('title', 'Data Kategori - Library App')
@section('header-title', 'Data Kategori')

@push('scripts')
    @vite(['resources/js/live-search/live-search-kategori.js'])
@endpush

@section('content')
    <div class="flex flex-col">
        <x-breadcrumb-component parent="Administrator" current="Kategori" class="mb-6 animate-enter" />

        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">

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
                    <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Total Kategori:</span>
                    <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $totalKategori }}</span>
                </div>
                <div
                    class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                    <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-green-700 dark:text-green-400">Baru Bulan Ini:</span>
                    <span class="text-sm font-bold text-green-700 dark:text-green-400">{{ $kategoriBaru }}</span>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <x-datatable :data="$kategori" search-placeholder="Cari ID atau kategori..." search-id="searchKategoriInput"
            :search-value="request('search')">
            <x-slot:header>
                <th class="p-4 pl-6 font-medium w-20 cursor-pointer hover:text-primary transition-colors"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_kategori', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        ID
                        @if(request('sort') == 'id_kategori')
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
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
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium">Deskripsi</th>
                <th class="p-4 pr-6 font-medium text-right w-32">Aksi</th>
            </x-slot:header>

            <x-slot:body>
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
                                class="cursor-pointer p-2 rounded-lg hover:bg-blue-500/20 text-blue-600 transition-colors"
                                title="Edit">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </button>
                            <form action="{{ route('kategori.destroy', $item->id_kategori) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus kategori ini?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="cursor-pointer p-2 rounded-lg hover:bg-red-500/20 text-red-600 transition-colors"
                                    title="Hapus">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-slate-500 dark:text-white/40">Belum ada kategori buku.</td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>

        <!-- MODAL TAMBAH -->
        <x-modal id="createModal" title="Tambah Kategori">
            <form action="{{ route('kategori.store') }}" method="POST" class="flex flex-col gap-5">
                @csrf
                <x-input name="nama_kategori" label="Nama Kategori" placeholder="Misal: Fiksi Ilmiah" required />
                <x-textarea name="deskripsi" label="Deskripsi" placeholder="Keterangan singkat..." />

                <div class="mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                    <button type="button" onclick="closeModal('createModal')"
                        class="cursor-pointer px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                    <button type="submit"
                        class="cursor-pointer px-4 py-2 rounded-lg bg-primary/20 dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Simpan</button>
                </div>
            </form>
        </x-modal>

        <!-- MODAL EDIT -->
        <x-modal id="editModal" title="Edit Kategori">
            <form id="editForm" method="POST" class="flex flex-col gap-5">
                @csrf @method('PUT')
                <x-input id="edit_nama" name="nama_kategori" label="Nama Kategori" required />
                <x-textarea id="edit_deskripsi" name="deskripsi" label="Deskripsi" />

                <div class="mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-[#36271F]">
                    <button type="button" onclick="closeModal('editModal')"
                        class="cursor-pointer px-4 py-2 rounded-lg border border-slate-200 dark:border-[#36271F] text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold">Batal</button>
                    <button type="submit"
                        class="cursor-pointer px-4 py-2 rounded-lg bg-primary/20 dark:bg-accent text-primary-dark text-sm font-bold hover:brightness-110">Update</button>
                </div>
            </form>
        </x-modal>
    </div>
@endsection