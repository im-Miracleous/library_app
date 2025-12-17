<?php
namespace Database\Seeders;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriFiksi = Kategori::where('nama_kategori', 'Fiksi & Sastra')->first();
        $kategoriSains = Kategori::where('nama_kategori', 'Sains & Teknologi')->first();

        Buku::create([
            'id_kategori' => $kategoriFiksi->id_kategori, 
            'judul' => 'Laskar Pelangi',
            'kode_dewey' => '899.221',
            'isbn' => '978-123-456-7890',
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'tahun_terbit' => 2005,
            'stok_total' => 10,
            'stok_tersedia' => 10,
            'deskripsi' => 'Kisah perjuangan anak-anak di Belitung untuk mendapatkan pendidikan.',
            'status' => 'tersedia'
        ]);
        
        Buku::create([
            'id_kategori' => $kategoriSains->id_kategori,
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