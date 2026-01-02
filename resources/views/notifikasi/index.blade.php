@extends('layouts.app')

@section('title', 'Notifikasi - Library App')
@section('header-title', 'Notifikasi')

@section('content')
    <div class="flex flex-col animate-enter">
        <div class="p-4 sm:p-8 max-w-4xl mx-auto w-full">

            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() == url()->current() ? route('dashboard') : url()->previous() }}"
                        class="flex items-center justify-center size-10 rounded-xl bg-white dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-primary transition-all shadow-sm group">
                        <span class="material-symbols-outlined text-xl group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    </a>
                    <h2 class="text-xl font-bold text-primary-dark dark:text-white">Semua Notifikasi</h2>
                </div>

                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifikasi.readAll') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-surface-dark border border-gray-200 dark:border-gray-700 rounded-xl text-primary font-bold text-xs hover:bg-gray-50 dark:hover:bg-white/5 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-lg">done_all</span>
                            Tandai Semua Dibaca
                        </button>
                    </form>
                @endif
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/10 dark:border-border-dark overflow-hidden shadow-sm">

                {{-- 1. Verification Tasks (High Priority) --}}
                @if(($pendingVerificationCount ?? 0) > 0)
                    <div class="p-5 bg-blue-50/70 dark:bg-blue-900/10 border-b border-blue-100 dark:border-blue-900/30">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex gap-4">
                                <div class="shrink-0">
                                    <div class="size-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/30">
                                        <span class="material-symbols-outlined text-2xl">assignment_late</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-bold text-blue-900 dark:text-blue-300">Tugas Verifikasi</h4>
                                        <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">PENTING</span>
                                    </div>
                                    <p class="text-sm text-blue-800/70 dark:text-blue-400/60 font-medium">Ada
                                        {{ $pendingVerificationCount }} permintaan peminjaman buku yang menunggu
                                        persetujuan Anda.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('peminjaman.index', ['status' => 'menunggu_verifikasi']) }}"
                                class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-md shadow-blue-500/20 text-center text-sm">
                                Proses Sekarang
                            </a>
                        </div>
                    </div>
                @endif

                @forelse($notifications as $notification)
                    <div class="p-4 border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors flex gap-4 {{ $notification->read_at ? 'opacity-70' : 'bg-blue-50/50 dark:bg-blue-900/10' }}">
                        <div class="shrink-0 mt-1">
                            @php
                                $ntype = $notification->data['type'] ?? '';
                                $nicon = $notification->data['icon'] ?? ($ntype === 'warning' ? 'warning' : ($ntype === 'success' ? 'check_circle' : 'info'));
                            @endphp
                            @if($ntype == 'warning')
                                <div class="size-10 rounded-full bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                    <span class="material-symbols-outlined">{{ $nicon }}</span>
                                </div>
                            @elseif($ntype == 'success')
                                <div class="size-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <span class="material-symbols-outlined">{{ $nicon }}</span>
                                </div>
                            @elseif($ntype == 'info')
                                <div class="size-10 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <span class="material-symbols-outlined">{{ $nicon }}</span>
                                </div>
                            @else
                                <div class="size-10 rounded-full bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center text-gray-600 dark:text-gray-400">
                                    <span class="material-symbols-outlined">{{ $nicon }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-1">
                                    {{ $notification->data['title'] ?? 'Notifikasi' }}
                                    @if(!$notification->read_at)
                                        <span class="ml-2 inline-block size-2 bg-red-500 rounded-full"></span>
                                    @endif
                                </h4>
                                <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 leading-relaxed">
                                {{ $notification->data['message'] ?? '' }}
                            </p>

                            <div class="flex gap-3">
                                @if(isset($notification->data['link']))
                                    <form action="{{ route('notifikasi.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-bold text-primary hover:text-primary-dark dark:text-accent dark:hover:text-white flex items-center gap-1">
                                            Lihat Detail
                                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                        </button>
                                    </form>
                                @endif

                                @if(!$notification->read_at)
                                    <form action="{{ route('notifikasi.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    @if(($pendingVerificationCount ?? 0) == 0)
                        <div class="p-12 text-center">
                            <div class="inline-flex items-center justify-center size-16 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400 mb-4">
                                <span class="material-symbols-outlined text-3xl">notifications_off</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Tidak ada notifikasi</h3>
                            <p class="text-gray-500 dark:text-gray-400">Anda belum memiliki notifikasi apapun saat ini.</p>
                        </div>
                    @endif
                @endforelse
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>

        </div>
    </div>
@endsection