@forelse($data as $index => $item)
    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors">
        <td class="p-4 pl-6 font-bold text-xl text-primary/50 dark:text-white/30">
            #{{ $index + 1 }}
        </td>
        <td class="p-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-16 rounded-md bg-slate-200 dark:bg-white/5 overflow-hidden flex-shrink-0 border border-black/5">
                    @if($item->buku->gambar_sampul)
                        <img src="{{ asset('storage/' . $item->buku->gambar_sampul) }}" alt="Cover" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <span class="material-symbols-outlined">book</span>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="font-bold text-slate-800 dark:text-white line-clamp-2">{{ $item->buku->judul }}</div>
                    <div class="text-xs text-slate-500 dark:text-white/50">ID: {{ $item->id_buku }}</div>
                </div>
            </div>
        </td>
        <td class="p-4 text-slate-600 dark:text-white/70">
            {{ $item->buku->penulis }}
        </td>
        <td class="p-4 pr-6 text-right">
            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 rounded-full text-sm font-bold">
                {{ $item->total_dipinjam }} kali
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="p-12 text-center text-slate-400 dark:text-white/40">
            Tidak ada data peminjaman pada periode ini.
        </td>
    </tr>
@endforelse
