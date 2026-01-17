@forelse($data as $item)
    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
        <td class="p-4 pl-6 text-slate-600 dark:text-white/70 whitespace-nowrap">
            {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y H:i') }}
        </td>
        <td class="p-4 font-bold text-slate-800 dark:text-white">
            {{ $item->nama_pengunjung }}
        </td>
        <td class="p-4">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                {{ $item->jenis_pengunjung }}
            </span>
        </td>
        <td class="p-4 text-slate-600 dark:text-white/70 pr-6">
            {{ $item->keperluan }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="p-12 text-center text-slate-400 dark:text-white/40">
            <div class="flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-4xl opacity-50">diversity_3</span>
                <span class="font-semibold">Tidak ada data pengunjung</span>
                <span class="text-sm text-slate-400 dark:text-white/30">Belum ada data kunjungan pada periode ini</span>
            </div>
        </td>
    </tr>
@endforelse
