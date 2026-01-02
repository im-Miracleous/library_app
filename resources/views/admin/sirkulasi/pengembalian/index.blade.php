@extends('layouts.app')

@section('title', 'Pengembalian & Denda - Library App')

@section('header-title', 'Pengembalian & Denda')

@section('content')
    <div class="flex flex-col gap-4">
        <div class="animate-enter py-1">
            <x-breadcrumb-component parent="Sirkulasi" current="Pengembalian" />
        </div>

        <!-- Table Container replaced with x-datatable -->
        <x-datatable :data="$peminjaman" search-placeholder="Cari Kode atau Peminjam..." search-id="returnSearchInput" :search-value="request('search')">
            <x-slot:header>
                <th class="p-4 pl-6 font-medium w-44 cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_peminjaman', 'direction' => request('direction', 'desc') == 'desc' ? 'asc' : 'desc']) }}'">
                    <div class="flex items-center gap-1">
                        Kode
                        @if(request('sort', 'id_peminjaman') == 'id_peminjaman')
                            <span class="material-symbols-outlined text-sm">{{ request('direction', 'desc') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium w-72 cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_anggota', 'direction' => request('direction', 'desc') == 'desc' ? 'asc' : 'desc']) }}'">
                    <div class="flex items-center gap-1">
                        Peminjam
                        @if(request('sort') == 'nama_anggota')
                            <span class="material-symbols-outlined text-sm">{{ request('direction', 'desc') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium w-40 cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_pinjam', 'direction' => request('direction', 'desc') == 'desc' ? 'asc' : 'desc']) }}'">
                    <div class="flex items-center gap-1">
                        Tgl Pinjam
                        @if(request('sort') == 'tanggal_pinjam')
                            <span class="material-symbols-outlined text-sm">{{ request('direction', 'desc') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium w-40 cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_jatuh_tempo', 'direction' => request('direction', 'desc') == 'desc' ? 'asc' : 'desc']) }}'">
                    <div class="flex items-center gap-1">
                        Jatuh Tempo
                        @if(request('sort') == 'tanggal_jatuh_tempo')
                            <span class="material-symbols-outlined text-sm">{{ request('direction', 'desc') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium text-center w-40">Sisa Waktu</th>
                <th class="p-4 font-medium text-right pr-6">Aksi</th>
            </x-slot:header>

            <x-slot:body>
                @forelse($peminjaman as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 pl-6 font-mono font-bold text-primary dark:text-accent whitespace-nowrap">
                            {{ $item->id_peminjaman }}
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
                        <td class="p-4 text-center">
                            @php
                                $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();
                                $diffDays = now()->startOfDay()->diffInDays($jatuhTempo, false);
                            @endphp
                            
                            @if($diffDays < 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 border-red-200 dark:border-red-500/30 animate-pulse">
                                    <span class="material-symbols-outlined text-sm">warning</span>
                                    Telat {{ abs($diffDays) }} hari
                                </span>
                            @elseif($diffDays == 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 border-amber-200 dark:border-amber-500/30">
                                    <span class="material-symbols-outlined text-sm">event</span>
                                    Hari Ini
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 border-blue-200 dark:border-blue-500/30">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    {{ $diffDays }} hari lagi
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right pr-6">
                            <a href="{{ route('pengembalian.show', $item->id_peminjaman) }}"
                                class="px-3 py-1 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary-dark shadow-md shadow-primary/30 transition-all inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">sync_alt</span>
                                Proses
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-50">data_loss_prevention</span>
                                <span>Tidak ada peminjaman yang sedang berjalan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/live-search/live-search-sirkulasi-pengembalian.js'])
@endpush