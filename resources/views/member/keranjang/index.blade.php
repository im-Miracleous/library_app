@extends('layouts.app')

@section('title', 'Keranjang Buku - Library App')
@section('header-title', 'Keranjang Peminjaman')

@section('content')
    <div
        class="bg-white dark:bg-surface-dark rounded-3xl shadow-sm border border-primary/10 dark:border-white/5 overflow-hidden p-6 sm:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-primary-dark dark:text-white">Daftar Buku
                ({{ $items->count() }})</h2>
            <div class="flex gap-4 items-center">
                @if(!$items->isEmpty())
                    <form action="{{ route('member.keranjang.clear') }}" method="POST"
                        onsubmit="return confirm('Hapus semua buku dari keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-sm font-bold text-red-500 hover:text-red-700 transition-all hover:scale-[1.03] active:scale-95 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Hapus Semua
                        </button>
                    </form>
                @endif
                @if($limitReached)
                    <span class="text-sm font-bold text-slate-400 flex items-center gap-1 cursor-not-allowed opacity-60"
                        title="Batas peminjaman 3 buku tercapai">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                        Tambah Buku
                    </span>
                @else
                    <a href="{{ route('member.buku.index') }}"
                        class="text-sm font-bold text-primary hover:text-primary-dark transition-all hover:scale-[1.03] active:scale-95 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Tambah Buku
                    </a>
                @endif
            </div>
        </div>

        @if($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center text-primary-mid/60 dark:text-white/40">
                <span class="material-symbols-outlined text-[64px] mb-4 opacity-50">shopping_cart_off</span>
                <h3 class="text-lg font-bold mb-1 text-primary-dark dark:text-white">Keranjang Kosong</h3>
                <p class="text-sm mb-6">Belum ada buku yang ditambahkan.</p>
                <a href="{{ route('member.buku.index') }}"
                    class="bg-primary text-white px-6 py-2 rounded-xl font-bold hover:bg-primary-dark transition-colors">
                    Cari Buku
                </a>
            </div>
        @else
            <div class="space-y-4 mb-8">
                @foreach($items as $item)
                    <div
                        class="flex flex-col md:flex-row gap-4 p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-primary/5 dark:border-white/5 group">
                        <!-- Minimalist Cover or Icon -->
                        <div
                            class="w-full md:w-20 aspect-[2/3] bg-slate-200 dark:bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-3xl text-slate-400">auto_stories</span>
                        </div>

                        <div class="flex-1 flex flex-col justify-center">
                            <h3 class="font-bold text-primary-dark dark:text-white line-clamp-2 text-lg">
                                {{ $item->buku->judul }}
                            </h3>
                            <p class="text-sm text-primary-mid dark:text-white/60 mb-2">{{ $item->buku->penulis }}
                            </p>
                            <div class="flex items-center gap-2 text-xs">
                                <span
                                    class="bg-white dark:bg-white/10 px-2 py-1 rounded-md border border-primary/10 dark:border-white/5 text-primary-dark dark:text-white">
                                    {{ $item->buku->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                                <span class="{{ $item->buku->stok_tersedia > 0 ? 'text-green-600' : 'text-red-500' }} font-bold">
                                    {{ $item->buku->stok_tersedia > 0 ? 'Stok Tersedia' : 'Stok Habis' }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('member.keranjang.destroy', $item->id_keranjang) }}" method="POST"
                            class="flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-3 rounded-xl bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-500/30 transition-colors"
                                title="Hapus">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <!-- Checkout Action -->
            <div
                class="border-t border-dashed border-primary/10 dark:border-white/10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-primary-mid dark:text-white/60">
                    <p>Pastikan buku yang dipilih sudah benar.</p>
                    <p>Durasi peminjaman default adalah <strong>7 Hari</strong>.</p>
                </div>
                <a href="{{ route('member.peminjaman.confirm') }}"
                    class="bg-primary text-white text-lg font-bold px-8 py-3 rounded-xl hover:bg-primary-dark shadow-lg shadow-primary/30 transition-all active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined">assignment_turned_in</span>
                    Ajukan Peminjaman
                </a>
            </div>
        @endif
    </div>

@endsection