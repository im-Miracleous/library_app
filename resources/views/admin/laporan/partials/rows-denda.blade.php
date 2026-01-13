@forelse($data as $item)
    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
        <td class="p-4 pl-6 font-mono text-sm text-slate-600 dark:text-white/70">
            #{{ $item->id_denda }}
        </td>
        <td class="p-4">
            <div class="flex flex-col">
                <span class="font-bold text-slate-800 dark:text-white">{{ $item->nama_anggota }}</span>
                <a href="{{ route('peminjaman.index', ['search' => $item->id_peminjaman]) }}" class="text-xs text-blue-500 hover:underline">
                    Ref: {{ $item->id_peminjaman }}
                </a>
            </div>
        </td>
        <td class="p-4 font-bold text-rose-600 dark:text-rose-400">
            Rp {{ number_format($item->jumlah_denda, 0, ',', '.') }}
        </td>
        <td class="p-4">
            @if($item->status_bayar == 'lunas')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-sm">check_circle</span> Lunas
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400">
                    <span class="material-symbols-outlined text-sm">warning</span> Belum Bayar
                </span>
            @endif
        </td>
        <td class="p-4 text-slate-600 dark:text-white/70 text-sm">
            {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y H:i') }}
        </td>
        <td class="p-4 text-center">
            @if($item->status_bayar == 'belum_bayar')
                <form action="{{ route('laporan.denda.bayar', $item->id_denda) }}" method="POST" onsubmit="return confirm('Tandai denda ini sebagai LUNAS?');">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Tandai Lunas">
                        <span class="material-symbols-outlined">payments</span>
                    </button>
                </form>
            @else
                <span class="text-slate-300 dark:text-white/20">-</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
            <div class="flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-4xl opacity-50">search_off</span>
                <span>Tidak ada denda pada periode ini.</span>
            </div>
        </td>
    </tr>
@endforelse
