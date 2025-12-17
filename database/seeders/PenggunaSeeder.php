<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat ADMIN
        // Ekspektasi ID: U-A25001 (Jika tahun 2025)
        Pengguna::create([
            'nama' => 'Super Admin',
            'email' => 'admin@library.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'telepon' => '081234567890',
            'alamat' => 'Ruang Server Lt. 1'
        ]);

        // 2. Buat PETUGAS (Staff)
        // Ekspektasi ID: U-S25001
        Pengguna::create([
            'nama' => 'Budi Pustakawan',
            'email' => 'budi@library.com',
            'password' => Hash::make('password123'),
            'peran' => 'petugas',
            'telepon' => '089876543210',
            'alamat' => 'Meja Resepsionis'
        ]);

        // 3. Buat ANGGOTA (Member) - User 1
        // Ekspektasi ID: U-M25001
        Pengguna::create([
            'nama' => 'Siti Mahasiswa',
            'email' => 'siti@student.com',
            'password' => Hash::make('password123'),
            'peran' => 'anggota',
            'telepon' => '085555555555',
            'alamat' => 'Jl. Kampus No. 5'
        ]);

        // 4. Buat ANGGOTA (Member) - User 2
        // Ekspektasi ID: U-M25002 (Nomor urut bertambah)
        Pengguna::create([
            'nama' => 'Ahmad Anggota',
            'email' => 'ahmad@gmail.com',
            'password' => Hash::make('password123'),
            'peran' => 'anggota',
            'telepon' => '087777777777',
            'alamat' => 'Asrama Putra'
        ]);
    }
}