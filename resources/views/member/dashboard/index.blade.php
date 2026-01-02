@extends('layouts.app')

@section('title', 'Dashboard Anggota - Library App')
@section('header-title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div
        class="bg-gradient-to-r from-primary to-primary-dark rounded-3xl p-8 text-white relative overflow-hidden shadow-lg animate-enter">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <span class="material-symbols-outlined text-[200px]">auto_stories</span>
        </div>
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->nama }}! 👋</h1>
            <p class="text-white/80 text-lg mb-6">Jelajahi ribuan koleksi buku kami dan mulai petualangan
                membaca Anda hari ini.</p>
            <a href="{{ route('member.buku.index') }}"
                class="inline-flex items-center gap-2 bg-white text-primary-dark px-6 py-3 rounded-xl font-bold hover:bg-white/90 transition-colors shadow-md">
                <span class="material-symbols-outlined">search</span>
                Mulai Jelajah
            </a>
        </div>
    </div>

    <!-- Stats & Quick Access Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-enter delay-100">
        <!-- Stat 1: Sedang Dipinjam -->
        <div
            class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-primary/10 dark:border-white/5 flex flex-col justify-between group hover:border-primary/30 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-primary-mid dark:text-white/60 text-sm font-medium uppercase tracking-wider">
                        Sedang Dipinjam</p>
                    <h3 class="text-3xl font-bold text-primary-dark dark:text-white mt-1">
                        {{ $activeLoansCount }} <span class="text-sm font-normal text-primary-mid/60">Buku</span>
                    </h3>
                </div>
                <div
                    class="size-12 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined">menu_book</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-dashed border-primary/10 dark:border-white/10 flex">
                <a href="{{ route('member.peminjaman.index', ['tab' => 'berjalan']) }}"
                    class="inline-flex items-center gap-1 text-sm font-bold text-primary px-3 py-1.5 rounded-lg hover:bg-primary/5 transition-all">
                    Lihat Semua <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Stat 2: Menunggu Verifikasi -->
        <div
            class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-primary/10 dark:border-white/5 flex flex-col justify-between group hover:border-primary/30 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-primary-mid dark:text-white/60 text-sm font-medium uppercase tracking-wider">
                        Menunggu Verifikasi</p>
                    <h3 class="text-3xl font-bold text-primary-dark dark:text-white mt-1">
                        {{ $pendingLoansCount }} <span class="text-sm font-normal text-primary-mid/60">Pengajuan</span>
                    </h3>
                </div>
                <div
                    class="size-12 rounded-full bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400 flex items-center justify-center">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-dashed border-primary/10 dark:border-white/10 flex">
                <a href="{{ route('member.peminjaman.index', ['tab' => 'diajukan']) }}"
                    class="inline-flex items-center gap-1 text-sm font-bold text-primary px-3 py-1.5 rounded-lg hover:bg-primary/5 transition-all">
                    Cek Status <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Stat 3: Koleksi Saya (Bookmarks) -->
        <div
            class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-primary/10 dark:border-white/5 flex flex-col justify-between group hover:border-primary/30 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-primary-mid dark:text-white/60 text-sm font-medium uppercase tracking-wider">
                        Koleksi Tersimpan</p>
                    <h3 class="text-3xl font-bold text-primary-dark dark:text-white mt-1">
                        {{ $bookmarksCount }} <span class="text-sm font-normal text-primary-mid/60">Buku</span>
                    </h3>
                </div>
                <div
                    class="size-12 rounded-full bg-pink-100 text-pink-600 dark:bg-pink-500/20 dark:text-pink-400 flex items-center justify-center">
                    <span class="material-symbols-outlined">favorite</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-dashed border-primary/10 dark:border-white/10 flex">
                <a href="{{ route('member.buku.index', ['filter' => 'bookmarks']) }}"
                    class="inline-flex items-center gap-1 text-sm font-bold text-primary px-3 py-1.5 rounded-lg hover:bg-primary/5 transition-all">
                    Lihat Koleksi <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Books Section -->
    <div class="animate-enter delay-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-primary-dark dark:text-white">Koleksi Terbaru</h2>
            <a href="{{ route('member.buku.index') }}" class="text-sm font-bold text-primary hover:underline">Lihat
                Semua</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
            @foreach($recentBooks as $item)
                <a href="{{ route('member.buku.show', $item->id_buku) }}"
                    class="group bg-white dark:bg-surface-dark rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-primary/10 dark:border-white/5 overflow-hidden flex flex-col h-full hover:-translate-y-1">

                    <!-- Cover Image Area -->
                    <div class="aspect-[2/3] relative overflow-hidden bg-slate-100 dark:bg-white/5">
                        @php
                            $colors = [
                                'from-blue-400 to-indigo-500',
                                'from-emerald-400 to-teal-500',
                                'from-orange-400 to-red-500',
                                'from-purple-400 to-pink-500',
                                'from-cyan-400 to-blue-500',
                                'from-rose-400 to-orange-500'
                            ];
                            $colorIndex = abs(crc32($item->id_buku)) % count($colors);
                            $randomColor = $colors[$colorIndex]; 
                        @endphp
                        <div
                            class="w-full h-full bg-gradient-to-br {{ $randomColor }} opacity-80 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <span class="material-symbols-outlined text-[32px] drop-shadow-md mb-2">auto_stories</span>
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest line-clamp-2 leading-tight drop-shadow-md">
                                    {{ $item->judul }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="text-sm font-bold text-primary-dark dark:text-white mb-1 line-clamp-2 leading-tight">
                            {{ $item->judul }}
                        </h3>
                        <p class="text-xs text-primary-mid dark:text-white/60 truncate">{{ $item->penulis }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

@endsection