<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // 0. ROOT / OWNER (1 Orang)
        Pengguna::create([
            'nama' => 'Root',
            'email' => 'root@library.com',
            'password' => Hash::make('password123'),
            'peran' => 'owner',
            'telepon' => '080000000000',
            'alamat' => 'System Root',
            'status' => 'aktif'
        ]);

        // 1. ADMIN (2 Orang)
        // Convention: [name]@admin.library.com
        Pengguna::create([
            'nama' => 'Mire',
            'email' => 'mire@admin.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'telepon' => '081234567890',
            'alamat' => 'Ruang Server Lt. 1',
            'status' => 'aktif'
        ]);

        Pengguna::create([
            'nama' => 'Admin Cadangan',
            'email' => 'backup@admin.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'telepon' => '081234567891',
            'alamat' => 'Ruang IT',
            'status' => 'aktif'
        ]);

        // 2. PETUGAS (3 Orang)
        // Convention: [name]@staff.library.com
        Pengguna::create([
            'nama' => 'Yoel',
            'email' => 'yoel@staff.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'petugas',
            'telepon' => '089876543210',
            'alamat' => 'Meja Resepsionis A',
            'status' => 'aktif'
        ]);

        Pengguna::create([
            'nama' => 'Ani Staff',
            'email' => 'ani@staff.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'petugas',
            'telepon' => '089876543211',
            'alamat' => 'Meja Resepsionis B',
            'status' => 'aktif'
        ]);

        Pengguna::create([
            'nama' => 'Citra Nonaktif',
            'email' => 'citra@staff.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'petugas',
            'telepon' => '089876543212',
            'alamat' => 'Cuti Panjang',
            'status' => 'nonaktif'
        ]);

        // 3. ANGGOTA (15 Orang)
        // Convention: [name]@domain
        // 5 Nonaktif, 10 Aktif
        $faker = Faker::create('id_ID');
        $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];

        // 3a. Anggota Tetap (Jojo)
        Pengguna::create([
            'nama' => 'Jojo',
            'email' => 'jojo@gmail.com',
            'password' => Hash::make('password123'),
            'peran' => 'anggota',
            'telepon' => '08123123123',
            'alamat' => 'Jl. Kenangan No. 1',
            'status' => 'aktif'
        ]);

        // 3b. Anggota Random (14 Orang: 5 Nonaktif, 9 Aktif)
        
        // 5 Anggota Nonaktif
        for ($i = 1; $i <= 5; $i++) {
            $firstName = $faker->firstName;
            Pengguna::create([
                'nama' => "$firstName (Nonaktif)",
                'email' => strtolower($firstName) . $i . '_na@' . $faker->randomElement($domains),
                'password' => Hash::make('password123'),
                'peran' => 'anggota',
                'telepon' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'status' => 'nonaktif'
            ]);
        }

        // 9 Anggota Aktif (Sisa)
        for ($i = 1; $i <= 9; $i++) {
            $firstName = $faker->firstName;
            Pengguna::create([
                'nama' => $firstName . " Anggota",
                'email' => strtolower($firstName) . $i . '_a@' . $faker->randomElement($domains),
                'password' => Hash::make('password123'),
                'peran' => 'anggota',
                'telepon' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'status' => 'aktif'
            ]);
        }
    }
}