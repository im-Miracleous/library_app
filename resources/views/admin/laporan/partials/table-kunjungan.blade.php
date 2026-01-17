<x-datatable :data="$data" search-placeholder="Cari nama atau keperluan..." search-id="searchKunjunganInput" :search-value="request('search')">
    <x-slot:header>
        <th class="p-4 pl-6 font-medium w-48 text-left cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Tanggal
                @if(request('sort') == 'created_at')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-left cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_pengunjung', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Nama Pengunjung
                @if(request('sort') == 'nama_pengunjung')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-left cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'jenis_pengunjung', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Kategori
                @if(request('sort') == 'jenis_pengunjung')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-left select-none pr-6">
            Keperluan
        </th>
    </x-slot:header>

    <x-slot:body>
        @include('admin.laporan.partials.rows-kunjungan', ['data' => $data])
    </x-slot:body>
</x-datatable>
