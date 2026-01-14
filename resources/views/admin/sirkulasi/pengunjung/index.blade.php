@extends('layouts.app')

@section('title', 'Data Pengunjung - Library App')
@section('header-title', 'Data Pengunjung')

@push('scripts')
    @vite(['resources/js/live-search/live-search-pengunjung.js', 'resources/js/logic/modals/pengunjung.js'])
@endpush

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-enter">
            <x-breadcrumb-component parent="Sirkulasi" current="Data Pengunjung" />

            <div class="flex gap-2">
                <button onclick="window.print()"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm active:scale-95">
                    <span class="material-symbols-outlined text-lg">print</span>
                    Laporan Pengunjung
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h3 class="text-lg font-bold text-primary-dark dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">bar_chart</span>
                    Statistik Kunjungan
                </h3>
                
                <!-- Time Filter -->
                <div class="flex bg-slate-50 dark:bg-white/5 rounded-xl border border-primary/10 dark:border-border-dark p-1 shadow-sm">
                    <button onclick="updatePengunjungChart('today')" id="btn-today" 
                        class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ $filter == 'today' ? 'bg-primary text-white shadow-md' : 'text-slate-500 hover:bg-white dark:hover:bg-white/5 dark:text-white/60' }}">
                        Hari Ini
                    </button>
                    <button onclick="updatePengunjungChart('week')" id="btn-week" 
                        class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ $filter == 'week' ? 'bg-primary text-white shadow-md' : 'text-slate-500 hover:bg-white dark:hover:bg-white/5 dark:text-white/60' }}">
                        Minggu Ini
                    </button>
                    <button onclick="updatePengunjungChart('month')" id="btn-month" 
                        class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all {{ $filter == 'month' ? 'bg-primary text-white shadow-md' : 'text-slate-500 hover:bg-white dark:hover:bg-white/5 dark:text-white/60' }}">
                        Bulan Ini
                    </button>
                </div>
            </div>
            
            <div class="relative h-64 w-full">
                <canvas id="pengunjungChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter shadow-sm">
            <h3 class="text-lg font-bold text-primary-dark dark:text-white mb-6 flex items-center gap-2">
                <div class="size-8 rounded-lg bg-primary/10 dark:bg-accent/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary dark:text-accent">edit_note</span>
                </div>
                Input Buku Tamu
            </h3>
            <form action="{{ route('pengunjung.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <x-input id="nama_pengunjung" name="nama_pengunjung" label="Nama Pengunjung" placeholder="Masukkan nama..." required />

                <x-select id="jenis_pengunjung" name="jenis_pengunjung" label="Status / Kategori" required placeholder="">
                    
                    <optgroup label="Personal & Akademik">
                        <option value="Umum / Tamu">Umum / Tamu</option>
                        <option value="Anggota / Mahasiswa">Anggota / Mahasiswa</option>
                        <option value="Pelajar / Siswa Sekolah">Pelajar / Siswa Sekolah</option>
                        <option value="Dosen / Staff Pengajar">Dosen / Staff Pengajar</option>
                        <option value="Peneliti / Riset">Peneliti / Riset</option>
                    </optgroup>

                    <optgroup label="Organisasi & Komunitas">
                        <option value="Organisasi Internal Kampus">Organisasi Internal Kampus</option>
                        <option value="Organisasi / Komunitas Luar">Organisasi / Komunitas Luar</option>
                        <option value="Yayasan / Nonprofit / NGO">Yayasan / Nonprofit / NGO</option>
                    </optgroup>

                    <optgroup label="Instansi & Perusahaan">
                        <option value="Pemerintahan / Dinas">Pemerintahan / Dinas</option>
                        <option value="Korporasi / Perusahaan Swasta">Korporasi / Perusahaan Swasta</option>
                    </optgroup>

                    <optgroup label="Kunjungan Khusus">
                        <option value="Tamu Undangan / VIP">Tamu Undangan / VIP</option>
                        <option value="Media / Jurnalis">Media / Jurnalis</option>
                        <option value="Lainnya">Lainnya</option>
                    </optgroup>

                </x-select>

                <x-input id="keperluan" name="keperluan" label="Keperluan (Opsional)"
                    placeholder="Contoh: Baca buku, Pinjam..." />

                <button type="submit"
                    class="bg-primary dark:bg-accent text-white dark:text-primary-dark hover:brightness-110 px-4 py-2.5 rounded-lg font-bold text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm transition-all duration-200 h-[46px] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Catat Kunjungan
                </button>
            </form>
        </div>

        <x-datatable :data="$pengunjung" search-placeholder="Cari nama atau keperluan..." search-id="searchInput"
            :search-value="request('search')">
            <x-slot:header>
                <th class="p-4 pl-6 font-medium w-16">No</th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
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
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'jenis_pengunjung', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Status
                        @if(request('sort') == 'jenis_pengunjung')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium">Keperluan</th>
                <th class="p-4 font-medium cursor-pointer hover:text-primary transition-colors select-none"
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}'">
                    <div class="flex items-center gap-1">
                        Tanggal Masuk
                        @if(request('sort') == 'created_at')
                            <span class="material-symbols-outlined text-sm">{{ request('direction') == 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                        @else
                            <span class="material-symbols-outlined text-sm opacity-30">unfold_more</span>
                        @endif
                    </div>
                </th>
                <th class="p-4 font-medium text-right pr-6">Aksi</th>
            </x-slot:header>

            <x-slot:body>
                @forelse($pengunjung as $index => $item)
                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 pl-6 font-mono text-slate-400 font-bold">
                            {{ $pengunjung->firstItem() + $index }}
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-slate-800 dark:text-white">{{ $item->nama_pengunjung }}</span>
                            @if($item->id_pengguna)
                                <div class="text-[10px] text-green-600 dark:text-green-400 flex items-center gap-1 mt-0.5 font-bold">
                                    <span class="material-symbols-outlined text-[12px]">verified</span>Terdaftar
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            @php
                                $badgeClass = match ($item->jenis_pengunjung) {
                                    // Kategori Umum
                                    'Umum / Tamu', 'Lainnya' => 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300',
                                    'Umum' => 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300', // Legacy support
                                    
                                    // Kategori Akademik
                                    'Anggota / Mahasiswa', 'anggota' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                                    'Pelajar / Siswa Sekolah', 'Pelajar / Siswa' => 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-400',
                                    'Dosen / Staff Pengajar', 'Dosen / Staff PJS', 'Peneliti / Riset', 'Peneliti' => 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400',
                                    
                                    // Kategori Organisasi
                                    'Organisasi Internal Kampus', 'Organisasi Internal' => 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400',
                                    'Organisasi / Komunitas Luar', 'Organisasi Eksternal' => 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400',
                                    'Korporasi / Perusahaan Swasta', 'Korporasi', 'Yayasan / Nonprofit / NGO', 'Nonprofit' => 'bg-fuchsia-100 dark:bg-fuchsia-500/20 text-fuchsia-700 dark:text-fuchsia-400',
                                    
                                    // Kategori Resmi/VIP
                                    'Pemerintahan / Dinas', 'Pemerintahan', 'petugas' => 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400',
                                    'Tamu Undangan / VIP', 'Tamu Undangan', 'Media / Jurnalis', 'Media / Pers' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400',
                                    
                                    // Admin
                                    'admin' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400',
                                    
                                    default => 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300'
                                };
                                
                                $roleDisplay = ucwords($item->jenis_pengunjung);
                            @endphp
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $badgeClass }}">
                                {{ $roleDisplay }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-600 dark:text-white/70 text-sm">{{ $item->keperluan ?? '-' }}</td>
                        <td class="p-4 font-mono text-xs text-slate-500 dark:text-white/50">
                            {{ $item->created_at->translatedFormat('d F Y') }}
                            <div class="text-slate-800 dark:text-white font-bold">{{ $item->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="p-4 text-right pr-6">
                            <div class="flex justify-end gap-1">
                                <button onclick='openEditPengunjung(@json($item))'
                                    class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                    title="Edit Log">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <form action="{{ route('pengunjung.destroy', $item->id_pengunjung) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus log ini?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                        title="Hapus Log">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400 dark:text-white/40">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-4xl opacity-50">data_loss_prevention</span>
                                <span>Tidak ada data pengunjung.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </div>

    <x-modal id="editModal" title="Edit Data Pengunjung" maxWidth="lg">
        <x-slot:title_icon>
            <span class="material-symbols-outlined text-blue-500">edit_note</span>
        </x-slot:title_icon>

        <form id="editForm" method="POST" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            <x-input id="edit_nama" name="nama_pengunjung" label="Nama Pengunjung" required />

            <x-select id="edit_jenis" name="jenis_pengunjung" label="Status / Role" required placeholder="">
                
                <optgroup label="Personal & Akademik">
                    <option value="Umum / Tamu">Umum / Tamu</option>
                    <option value="Anggota / Mahasiswa">Anggota / Mahasiswa</option>
                    <option value="Pelajar / Siswa Sekolah">Pelajar / Siswa Sekolah</option>
                    <option value="Dosen / Staff Pengajar">Dosen / Staff Pengajar</option>
                    <option value="Peneliti / Riset">Peneliti / Riset</option>
                </optgroup>

                <optgroup label="Organisasi & Komunitas">
                    <option value="Organisasi Internal Kampus">Organisasi Internal Kampus</option>
                    <option value="Organisasi / Komunitas Luar">Organisasi / Komunitas Luar</option>
                    <option value="Yayasan / Nonprofit / NGO">Yayasan / Nonprofit / NGO</option>
                </optgroup>

                <optgroup label="Instansi & Perusahaan">
                    <option value="Pemerintahan / Dinas">Pemerintahan / Dinas</option>
                    <option value="Korporasi / Perusahaan Swasta">Korporasi / Perusahaan Swasta</option>
                </optgroup>

                <optgroup label="Kunjungan Khusus">
                    <option value="Tamu Undangan / VIP">Tamu Undangan / VIP</option>
                    <option value="Media / Jurnalis">Media / Jurnalis</option>
                    <option value="Lainnya">Lainnya</option>
                </optgroup>

            </x-select>

            <x-input id="edit_keperluan" name="keperluan" label="Keperluan" />

            <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                <button type="button" onclick="closeModal('editModal')"
                    class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-primary dark:bg-accent text-white dark:text-primary-dark text-sm font-bold transition-all shadow-sm hover:scale-105 active:scale-95 duration-200">Simpan
                    Perubahan</button>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        window.initialChartData = @json($chartData);
        window.activeFilter = '{{ $filter }}';
    </script>
    @vite(['resources/js/pengunjung/chart.js'])
@endpush