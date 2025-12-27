<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Denda;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pengaturan;

class PengembalianController extends Controller
{
    /**
     * Display a listing of active transactions (Loans).
     */
    public function index(Request $request)
    {
        // Hanya tampilkan yang STATUS = 'berjalan'
        $query = Peminjaman::with(['pengguna', 'details.buku'])
            ->where('status_transaksi', 'berjalan')
            ->orderBy('tanggal_jatuh_tempo', 'asc'); // Prioritaskan yang mau jatuh tempo

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_peminjaman', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        return view('sirkulasi.pengembalian.index', compact('peminjaman'));
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

        if ($peminjaman->status_transaksi == 'berjalan') {
            // Gunakan startOfDay agar jam tidak mempengaruhi perhitungan hari
            $jatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo)->startOfDay();
            $hariIni = Carbon::now()->startOfDay();

            if ($hariIni->gt($jatuhTempo)) {
                // diffInDays returns absolute value by default, but let's be explicit
                // If today is 2025-12-27 and due is 2025-12-19 -> diff is 8
                $terlambatHari = $jatuhTempo->diffInDays($hariIni);

                // Ambil tarif denda dari pengaturan
                $pengaturan = Pengaturan::first();
                $tarifDenda = $pengaturan->denda_per_hari ?? 0;

                $jumlahBukuDipinjam = $peminjaman->details->where('status_buku', 'dipinjam')->count();
                $estimasiDenda = $terlambatHari * $tarifDenda * $jumlahBukuDipinjam;
            }
        }

        return view('sirkulasi.pengembalian.show', compact('peminjaman', 'terlambatHari', 'estimasiDenda'));
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
            DB::beginTransaction();

            $peminjaman = Peminjaman::findOrFail($request->id_peminjaman);
            $returnedDetailsIds = $request->details; // Checkbox IDs

            $jatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo)->startOfDay();
            $hariIni = Carbon::now()->startOfDay();
            $isLate = $hariIni->gt($jatuhTempo);
            $lateDays = $isLate ? $jatuhTempo->diffInDays($hariIni) : 0;

            $pengaturan = Pengaturan::first();
            $dendaPerBukuPerHari = $pengaturan->denda_per_hari ?? 0;

            $totalDenda = 0;

            foreach ($returnedDetailsIds as $detailId) {
                $detail = DetailPeminjaman::findOrFail($detailId);

                // Skip if already returned
                if ($detail->status_buku !== 'dipinjam')
                    continue;

                // Update Detail
                $detail->update([
                    'status_buku' => 'dikembalikan',
                    'tanggal_kembali_aktual' => $hariIni,
                ]);

                // Increment Book Stock
                $buku = Buku::find($detail->id_buku);
                $buku->increment('stok_tersedia');

                // Calculate Fine for THIS book
                if ($isLate) {
                    $dendaAmount = $lateDays * $dendaPerBukuPerHari;
                    $totalDenda += $dendaAmount;

                    Denda::create([
                        'id_detail_peminjaman' => $detail->id_detail_peminjaman,
                        'jenis_denda' => 'terlambat',
                        'jumlah_denda' => $dendaAmount,
                        'status_bayar' => 'belum_bayar', // Bisa langsung bayar jika fitur allow
                        'keterangan' => "Terlambat $lateDays hari"
                    ]);
                }
            }

            // Handle Payment of Fine (if any)
            // Logic Simplified: If user inputs partial payment, we allocate it?
            // For now, let's assume if there IS a fine created, we might want to mark it as paid if the user pays immediately.
            // But complex partial payment logic is tricky. Let's just create the debt first.

            // Check if ALL items in this transaction are returned
            $remainingItems = DetailPeminjaman::where('id_peminjaman', $peminjaman->id_peminjaman)
                ->where('status_buku', 'dipinjam')
                ->count();

            if ($remainingItems === 0) {
                $peminjaman->update(['status_transaksi' => 'selesai']);
            }

            DB::commit();

            $msg = 'Pengembalian berhasil diproses.';
            if ($totalDenda > 0) {
                $msg .= " Total Denda Tercatat: Rp " . number_format($totalDenda, 0, ',', '.');
            }

            return redirect()->route('pengembalian.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}
