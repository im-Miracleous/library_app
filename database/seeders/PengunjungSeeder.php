<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengunjungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Variasi Data Pengunjung
        $data = [
            [
                'nama_pengunjung' => 'Budi Santoso',
                'jenis_pengunjung' => 'umum',
                'keperluan' => 'Mencari referensi skripsi sejarah',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'nama_pengunjung' => 'Siti Aminah',
                'jenis_pengunjung' => 'anggota',
                'id_pengguna' => 'A001', // Asumsi ada anggota A001 (Jojo atau seed data lain)
                'keperluan' => 'Membaca buku novel',
                'created_at' => Carbon::now()->subHours(4),
            ],
            [
                'nama_pengunjung' => 'Rudi Hartono',
                'jenis_pengunjung' => 'umum',
                'keperluan' => 'Menumpang wifi untuk tugas',
                'created_at' => Carbon::now()->subDays(1)->hour(9),
            ],
            [
                'nama_pengunjung' => 'Dewi Sartika',
                'jenis_pengunjung' => 'anggota',
                'id_pengguna' => null, // Anggota tapi lupa bawa kartu/login manual
                'keperluan' => 'Meminjam buku pelajaran',
                'created_at' => Carbon::now()->subDays(1)->hour(14),
            ],
            [
                'nama_pengunjung' => 'Andi Wijaya',
                'jenis_pengunjung' => 'mahasiswa', // Kategori tambahan untuk test fleksibilitas
                'keperluan' => 'Diskusi kelompok',
                'created_at' => Carbon::now()->subDays(2)->hour(10),
            ],
            [
                'nama_pengunjung' => 'Lina Marlina',
                'jenis_pengunjung' => 'umum',
                'keperluan' => 'Mengantar anak membaca',
                'created_at' => Carbon::now()->subDays(2)->hour(16),
            ],
            [
                'nama_pengunjung' => 'Yoel Pustakawan',
                'jenis_pengunjung' => 'petugas',
                'id_pengguna' => 'P001', // Asumsi P001 ada
                'keperluan' => 'Cek kondisi rak buku',
                'created_at' => Carbon::now()->subDays(3)->hour(8),
            ],
            [
                'nama_pengunjung' => 'Tamu Dinas',
                'jenis_pengunjung' => 'dinas',
                'keperluan' => 'Studi banding perpustakaan',
                'created_at' => Carbon::now()->subDays(4)->hour(11),
            ],
            [
                'nama_pengunjung' => 'Rina Kurnia',
                'jenis_pengunjung' => 'anggota',
                'id_pengguna' => 'A002', 
                'keperluan' => 'Mengembalikan buku',
                'created_at' => Carbon::now()->subDays(5)->hour(13),
            ],
            [
                'nama_pengunjung' => 'Joko Anwar',
                'jenis_pengunjung' => 'umum',
                'keperluan' => 'Sekedar melihat-lihat',
                'created_at' => Carbon::now()->subDays(6)->hour(15),
            ],
        ];

        // Ensure IDs exist if foreign keys are enforced, otherwise set to null
        foreach ($data as &$item) {
            if (isset($item['id_pengguna'])) {
                $exists = DB::table('pengguna')->where('id_pengguna', $item['id_pengguna'])->exists();
                if (!$exists) {
                    $item['id_pengguna'] = null;
                }
            } else {
                $item['id_pengguna'] = null;
            }
            $item['updated_at'] = $item['created_at'];
        }

        DB::table('pengunjung')->insert($data);
    }
}
