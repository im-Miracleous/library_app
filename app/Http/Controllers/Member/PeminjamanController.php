<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Keranjang;
use App\Models\Buku;
use App\Models\DetailPeminjaman;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id_pengguna;
        $tab = $request->get('tab', 'diajukan'); // Default tab: diajukan

        $query = Peminjaman::with(['details.buku', 'details.denda'])
            ->where('id_pengguna', $userId)
            ->orderBy('id_peminjaman', 'desc');

        if ($tab == 'diajukan') {
            $query->whereIn('status_transaksi', ['menunggu_verifikasi', 'ditolak']);
        } elseif ($tab == 'berjalan') {
            $query->where('status_transaksi', 'berjalan');
        } elseif ($tab == 'selesai') {
            $query->whereIn('status_transaksi', ['selesai', 'dibatalkan']);
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        return view('member.peminjaman.index', compact('peminjaman', 'tab'));
    }

    public function confirm()
    {
        $userId = Auth::user()->id_pengguna;
        $items = Keranjang::with('buku')->where('id_pengguna', $userId)->get();

        if ($items->isEmpty()) {
            return redirect()->route('member.keranjang.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Validate Limit Here (Just visual check/redirect if fail before showing confirm?)
        // Better to validate in store(), but we can check here too.
        $activeLoans = Peminjaman::where('id_pengguna', $userId)
            ->whereIn('status_transaksi', ['berjalan', 'menunggu_verifikasi'])
            ->count(); // Count transactions, OR count books? Usually count BOOKS.

        // Count total books in active loans
        $activeBooksCount = DB::table('detail_peminjaman')
            ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
            ->where('peminjaman.id_pengguna', $userId)
            ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
            ->whereIn('detail_peminjaman.status_buku', ['dipinjam', 'diajukan'])
            ->sum('detail_peminjaman.jumlah');

        $cartCount = $items->count();

        $maxPeminjaman = \App\Models\Pengaturan::first()->maksimal_buku_pinjam ?? 3;

        if (($activeBooksCount + $cartCount) > $maxPeminjaman) {
            return redirect()->route('member.keranjang.index')
                ->with('error', "Batas peminjaman maks $maxPeminjaman buku. Sedang dipinjam: $activeBooksCount, Keranjang: $cartCount.");
        }

        return view('member.peminjaman.confirm', compact('items'));
    }

    public function store(Request $request)
    {
        $userId = Auth::user()->id_pengguna;
        $items = Keranjang::where('id_pengguna', $userId)->get();

        if ($items->isEmpty()) {
            return redirect()->route('member.keranjang.index')->with('error', 'Keranjang kosong');
        }

        try {

            // 1. Validate Stock again
            foreach ($items as $item) {
                $buku = Buku::find($item->id_buku);
                if ($buku->stok_tersedia <= 0) {
                    throw new \Exception("Buku '{$buku->judul}' stok habis.");
                }
            }

            // 2. Validate Limit again (Security)
            $activeBooksCount = DB::table('detail_peminjaman')
                ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
                ->where('peminjaman.id_pengguna', $userId)
                ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
                ->whereIn('detail_peminjaman.status_buku', ['dipinjam', 'diajukan'])
                ->sum('detail_peminjaman.jumlah');

            $limitPeminjaman = \App\Models\Pengaturan::first()->maksimal_buku_pinjam ?? 3;
            if (($activeBooksCount + $items->count()) > $limitPeminjaman) {
                throw new \Exception("Melebihi batas total peminjaman (Maks $limitPeminjaman Buku).");
            }

            // 2b. Validate Duplicate Books in Active Loans
            foreach ($items as $item) {
                $isAlreadyBorrowed = DB::table('detail_peminjaman')
                    ->join('peminjaman', 'detail_peminjaman.id_peminjaman', '=', 'peminjaman.id_peminjaman')
                    ->where('peminjaman.id_pengguna', $userId)
                    ->where('detail_peminjaman.id_buku', $item->id_buku)
                    ->whereIn('peminjaman.status_transaksi', ['berjalan', 'menunggu_verifikasi'])
                    ->whereIn('detail_peminjaman.status_buku', ['dipinjam', 'diajukan'])
                    ->exists();

                if ($isAlreadyBorrowed) {
                    $buku = Buku::find($item->id_buku);
                    throw new \Exception("Buku '{$buku->judul}' sudah Anda pinjam dan belum dikembalikan.");
                }
            }

            // 3. Prepare Data for SP
            $jsonBuku = $items->pluck('id_buku')->toJson();
            $tglPinjam = now()->format('Y-m-d');
            $tglJatuhTempo = now()->addDays(7)->format('Y-m-d');
            
            // 4. Call SP
            DB::statement("CALL sp_ajukan_peminjaman(?, ?, ?, ?, ?, @output)", [
                $userId,
                $tglPinjam,
                $tglJatuhTempo,
                'Pengajuan via Web',
                $jsonBuku
            ]);

            // 5. Check Output
            $result = DB::select("SELECT @output as message")[0]->message;

            if ($result !== 'Success') {
                throw new \Exception("Gagal mengajukan peminjaman: " . $result);
            }

            // 6. Clear Cart
            Keranjang::where('id_pengguna', $userId)->delete();

            // 7. Get New ID (for redirect)
            $newId = Peminjaman::where('id_pengguna', $userId)
                ->orderBy('created_at', 'desc')
                ->value('id_peminjaman');

            return redirect()->route('member.peminjaman.index')
                ->with('success', 'Pengajuan peminjaman berhasil!')
                ->with('detail_url', route('member.peminjaman.show', $newId));

        } catch (\Exception $e) {
            return redirect()->route('member.keranjang.index')->with('error', $e->getMessage());
        }
    }
    public function show($id)
    {
        $userId = Auth::user()->id_pengguna;

        $peminjaman = Peminjaman::with(['details.buku', 'details.denda'])
            ->where('id_pengguna', $userId)
            ->where('id_peminjaman', $id)
            ->firstOrFail();

        return view('member.peminjaman.show', compact('peminjaman'));
    }
}