<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Buku;
use App\Models\Pengguna;
use App\Models\Peminjaman;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'today'); // today, week, month

        // 1. Determine Date Range
        $endDate = Carbon::now();
        if ($filter === 'today') {
            $startDate = Carbon::today();
            $dateFormat = 'H:00'; // Group by Hour
            $labelFormat = 'H:i';
        } elseif ($filter === 'month') {
            $startDate = Carbon::now()->subDays(30);
            $dateFormat = 'Y-m-d'; // Group by Day
            $labelFormat = 'd M';
        } else { // week
            $startDate = Carbon::now()->subDays(7);
            $dateFormat = 'Y-m-d';
            $labelFormat = 'D, d M';
        }

        // 2. Fetch Chart Data (Generic Helper)
        $minDate = $startDate->copy();
        $maxDate = $endDate->copy();

        // A. Peminjaman Trend (Line Chart)
        $peminjamanQuery = Peminjaman::select(
                DB::raw($filter === 'today' ? 'DATE_FORMAT(created_at, "%H:00") as date' : 'DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status_transaksi', ['berjalan', 'selesai']) // Only valid transactions
            ->groupBy('date')
            ->orderBy('date');

        $peminjamanData = $peminjamanQuery->get()->pluck('count', 'date')->toArray();

        // B. Pengunjung Count (Bar Chart)
        $pengunjungQuery = \App\Models\Pengunjung::select(
                DB::raw($filter === 'today' ? 'DATE_FORMAT(created_at, "%H:00") as date' : 'DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        $pengunjungData = $pengunjungQuery->get()->pluck('count', 'date')->toArray();

        // 3. Fill Missing Dates/Hours
        $labels = [];
        $chartPeminjaman = [];
        $chartPengunjung = [];

        $current = $minDate->copy();
        while ($current <= $maxDate) {
            $key = $filter === 'today' ? $current->format('H:00') : $current->format('Y-m-d');
            $label = $filter === 'today' ? $current->format('H:00') : $current->format($labelFormat);
            
            $labels[] = $label;
            $chartPeminjaman[] = $peminjamanData[$key] ?? 0;
            $chartPengunjung[] = $pengunjungData[$key] ?? 0;

            if ($filter === 'today') {
                $current->addHour();
            } else {
                $current->addDay();
            }
        }

        $chartData = [
            'labels' => $labels,
            'peminjaman' => $chartPeminjaman,
            'pengunjung' => $chartPengunjung,
        ];

        if ($request->ajax()) {
            return response()->json($chartData);
        }

        try {
            $stats = [
                'total_buku' => Buku::count(),
                'total_anggota' => Pengguna::where('peran', 'anggota')->count(),
                'peminjaman_aktif' => Peminjaman::where('status_transaksi', 'berjalan')->count(),
                'total_denda' => Denda::where('status_bayar', 'belum_bayar')->sum('jumlah_denda'),
                'pending_verifications' => Peminjaman::where('status_transaksi', 'menunggu_verifikasi')->count(),
            ];

            $peminjamanTerbaru = Peminjaman::with('pengguna')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            $stats = [
                'total_buku' => 0,
                'total_anggota' => 0,
                'peminjaman_aktif' => 0,
                'total_denda' => 0,
            ];
            $peminjamanTerbaru = [];
        }

        return view('admin.dashboard', compact('stats', 'peminjamanTerbaru', 'chartData', 'filter'));
    }
}
