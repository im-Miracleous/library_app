<x-datatable :data="$data" search-placeholder="Cari ID transaksi atau nama..." search-id="searchTransaksiInput" :search-value="request('search')">
    <x-slot:header>
        <th class="p-4 pl-6 font-medium w-44 cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'id_peminjaman', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Kode
                @if(request('sort') == 'id_peminjaman')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'nama_anggota', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Peminjam
                @if(request('sort') == 'nama_anggota')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_pinjam', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Tanggal Pinjam
                @if(request('sort') == 'tanggal_pinjam')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'tanggal_jatuh_tempo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center gap-1">
                Jatuh Tempo
                @if(request('sort') == 'tanggal_jatuh_tempo')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-center">Jml Buku</th>
        <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none text-right pr-6"
            onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'status_transaksi', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
            <div class="flex items-center justify-end gap-1">
                Status
                @if(request('sort') == 'status_transaksi')
                    <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                @else
                    <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                @endif
            </div>
        </th>
        <th class="p-4 font-medium text-center">Aksi</th>
    </x-slot:header>

    <x-slot:body>
        @include('admin.laporan.partials.rows-transaksi', ['data' => $data])
    </x-slot:body>
</x-datatable>
