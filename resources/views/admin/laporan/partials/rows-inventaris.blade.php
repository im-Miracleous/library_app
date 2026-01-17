@forelse($data as $item)
    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group animate-enter">
        <td class="p-4 pl-6">
            <div class="flex flex-col max-w-[300px]">
                <span class="font-bold text-slate-800 dark:text-white truncate" title="{{ $item->judul }}">{{ $item->judul }}</span>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-white/50">
                    <span class="font-mono">{{ $item->isbn }}</span>
                    <span>&bull;</span>
                    <span>{{ $item->penulis }}</span>
                </div>
            </div>
        </td>
        <td class="p-4">
             <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-white/80">
                {{ $item->kategori->nama_kategori ?? '-' }}
            </span>
        </td>
        <td class="p-4 text-center font-bold text-slate-700 dark:text-white">
            {{ $item->stok_total }}
        </td>
        <td class="p-4 text-center text-emerald-600 dark:text-emerald-400 font-bold">
            {{ $item->stok_tersedia }}
        </td>
        <td class="p-4 text-center text-orange-500 font-bold">
            {{ $item->stok_rusak }}
        </td>
        <td class="p-4 text-center text-rose-500 font-bold pr-6">
            {{ $item->stok_hilang }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
            <div class="flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-4xl opacity-50">library_books</span>
                <span class="font-semibold">Tidak ada data buku</span>
                <span class="text-sm text-slate-400 dark:text-white/30">Inventaris kosong</span>
            </div>
        </td>
    </tr>
@endforelse
