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
                'denda_per_hari' => 500.00, // Denda 500 per hari
                'batas_peminjaman_hari' => 14, // 14 hari
                'maksimal_buku_pinjam' => 5,  // Maksimal 5 buku
            ]
        );
    }
}
