<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Anggota - Library App</title>
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

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme-toggle.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-700 dark:text-white font-display">
    <div class="flex h-screen w-full relative">

        <!-- SIDEBAR -->
        <x-sidebar-component />

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto relative z-10 w-full">

            <x-header-component title="Data Anggota" />

            <div class="p-4 sm:p-8">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <!-- Statistik Bar -->
                    <div class="flex flex-wrap gap-3">
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-blue-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Total:</span>
                            <span class="text-sm font-bold text-blue-700 dark:text-blue-400">{{ $totalAnggota }}</span>
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-green-700 dark:text-green-400">Aktif:</span>
                            <span class="text-sm font-bold text-green-700 dark:text-green-400">{{ $totalAktif }}</span>
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg">
                            <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-red-700 dark:text-red-400">Nonaktif:</span>
                            <span class="text-sm font-bold text-red-700 dark:text-red-400">{{ $totalNonaktif }}</span>
                        </div>
                    </div>

                    <button onclick="openModal('createModal')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-surface dark:bg-accent text-primary-dark rounded-xl font-bold text-sm shadow-sm dark:shadow-lg dark:shadow-accent/10 transition-all hover:scale-105 active:scale-95 duration-200 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Anggota
                    </button>
                </div>

                @if (session('success'))
                    <div
                        class="mb-6 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3 animate-enter">
                        <span class="material-symbols-outlined">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark overflow-hidden animate-enter delay-100 shadow-sm dark:shadow-none transition-colors">

                    <!-- Table Header & Filter -->
                    <div
                        class="p-4 border-b border-primary/20 dark:border-[#36271F] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface dark:bg-[#1A1410]">
                        <!-- Filter Tabs (Animated Sliding Pill with Colors) -->
                        <div
                            class="relative bg-slate-100 dark:bg-black/20 rounded-xl p-1 grid grid-cols-3 w-full sm:w-[320px]">
                            {{-- Pill Background yang Bergerak & Berubah Warna --}}
                            <div id="filter-pill"
                                class="absolute top-1 bottom-1 shadow-sm dark:shadow-md rounded-lg transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] z-0 box-border"
                                style="width: calc(33.33% - 0.4rem); left: 0.2rem;">
                            </div>

                            {{-- Tab Items --}}
                            <a href="{{ route('pengguna.index') }}" onclick="movePill(this, 'all')" data-color="all"
                                class="filter-tab relative z-10 flex items-center justify-center py-2 text-xs font-bold rounded-lg transition-colors duration-300 cursor-pointer {{ !request('status') ? 'text-white active' : 'text-slate-500 dark:text-white/50 hover:text-slate-700 dark:hover:text-white/80' }}">
                                Semua
                            </a>
                            <a href="{{ route('pengguna.index', ['status' => 'aktif']) }}"
                                onclick="movePill(this, 'active')" data-color="active"
                                class="filter-tab relative z-10 flex items-center justify-center py-2 text-xs font-bold rounded-lg transition-colors duration-300 cursor-pointer {{ request('status') == 'aktif' ? 'text-white active' : 'text-slate-500 dark:text-white/50 hover:text-slate-700 dark:hover:text-white/80' }}">
                                Aktif
                            </a>
                            <a href="{{ route('pengguna.index', ['status' => 'nonaktif']) }}"
                                onclick="movePill(this, 'inactive')" data-color="inactive"
                                class="filter-tab relative z-10 flex items-center justify-center py-2 text-xs font-bold rounded-lg transition-colors duration-300 cursor-pointer {{ request('status') == 'nonaktif' ? 'text-white active' : 'text-slate-500 dark:text-white/50 hover:text-slate-700 dark:hover:text-white/80' }}">
                                Nonaktif
                            </a>
                        </div>

                        <script>
                            function initPill() {
                                const pill = document.getElementById('filter-pill');
                                let activeTab = document.querySelector('.filter-tab.active');

                                if (activeTab && pill) {
                                    // Set warna awal dan posisi
                                    setPillColor(pill, activeTab.dataset.color);
                                    positionPill(activeTab, pill);
                                } else if (pill) {
                                    // Default ke 'all' jika tidak ada yang active (fallback)
                                    setPillColor(pill, 'all');
                                }
                            }

                            function movePill(el, colorType) {
                                const pill = document.getElementById('filter-pill');

                                // Reset text classes
                                document.querySelectorAll('.filter-tab').forEach(t => {
                                    t.classList.remove('active', 'text-white');
                                    t.classList.add('text-slate-500', 'dark:text-white/50');
                                });

                                // Set active state to clicked element
                                el.classList.remove('text-slate-500', 'dark:text-white/50');
                                el.classList.add('active', 'text-white');

                                // Move and Color Pill
                                setPillColor(pill, colorType);
                                positionPill(el, pill);
                            }

                            function setPillColor(pill, type) {
                                // Reset warna
                                pill.classList.remove('bg-primary', 'dark:bg-accent', 'bg-green-500', 'bg-red-500');

                                // Set warna baru
                                if (type === 'active') {
                                    pill.classList.add('bg-green-500');
                                } else if (type === 'inactive') {
                                    pill.classList.add('bg-red-500');
                                } else {
                                    pill.classList.add('bg-primary', 'dark:bg-accent'); // Default Cokelat
                                }
                            }

                            function positionPill(element, pill) {
                                const parentRect = element.parentElement.getBoundingClientRect();
                                const rect = element.getBoundingClientRect();
                                const left = rect.left - parentRect.left;

                                pill.style.width = `${rect.width}px`;
                                pill.style.transform = `translateX(${left}px)`;
                                pill.style.left = '0';
                            }

                            document.addEventListener('DOMContentLoaded', initPill);
                        </script>

                        <!-- Search Bar -->
                        <form action="{{ route('pengguna.index') }}" method="GET" class="relative w-full sm:w-64">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 dark:text-white/40 text-lg">search</span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama atau email..."
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-[#36271F] rounded-lg pl-10 pr-4 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none placeholder-primary-mid/60 dark:placeholder-white/40">
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr
                                    class="border-b border-primary/20 dark:border-border-dark text-slate-500 dark:text-white/40 text-xs uppercase tracking-wider bg-surface dark:bg-[#1A1410]">
                                    <th class="p-4 pl-6 font-medium">ID</th>
                                    <th class="p-4 font-medium">Nama Anggota</th>
                                    <th class="p-4 font-medium">Email</th>
                                    <th class="p-4 font-medium">Telepon</th>
                                    <th class="p-4 font-medium">Status</th>
                                    <th class="p-4 pr-6 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-[#36271F] text-sm text-slate-600 dark:text-white/80">
                                @forelse($pengguna as $user)
                                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors group">
                                        <td class="p-4 pl-6 font-mono text-primary dark:text-accent font-bold">
                                            {{ $user->id_pengguna }}
                                        </td>
                                        <td class="p-4 font-bold text-slate-800 dark:text-white">{{ $user->nama }}</td>
                                        <td class="p-4">{{ $user->email }}</td>
                                        <td class="p-4">{{ $user->telepon ?? '-' }}</td>
                                        <td class="p-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold {{ $user->status == 'aktif' ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-500' : 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-500' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td class="p-4 pr-6 text-right flex justify-end gap-2">
                                            <button onclick="openEditPengguna('{{ $user->id_pengguna }}')"
                                                class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <form action="{{ route('pengguna.destroy', $user->id_pengguna) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 transition-colors"
                                                    title="Hapus">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 dark:text-white/40">Belum ada
                                            data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-200 dark:border-border-dark">
                        {{ $pengguna->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH -->
    <div id="createModal" class="fixed inset-0 z-50 transition-all duration-200 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 dark:bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('createModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <!-- Panel Modal -->
                <div id="createModalPanel"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-left shadow-2xl transition-all duration-300 scale-95 sm:w-full sm:max-w-2xl">

                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-border-dark flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary dark:text-accent">person_add</span>
                            Tambah Anggota
                        </h3>
                        <button onclick="closeModal('createModal')"
                            class="text-slate-500 dark:text-white/60 hover:text-slate-800 dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('pengguna.store') }}" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Lengkap</label>
                            <input type="text" name="nama"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none transition-colors"
                                required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Email</label>
                                <input type="email" name="email"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Telepon</label>
                                <input type="text" name="telepon"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Alamat</label>
                            <textarea name="alamat" rows="2"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Password</label>
                                <input type="password" name="password"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Konfirmasi
                                    Password</label>
                                <input type="password" name="password_confirmation"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                        </div>

                        <div
                            class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                            <button type="button" onclick="closeModal('createModal')"
                                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold transition-all shadow-sm dark:shadow-md hover:scale-105 active:scale-95 duration-200">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div id="editModal" class="fixed inset-0 z-50 transition-all duration-200 opacity-0 pointer-events-none"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 dark:bg-black/70 backdrop-blur-sm transition-opacity duration-300"
            onclick="closeModal('editModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div id="editModalPanel"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-surface-dark border border-slate-200 dark:border-border-dark text-left shadow-2xl transition-all duration-300 scale-95 sm:my-8 sm:w-full sm:max-w-2xl">
                    <div
                        class="px-6 py-4 border-b border-primary/20 dark:border-border-dark flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">edit</span>
                            Edit Anggota
                        </h3>
                        <button onclick="closeModal('editModal')"
                            class="text-slate-500 dark:text-white/60 hover:text-slate-800 dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <form id="editForm" method="POST" class="p-6 flex flex-col gap-5">
                        @csrf @method('PUT')
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Nama
                                Lengkap</label>
                            <input type="text" id="edit_nama" name="nama"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Email</label>
                                <input type="email" id="edit_email" name="email"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    required>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Telepon</label>
                                <input type="text" id="edit_telepon" name="telepon"
                                    class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Alamat</label>
                            <textarea id="edit_alamat" name="alamat" rows="2"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none resize-none"></textarea>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider">Status</label>
                            <select id="edit_status" name="status"
                                class="bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-4 py-3 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none cursor-pointer">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div
                            class="p-3 bg-yellow-50 dark:bg-yellow-500/5 rounded-lg border border-yellow-200 dark:border-yellow-500/10 mt-2">
                            <p
                                class="text-[10px] text-yellow-600 dark:text-yellow-500 mb-2 font-bold uppercase tracking-wider">
                                Ubah Password (Opsional)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <input type="password" name="password"
                                    class="bg-white dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    placeholder="Password Baru">
                                <input type="password" name="password_confirmation"
                                    class="bg-white dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2 text-primary-dark dark:text-white text-sm focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none"
                                    placeholder="Konfirmasi">
                            </div>
                        </div>
                        <div
                            class="mt-4 flex justify-end gap-3 pt-4 border-t border-primary/20 dark:border-border-dark">
                            <button type="button" onclick="closeModal('editModal')"
                                class="px-4 py-2 rounded-lg border border-slate-200 dark:border-border-dark text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-bold transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-surface dark:bg-accent text-primary-dark text-sm font-bold transition-all shadow-sm dark:shadow-md hover:scale-105 active:scale-95 duration-200">Update
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>