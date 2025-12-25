<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaturan;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kita menggunakan updateOrCreate untuk memastikan hanya ada 1 baris pengaturan
        // Jika ID 1 sudah ada, dia akan mengupdate, jika belum, dia membuat baru.
        Pengaturan::updateOrCreate(
            ['id' => 1], // Kondisi pencarian
            [
                'nama_perpustakaan' => 'Perpustakaan Digital',
                'denda_per_hari' => 1000.00, // Contoh: Rp 1.000 per hari
                'batas_peminjaman_hari' => 7, // Contoh: 7 hari
                'maksimal_buku_pinjam' => 3,  // Contoh: Maksimal 3 buku
            ]
        );
    }
}
