@extends('layouts.app')

@section('title', 'Transaksi Peminjaman - Library App')

@section('header-title', 'Transaksi Peminjaman')

@section('content')
    <div class="flex flex-col gap-4">
        <div class="animate-enter py-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <x-breadcrumb-component parent="Sirkulasi" current="Peminjaman" />

                <a href="{{ route('peminjaman.create') }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-surface dark:bg-accent text-primary-dark hover:bg-amber-300 dark:hover:bg-amber-500 rounded-xl font-bold text-sm transition-all shadow-sm hover:translate-y-[-2px]">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Transaksi Baru
                </a>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="animate-enter delay-[50ms]">
            <div class="flex flex-wrap gap-2 border-b border-slate-400 dark:border-white/10 pb-1">
                <a href="{{ route('peminjaman.index') }}"
                    class="px-4 py-2 text-sm font-bold transition-all {{ !request('status') ? 'border-b-2 border-primary text-primary dark:text-accent dark:border-accent' : 'text-gray-500 hover:text-primary dark:text-white/60 dark:hover:text-white border-b-2 border-transparent' }}">
                    Semua
                </a>
                <a href="{{ route('peminjaman.index', ['status' => 'menunggu_verifikasi']) }}"
                    class="px-4 py-2 text-sm font-bold transition-all {{ request('status') == 'menunggu_verifikasi' ? 'border-b-2 border-orange-600 text-orange-600 dark:text-orange-400 dark:border-orange-400' : 'text-gray-500 hover:text-orange-600 dark:text-white/60 dark:hover:text-orange-400 border-b-2 border-transparent' }}">
                    Menunggu Verifikasi
                </a>
                <a href="{{ route('peminjaman.index', ['status' => 'berjalan']) }}"
                    class="px-4 py-2 text-sm font-bold transition-all {{ request('status') == 'berjalan' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'text-gray-500 hover:text-blue-600 dark:text-white/60 dark:hover:text-blue-400 border-b-2 border-transparent' }}">
                    Sedang Berjalan
                </a>
                <a href="{{ route('peminjaman.index', ['status' => 'selesai']) }}"
                    class="px-4 py-2 text-sm font-bold transition-all {{ request('status') == 'selesai' ? 'border-b-2 border-green-600 text-green-600 dark:text-green-400 dark:border-green-400' : 'text-gray-500 hover:text-green-600 dark:text-white/60 dark:hover:text-green-400 border-b-2 border-transparent' }}">
                    Selesai
                </a>
            </div>
        </div>

        <!-- Table Container replaced with x-datatable -->
        <x-datatable :data="$peminjaman" search-placeholder="Cari Kode atau Peminjam..." search-id="searchInput"
            :search-value="request('search')">
            <x-slot:filters>
                <!-- Filter Slot is now handled by Tabs above -->
            </x-slot:filters>

            <x-slot:header>
                <th class="p-4 pl-6 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_peminjaman', 'direction' => request('direction', 'desc') == 'desc' ? 'asc' : 'desc']) }}'">
                    <div class="flex items-center gap-1">
                        Kode
                        @if(request('sort', 'id_peminjaman') == 'id_peminjaman')
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction', 'desc') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
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
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium text-center">Buku</th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_pinjam', 'direction' => request('direction', 'desc') == 'desc' ? 'asc' : 'desc']) }}'">
                    <div class="flex items-center gap-1">
                        Tgl Pinjam
                        @if(request('sort') == 'tanggal_pinjam')
                            <span
                                class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium">Jatuh Tempo</th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'status_transaksi', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Status
                        @if(request('sort') == 'status_transaksi')
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
                        <td class="p-4 text-center font-bold text-slate-700 dark:text-white">{{ $item->total_buku }}</td>
                        <td class="p-4 text-slate-600 dark:text-white/70">
                            {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}
                        </td>
                        <td class="p-4">
                            @php
                                $tglJatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo);
                                $isLate = $tglJatuhTempo->startOfDay()->lt(now()->startOfDay()) && $item->status_transaksi == 'berjalan';
                                $isExtended = $item->is_extended ?? false; 
                            @endphp

                            <div class="flex flex-col">
                                <span class="{{ $isLate ? 'text-red-600 font-bold animate-pulse' : ($isExtended ? 'text-cyan-600 dark:text-cyan-400 font-bold' : 'text-slate-600 dark:text-white/70') }}">
                                    {{ $tglJatuhTempo->translatedFormat('d M Y') }}
                                </span>
                                
                                <div class="flex items-center gap-1 mt-1">
                                    @if($isLate)
                                        <span class="text-[10px] bg-red-100 text-red-600 px-1 rounded uppercase font-bold">Telat</span>
                                    @endif
                                    
                                    @if($isExtended)
                                        <span class="text-[9px] bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 px-1.5 py-0.5 rounded uppercase font-bold tracking-wider w-fit">
                                            Extend
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            @php
                                $badgeClass = match ($item->status_transaksi) {
                                    'berjalan' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                                    'selesai' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
                                    'menunggu_verifikasi' => 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400',
                                    'ditolak' => 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400',
                                    default => 'bg-slate-100 text-slate-600'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                {{ str_replace('_', ' ', $item->status_transaksi) }}
                            </span>
                        </td>
                        <td class="p-4 text-right pr-6">
                            <a href="{{ route('peminjaman.show', $item->id_peminjaman) }}"
                                class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 dark:hover:bg-white/10 transition-colors inline-block"
                                title="Lihat Detail">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-slate-400 dark:text-white/40">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-50">event_busy</span>
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
    @vite(['resources/js/live-search/live-search-sirkulasi-peminjaman.js'])
@endpush