<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Denda - Library App</title>
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
            <x-header-component title="Laporan Denda" />

            <div class="p-4 sm:p-8">
                <!-- Header & Breadcrumb -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 animate-enter">
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-white/60">
                        <span class="material-symbols-outlined text-base">home</span>
                        <span>/</span>
                        <span>Laporan</span>
                        <span>/</span>
                        <span class="font-bold text-primary dark:text-white">Denda</span>
                    </div>
                </div>

                <!-- Filters -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark p-6 shadow-sm mb-6 animate-enter delay-100">
                    <form action="{{ route('laporan.denda') }}" method="GET"
                        class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="w-full md:w-auto">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Dari
                                Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                        </div>
                        <div class="w-full md:w-auto">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Sampai
                                Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                        </div>
                        <div class="w-full md:w-48">
                            <label
                                class="text-xs font-bold text-slate-500 dark:text-white/60 uppercase tracking-wider mb-1.5 block">Status
                                Pembayaran</label>
                            <select name="status_bayar"
                                class="w-full bg-background-light dark:bg-[#120C0A] border border-primary/20 dark:border-border-dark rounded-lg px-3 py-2.5 text-primary-dark dark:text-white focus:ring-1 focus:ring-primary dark:focus:ring-accent outline-none">
                                <option value="">Semua</option>
                                <option value="lunas" {{ $statusBayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="belum_bayar" {{ $statusBayar == 'belum_bayar' ? 'selected' : '' }}>Belum
                                    Bayar</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full md:w-auto px-6 py-2.5 bg-primary text-white dark:bg-accent dark:text-primary-dark rounded-xl font-bold hover:brightness-110 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">filter_list</span>
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 animate-enter delay-200">
                    <div class="bg-red-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">money_off</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-red-100 text-sm font-bold uppercase tracking-wider mb-1">Total Denda
                                (Akumulasi)</div>
                            <div class="text-3xl font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="bg-green-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">attach_money</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-green-100 text-sm font-bold uppercase tracking-wider mb-1">Sudah Dibayar
                            </div>
                            <div class="text-3xl font-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="bg-yellow-500 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden group">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[120px]">hourglass_empty</span>
                        </div>
                        <div class="relative z-10">
                            <div class="text-yellow-100 text-sm font-bold uppercase tracking-wider mb-1">Belum Dibayar
                            </div>
                            <div class="text-3xl font-bold">Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div
                    class="bg-white dark:bg-surface-dark rounded-2xl border border-primary/20 dark:border-border-dark overflow-hidden shadow-sm animate-enter delay-300">
                    <div
                        class="p-4 border-b border-primary/20 dark:border-white/10 flex justify-between items-center bg-surface dark:bg-[#1A1410]">
                        <h3 class="font-bold text-slate-800 dark:text-white">Riwayat Denda</h3>
                        <div class="text-xs text-slate-500 dark:text-white/60">
                            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 dark:bg-white/5 text-slate-500 dark:text-white/60 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-medium">Tanggal</th>
                                    <th class="p-4 font-medium">Peminjam</th>
                                    <th class="p-4 font-medium">Jenis Denda</th>
                                    <th class="p-4 font-medium">Buku</th>
                                    <th class="p-4 font-medium text-right">Nominal</th>
                                    <th class="p-4 font-medium text-center">Status</th>
                                    <th class="p-4 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 dark:divide-white/10 text-sm text-slate-600 dark:text-white/80">
                                @forelse ($denda as $item)
                                    <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-colors">
                                        <td class="p-4">{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td class="p-4">
                                            <div class="font-bold">{{ $item->detail->peminjaman->pengguna->nama ?? '-' }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                {{ $item->detail->peminjaman->kode_peminjaman ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="p-4 capitalize">{{ $item->jenis_denda }}</td>
                                        <td class="p-4">{{ $item->detail->buku->judul ?? '-' }}</td>
                                        <td class="p-4 text-right font-mono font-bold">
                                            Rp {{ number_format($item->jumlah_denda, 0, ',', '.') }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ $item->status_bayar == 'lunas' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' }}">
                                                {{ $item->status_bayar == 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            @if ($item->status_bayar == 'belum_bayar')
                                                <form action="{{ route('denda.update', $item->id_denda) }}" method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan pembayaran ini? Status akan berubah menjadi Lunas.')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-primary text-white dark:bg-accent dark:text-primary-dark rounded-md text-xs font-bold shadow-sm hover:brightness-110 transition-all">
                                                        Bayar
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400 dark:text-white/40">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-slate-400 dark:text-white/40 italic">
                                            Tidak ada catatan denda pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>

</html>