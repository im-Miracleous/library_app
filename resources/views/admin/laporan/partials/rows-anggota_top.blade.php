@forelse($data as $index => $item)
    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors">
        <td class="p-4 pl-6 font-bold text-xl text-primary/50 dark:text-white/30">
            #{{ $index + 1 }}
        </td>
        <td class="p-4">
            <div class="flex items-center gap-4">
                <div class="size-10 rounded-full bg-slate-200 dark:bg-white/5 overflow-hidden flex-shrink-0 border border-black/5">
                    @if($item->pengguna->foto_profil)
                        <img src="{{ asset('storage/' . $item->pengguna->foto_profil) }}" alt="Foto" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold">
                            {{ substr($item->pengguna->nama, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="font-bold text-slate-800 dark:text-white">{{ $item->pengguna->nama }}</div>
            </div>
        </td>
        <td class="p-4 text-slate-600 dark:text-white/70">
            {{ $item->pengguna->email }}
        </td>
        <td class="p-4 pr-6 text-right">
            <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400 rounded-full text-sm font-bold">
                {{ $item->total_transaksi }} kali
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="p-12 text-center text-slate-400 dark:text-white/40">
            Tidak ada aktivitas anggota pada periode ini.
        </td>
    </tr>
@endforelse
