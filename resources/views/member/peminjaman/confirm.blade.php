@extends('layouts.app')

@section('title', 'Konfirmasi Peminjaman - Library App')
@section('header-title', 'Konfirmasi Peminjaman')

@section('content')
    <!-- Alert Messages (Handled by layouts.app, but if needed here specialized) -->

    <div
        class="bg-white dark:bg-surface-dark rounded-3xl shadow-sm border border-primary/10 dark:border-white/5 overflow-hidden p-6 sm:p-8">

        <!-- Informasi Pustaka -->
        <div class="mb-8 border-b border-primary/5 dark:border-white/5 pb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-primary">library_books</span>
                <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Pustaka</h2>
            </div>

            <div class="bg-slate-50 dark:bg-white/5 rounded-xl border border-primary/5 dark:border-white/5 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-primary/5 dark:bg-white/5 text-primary-dark dark:text-white font-bold">
                        <tr>
                            <th class="p-4">Judul Buku</th>
                            <th class="p-4">Penulis</th>
                            <th class="p-4 text-center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5 dark:divide-white/5">
                        @foreach($items as $item)
                            <tr class="hover:bg-white dark:hover:bg-white/5 transition-colors">
                                <td class="p-4 font-bold text-primary-dark dark:text-white">
                                    {{ $item->buku->judul }}
                                </td>
                                <td class="p-4 text-primary-mid dark:text-white/60">{{ $item->buku->penulis }}
                                </td>
                                <td class="p-4 text-center font-bold">1</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Informasi Peminjam -->
        <div class="mb-8 border-b border-primary/5 dark:border-white/5 pb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-primary">person</span>
                <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Peminjam</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-primary/5 dark:border-white/5">
                    <p class="text-xs text-primary-mid dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                        Nama Peminjam</p>
                    <p class="font-bold text-lg text-primary-dark dark:text-white">
                        {{ Auth::user()->nama }}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-primary/5 dark:border-white/5">
                    <p class="text-xs text-primary-mid dark:text-white/40 uppercase font-bold tracking-widest mb-1">
                        Email</p>
                    <p class="font-bold text-lg text-primary-dark dark:text-white">{{ Auth::user()->email }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Informasi Peminjaman -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-primary">calendar_month</span>
                <h2 class="text-xl font-bold text-primary-dark dark:text-white">Informasi Peminjaman</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20">
                    <p class="text-xs text-blue-600 dark:text-blue-300 uppercase font-bold tracking-widest mb-1">
                        Tanggal Pengajuan</p>
                    <p class="font-bold text-lg text-blue-800 dark:text-blue-100">
                        {{ \Carbon\Carbon::now()->format('d F Y') }}
                    </p>
                </div>
                <div
                    class="p-4 rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20">
                    <p class="text-xs text-orange-600 dark:text-orange-300 uppercase font-bold tracking-widest mb-1">
                        Batas Pengembalian (Est.)</p>
                    <p class="font-bold text-lg text-orange-800 dark:text-orange-100">
                        {{ \Carbon\Carbon::now()->addDays(7)->format('d F Y') }}
                    </p>
                    <p class="text-xs text-orange-600/80 dark:text-orange-300/80 mt-1">*Batas waktu dapat
                        berubah saat verifikasi</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div
            class="flex flex-col-reverse sm:flex-row justify-end items-center gap-4 pt-6 border-t border-dashed border-primary/10 dark:border-white/10">
            <a href="{{ route('member.keranjang.index') }}"
                class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-primary-mid hover:bg-slate-100 dark:hover:bg-white/5 text-center transition-colors">
                Kembali
            </a>
            <form action="{{ route('member.peminjaman.store') }}" method="POST" class="w-full sm:w-auto"
                id="confirm-loan-form">
                @csrf
                <button type="submit" id="btn-confirm-loan"
                    class="w-full sm:w-auto bg-primary text-white text-lg font-bold px-8 py-3 rounded-xl hover:bg-primary-dark shadow-lg shadow-primary/30 transition-all active:scale-95 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined default-icon">check_circle</span>
                    <span class="material-symbols-outlined loading-spinner animate-spin-fast"
                        style="display: none;">progress_activity</span>
                    <span class="btn-text">Konfirmasi Peminjaman</span>
                </button>
            </form>
        </div>

        <script>
            document.getElementById('confirm-loan-form').addEventListener('submit', function (e) {
                const btn = document.getElementById('btn-confirm-loan');
                if (btn.classList.contains('loading')) {
                    e.preventDefault();
                    return;
                }

                // Add loading state
                btn.classList.add('loading');
                btn.disabled = true;

                // Toggle icons
                btn.querySelector('.default-icon').style.display = 'none';
                btn.querySelector('.loading-spinner').style.display = 'inline-block';

                // Change text
                btn.querySelector('.btn-text').textContent = 'Memproses...';
            });
        </script>
    </div>
@endsection