<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Pengguna;
use App\Models\Buku;
use App\Models\Denda; // Import Model Denda
use App\Models\Pengaturan;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil User Anggota yang AKTIF saja untuk peminjaman
        $members = Pengguna::where('peran', 'anggota')
            ->where('status', 'aktif')
            ->get();

        if ($members->isEmpty()) {
            return; // Safety check
        }

        // Ambil semua buku
        $books = Buku::all();

        // Helper untuk generate ID Peminjaman
        // P-[YYYY]-[MM]-[DD][NNN] (Simplified for seeder)
        $counter = 1;
        $generateId = function ($date) use (&$counter) {
            $dateCode = $date->format('Ymd');
            $id = 'P-' . $dateCode . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
            return $id;
        };

        // Helper untuk membuat transaksi peminjaman + detail + denda
        $makeLoan = function ($member, $statusTrans, $isLate, $isReturned, $isPaid = false) use ($books, $generateId) {
            $tglPinjam = Carbon::now()->subDays(rand(10, 30)); // Pinjam 10-30 hari lalu
            $duration = 7; // Lama pinjam standar 7 hari
            $jatuhTempo = (clone $tglPinjam)->addDays($duration);

            $tglKembali = null;
            $daysLate = 0;

            if ($isReturned) {
                if ($isLate) {
                    // Telat 1-5 hari
                    $daysLate = rand(1, 5);
                    $tglKembali = (clone $jatuhTempo)->addDays($daysLate);
                } else {
                    // Kembali tepat waktu atau sebelum
                    $daysEarly = rand(0, 2);
                    $tglKembali = (clone $jatuhTempo)->subDays($daysEarly);
                }
            } else {
                // Masih Berjalan
                if ($isLate) {
                    // Sudah lewat jatuh tempo
                    $jatuhTempo = Carbon::now()->subDays(rand(1, 5));
                    $tglPinjam = (clone $jatuhTempo)->subDays($duration);
                } else {
                    // Masih dalam masa pinjam
                    $jatuhTempo = Carbon::now()->addDays(rand(1, 5));
                    $tglPinjam = (clone $jatuhTempo)->subDays($duration);
                }
            }

            // Pengecekan Limit untuk Peminjaman Berjalan
            if ($statusTrans == 'berjalan') {
                $currentActiveBooks = DetailPeminjaman::whereHas('peminjaman', function ($q) use ($member) {
                    $q->where('id_pengguna', $member->id_pengguna)
                        ->where('status_transaksi', 'berjalan');
                })->where('status_buku', 'dipinjam')->count();

                $maxLoan = Pengaturan::first()->maksimal_buku_pinjam;
                $remainingQuota = $maxLoan - $currentActiveBooks;

                if ($remainingQuota <= 0) {
                    return;
                }
                $bookCount = rand(1, min(3, $remainingQuota));
            } else {
                $bookCount = rand(1, 3);
            }

            // Book Selection
            $borrowedInActiveStatus = DetailPeminjaman::whereHas('peminjaman', function ($q) use ($member) {
                $q->where('id_pengguna', $member->id_pengguna)
                    ->whereIn('status_transaksi', ['berjalan', 'menunggu_verifikasi']);
            })->where('status_buku', 'dipinjam')->pluck('id_buku')->toArray();

            $availableForThisMember = $books->whereNotIn('id_buku', $borrowedInActiveStatus);

            if ($availableForThisMember->isEmpty()) {
                return;
            }

            $randomBooks = $availableForThisMember->random(min($bookCount, $availableForThisMember->count()));

            if ($randomBooks->isEmpty()) {
                return;
            }

            // PREPARE ID
            $newId = $generateId($tglPinjam);

            // CREATE HEADER
            $peminjaman = Peminjaman::create([
                'id_peminjaman' => $newId,
                'id_pengguna' => $member->id_pengguna,
                'tanggal_pinjam' => $tglPinjam,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                'status_transaksi' => $statusTrans,
                'keterangan' => Arr::random([
                    'Keperluan tugas akhir',
                    'Referensi penelitian',
                    'Bacaan santai',
                    'Materi kuliah sejarah',
                    'Persiapan lomba essay',
                    'Tugas makalah biologi',
                    'Pinjam untuk adik',
                    'Bahan diskusi kelompok',
                    'Keperluan skripsi',
                    'Hiburan akhir pekan'
                ])
            ]);

            // CREATE DETAILS
            foreach ($randomBooks as $book) {
                $detail = DetailPeminjaman::create([
                    'id_peminjaman' => $peminjaman->id_peminjaman,
                    'id_buku' => $book->id_buku,
                    'jumlah' => 1,
                    'status_buku' => $statusTrans == 'selesai' ? 'dikembalikan' : 'dipinjam',
                    'tanggal_kembali_aktual' => $isReturned ? $tglKembali : null
                ]);

                if ($isLate && $isReturned) {
                    $dendaAmount = $daysLate * 1000;
                    try {
                        Denda::create([
                            'id_detail_peminjaman' => $detail->id_detail_peminjaman,
                            'jenis_denda' => 'terlambat',
                            'jumlah_denda' => $dendaAmount,
                            'status_bayar' => $isPaid ? 'lunas' : 'belum_bayar',
                            'tanggal_bayar' => $isPaid ? Carbon::now() : null,
                            'keterangan' => "Telat $daysLate hari"
                        ]);
                    } catch (\Exception $e) {
                        DB::table('denda')->insert([
                            'id_detail_peminjaman' => $detail->id_detail_peminjaman,
                            'jenis_denda' => 'terlambat',
                            'jumlah_denda' => $dendaAmount,
                            'status_bayar' => $isPaid ? 'lunas' : 'belum_bayar',
                            'tanggal_bayar' => $isPaid ? Carbon::now() : null,
                            'keterangan' => "Telat $daysLate hari",
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        };

        // Kebutuhan: 10 Row Data
        // 1. 2 record Selesai, Tepat Waktu
        $makeLoan($members->random(), 'selesai', false, true);
        $makeLoan($members->random(), 'selesai', false, true);

        // 2. 2 record Selesai, Terlambat (1 Lunas, 1 Belum)
        $makeLoan($members->random(), 'selesai', true, true, true); // Lunas
        $makeLoan($members->random(), 'selesai', true, true, false); // Belum bayar

        // 3. 2 record Berjalan, Terlambat (Melebihi jatuh tempo)
        // Di sini denda belum dibuat karena belum dikembalikan (denda dihitung saat kembali biasanya), 
        // tapi statusnya sudah terlambat secara logika aplikasi.
        $makeLoan($members->random(), 'berjalan', true, false);
        $makeLoan($members->random(), 'berjalan', true, false);

        // 4. 4 record Berjalan, On Time (Belum jatuh tempo)
        $makeLoan($members->random(), 'berjalan', false, false);
        $makeLoan($members->random(), 'berjalan', false, false);
        $makeLoan($members->random(), 'berjalan', false, false);
        $makeLoan($members->random(), 'berjalan', false, false);
    }
}