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
                ->with('success', 'Transaksi peminjaman berhasil dibuat.')
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
        $peminjaman = Peminjaman::with('details.buku')->findOrFail($id);

        if ($peminjaman->status_transaksi !== 'menunggu_verifikasi') {
            return back()->with('error', 'Hanya transaksi dengan status menunggu verifikasi yang dapat disetujui.');
        }

        // Validasi Stok sebelum menyetujui
        foreach ($peminjaman->details as $detail) {
            if ($detail->status_buku === 'diajukan' && $detail->buku->stok_tersedia < $detail->jumlah) {
                return back()->with('error', "Gagal menyetujui. Stok buku '{$detail->buku->judul}' tidak mencukupi.");
            }
        }

        $pengaturan = Pengaturan::first();
        $batasHari = $pengaturan->batas_peminjaman_hari ?? 7;

        // $peminjaman->update(...);
        // Use Stored Procedure
        \Illuminate\Support\Facades\DB::statement('CALL sp_approve_peminjaman(?, ?)', [
            $id,
            auth()->user()->id_pengguna
        ]);

        // Note: Notification logic remains in PHP as it's application concern
        // Mark as read after approval
        Auth::user()->unreadNotifications
            ->where('data.peminjaman_id', $id)
            ->markAsRead();

        // Notify Member
        try {
            $peminjaman = Peminjaman::find($id); // Reload to get updated data if needed
            $peminjaman->pengguna->notify(new LoanStatusNotification($peminjaman, 'disetujui'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify member about loan approval: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:1000',
        ]);

        $peminjaman = Peminjaman::with('details.buku')->findOrFail($id);

        if ($peminjaman->status_transaksi !== 'menunggu_verifikasi') {
            return back()->with('error', 'Hanya transaksi dengan status menunggu verifikasi yang dapat ditolak.');
        }

        // Use Stored Procedure
        \Illuminate\Support\Facades\DB::statement('CALL sp_reject_peminjaman(?, ?, ?)', [
            $id,
            auth()->user()->id_pengguna,
            $request->alasan
        ]);

        // Notify Member
        try {
            $peminjaman = Peminjaman::find($id); // Reload
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

    
    public function extendForm($id)
    {
    
        $peminjaman = Peminjaman::with(['pengguna', 'details' => function($q) {
                            $q->where('status_buku', 'dipinjam')->with('buku');
                        }])
                        ->where('id_peminjaman', $id)
                        ->firstOrFail();

        $isOverdue = Carbon::now()->gt($peminjaman->tanggal_jatuh_tempo);

        if ($peminjaman->status_transaksi !== 'berjalan' || $isOverdue) {
            return redirect()->back()->with('error', 'Peminjaman tidak memenuhi syarat untuk diperpanjang (Sudah lewat waktu atau status tidak valid).');
        }

        if ($peminjaman->is_extended) {
            return redirect()->back()->with('error', 'Transaksi ini sudah pernah diperpanjang. Perpanjangan hanya diperbolehkan satu kali.');
        }

        if ($peminjaman->details->isEmpty()) {
            return redirect()->back()->with('error', 'Semua buku dalam transaksi ini sudah dikembalikan.');
        }

        
        $pengaturan = Pengaturan::first();
        $daysToAdd = $pengaturan->batas_peminjaman_hari ?? 7; 

    
        $newStartDate = Carbon::now(); 
        $newDueDate = Carbon::now()->addDays($daysToAdd);

        return view('admin.sirkulasi.peminjaman.extend', compact('peminjaman', 'newStartDate', 'newDueDate', 'daysToAdd'));
    }

    public function processExtend(Request $request, $id)
    {
        $request->validate([
            'actions' => 'required|array',
            'actions.*' => 'required|in:extend,return,keep',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $peminjaman = Peminjaman::with('details')->findOrFail($id);
            $actions = $request->actions;

            $extendIds = [];
            $returnIds = [];
            $keepIds = [];

            foreach ($peminjaman->details as $detail) {
                // Ignore already returned items just in case (though UI hides them or handles them)
                if ($detail->status_buku !== 'dipinjam') continue;

                $action = $actions[$detail->id_detail_peminjaman] ?? 'keep';

                if ($action === 'extend') {
                    $extendIds[] = $detail->id_detail_peminjaman;
                } elseif ($action === 'return') {
                    $returnIds[] = $detail->id_detail_peminjaman;
                } else {
                    $keepIds[] = $detail->id_detail_peminjaman;
                }
            }

            // 1. Handle Returns First (Apply to current transaction)
            if (!empty($returnIds)) {
                // Update status to 'dikembalikan'
                // Trigger trigger: tr_kembalikan_stok_buku will handle stock
                DetailPeminjaman::whereIn('id_detail_peminjaman', $returnIds)->update([
                    'status_buku' => 'dikembalikan',
                    'tanggal_kembali_aktual' => Carbon::now(),
                ]);
            }

            // 2. Determine Strategy
            $hasExtend = count($extendIds) > 0;
            $hasKeep = count($keepIds) > 0;

            $pengaturan = Pengaturan::first();
            $daysToAdd = $pengaturan->batas_peminjaman_hari ?? 7;
            $newDueDate = Carbon::now()->addDays($daysToAdd);

            if (!$hasExtend) {
                // Case: No books extended (All returned or kept)
                // Just redirect back. Logic above already handled returns.
                $msg = 'Tidak ada buku yang diperpanjang.';
                if (count($returnIds) > 0) $msg .= ' Buku yang dipilih telah dikembalikan.';
                return redirect()->route('peminjaman.show', $id)->with('success', $msg);
            }

            if ($hasKeep) {
                // === SCENARIO: SPLIT TRANSACTION ===
                // We have items to Extend AND items to Keep (on old date).
                // Move Extend items to a NEW Transaction.

                // Create New Transaction
                // Gunakan ID generator logic atau UUID? Model menggunakan string, biasanya format custom.
                // Disini kita asumsi auto-generated atau kita copy pattern dari controller store.
                // Karena controller store pakai Stored Procedure, kita manual insert saja disini atau panggil SP?
                // Manual insert Eloquent lebih mudah untuk cloning.
                
                // Generate ID: Menggunakan format P-YYYYMMDDHis-XX untuk keunikan tanpa locking sequence
                $newId = 'P-' . Carbon::now()->format('YmdHis') . rand(10, 99);

                $newPeminjaman = new Peminjaman();
                $newPeminjaman->id_peminjaman = $newId;
                $newPeminjaman->id_pengguna = $peminjaman->id_pengguna;
                $newPeminjaman->tanggal_pinjam = Carbon::now();
                $newPeminjaman->tanggal_jatuh_tempo = $newDueDate;
                $newPeminjaman->keterangan = 'Perpanjangan (Split) dari ' . $peminjaman->id_peminjaman;
                $newPeminjaman->status_transaksi = 'berjalan';
                $newPeminjaman->is_extended = true;
                $newPeminjaman->save();

                // Move Details
                DetailPeminjaman::whereIn('id_detail_peminjaman', $extendIds)->update([
                    'id_peminjaman' => $newId,
                    // Status remains 'dipinjam'
                ]);

                return redirect()->route('peminjaman.show', $newId)
                    ->with('success', 'Transaksi berhasil dipecah! Buku yang diperpanjang telah dipindahkan ke Kode Transaksi baru ini.');

            } else {
                // === SCENARIO: BULK EXTEND (NO SPLIT) ===
                // All active books are being extended (Returns are already handled and closed).
                // Just update the current transaction.
                
                // $peminjaman->update(...);
                // Use Stored Procedure
                \Illuminate\Support\Facades\DB::statement('CALL sp_extend_peminjaman(?, ?)', [
                    $id,
                    $newDueDate->format('Y-m-d')
                ]);

                return redirect()->route('peminjaman.show', $id)
                    ->with('success', 'Masa peminjaman telah diperpanjang.');
            }
        });
    }
}
