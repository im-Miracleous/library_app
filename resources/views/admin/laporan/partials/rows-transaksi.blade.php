@forelse($data as $item)
    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
        <td class="p-4 pl-6 font-mono text-sm text-slate-600 dark:text-white/70 whitespace-nowrap">
            <span class="font-bold text-primary dark:text-accent">{{ $item->id_peminjaman }}</span>
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
        <td class="p-4 text-center font-bold text-slate-700 dark:text-white">{{ $item->total_buku }}</td>
        <td class="p-4 text-right pr-6">
            @php
                $badgeClass = match($item->status_transaksi) {
                    'berjalan' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                    'selesai' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                    'terlambat' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                    default => 'bg-slate-100 text-slate-600'
                };
            @endphp
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $badgeClass }}">
                {{ $item->status_transaksi }}
            </span>
        </td>
        <td class="p-4">
            <div class="flex justify-center gap-1">
                @if(auth()->user()->peran == 'owner')
                <a href="{{ route('peminjaman.edit', $item->id_peminjaman) }}"
                   class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                   title="Detail/Edit">
                    <span class="material-symbols-outlined text-lg">visibility</span>
                </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="p-12 text-center text-slate-400 dark:text-white/40">
            <div class="flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-4xl opacity-50">manage_search</span>
                <span class="font-semibold">Tidak ada data untuk ditampilkan</span>
                <span class="text-sm text-slate-400 dark:text-white/30">Silakan pilih filter lain</span>
            </div>
        </td>
    </tr>
@endforelse
