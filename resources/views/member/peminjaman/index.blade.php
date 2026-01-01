@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Library App')

@section('header-title', 'Riwayat Transaksi')

@section('content')
    <div class="flex flex-col gap-4">
        <div class="animate-enter py-1">
            <!-- Tabs -->
            <div
                class="flex items-center gap-2 mb-4 bg-white dark:bg-surface-dark p-1 rounded-2xl w-fit shadow-sm border border-primary/10 dark:border-white/5">
                <a href="{{ route('member.peminjaman.index', ['tab' => 'diajukan']) }}"
                    class="px-6 py-2 rounded-xl text-sm font-bold transition-all {{ $tab == 'diajukan' ? 'bg-primary text-white shadow-md' : 'text-primary-mid dark:text-white/60 hover:bg-primary/5' }}">
                    Diajukan
                </a>
                <a href="{{ route('member.peminjaman.index', ['tab' => 'berjalan']) }}"
                    class="px-6 py-2 rounded-xl text-sm font-bold transition-all {{ $tab == 'berjalan' ? 'bg-primary text-white shadow-md' : 'text-primary-mid dark:text-white/60 hover:bg-primary/5' }}">
                    Sedang Berjalan
                </a>
                <a href="{{ route('member.peminjaman.index', ['tab' => 'selesai']) }}"
                    class="px-6 py-2 rounded-xl text-sm font-bold transition-all {{ $tab == 'selesai' ? 'bg-primary text-white shadow-md' : 'text-primary-mid dark:text-white/60 hover:bg-primary/5' }}">
                    Riwayat Selesai
                </a>
            </div>

            <div class="mt-2">
                <x-datatable :data="$peminjaman">
                    <x-slot name="header">
                        <th class="p-4">Kode Transaksi</th>
                        <th class="p-4">Pustaka</th>
                        <th class="p-4">Tanggal Pengajuan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </x-slot>

                    <x-slot name="body">
                        @forelse($peminjaman as $loan)
                            <tr
                                class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border-b border-primary/5 dark:border-white/5">
                                <td class="p-4 font-bold text-primary-dark dark:text-white align-top whitespace-nowrap">
                                    {{ $loan->id_peminjaman }}
                                </td>
                                <td class="p-4 align-top">
                                    <div class="flex flex-col gap-1">
                                        @foreach($loan->details as $detail)
                                            <div class="text-xs text-slate-700 dark:text-white/80 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[16px] text-primary/60">menu_book</span>
                                                <div>
                                                    <span
                                                        class="font-bold block">{{ $detail->buku->judul ?? 'Buku dihapus' }}</span>
                                                    <span
                                                        class="text-slate-500 dark:text-white/50">{{ $detail->buku->penulis ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4 align-top whitespace-nowrap">
                                    <p class="text-sm font-medium text-slate-700 dark:text-white/80">
                                        {{ \Carbon\Carbon::parse($loan->created_at)->translatedFormat('d F Y') }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-white/50">
                                        {{ \Carbon\Carbon::parse($loan->created_at)->format('H:i:s') }}
                                    </p>
                                </td>
                                <td class="p-4 align-top">
                                    @if($loan->status_transaksi == 'menunggu_verifikasi')
                                        <span
                                            class="px-2 py-1 rounded-md bg-orange-100 text-orange-600 text-[10px] font-bold uppercase tracking-wider block w-fit whitespace-nowrap">
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif($loan->status_transaksi == 'berjalan')
                                                            <span
                                                                class="px-2 py-1 rounded-md bg-blue-100 text-blue-600 text-[10px] font-bold uppercase tracking-wider block w-fit whitespace-nowrap">
                                                                Sedang Berjalan
                                                            </span>
                                                            @php
                                                                $isOverdue = \Carbon\Carbon::parse($loan->tanggal_jatuh_tempo)->isPast();
                                                            @endphp
                                         <p
                                                                class="text-[10px] mt-1 whitespace-nowrap {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-bold animate-pulse' : 'text-slate-500 dark:text-white/40' }}">
                                                                Jatuh Tempo: {{ \Carbon\Carbon::parse($loan->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                                            </p>
                                    @elseif($loan->status_transaksi == 'selesai')
                                        <span
                                            class="px-2 py-1 rounded-md bg-green-100 text-green-600 text-[10px] font-bold uppercase tracking-wider block w-fit whitespace-nowrap">
                                            Selesai
                                        </span>
                                    @elseif($loan->status_transaksi == 'ditolak')
                                        <span
                                            class="px-2 py-1 rounded-md bg-red-100 text-red-600 text-[10px] font-bold uppercase tracking-wider block w-fit whitespace-nowrap">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right align-top whitespace-nowrap">
                                    <a href="{{ route('member.peminjaman.show', $loan->id_peminjaman) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary/5 hover:bg-primary/10 text-primary text-xs font-bold transition-colors">
                                        Detail
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500 dark:text-white/60">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-primary/5 dark:bg-white/5 p-4 rounded-full mb-3">
                                            <span class="material-symbols-outlined text-[32px] opacity-50">history_edu</span>
                                        </div>
                                        <p class="font-bold">Tidak ada data transaksi</p>
                                        <p class="text-xs">Belum ada riwayat peminjaman pada tab ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-datatable>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $peminjaman->links() }}
        </div>
    </div>
@endsection