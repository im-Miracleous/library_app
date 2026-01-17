@extends('layouts.app') 

@section('title', 'Log Aktivitas')

@section('content')
<div class="flex flex-col gap-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-white">
                Log Aktivitas Sistem
            </h1>
            <p class="text-sm text-primary-mid dark:text-white/60 mt-1">
                Rekaman jejak digital pengguna (CCTV Sistem).
            </p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-primary/10 dark:bg-white/10 px-4 py-2 rounded-xl text-primary-dark dark:text-white text-sm font-bold">
                Total Record: {{ $logs->total() }}
            </div>
        </div>
    </div>

    {{-- Tabel Card --}}
    <div class="bg-white dark:bg-surface-dark rounded-xl border border-primary/20 dark:border-white/5 shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-primary/10 dark:border-white/5 bg-primary/5 dark:bg-white/5">
                        <th class="px-6 py-4 text-xs font-bold text-primary-mid dark:text-white/60 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-primary-mid dark:text-white/60 uppercase tracking-wider">User / Pelaku</th>
                        <th class="px-6 py-4 text-xs font-bold text-primary-mid dark:text-white/60 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-4 text-xs font-bold text-primary-mid dark:text-white/60 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-xs font-bold text-primary-mid dark:text-white/60 uppercase tracking-wider">Info Perangkat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/10 dark:divide-white/5">
                    @forelse($logs as $log)
                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                        
                        {{-- Kolom Waktu --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-primary-dark dark:text-white">
                                    {{ $log->created_at->format('d M Y') }}
                                </span>
                                <span class="text-xs text-primary-mid dark:text-white/50 font-mono">
                                    {{ $log->created_at->format('H:i:s') }}
                                </span>
                                <span class="text-[10px] text-primary-mid/70 dark:text-white/40 mt-1">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </td>

                        {{-- Kolom User --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-full bg-primary/20 dark:bg-accent/20 flex items-center justify-center text-xs font-bold text-primary-dark dark:text-accent">
                                    {{ substr($log->user->nama ?? 'S', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-primary-dark dark:text-white">
                                        {{ $log->user->nama ?? 'System / Deleted User' }}
                                    </span>
                                    <span class="text-xs text-primary-mid dark:text-white/50 capitalize">
                                        {{ $log->user->peran ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom Aksi (Badge Warna-warni) --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $color = 'gray'; // Default
                                $icon = 'info';
                                if($log->action == 'LOGIN') { $color = 'emerald'; $icon = 'login'; }
                                elseif($log->action == 'LOGOUT') { $color = 'rose'; $icon = 'logout'; }
                                elseif($log->action == 'CREATE') { $color = 'blue'; $icon = 'add_circle'; }
                                elseif($log->action == 'UPDATE') { $color = 'amber'; $icon = 'edit'; }
                                elseif($log->action == 'DELETE') { $color = 'red'; $icon = 'delete'; }
                            @endphp

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border 
                                border-{{ $color }}-500/20 bg-{{ $color }}-500/10 text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                {{-- Icon Material --}}
                                <span class="material-symbols-outlined text-[14px]">{{ $icon }}</span>
                                {{ $log->action }}
                            </span>
                        </td>

                        {{-- Kolom Deskripsi --}}
                        <td class="px-6 py-4">
                            <p class="text-sm text-primary-dark/80 dark:text-white/80 line-clamp-2" title="{{ $log->description }}">
                                {{ $log->description }}
                            </p>
                        </td>

                        {{-- Kolom IP & Agent --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 text-xs text-primary-dark dark:text-white font-mono bg-primary/5 dark:bg-white/5 px-2 py-1 rounded w-fit">
                                    <span class="material-symbols-outlined text-[12px]">lan</span>
                                    {{ $log->ip_address }}
                                </div>
                                <span class="text-[10px] text-primary-mid dark:text-white/40 truncate max-w-[150px]" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 20) }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-primary-mid dark:text-white/40">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">history_toggle_off</span>
                            <p>Belum ada aktivitas yang terekam.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-primary/10 dark:border-white/5">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection