@extends('layouts.app')

@section('title', 'Laporan Transaksi - Library App')
@section('header-title', 'Laporan Transaksi')

@push('scripts')
    @vite(['resources/js/live-search/live-search-laporan-transaksi.js'])
@endpush

@section('content')
    <div class="flex flex-col">
        <!-- Header & Breadcrumb -->
        <x-breadcrumb-component parent="Laporan" current="Transaksi" class="mb-6 animate-enter" />

        <!-- Filters -->
        <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm mb-6 animate-enter delay-100">
            <form action="{{ route('laporan.peminjaman') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-auto">
                    <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm">
                </div>
                <div class="w-full md:w-auto">
                    <label class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm">
                </div>
                <div class="w-full md:w-48">
                    <x-select name="status" label="Status" placeholder="Semua" :selected="$status">
                        <option value="berjalan" {{ $status == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                        <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </x-select>
                </div>
                <button type="submit"
                    class="w-full md:w-auto px-6 py-2.5 bg-primary text-white dark:bg-accent dark:text-primary-dark rounded-xl font-bold hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 shadow-sm duration-200">
                    <span class="material-symbols-outlined text-lg">filter_list</span>
                    Filter
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-enter delay-200">
            <div class="bg-blue-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[100px]">receipt_long</span>
                </div>
                <div class="relative z-10">
                    <div class="text-blue-100 text-[10px] font-bold uppercase tracking-wider mb-1">Total Transaksi</div>
                    <div class="text-2xl font-bold">{{ number_format($totalTransaksi) }}</div>
                </div>
            </div>
            <div class="bg-emerald-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[100px]">menu_book</span>
                </div>
                <div class="relative z-10">
                    <div class="text-emerald-100 text-[10px] font-bold uppercase tracking-wider mb-1">Total Buku Keluar</div>
                    <div class="text-2xl font-bold">{{ number_format($totalBukuDipinjam) }}</div>
                </div>
            </div>
            <div class="bg-orange-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[100px]">pending</span>
                </div>
                <div class="relative z-10">
                    <div class="text-orange-100 text-[10px] font-bold uppercase tracking-wider mb-1">Sedang Berjalan</div>
                    <div class="text-2xl font-bold">{{ number_format($transaksiBerjalan) }}</div>
                </div>
            </div>
            <div class="bg-indigo-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[100px]">check_circle</span>
                </div>
                <div class="relative z-10">
                    <div class="text-indigo-100 text-[10px] font-bold uppercase tracking-wider mb-1">Selesai</div>
                    <div class="text-2xl font-bold">{{ number_format($transaksiSelesai) }}</div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <x-datatable :data="$peminjaman" search-placeholder="Cari ID transaksi atau nama..." search-id="searchTransaksiInput" :search-value="request('search')">
            <x-slot:header>
                <th class="p-4 pl-6 font-medium w-44 cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_peminjaman', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Kode
                        @if(request('sort') == 'id_peminjaman')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_anggota', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Peminjam
                        @if(request('sort') == 'nama_anggota')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_pinjam', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Tanggal Pinjam
                        @if(request('sort') == 'tanggal_pinjam')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_jatuh_tempo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Jatuh Tempo
                        @if(request('sort') == 'tanggal_jatuh_tempo')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium text-center">Jml Buku</th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none text-right pr-6"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'status_transaksi', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center justify-end gap-1">
                        Status
                        @if(request('sort') == 'status_transaksi')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                @if(auth()->user()->peran == 'owner')
                    <th class="p-4 font-medium text-center">Aksi</th>
                @endif
            </x-slot:header>

            <x-slot:body>
                @forelse($peminjaman as $item)
                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 pl-6 font-mono text-sm text-slate-600 dark:text-white/70 whitespace-nowrap">
                            <span class="font-bold text-primary dark:text-accent">{{ $item->id_peminjaman }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $item->nama_anggota }}</span>
                                <span class="text-xs text-slate-500 dark:text-white/50">{{ $item->email_anggota ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-slate-600 dark:text-white/70">
                            {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}
                        </td>
                        <td class="p-4 text-slate-600 dark:text-white/70">
                            {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                        </td>
                        <td class="p-4 text-center font-bold text-slate-700 dark:text-white">{{ $item->total_buku }}</td>
                        <td class="p-4 text-right pr-6">
                            @php
                                $badgeClass = match($item->status_transaksi) {
                                    'berjalan' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                    'selesai' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                                    'terlambat' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                                    default => 'bg-slate-100 text-slate-600'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $badgeClass }}">
                                {{ $item->status_transaksi }}
                            </span>
                        </td>
                        @if(auth()->user()->peran == 'owner')
                            <td class="p-4">
                                <div class="flex justify-center gap-1">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('peminjaman.edit', $item->id_peminjaman) }}"
                                        class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                        title="Edit Transaksi">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('peminjaman.destroy', $item->id_peminjaman) }}" method="POST"
                                        onsubmit="return confirm('PERINGATAN: Menghapus data transaksi akan menghapus history selamanya. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                            title="Hapus Paksa">
                                            <span class="material-symbols-outlined text-lg">delete_forever</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->peran == 'owner' ? 7 : 6 }}" class="p-12 text-center text-slate-400 dark:text-white/40">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-50">search_off</span>
                                <span>Tidak ada data ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </div>
@endsection