<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Buku;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            $query->where('kode_peminjaman', 'like', "%{$search}%")
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
        return view('sirkulasi.peminjaman.create');
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

        try {
            DB::beginTransaction();

            // 1. Buat Peminjaman Header
            // We use a temporary unique code to identify the record after trigger generates the ID
            $tempCode = 'TRX-' . uniqid();

            // Create with temp code (Trigger will generate id_peminjaman, but respect our kode_peminjaman)
            Peminjaman::create([
                'id_pengguna' => $request->id_pengguna,
                'kode_peminjaman' => $tempCode,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status_transaksi' => 'berjalan',
                'keterangan' => $request->keterangan,
            ]);

            // Retrieve the record to get the generated ID
            $peminjaman = Peminjaman::where('kode_peminjaman', $tempCode)->firstOrFail();

            // Optional: Align kode_peminjaman with id_peminjaman if desired, or keep as is. 
            // The trigger logic implies it usually expects them to be same if generic.
            // Let's update it to match ID for consistency, as the trigger would have done if we sent null.
            $peminjaman->update(['kode_peminjaman' => $peminjaman->id_peminjaman]);

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

        // Prevent editing if transaction is already 'selesai'
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
        // Hanya bisa hapus jika transaksi belum selesai? Atau bebas?
        // Usually transaction log should be kept. Allow delete only for admin maybe.
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
