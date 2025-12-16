<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Pengguna;
use App\Models\Buku;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil User Anggota
        $anggota = Pengguna::where('email', 'siti@student.com')->first();
        
        // Ambil Buku
        $buku = Buku::where('judul', 'Laskar Pelangi')->first();

        if ($anggota && $buku) {
            // 1. Buat Peminjaman Header
            // Kita tidak menampung ke variabel $peminjaman langsung, karena ID-nya pasti null di PHP
            Peminjaman::create([
                'pengguna_id' => $anggota->id,
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(7), 
                'status' => 'dipinjam',
                'keterangan' => 'Peminjaman rutin'
            ]);

            // === AMBIL ULANG DARI DATABASE ===
            // Kita cari data peminjaman terbaru milik user ini untuk mendapatkan ID hasil Trigger
            $peminjaman = Peminjaman::where('pengguna_id', $anggota->id)
                                ->orderBy('created_at', 'desc') // Ambil yang paling baru dibuat
                                ->first();

            // 2. Buat Detail Peminjaman (Pivot)
            if ($peminjaman) {
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id, // Sekarang ID ini sudah ada isinya (misal: P-2025-...)
                    'buku_id' => $buku->id,
                    'jumlah' => 1,
                    'status' => 'dipinjam'
                ]);

                // Update stok buku (Simulasi sederhana)
                $buku->decrement('stok_tersedia');
            }
        }
    }
}