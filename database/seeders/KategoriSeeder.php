<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Fiksi & Sastra',
                'deskripsi' => 'Karya imajinatif termasuk novel, cerpen, dan puisi.',
            ],
            [
                'nama_kategori' => 'Sains & Teknologi',
                'deskripsi' => 'Buku yang membahas ilmu pengetahuan alam dan perkembangan teknologi.',
            ],
            [
                'nama_kategori' => 'Sejarah & Budaya',
                'deskripsi' => 'Dokumentasi peristiwa masa lalu dan kebudayaan masyarakat.',
            ],
            [
                'nama_kategori' => 'Biografi & Memoar',
                'deskripsi' => 'Kisah hidup tokoh-tokoh inspiratif dan berpengaruh.',
            ],
            [
                'nama_kategori' => 'Bisnis & Ekonomi',
                'deskripsi' => 'Panduan manajemen, investasi, dan teori ekonomi.',
            ],
        ];

        foreach ($categories as $category) {
            Kategori::create($category);
        }
    }
}