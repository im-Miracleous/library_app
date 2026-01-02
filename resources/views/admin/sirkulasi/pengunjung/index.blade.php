@extends('layouts.app')

@section('title', 'Data Pengunjung - Library App')
@section('header-title', 'Data Pengunjung')

@push('scripts')
    @vite(['resources/js/live-search/live-search-pengunjung.js', 'resources/js/logic/modals/pengunjung.js'])
@endpush

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-enter">
            <x-breadcrumb-component parent="Sirkulasi" current="Data Pengunjung" />

            <div class="flex gap-2">
                <button onclick="window.print()"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm active:scale-95">
                    <span class="material-symbols-outlined text-lg">print</span>
                    Laporan Pengunjung
                </button>
            </div>
        </div>

        <!-- Input Section (Guest Book) -->
        <div
            class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter shadow-sm">
            <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-6 flex items-center gap-2">
                <div class="size-8 rounded-lg bg-primary/10 dark:bg-accent/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary dark:text-accent">edit_note</span>
                </div>
                Input Buku Tamu
            </h3>
            <form action="{{ route('pengunjung.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <x-input id="nama_pengunjung" name="nama_pengunjung" label="Nama Pengunjung" placeholder="Masukkan nama..."
                    required />

                <x-select id="jenis_pengunjung" name="jenis_pengunjung" label="Status / Role" placeholder="">
                    <option value="umum">Umum</option>
                    <option value="anggota">Anggota</option>
                    <option value="petugas">Staff / Petugas</option>
                    <option value="admin">Admin</option>
                </x-select>

                <x-input id="keperluan" name="keperluan" label="Keperluan (Opsional)"
                    placeholder="Contoh: Baca buku, Pinjam..." />

                <button type="submit"
                    class="bg-primary dark:bg-accent text-white dark:text-primary-dark hover:brightness-110 px-4 py-2.5 rounded-lg font-bold text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm transition-all duration-200 h-[46px] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Catat Kunjungan
                </button>
            </form>
        </div>

        <!-- Table Section -->
        <x-datatable :data="$pengunjung" search-placeholder="Cari nama atau keperluan..." search-id="searchInput"
            :search-value="request('search')">
            <x-slot:header>
                <th class="p-4 pl-6 font-medium w-16">No</th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_pengunjung', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Nama Pengunjung
                        @if(request('sort') == 'nama_pengunjung')
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'jenis_pengunjung', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Status
                        @if(request('sort') == 'jenis_pengunjung')
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium">Keperluan</th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Tanggal Masuk
                        @if(request('sort') == 'created_at')
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium text-right pr-6">Aksi</th>
            </x-slot:header>

            <x-slot:body>
                @forelse($pengunjung as $index => $item)
                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 pl-6 font-mono text-slate-400 font-bold">
                            {{ $pengunjung->firstItem() + $index }}
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-slate-800 dark:text-white">{{ $item->nama_pengunjung }}</span>
                            @if($item->id_pengguna)
                                <div
                                    class="text-[10px] text-green-600 dark:text-green-400 flex items-center gap-1 mt-0.5 font-bold">
                                    <span class="material-symbols-outlined text-[12px]">verified</span>Terdaftar
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            @php
                                $badgeClass = match ($item->jenis_pengunjung) {
                                    'umum' => 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300',
                                    'anggota' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                                    'petugas' => 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400',
                                    'admin' => 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400',
                                    default => 'bg-slate-100'
                                };
                                $roleDisplay = $item->jenis_pengunjung === 'petugas' ? 'Staff' : ucfirst($item->jenis_pengunjung);
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $badgeClass }}">
                                {{ $roleDisplay }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-600 dark:text-white/70 text-sm">{{ $item->keperluan ?? '-' }}</td>
                        <td class="p-4 font-mono text-xs text-slate-500 dark:text-white/50">
                            {{ $item->created_at->translatedFormat('d F Y') }}
                            <div class="text-slate-800 dark:text-white font-bold">{{ $item->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="p-4 text-right pr-6">
                            <div class="flex justify-end gap-1">
                                <button onclick='openEditPengunjung(@json($item))'
                                    class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                    title="Edit Log">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <form action="{{ route('pengunjung.destroy', $item->id_pengunjung) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus log ini?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                        title="Hapus Log">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-50">data_loss_prevention</span>
                                <span>Tidak ada data pengunjung.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </div>

    <!-- MODAL EDIT -->
    <x-modal id="editModal" title="Edit Data Pengunjung" maxWidth="lg">
        <x-slot:title_icon>
            <span class="material-symbols-outlined text-blue-500">edit_note</span>
        </x-slot:title_icon>

        <form id="editForm" method="POST" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            <x-input id="edit_nama" name="nama_pengunjung" label="Nama Pengunjung" required />

            <x-select id="edit_jenis" name="jenis_pengunjung" label="Status / Role" required placeholder="">
                <option value="umum">Umum</option>
                <option value="anggota">Anggota</option>
                <option value="petugas">Staff / Petugas</option>
                <option value="admin">Admin</option>
            </x-select>

            <x-input id="edit_keperluan" name="keperluan" label="Keperluan" />

            <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                <button type="button" onclick="closeModal('editModal')"
                    class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary dark:bg-accent text-white dark:text-primary-dark text-sm font-bold transition-all shadow-sm hover:scale-105 active:scale-95 duration-200">Simpan
                    Perubahan</button>
            </div>
        </form>
    </x-modal>
@endsection