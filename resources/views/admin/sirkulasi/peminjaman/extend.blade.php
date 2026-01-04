@extends('layouts.app')

@section('title', 'Konfirmasi Perpanjangan')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-0 animate-enter">
    
    <div class="mb-6 flex items-center gap-2 text-sm text-slate-500 dark:text-white/50">
        <a href="{{ route('peminjaman.index') }}" class="hover:text-primary transition-colors">Peminjaman</a>
        <span class="material-symbols-outlined text-base">chevron_right</span>
        <span class="text-slate-800 dark:text-white font-bold">Konfirmasi Perpanjangan</span>
    </div>

    <form action="{{ route('peminjaman.extend.process', $peminjaman->id_peminjaman) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-white/5 p-6 shadow-sm">
                    <h3 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white mb-4">
                        <span class="material-symbols-outlined text-blue-500">person</span>
                        Informasi Peminjam
                    </h3>
                    <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5">
                        <div class="size-12 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl">
                            {{ substr($peminjaman->pengguna->nama_lengkap ?? $peminjaman->pengguna->username, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-base font-bold text-slate-800 dark:text-white">
                                {{ $peminjaman->pengguna->nama_lengkap ?? $peminjaman->pengguna->username }}
                            </div>
                            <div class="text-sm text-slate-500 dark:text-white/60">
                                {{ $peminjaman->pengguna->email }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-white/5 p-6 shadow-sm">
                    <h3 class="flex items-center gap-2 text-lg font-bold text-slate-800 dark:text-white mb-4">
                        <span class="material-symbols-outlined text-emerald-500">menu_book</span>
                        Buku yang Diperpanjang
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($peminjaman->details as $detail)
                        <div class="flex gap-4 p-3 hover:bg-slate-50 dark:hover:bg-white/5 rounded-xl transition-colors border border-transparent hover:border-slate-100 dark:hover:border-white/5 items-center">
                            <div class="w-12 h-16 bg-slate-200 dark:bg-white/10 rounded-md flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-slate-400 text-xl">book_2</span>
                            </div>
                            
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm line-clamp-1">
                                    {{ $detail->buku->judul }}
                                </h4>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-6">
                    <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/30 dark:border-primary/30 p-6 shadow-lg relative overflow-hidden">
                        
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-500/10 rounded-full blur-3xl"></div>

                        <h3 class="relative text-lg font-bold text-slate-800 dark:text-white mb-6">
                            Rincian Waktu
                        </h3>

                        <div class="relative space-y-6">
                            <div class="flex justify-between items-center group opacity-60">
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Jatuh Tempo Lama</div>
                                <div class="text-right">
                                    <div class="font-mono font-bold text-slate-700 dark:text-white line-through decoration-red-500 decoration-2">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex items-center justify-center py-2">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-dashed border-slate-300 dark:border-white/20"></div>
                                </div>
                                <div class="relative z-10 bg-yellow-100 text-yellow-700 border border-yellow-200 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 shadow-sm">
                                    <span class="material-symbols-outlined text-[14px]">add</span>
                                    
                                    {{-- LOGIKA PERBAIKAN: Menghitung selisih hari dengan startOfDay() agar bulat --}}
                                    @php
                                        $lama = \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->startOfDay();
                                        $baru = \Carbon\Carbon::parse($newDueDate)->startOfDay();
                                        $selisih = $lama->diffInDays($baru);
                                    @endphp
                                    
                                    {{ $selisih }} Hari
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-yellow-50 to-transparent dark:from-yellow-500/10 p-4 rounded-xl border border-yellow-200 dark:border-yellow-500/30 text-center">
                                <div class="text-xs font-bold uppercase tracking-wider text-yellow-600 dark:text-yellow-400 mb-1">Jatuh Tempo Baru</div>
                                <div class="font-mono text-2xl font-bold text-slate-800 dark:text-white">
                                    {{ \Carbon\Carbon::parse($newDueDate)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1">
                                    Sampai pukul 23:59 WIB
                                </div>
                            </div>
                        </div>

                        <p class="mt-6 text-xs text-slate-500 dark:text-white/50 leading-relaxed text-center">
                            Perpanjangan dihitung mulai hari ini ditambah durasi standar sistem.
                        </p>

                        <div class="mt-8 flex flex-col gap-3">
                            <button type="submit" class="w-full py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-bold shadow-lg shadow-yellow-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">update</span>
                                Konfirmasi Perpanjangan
                            </button>
                            
                            <a href="{{ route('peminjaman.show', $peminjaman->id_peminjaman) }}" class="w-full py-3 bg-transparent border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 text-slate-600 dark:text-white/70 rounded-xl font-bold transition-colors text-center text-sm">
                                Batal
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection