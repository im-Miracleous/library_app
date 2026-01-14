<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID kategori
        $catFiksi = Kategori::where('nama_kategori', 'Fiksi & Sastra')->first()->id_kategori;
        $catSains = Kategori::where('nama_kategori', 'Sains & Teknologi')->first()->id_kategori;
        $catSejarah = Kategori::where('nama_kategori', 'Sejarah & Budaya')->first()->id_kategori;
        $catBiografi = Kategori::where('nama_kategori', 'Biografi & Memoar')->first()->id_kategori;
        $catBisnis = Kategori::where('nama_kategori', 'Bisnis & Ekonomi')->first()->id_kategori;

        $books = [
            // Kategori Fiksi & Sastra (3 Buku)
            [
                'id_kategori' => $catFiksi,
                'judul' => 'Laskar Pelangi',
                'kode_dewey' => '899.221',
                'isbn' => '978-979-3062-79-2',
                'penulis' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'tahun_terbit' => 2005,
                'stok_total' => 1,
                'stok_tersedia' => 1,
                'deskripsi' => 'Kisah perjuangan anak-anak di Belitung untuk mendapatkan pendidikan yang layak.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9789793062792-L.jpg'
            ],
            [
                'id_kategori' => $catFiksi,
                'judul' => 'Bumi Manusia',
                'kode_dewey' => '899.221',
                'isbn' => '978-979-97312-3-4',
                'penulis' => 'Pramoedya Ananta Toer',
                'penerbit' => 'Lentera Dipantara',
                'tahun_terbit' => 1980,
                'stok_total' => 8,
                'stok_tersedia' => 8,
                'deskripsi' => 'Roman sejarah yang mengambil latar belakang masa kebangkitan nasional.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9789799731234-L.jpg'
            ],
            [
                'id_kategori' => $catFiksi,
                'judul' => 'Pulang',
                'kode_dewey' => '899.221',
                'isbn' => '978-602-03-2478-4',
                'penulis' => 'Leila S. Chudori',
                'penerbit' => 'Kepustakaan Populer Gramedia',
                'tahun_terbit' => 2012,
                'stok_total' => 5,
                'stok_tersedia' => 5,
                'deskripsi' => 'Kisah para eksil politik Indonesia yang berkelana di luar negeri.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9786020324784-L.jpg'
            ],

            // Kategori Sains & Teknologi
            [
                'id_kategori' => $catSains,
                'judul' => 'A Brief History of Time',
                'kode_dewey' => '523.1',
                'isbn' => '978-0-553-38016-3',
                'penulis' => 'Stephen Hawking',
                'penerbit' => 'Bantam Books',
                'tahun_terbit' => 1988,
                'stok_total' => 6,
                'stok_tersedia' => 6,
                'deskripsi' => 'Penjelasan populer tentang kosmologi, lubang hitam, dan teori Big Bang.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9780553380163-L.jpg'
            ],
            [
                'id_kategori' => $catSains,
                'judul' => 'Sapiens: A Brief History of Humankind',
                'kode_dewey' => '909',
                'isbn' => '978-0-06-231609-7',
                'penulis' => 'Yuval Noah Harari',
                'penerbit' => 'Harper',
                'tahun_terbit' => 2011,
                'stok_total' => 12,
                'stok_tersedia' => 12,
                'deskripsi' => 'Sejarah umat manusia dari zaman batu hingga abad ke-21.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9780062316097-L.jpg'
            ],
            [
                'id_kategori' => $catSains,
                'judul' => 'Clean Code',
                'kode_dewey' => '005.1',
                'isbn' => '978-0-13-235088-4',
                'penulis' => 'Robert C. Martin',
                'penerbit' => 'Prentice Hall',
                'tahun_terbit' => 2008,
                'stok_total' => 7,
                'stok_tersedia' => 7,
                'deskripsi' => 'Panduan untuk menulis kode perangkat lunak yang bersih dan mudah dipelihara.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg'
            ],

            // Kategori Sejarah & Budaya
            [
                'id_kategori' => $catSejarah,
                'judul' => 'Guns, Germs, and Steel',
                'kode_dewey' => '303.4',
                'isbn' => '978-0-393-31755-8',
                'penulis' => 'Jared Diamond',
                'penerbit' => 'W. W. Norton',
                'tahun_terbit' => 1997,
                'stok_total' => 4,
                'stok_tersedia' => 4,
                'deskripsi' => 'Analisis faktor geografis dan lingkungan yang membentuk sejarah manusia.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9780393317558-L.jpg'
            ],
            [
                'id_kategori' => $catSejarah,
                'judul' => 'Sejarah Tuhan',
                'kode_dewey' => '200.9',
                'isbn' => '978-602-441-025-4',
                'penulis' => 'Karen Armstrong',
                'penerbit' => 'Mizan',
                'tahun_terbit' => 1993,
                'stok_total' => 5,
                'stok_tersedia' => 5,
                'deskripsi' => 'Pencarian 4000 tahun Yudaisme, Kristen, dan Islam.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9786024410254-L.jpg'
            ],

            // Kategori Biografi & Memoar
            [
                'id_kategori' => $catBiografi,
                'judul' => 'Steve Jobs',
                'kode_dewey' => '920',
                'isbn' => '978-1-4516-4853-9',
                'penulis' => 'Walter Isaacson',
                'penerbit' => 'Simon & Schuster',
                'tahun_terbit' => 2011,
                'stok_total' => 9,
                'stok_tersedia' => 9,
                'deskripsi' => 'Biografi pendiri Apple yang ditulis berdasarkan wawancara eksklusif.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9781451648539-L.jpg'
            ],
            [
                'id_kategori' => $catBiografi,
                'judul' => 'Becoming',
                'kode_dewey' => '920',
                'isbn' => '978-1-5247-6313-8',
                'penulis' => 'Michelle Obama',
                'penerbit' => 'Crown',
                'tahun_terbit' => 2018,
                'stok_total' => 15,
                'stok_tersedia' => 15,
                'deskripsi' => 'Memoar mantan Ibu Negara Amerika Serikat.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9781524763138-L.jpg'
            ],
            [
                'id_kategori' => $catBiografi,
                'judul' => 'Habibie & Ainun',
                'kode_dewey' => '920',
                'isbn' => '978-979-1227-94-0',
                'penulis' => 'B.J. Habibie',
                'penerbit' => 'THC Mandiri',
                'tahun_terbit' => 2010,
                'stok_total' => 8,
                'stok_tersedia' => 8,
                'deskripsi' => 'Kisah cinta abadi antara B.J. Habibie dan Hasri Ainun Besari.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9789791227940-L.jpg'
            ],

            // Kategori Bisnis & Ekonomi
            [
                'id_kategori' => $catBisnis,
                'judul' => 'Rich Dad Poor Dad',
                'kode_dewey' => '332.024',
                'isbn' => '978-1-61268-019-4',
                'penulis' => 'Robert T. Kiyosaki',
                'penerbit' => 'Plata Publishing',
                'tahun_terbit' => 1997,
                'stok_total' => 20,
                'stok_tersedia' => 20,
                'deskripsi' => 'Apa yang diajarkan orang kaya pada anak-anak mereka tentang uang.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9781612680194-L.jpg'
            ],
            [
                'id_kategori' => $catBisnis,
                'judul' => 'Think and Grow Rich',
                'kode_dewey' => '158.1',
                'isbn' => '978-1-58542-433-7',
                'penulis' => 'Napoleon Hill',
                'penerbit' => 'TarcherPerigee',
                'tahun_terbit' => 1937,
                'stok_total' => 10,
                'stok_tersedia' => 10,
                'deskripsi' => 'Buku motivasi klasik tentang kesuksesan dan kekayaan.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9781585424337-L.jpg'
            ],
            [
                'id_kategori' => $catBisnis,
                'judul' => 'Psychology of Money',
                'kode_dewey' => '332.024',
                'isbn' => '978-0-85719-768-9',
                'penulis' => 'Morgan Housel',
                'penerbit' => 'Harriman House',
                'tahun_terbit' => 2020,
                'stok_total' => 12,
                'stok_tersedia' => 12,
                'deskripsi' => 'Pelajaran abadi mengenai kekayaan, keserakahan, dan kebahagiaan.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9780857197689-L.jpg'
            ],
            [
                'id_kategori' => $catBisnis,
                'judul' => 'Zero to One',
                'kode_dewey' => '658.4',
                'isbn' => '978-0-8041-3929-8',
                'penulis' => 'Peter Thiel',
                'penerbit' => 'Crown Business',
                'tahun_terbit' => 2014,
                'stok_total' => 7,
                'stok_tersedia' => 7,
                'deskripsi' => 'Catatan tentang start up dan cara membangun masa depan.',
                'status' => 'tersedia',
                'gambar_sampul' => 'https://covers.openlibrary.org/b/isbn/9780804139298-L.jpg'
            ],
        ];

        foreach ($books as $book) {
            Buku::create($book);
        }
    }
}