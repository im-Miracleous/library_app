<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Kita buat 3 Kategori
        // Ekspektasi ID: C-01, C-02, C-03
        
        $data = [
            [
                'nama_kategori' => 'Fiksi & Sastra',
                'deskripsi' => 'Novel, cerpen, dan karya sastra imajinatif.'
            ],
            [
                'nama_kategori' => 'Sains & Teknologi',
                'deskripsi' => 'Buku pengetahuan alam, komputer, dan teknik.'
            ],
            [
                'nama_kategori' => 'Sejarah',
                'deskripsi' => 'Dokumentasi peristiwa masa lalu.'
            ]
        ];

        foreach ($data as $item) {
            Kategori::create($item);
        }
    }
}