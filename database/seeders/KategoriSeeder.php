<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::create(['nama_kategori' => 'Fiksi & Sastra', 'deskripsi' => 'Novel, cerpen, dan karya sastra imajinatif.']);
        Kategori::create(['nama_kategori' => 'Sains & Teknologi', 'deskripsi' => 'Buku pengetahuan alam, komputer, dan teknik.']);
        Kategori::create(['nama_kategori' => 'Sejarah', 'deskripsi' => 'Dokumentasi peristiwa masa lalu.']);
    }
}