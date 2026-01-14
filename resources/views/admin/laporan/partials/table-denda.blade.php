<x-datatable :data="$data" search-placeholder="Cari ID denda atau nama..." search-id="searchDendaInput" :search-value="request('search')">
    <x-slot:header>
        <th class="p-4 pl-6 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_denda', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                ID Denda
                @if(request('sort') == 'id_denda')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_anggota', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Anggota
                @if(request('sort') == 'nama_anggota')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_kembali_aktual', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Tanggal Kembali
                @if(request('sort') == 'tanggal_kembali_aktual')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'jenis_denda', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Jenis Denda
                @if(request('sort') == 'jenis_denda')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium">Keterangan</th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'jumlah_denda', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Jumlah
                @if(request('sort') == 'jumlah_denda')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'status_bayar', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Status
                @if(request('sort') == 'status_bayar')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>

        <th class="p-4 font-medium text-center">Aksi</th>
    </x-slot:header>

    <x-slot:body>
        @include('admin.laporan.partials.rows-denda', ['data' => $data])
    </x-slot:body>
</x-datatable>
