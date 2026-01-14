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

        // Kebutuhan: Minimal 25 Row Data dengan variasi
        // Variasi:
        // - Status Transaksi: Selesai (Tepat, Telat), Berjalan (On Time, Overdue)
        // - Kondisi Buku Selesai: Dikembalikan (Baik), Hilang, Rusak
        // - Range Tanggal: 30 hari terakhir

        $generateTransactions = function($count, $type) use ($makeLoan, $members) {
             for ($i = 0; $i < $count; $i++) {
                $member = $members->random();
                
                // Status Mapping Logic
                // type 1: Selesai - Aman (Tepat Waktu)
                // type 2: Selesai - Telat (Lunas/Belum)
                // type 3: Selesai - Masalah (Hilang/Rusak)
                // type 4: Berjalan - Aman
                // type 5: Berjalan - Overdue

                try {
                    switch ($type) {
                        case 1: 
                            // Selesai Tepat Waktu
                            $makeLoan($member, 'selesai', false, true);
                            break;
                        case 2:
                            // Selesai Telat
                            $paid = rand(0, 1) == 1;
                            $makeLoan($member, 'selesai', true, true, $paid);
                            break;
                        case 3:
                            // Selesai - Dengan Masalah (Hilang atau Rusak)
                            // Kita buat transaksi selesai normal, variasi status buku (hilang/rusak) 
                            // akan diaplikasikan secara acak pada tahap post-processing di bawah.
                            $makeLoan($member, 'selesai', rand(0, 1), true, true);
                            break;
                        case 4:
                            // Berjalan Aman
                            $makeLoan($member, 'berjalan', false, false);
                             break;
                        case 5:
                            // Berjalan Overdue
                            $makeLoan($member, 'berjalan', true, false);
                            break;
                    }
                } catch (\Exception $e) {
                    continue; // Skip constraint errors
                }
             }
        };

        // Total Target ~25-30
        $generateTransactions(8, 1); // 8 Selesai Tepat
        $generateTransactions(5, 2); // 5 Selesai Telat
        $generateTransactions(4, 3); // 4 Selesai (Potential Masalah candidate)
        $generateTransactions(5, 4); // 5 Berjalan Aman
        $generateTransactions(5, 5); // 5 Berjalan Overdue

        // SIMULASI BUKU HILANG / RUSAK
        // Mengubah status beberapa buku yang sudah 'dikembalikan' menjadi 'hilang' atau 'rusak' 
        // untuk memicu data pada Laporan Kerusakan/Kehilangan.
        $problematicDetails = DetailPeminjaman::where('status_buku', 'dikembalikan')->inRandomOrder()->take(5)->get();
        
        foreach($problematicDetails as $index => $detail) {
            $status = ($index % 2 == 0) ? 'hilang' : 'rusak';
            $detail->update(['status_buku' => $status]);

            // Tambahkan denda ganti rugi (Estimasi Rp 50.000 - Rp 150.000)
            $fineAmount = rand(5, 15) * 10000;
            try {
                 Denda::create([
                    'id_detail_peminjaman' => $detail->id_detail_peminjaman,
                    'jenis_denda' => $status == 'hilang' ? 'ganti_rugi_hilang' : 'ganti_rugi_rusak',
                    'jumlah_denda' => $fineAmount,
                    'status_bayar' => 'belum_bayar',
                    'keterangan' => "Ganti rugi buku $status"
                ]);
            } catch (\Exception $e) {
                // Skip jika ada kendala database
            }
        }
        // GUARANTEE DATA FOR TODAY (Agar Dashboard "Hari Ini" tidak kosong)
        try {
            $member = $members->random();
            // Transaksi hari ini: Berjalan, Baru saja dipinjam
            $makeLoanToday = function ($member) use ($books, $generateId) {
                $tglPinjam = Carbon::now(); // HARI INI
                $duration = 7;
                $jatuhTempo = (clone $tglPinjam)->addDays($duration);
                $newId = $generateId($tglPinjam);

                $peminjaman = Peminjaman::create([
                    'id_peminjaman' => $newId,
                    'id_pengguna' => $member->id_pengguna,
                    'tanggal_pinjam' => $tglPinjam,
                    'tanggal_jatuh_tempo' => $jatuhTempo,
                    'status_transaksi' => 'berjalan',
                    'keterangan' => 'Baru pinjam hari ini (System Generated)'
                ]);

                // Add 1 Book
                $book = $books->random();
                DetailPeminjaman::create([
                    'id_peminjaman' => $peminjaman->id_peminjaman,
                    'id_buku' => $book->id_buku,
                    'jumlah' => 1,
                    'status_buku' => 'dipinjam',
                    'tanggal_kembali_aktual' => null
                ]);
            };

            $makeLoanToday($member);
            $makeLoanToday($members->random()); // Buat 2 biji biar yakin
        } catch (\Exception $e) {
            // Ignore if fails
        }
    }
}