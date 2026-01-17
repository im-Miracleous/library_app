<x-datatable :data="$data" search-placeholder="Cari judul, ISBN, atau penulis..." search-id="searchInventarisInput" :search-value="request('search')">
    <x-slot:header>
        <th class="p-4 pl-6 font-medium text-left cursor-pointer hover:text-primary transition-colors select-none max-w-[150px]"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'judul', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                ISBN / Judul
                @if(request('sort') == 'judul')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-left cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'kategori', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Kategori
                @if(request('sort') == 'kategori')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-center cursor-pointer hover:text-primary transition-colors select-none w-24"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'stok_total', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center justify-center gap-1">
                Total
                @if(request('sort') == 'stok_total')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-center cursor-pointer hover:text-primary transition-colors select-none w-24"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'stok_tersedia', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center justify-center gap-1">
                Tersedia
                @if(request('sort') == 'stok_tersedia')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-center cursor-pointer hover:text-primary transition-colors select-none w-24"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'stok_rusak', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center justify-center gap-1">
                Rusak
                @if(request('sort') == 'stok_rusak')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-center cursor-pointer hover:text-primary transition-colors select-none w-24 pr-6"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'stok_hilang', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center justify-center gap-1">
                Hilang
                @if(request('sort') == 'stok_hilang')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
    </x-slot:header>

    <x-slot:body>
        @include('admin.laporan.partials.rows-inventaris', ['data' => $data])
    </x-slot:body>
</x-datatable>
