<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder secara berurutan
        // PENTING: Master data (Kategori) harus duluan sebelum Buku
        $this->call([
            KategoriSeeder::class,
            PenggunaSeeder::class,
            BukuSeeder::class,
            PengaturanSeeder::class,
            PeminjamanSeeder::class,
            // PengunjungSeeder::class // Dikosongkan sesuai request
        ]);
    }
}