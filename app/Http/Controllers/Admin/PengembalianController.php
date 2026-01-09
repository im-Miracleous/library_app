<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Denda;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pengaturan;
use Illuminate\Pagination\LengthAwarePaginator;

class PengembalianController extends Controller
{
    /**
     * Display a listing of active transactions (Loans).
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');
        $sort = $request->input('sort') ?: 'id_peminjaman';
        $direction = $request->input('direction') ?: 'desc';

        // Call SP (Implicitly filters status='berjalan')
        $data = DB::select('CALL sp_get_pengembalian_list(?, ?, ?, ?, ?, @total)', [
            $search,
            $sort,
            $direction,
            $limit,
            $offset
        ]);
        $total = DB::select('SELECT @total as total')[0]->total;

        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'total' => $total,
                'links' => (string) (new LengthAwarePaginator(
                    [],
                    $total,
                    $limit,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                ))->links()
            ]);
        }

        // Manual Pagination for View
        $peminjaman = new LengthAwarePaginator(
            $data,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.sirkulasi.pengembalian.index', compact('peminjaman'));
    }

    /**
     * Show the form for processing a return.
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['pengguna', 'details.buku'])->findOrFail($id);

        // Periksa Denda Keterlambatan untuk Estimasi
        $jatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo);
        $hariIni = Carbon::now();
        $terlambatHari = 0;
        $estimasiDenda = 0;
        $pengaturan = Pengaturan::first();

        if ($peminjaman->status_transaksi == 'berjalan') {
            // Gunakan startOfDay agar jam tidak mempengaruhi perhitungan hari
            $jatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo)->startOfDay();
            $hariIni = Carbon::now()->startOfDay();

            if ($hariIni->gt($jatuhTempo)) {
                // diffInDays returns absolute value by default, but let's be explicit
                // If today is 2025-12-27 and due is 2025-12-19 -> diff is 8
                $terlambatHari = $jatuhTempo->diffInDays($hariIni);

                // Ambil tarif denda dari pengaturan
                $tarifDenda = $pengaturan->denda_per_hari ?? 0;

                $jumlahBukuDipinjam = $peminjaman->details->where('status_buku', 'dipinjam')->count();
                $estimasiDenda = $terlambatHari * $tarifDenda * $jumlahBukuDipinjam;
            }
        }

        return view('admin.sirkulasi.pengembalian.show', compact('peminjaman', 'terlambatHari', 'estimasiDenda', 'pengaturan'));
    }

    /**
     * Store (Process) the return.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_peminjaman' => 'required|exists:peminjaman,id_peminjaman',
            'details' => 'required|array', // Array of id_detail_peminjaman to return
            'bayar_denda' => 'nullable|numeric|min:0',
        ]);

        try {
            // DB::beginTransaction(); // Removed: SP handles transaction

            $peminjaman = Peminjaman::findOrFail($request->id_peminjaman);
            $returnedDetailsIds = $request->details; // Checkbox IDs

            $jatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo)->startOfDay();
            $hariIni = Carbon::now()->startOfDay();
            $isLate = $hariIni->gt($jatuhTempo);
            $lateDays = $isLate ? $jatuhTempo->diffInDays($hariIni) : 0;

            $pengaturan = Pengaturan::first();
            $dendaPerBukuPerHari = $pengaturan->denda_per_hari ?? 0;

            $totalDenda = 0;

            // Prepare JSON for SP
            $bukuConditions = [];
            foreach ($returnedDetailsIds as $detailId) {
                $detail = DetailPeminjaman::find($detailId);
                if ($detail && $detail->status_buku === 'dipinjam') {
                    $condition = $request->input("kondisi.{$detailId}", 'baik');
                    $bukuConditions[] = [
                        'id_buku' => $detail->id_buku,
                        'kondisi' => $condition
                    ];
                }
            }

            // Calculate total denda (Simplified: pass 0 and let SP create header, or calculate PHP side and pass total?)
            // SP takes p_denda_total. We computed $totalDenda in previous loop for logging/display.
            // But since we are moving logic to SP, ideally SP calculates it? 
            // My SP expects p_denda_total. So I will keep the PHP calculation logic above (lines 129-191) 
            // BUT I need to remove the DB::updates/inserts inside the loop and just gather data.
            
            // Wait, existing code calculates denda and Creates Denda Models inside the loop.
            // SP `sp_complete_peminjaman` processes the return AND inserts Denda.
            // So executing the PHP loop + SP would double-process or conflict.
            
            // REFACTOR STRATEGY:
            // 1. Loop PHP to calculate Total Denda (without DB writes) and build JSON.
            // 2. Call SP with JSON and Total Denda.
            
            // Reset Arrays/Counters
            $jsonPayload = [];
            $totalDendaCalculated = 0;

            foreach ($returnedDetailsIds as $detailId) {
                $detail = DetailPeminjaman::findOrFail($detailId);
                if ($detail->status_buku !== 'dipinjam') continue;

                $condition = $request->input("kondisi.{$detailId}", 'baik');
                
                // Calculate Denda per item
                $itemDenda = 0;
                $keteranganParts = [];
                
                if ($isLate) {
                    $days = $jatuhTempo->diffInDays($hariIni);
                    $itemDenda += $days * ($pengaturan->denda_per_hari ?? 0);
                    $keteranganParts[] = "Telat {$days} hari";
                }

                if ($condition === 'rusak') {
                    $itemDenda += $pengaturan->denda_rusak ?? 0;
                    $keteranganParts[] = "Buku Rusak";
                } elseif ($condition === 'hilang') {
                    $itemDenda += $pengaturan->denda_hilang ?? 0;
                    $keteranganParts[] = "Buku Hilang";
                }

                $totalDendaCalculated += $itemDenda;

                // Add to JSON
                $jsonPayload[] = [
                    'id_detail' => $detail->id_detail_peminjaman,
                    'id_buku' => $detail->id_buku,
                    'kondisi' => $condition,
                    'denda_amount' => $itemDenda,
                    'keterangan' => implode(', ', $keteranganParts)
                ];
            }

            // Call SP
            \Illuminate\Support\Facades\DB::statement('CALL sp_complete_peminjaman(?, ?, ?)', [
                $peminjaman->id_peminjaman,
                $hariIni->format('Y-m-d H:i:s'),
                json_encode($jsonPayload)
            ]);

            // \Illuminate\Support\Facades\DB::commit(); // Removed: SP handles transaction

            $msg = 'Pengembalian berhasil diproses (SP).';
            if ($totalDendaCalculated > 0) {
                $msg .= " Total Denda Tercatat: Rp " . number_format($totalDendaCalculated, 0, ',', '.');
            }

            return redirect()->route('pengembalian.index')
                ->with('success', $msg)
                ->with('detail_url', route('peminjaman.show', $peminjaman->id_peminjaman));

        } catch (\Exception $e) {
            // DB::rollBack(); // Removed: SP handles transaction rollback internally
            return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}
