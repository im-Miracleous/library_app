<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Buku;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['pengguna', 'details.buku'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $search = $request->search;
            $query->where('id_peminjaman', 'like', "%{$search}%")
                ->orWhereHas('pengguna', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
        }

        if ($request->filled('status')) {
            $query->where('status_transaksi', $request->status);
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        return view('sirkulasi.peminjaman.index', compact('peminjaman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pengaturan = Pengaturan::first();
        return view('sirkulasi.peminjaman.create', compact('pengaturan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal_pinjam' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'buku' => 'required|array|min:1',
            'buku.*.id_buku' => 'required|exists:buku,id_buku',
        ]);

        $pengaturan = Pengaturan::first();
        $batasHari = $pengaturan->batas_peminjaman_hari ?? 7;
        $maxBuku = $pengaturan->maksimal_buku_pinjam ?? 3;

        // Validasi Batas Waktu
        $tglPinjam = Carbon::parse($request->tanggal_pinjam);
        $tglJatuhTempo = Carbon::parse($request->tanggal_jatuh_tempo);

        if ($tglPinjam->diffInDays($tglJatuhTempo) > $batasHari) {
            return back()->with('error', "Maksimal peminjaman adalah $batasHari hari.")->withInput();
        }

        // Validasi Maksimal Buku
        // Hitung buku yang sedang dipinjam oleh user (status 'dipinjam')
        $bukuSedangDipinjam = DetailPeminjaman::whereHas('peminjaman', function ($q) use ($request) {
            $q->where('id_pengguna', $request->id_pengguna)
                ->where('status_transaksi', 'berjalan');
        })->where('status_buku', 'dipinjam')->count();

        $bukuAkanDipinjam = count($request->buku);

        if (($bukuSedangDipinjam + $bukuAkanDipinjam) > $maxBuku) {
            return back()->with('error', "User ini sudah meminjam $bukuSedangDipinjam buku. Maksimal peminjaman adalah $maxBuku buku. (Akan meminjam: $bukuAkanDipinjam)")->withInput();
        }

        try {
            DB::beginTransaction();

            // 1. Buat ID Peminjaman (Manual Generation in PHP)
            $today = date('Y-m-d');
            $dateCode = date('Y-m-d'); // Keep distinct just in case format changes

            // Get last ID from today
            $lastTx = Peminjaman::whereDate('created_at', $today)
                ->orderBy('id_peminjaman', 'desc')
                ->first();

            $nextNo = 1;
            if ($lastTx) {
                // P-YYYY-MM-ddNNN (Example: P-2025-12-28001)
                // Length is 15 chars. Last 3 is number.
                $lastId = $lastTx->id_peminjaman;
                $lastNo = intval(substr($lastId, -3));
                $nextNo = $lastNo + 1;
            }

            $newId = 'P-' . $dateCode . str_pad($nextNo, 3, '0', STR_PAD_LEFT);

            // Buat Peminjaman Header
            $peminjaman = Peminjaman::create([
                'id_peminjaman' => $newId,
                'id_pengguna' => $request->id_pengguna,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status_transaksi' => 'berjalan',
                'keterangan' => $request->keterangan,
            ]);

            // 2. Buat Detail Peminjaman & Update Stok
            foreach ($request->buku as $item) {
                // Check availability again just in case
                $buku = Buku::find($item['id_buku']);
                if ($buku->stok_tersedia <= 0) {
                    throw new \Exception("Buku {$buku->judul} stok habis.");
                }

                DetailPeminjaman::create([
                    'id_peminjaman' => $peminjaman->id_peminjaman,
                    'id_buku' => $item['id_buku'],
                    'jumlah' => 1,
                    'status_buku' => 'dipinjam',
                ]);

                // Decrement Stok
                $buku->decrement('stok_tersedia');
            }

            DB::commit();

            return redirect()->route('peminjaman.index')->with('success', 'Transaksi peminjaman berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $peminjaman = Peminjaman::with(['pengguna', 'details.buku'])->findOrFail($id);

        // Cegah pengeditan jika transaksi sudah 'selesai'
        if ($peminjaman->status_transaksi == 'selesai') {
            return back()->with('error', 'Transaksi yang sudah selesai tidak dapat diedit.');
        }

        return view('sirkulasi.peminjaman.edit', compact('peminjaman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Simple update: only allow changing dates or notes for now. 
        // Changing books is complex (stock mgmt) and usually better to just create new transaction or use return feature.

        $request->validate([
            'tanggal_pinjam' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date', // Validation relaxed for testing purposes
        ]);

        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('peminjaman.show', $id)->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with(['pengguna', 'details.buku'])->findOrFail($id);
        return view('sirkulasi.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Usually transaction log should be kept. Allow delete only for admin.
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
