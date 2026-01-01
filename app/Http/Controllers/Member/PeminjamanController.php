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

        DB::beginTransaction();
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

            // 3. Create Header (Trigger will generate ID)
            $peminjaman = new Peminjaman();
            $peminjaman->id_pengguna = $userId;
            $peminjaman->tanggal_pinjam = now();
            $peminjaman->tanggal_jatuh_tempo = now()->addDays(7);
            $peminjaman->status_transaksi = 'menunggu_verifikasi';
            $peminjaman->save();

            // 4. Retrieve Generated ID
            // Safe strategy: Get the latest record for this user created in the last few seconds.
            // Or simple latest() if traffic is low per user.
            $newId = Peminjaman::where('id_pengguna', $userId)
                ->orderBy('created_at', 'desc')
                ->value('id_peminjaman');

            if (!$newId) {
                throw new \Exception("Gagal mengambil ID Peminjaman baru.");
            }

            // 5. Create Details
            foreach ($items as $item) {
                DetailPeminjaman::create([
                    'id_peminjaman' => $newId,
                    'id_buku' => $item->id_buku,
                    'jumlah' => 1,
                    'status_buku' => 'diajukan'
                ]);

                // Decrement Stock: 
                // Removed because database trigger 'tr_kurangi_stok_buku' 
                // already handles this on DetailPeminjaman insert.
                // Buku::where('id_buku', $item->id_buku)->decrement('stok_tersedia');
            }

            // 6. Clear Cart
            Keranjang::where('id_pengguna', $userId)->delete();

            // 7. Consolidation:
            // Discrete notifications are removed to avoid "notification overload".
            // The system now uses a status-based "Verification Task" card in the 
            // dashboard and notification dropdown that automatically aggregates counts.

            DB::commit();

            return redirect()->route('member.peminjaman.index')
                ->with('success', 'Pengajuan peminjaman berhasil!')
                ->with('detail_url', route('member.peminjaman.show', $newId));

        } catch (\Exception $e) {
            DB::rollBack();
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