<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Pengguna;
use App\Models\Buku;
use App\Models\Denda; // Import Model Denda
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
        $dateCode = Carbon::now()->format('Ymd');
        $counter = 1;

        $generateId = function () use ($dateCode, &$counter) {
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

            $newId = $generateId();

            // Create Header Peminjaman (Tanpa kolom denda dan tanggal_kembali)
            $peminjaman = Peminjaman::create([
                'id_peminjaman' => $newId,
                'id_pengguna' => $member->id_pengguna,
                'tanggal_pinjam' => $tglPinjam,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                // Pastikan status_transaksi sesuai enum ('berjalan', 'selesai')
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

            // Jika sudah kembali, kita update tanggal_kembali di header? 
            // Cek skema: tabel peminjaman tidak ada kolom tanggal_kembali (ada di detail? Cek migration lagi)
            // Migration TABEL PEMINJAMAN: id, kode, id_pengguna, tgl_pinjam, tgl_jatuh_tempo, status_transaksi...
            // TIDAK ADA tanggal_kembali di Header Peminjaman di migration 2025_12_17_190402_create_library_transaction_tables_v2.php
            // Ada tanggal_kembali_aktual di DETAIL PEMINJAMAN.

            // Jadi Header Peminjaman tidak nyimpan tanggal kembali.

            // Create Detail (1-3 buku acak)
            $bookCount = rand(1, 3);
            $randomBooks = $books->random($bookCount);

            foreach ($randomBooks as $book) {
                $detail = DetailPeminjaman::create([
                    'id_peminjaman' => $peminjaman->id_peminjaman,
                    'id_buku' => $book->id_buku,
                    'jumlah' => 1,
                    'status_buku' => $statusTrans == 'selesai' ? 'dikembalikan' : 'dipinjam',
                    'tanggal_kembali_aktual' => $isReturned ? $tglKembali : null
                ]);

                // Handle Denda jika Terlambat dan Selesai (atau status denda bisa muncul walau belum selesai? Biasanya denda dihitung saat kembali)
                // Requirement: "2 row/record sudah berstatus Selesai, dikembalikan terlambat sehingga memunculkan Denda"
                // Dan "Satu record dibuat "Belum Dibayar", dan satu recordnya lagi dibuat sudah "Lunas"."

                if ($isLate && $isReturned) {
                    $dendaAmount = $daysLate * 1000; // 1000 per hari per buku

                    // Pastikan Model Denda ada. Jika tidak, pakai DB::table
                    // Asumsi Model Denda ada di App\Models\Denda
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
                        // Fallback jika Model Denda belum dibuat tapi tabel ada
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