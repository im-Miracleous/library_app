<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Pengunjung - Library App</title>
    <link rel="icon" type="image/png" href="https://laravel.com/img/favicon/favicon-32x32.png">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">
        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">
            <x-header-component title="Data Pengunjung" />

            <div class="p-4 sm:p-8">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-white/60">
                        <span class="material-symbols-outlined text-base">home</span>
                        <span>/</span>
                        <span>Sirkulasi</span>
                        <span>/</span>
                        <span class="font-bold text-primary dark:text-white">Data Pengunjung</span>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="window.print()"
                            class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-lg">print</span>
                            Laporan Pengunjung
                        </button>
                    </div>
                </div>

                <!-- Input Section (Guest Book) -->
                <div
                    class="mb-6 bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 animate-enter shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <div class="size-8 rounded-lg bg-primary/10 dark:bg-accent/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary dark:text-accent">edit_note</span>
                        </div>
                        Input Buku Tamu
                    </h3>
                    <form action="{{ route('pengunjung.store') }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        @csrf
                        <div class="flex flex-col gap-1.5">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Pengunjung</label>
                            <input type="text" name="nama_pengunjung" placeholder="Masukkan nama..." required
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm transition-all hover:border-primary/50">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Status
                                / Role</label>
                            <select name="jenis_pengunjung"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer text-sm hover:border-primary/50">
                                <option value="umum">Umum</option>
                                <option value="anggota">Anggota</option>
                                <option value="petugas">Staff / Petugas</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Keperluan
                                (Opsional)</label>
                            <input type="text" name="keperluan" placeholder="Contoh: Baca buku, Pinjam..."
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm transition-all hover:border-primary/50">
                        </div>
                        <button type="submit"
                            class="bg-surface dark:bg-accent text-primary-dark hover:bg-amber-300 px-4 py-2.5 rounded-lg font-bold text-sm shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm transition-all duration-200 h-[42px] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">add_circle</span>
                            Catat Kunjungan
                        </button>
                    </form>
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter shadow-sm">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 flex flex-col gap-1 animate-enter shadow-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span>
                                {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- Table Section -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark overflow-hidden animate-enter delay-100 shadow-sm">
                    <div
                        class="p-4 border-b border-primary/20 dark:border-[#36271F] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface dark:bg-[#1A1410]">
                        <div class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400">history</span>
                            Riwayat Kunjungan
                        </div>
                        <form method="GET" action="{{ route('pengunjung.index') }}" class="relative w-full sm:w-64">
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 dark:text-white/40 text-lg">search</span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama atau keperluan..."
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg pl-10 pr-4 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-all placeholder-primary-mid/60 dark:placeholder-white/40">
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr
                                    class="border-b border-primary/20 dark:border-border-dark text-slate-500 dark:text-white/40 text-xs uppercase tracking-wider bg-surface dark:bg-[#1A1410]">
                                    <th class="p-4 pl-6 font-medium w-16">No</th>
                                    <th class="p-4 font-medium">Nama Pengunjung</th>
                                    <th class="p-4 font-medium">Status</th>
                                    <th class="p-4 font-medium">Keperluan</th>
                                    <th class="p-4 font-medium">Tanggal Masuk</th>
                                    <th class="p-4 font-medium text-right pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-[#36271F] text-sm text-slate-600 dark:text-white/80">
                                @forelse($pengunjung as $index => $item)
                                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                                        <td class="p-4 pl-6 font-mono text-slate-400 font-bold">
                                            {{ $loop->iteration + $pengunjung->firstItem() - 1 }}
                                        </td>
                                        <td class="p-4">
                                            <span
                                                class="font-bold text-slate-800 dark:text-white">{{ $item->nama_pengunjung }}</span>
                                            @if($item->id_pengguna)
                                                <div
                                                    class="text-[10px] text-green-600 dark:text-green-400 flex items-center gap-1 mt-0.5">
                                                    <span class="material-symbols-outlined text-[10px]">verified</span>
                                                    Terdaftar
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @php
                                                $badges = [
                                                    'umum' => 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300',
                                                    'anggota' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400',
                                                    'petugas' => 'bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400',
                                                    'admin' => 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400',
                                                ];
                                                $badgeClass = $badges[$item->jenis_pengunjung] ?? 'bg-slate-100 text-slate-600';

                                                // Capitalize logic
                                                $display = $item->jenis_pengunjung == 'petugas' ? 'Staff' : ucfirst($item->jenis_pengunjung);
                                            @endphp
                                            <span
                                                class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                                {{ $display }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-600 dark:text-white/70">{{ $item->keperluan ?? '-' }}</td>
                                        <td class="p-4 font-mono text-slate-500 dark:text-white/50">
                                            {{ $item->created_at->translatedFormat('d F Y') }}, <span
                                                class="text-slate-800 dark:text-white font-bold">{{ $item->created_at->translatedFormat('H:i:s') }}</span>
                                            {{ $item->created_at->format('T') }}
                                        </td>
                                        <td class="p-4 text-right pr-6 flex justify-end gap-2">
                                            <button onclick="openEditPengunjung({{ $item->toJson() }})"
                                                class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                                title="Edit Log">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <form action="{{ route('pengunjung.destroy', $item->id_pengunjung) }}"
                                                method="POST" onsubmit="return confirm('Yakin hapus log ini?');"
                                                class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                                    title="Hapus Log">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-12">
                                            <div
                                                class="flex flex-col items-center justify-center gap-2 text-center text-slate-400 dark:text-white/40">
                                                <span
                                                    class="material-symbols-outlined text-4xl opacity-50">event_busy</span>
                                                <span class="font-medium">Belum ada data pengunjung hari ini.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-200 dark:border-border-dark">
                        {{ $pengunjung->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL EDIT -->
    <div id="editModal" class="fixed inset-0 z-50 transition-all duration-200 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 dark:bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-left shadow-2xl transition-all duration-300 scale-95 sm:w-full sm:max-w-lg">
                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-border-dark flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">edit_note</span>
                            Edit Data Pengunjung
                        </h3>
                        <button onclick="closeModal('editModal')"
                            class="text-slate-500 dark:text-white/60 hover:text-slate-800 dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <form id="editForm" method="POST" class="p-6 flex flex-col gap-4">
                        @csrf @method('PUT')
                        <div class="flex flex-col gap-1.5">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Pengunjung</label>
                            <input type="text" id="edit_nama" name="nama_pengunjung" required
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Status
                                / Role</label>
                            <select id="edit_jenis" name="jenis_pengunjung"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer text-sm">
                                <option value="umum">Umum</option>
                                <option value="anggota">Anggota</option>
                                <option value="petugas">Staff / Petugas</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Keperluan</label>
                            <input type="text" id="edit_keperluan" name="keperluan"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none text-sm">
                        </div>
                        <div
                            class="mt-2 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                            <button type="button" onclick="closeModal('editModal')"
                                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold transition-all shadow-sm hover:scale-105 active:scale-95 duration-200">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('opacity-0', 'pointer-events-none');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('opacity-0', 'pointer-events-none');
        }

        function openEditPengunjung(item) {
            document.getElementById('edit_nama').value = item.nama_pengunjung;
            document.getElementById('edit_jenis').value = item.jenis_pengunjung;
            document.getElementById('edit_keperluan').value = item.keperluan || '';

            // Update Form Action URL
            document.getElementById('editForm').action = `{{ url('pengunjung') }}/${item.id_pengunjung}`;

            openModal('editModal');
        }
    </script>
</body>

</html>