<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Buku;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan;
use App\Notifications\LoanStatusNotification;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');
        $status = $request->input('status');
        $sort = $request->input('sort') ?: 'id_peminjaman';
        $direction = $request->input('direction') ?: 'desc';

        // Call SP
        $data = DB::select('CALL sp_get_peminjaman_list(?, ?, ?, ?, ?, ?, @total)', [
            $search,
            $status,
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

        return view('admin.sirkulasi.peminjaman.index', compact('peminjaman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pengaturan = Pengaturan::first();
        return view('admin.sirkulasi.peminjaman.create', compact('pengaturan'));
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
            // Persiapkan Data untuk Stored Procedure
            $idPengguna = $request->id_pengguna;
            $tglPinjam = $request->tanggal_pinjam;
            $tglJatuhTempo = $request->tanggal_jatuh_tempo;
            $keterangan = $request->keterangan;

            // Format buku ke JSON Array of Strings: ["B001", "B002"]
            $jsonBuku = collect($request->buku)->pluck('id_buku')->toJson();

            // Panggil Stored Procedure
            // Karena menggunakan OUT parameter di MySQL Driver PHP biasa agak ribet, 
            // kita gunakan session variable @output untuk menangkap pesan.

            DB::statement("CALL sp_buat_peminjaman(?, ?, ?, ?, ?, @output)", [
                $idPengguna,
                $tglPinjam,
                $tglJatuhTempo,
                $keterangan,
                $jsonBuku
            ]);

            // Ambil Output Message
            $result = DB::select("SELECT @output as message")[0]->message;

            if ($result !== 'Success') {
                return back()->with('error', 'Gagal memproses peminjaman: ' . $result)->withInput();
            }

            // Get the newly created ID to provide the detail link
            $newId = Peminjaman::where('id_pengguna', $idPengguna)
                ->orderBy('created_at', 'desc')
                ->value('id_peminjaman');

            return redirect()->route('peminjaman.index')
                ->with('success', 'Transaksi peminjaman berhasil dibuat (via Stored Procedure).')
                ->with('detail_url', route('peminjaman.show', $newId));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $peminjaman = Peminjaman::with(['pengguna', 'details.buku'])->findOrFail($id);

        // Cegah pengeditan jika transaksi sudah 'selesai' (Kecuali Owner)
        if ($peminjaman->status_transaksi == 'selesai' && auth()->user()->peran !== 'owner') {
            return back()->with('error', 'Transaksi yang sudah selesai hanya dapat diedit oleh Owner.');
        }

        return view('admin.sirkulasi.peminjaman.edit', compact('peminjaman'));
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
        $peminjaman = Peminjaman::with(['pengguna', 'details.buku', 'details.denda'])->findOrFail($id);

        // Auto-mark notification as read for this loan
        if (Auth::check()) {
            Auth::user()->unreadNotifications
                ->where('data.peminjaman_id', $id)
                ->markAsRead();
        }

        return view('admin.sirkulasi.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function approve($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status_transaksi !== 'menunggu_verifikasi') {
            return back()->with('error', 'Hanya transaksi dengan status menunggu verifikasi yang dapat disetujui.');
        }

        $pengaturan = Pengaturan::first();
        $batasHari = $pengaturan->batas_peminjaman_hari ?? 7;

        $peminjaman->update([
            'status_transaksi' => 'berjalan',
            'tanggal_pinjam' => now(),
            'tanggal_jatuh_tempo' => now()->addDays($batasHari),
        ]);

        // Mark as read after approval
        Auth::user()->unreadNotifications
            ->where('data.peminjaman_id', $id)
            ->markAsRead();

        // Notify Member
        try {
            $peminjaman->pengguna->notify(new LoanStatusNotification($peminjaman, 'disetujui'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify member about loan approval: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject($id)
    {
        $peminjaman = Peminjaman::with('details.buku')->findOrFail($id);

        if ($peminjaman->status_transaksi !== 'menunggu_verifikasi') {
            return back()->with('error', 'Hanya transaksi dengan status menunggu verifikasi yang dapat ditolak.');
        }

        DB::transaction(function () use ($peminjaman, $id) {
            // Restore stock
            foreach ($peminjaman->details as $detail) {
                $detail->buku->increment('stok_tersedia', $detail->jumlah);
            }

            $peminjaman->update([
                'status_transaksi' => 'ditolak',
            ]);

            // Mark as read after rejection
            Auth::user()->unreadNotifications
                ->where('data.peminjaman_id', $id)
                ->markAsRead();
        });

        // Notify Member
        try {
            $peminjaman->pengguna->notify(new LoanStatusNotification($peminjaman, 'ditolak'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify member about loan rejection: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Restriction: Only Owner can delete transactions
        if (auth()->user()->peran !== 'owner') {
            abort(403, 'Hanya Owner yang dapat menghapus data transaksi.');
        }

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
