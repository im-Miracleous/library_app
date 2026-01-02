@extends('layouts.app')

@section('title', $buku->judul . ' - Library App')
@section('header-title', 'Detail Buku')

@push('scripts')
    @vite(['resources/js/member/buku-show.js'])
@endpush

@section('content')
    <div class="flex flex-col gap-4 flex-1">
        <!-- Back Button -->
        <a href="javascript:history.back()"
            class="self-start w-fit inline-flex items-center gap-2 text-sm font-bold text-primary dark:text-accent px-3 py-2 rounded-xl hover:bg-primary/10 transition-all group">
            <span
                class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Kembali
        </a>

        <div
            class="flex-1 bg-white dark:bg-surface-dark rounded-3xl shadow-sm border border-primary/10 dark:border-white/5 overflow-hidden flex flex-col md:flex-row">

        <!-- Cover Section -->
        <div
            class="w-full md:w-[300px] xl:w-[340px] relative bg-slate-100 dark:bg-white/5 p-8 md:p-6 flex items-center justify-center shrink-0 transition-all duration-300">
            @php
                $colors = ['from-blue-400 to-indigo-500', 'from-emerald-400 to-teal-500', 'from-orange-400 to-red-500', 'from-purple-400 to-pink-500', 'from-cyan-400 to-blue-500', 'from-rose-400 to-orange-500'];
                $colorIndex = abs(crc32($buku->id_buku)) % count($colors);
                $randomColor = $colors[$colorIndex]; 
            @endphp
            @if($buku->gambar_sampul)
                <div
                    class="relative w-full max-w-[180px] md:max-w-[220px] xl:max-w-[240px] aspect-[2/3] md:h-auto shadow-2xl rounded-tr-xl rounded-br-xl overflow-hidden {{ $buku->stok_tersedia <= 0 ? 'grayscale opacity-50' : '' }} transition-all duration-300">
                    <img src="{{ asset('storage/' . $buku->gambar_sampul) }}" alt="{{ $buku->judul }}"
                        class="w-full h-full object-cover">
                    <!-- Spine Shadow -->
                    <div class="absolute inset-y-0 left-0 w-3 bg-gradient-to-r from-black/30 to-transparent"></div>
                    <!-- Spine Edge Highlight -->
                    <div class="absolute inset-y-0 left-3 w-[1px] bg-white/10"></div>
                </div>
            @else
                <div
                    class="w-full max-w-[180px] md:max-w-[220px] xl:max-w-[240px] aspect-[2/3] md:h-auto shadow-2xl rounded-tr-xl rounded-br-xl bg-gradient-to-br {{ $randomColor }} relative flex items-center justify-center text-white p-6 {{ $buku->stok_tersedia <= 0 ? 'grayscale opacity-50' : '' }} transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 w-3 bg-white/20"></div>
                    <div class="text-center">
                        <span class="material-symbols-outlined text-[80px] drop-shadow-xl mb-4">auto_stories</span>
                        <h2 class="text-xl font-black uppercase tracking-widest drop-shadow-md border-t-2 border-white/40 pt-4">
                            {{ $buku->judul }}
                        </h2>
                    </div>
                </div>
            @endif
        </div>

        <!-- Details Section -->
        <div class="flex-1 p-6 md:p-8 flex flex-col min-w-0">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span
                        class="inline-block px-3 py-1 bg-primary/10 dark:bg-accent/10 text-primary dark:text-accent text-xs font-bold uppercase tracking-wider rounded-full mb-2">
                        {{ $buku->kategori->nama_kategori ?? 'Umum' }}
                    </span>
                    <h1 class="text-3xl xl:text-4xl font-bold text-primary-dark dark:text-white mb-2 leading-tight transition-all duration-300">
                        {{ $buku->judul }}
                    </h1>
                    <p class="text-lg xl:text-xl text-primary-mid dark:text-white/60 font-medium transition-all duration-300">{{ $buku->penulis }}
                    </p>
                </div>

                <!-- BUtton Bookmark -->
                <button onclick="toggleBookmark('{{ $buku->id_buku }}')" id="btn-bookmark"
                    class="size-12 rounded-full flex items-center justify-center transition-all duration-300 {{ $isBookmarked ? 'bg-pink-100 dark:bg-pink-500/20 text-pink-600 dark:text-pink-400 hover:bg-pink-200 dark:hover:bg-pink-500/30' : 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-white/50 hover:bg-slate-200 dark:hover:bg-white/20' }}"
                    title="Simpan ke Koleksi">
                    <span class="material-symbols-outlined {{ $isBookmarked ? 'filled' : '' }}"
                        style="{{ $isBookmarked ? 'font-variation-settings: \'FILL\' 1;' : '' }}">favorite</span>
                </button>
            </div>

            <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 my-6 p-6 bg-primary/5 dark:bg-white/5 rounded-2xl border border-primary/5 dark:border-white/5">
                <div>
                    <p class="text-xs xl:text-sm text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                        Penerbit</p>
                    <p class="font-bold text-base xl:text-lg text-primary-dark dark:text-white">{{ $buku->penerbit }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                        Tahun Terbit</p>
                    <p class="font-bold text-primary-dark dark:text-white">{{ $buku->tahun_terbit }}</p>
                </div>
                <div>
                    <p class="text-xs text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                        ISBN</p>
                    <p class="font-mono text-sm font-bold text-primary-dark dark:text-white">
                        {{ $buku->isbn }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-primary-mid/60 dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                        Stok Tersedia</p>
                    <p class="font-bold {{ $buku->stok_tersedia > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $buku->stok_tersedia }} <span
                            class="text-xs font-normal text-primary-mid/60 dark:text-white/40">Exemplar</span>
                    </p>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg xl:text-xl font-bold text-primary-dark dark:text-white mb-2">Deskripsi</h3>
                <p class="text-primary-mid dark:text-white/70 leading-relaxed text-sm xl:text-base transition-all duration-300">
                    {{ $buku->deskripsi ?? 'Tidak ada deskripsi tersedia untuk buku ini.' }}
                </p>
            </div>

            <div class="mt-auto flex flex-col md:flex-row gap-4">
                @if($buku->stok_tersedia > 0)
                    @if($isBorrowed)
                        <button disabled
                            class="w-full bg-emerald-50 dark:bg-emerald-500/10 border-2 border-emerald-500/50 text-emerald-600 dark:text-emerald-400 font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 cursor-default">
                            <span class="material-symbols-outlined">check_circle</span>
                            Anda Sedang Meminjam Buku Ini
                        </button>
                    @elseif($isPending)
                        <button disabled
                            class="w-full bg-amber-50 dark:bg-amber-500/10 border-2 border-amber-500/50 text-amber-600 dark:text-amber-400 font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 cursor-default">
                            <span class="material-symbols-outlined">hourglass_top</span>
                            Dalam Proses Pengajuan
                        </button>
                    @elseif($isInCart)
                        <button disabled
                            class="flex-1 bg-slate-100 dark:bg-white/5 border-2 border-slate-200 dark:border-white/10 text-slate-400 font-bold py-3 px-6 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">shopping_cart_checkout</span>
                            Sudah di Keranjang
                        </button>
                    @elseif($limitReached)
                        <button disabled
                            class="flex-1 bg-slate-100 dark:bg-white/5 border-2 border-slate-200 dark:border-white/10 text-slate-400 font-bold py-3 px-6 rounded-xl cursor-not-allowed flex items-center justify-center gap-2"
                            title="Batas peminjaman maks 3 buku tercapai">
                            <span class="material-symbols-outlined text-[20px]">block</span>
                            Limit Tercapai
                        </button>
                    @else
                        <button onclick="addToCart('{{ $buku->id_buku }}')" id="btn-add-cart"
                            class="flex-1 bg-white dark:bg-primary/5 border-2 border-primary text-primary font-bold py-3 px-6 rounded-xl hover:bg-primary/5 dark:hover:bg-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 btn-loading-state disabled:opacity-70 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined default-icon">add_shopping_cart</span>
                            <span class="material-symbols-outlined loading-spinner animate-spin-fast">progress_activity</span>
                            <span class="btn-text">Tambah Ke Keranjang</span>
                        </button>
                    @endif

                    @if(!$isBorrowed && !$isPending)
                        @if($isInCart)
                            <button onclick="window.location.href='{{ route('member.keranjang.index') }}'" id="btn-loan-now"
                                class="flex-1 bg-primary text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-dark hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">rocket_launch</span>
                                <span class="btn-text">Proses Pengajuan</span>
                            </button>
                        @elseif($limitReached)
                            <button disabled
                                class="flex-1 bg-slate-200 dark:bg-white/10 text-slate-400 font-bold py-3 px-6 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">block</span>
                                <span class="btn-text">Batas Maksimum</span>
                            </button>
                        @else
                            <button onclick="loanNow('{{ $buku->id_buku }}')" id="btn-loan-now"
                                class="flex-1 bg-primary text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-dark hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2 btn-loading-state disabled:opacity-70 disabled:cursor-not-allowed">
                                <span class="material-symbols-outlined default-icon">assignment_add</span>
                                <span class="material-symbols-outlined loading-spinner animate-spin-fast">progress_activity</span>
                                <span class="btn-text">Ajukan Peminjaman</span>
                            </button>
                        @endif
                    @endif
                @else
                    <div
                        class="w-full p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl flex items-center gap-3">
                        <div
                            class="size-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-red-600 dark:text-red-400">inventory_2</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-800 dark:text-red-400">Buku Tidak Tersedia</p>
                            <p class="text-xs text-red-600 dark:text-red-400/70">Maaf, semua unit buku ini sedang dipinjam atau
                                sedang tidak tersedia saat ini.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    </div>
@endsection