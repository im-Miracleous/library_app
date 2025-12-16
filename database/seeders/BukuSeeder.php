<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Kategori Pertama (Fiksi/C-01)
        $kategoriFiksi = Kategori::where('nama_kategori', 'Fiksi & Sastra')->first();
        
        // Ambil Kategori Kedua (Sains/C-02)
        $kategoriSains = Kategori::where('nama_kategori', 'Sains & Teknologi')->first();

        // 1. Buku Fiksi
        // Ekspektasi ID: B-01-001 (01 dari ID kategori C-01)
        Buku::create([
            'kategori_id' => $kategoriFiksi->id, 
            'judul' => 'Laskar Pelangi',
            'kode_dewey' => '899.221',
            'isbn' => '978-123-456-7890',
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'tahun_terbit' => 2005,
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'deskripsi' => 'Kisah perjuangan anak-anak Belitong.',
            'status' => 'tersedia'
        ]);

        // 2. Buku Sains
        // Ekspektasi ID: B-02-001 (02 dari ID kategori C-02)
        Buku::create([
            'kategori_id' => $kategoriSains->id,
            'judul' => 'Pengantar Algoritma',
            'kode_dewey' => '005.1',
            'isbn' => '978-987-654-3210',
            'penulis' => 'Thomas H. Cormen',
            'penerbit' => 'MIT Press',
            'tahun_terbit' => 2009,
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'deskripsi' => 'Kitab suci algoritma pemrograman.',
            'status' => 'tersedia'
        ]);
    }
}