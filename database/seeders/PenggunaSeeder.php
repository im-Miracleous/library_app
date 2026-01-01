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
            'nama' => 'Super Admin',
            'email' => 'super@admin.library.com',
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
        // Convention: [name]@petugas.library.com
        Pengguna::create([
            'nama' => 'Budi Pustakawan',
            'email' => 'budi@petugas.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'petugas',
            'telepon' => '089876543210',
            'alamat' => 'Meja Resepsionis A',
            'status' => 'aktif'
        ]);

        Pengguna::create([
            'nama' => 'Ani Staff',
            'email' => 'ani@petugas.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'petugas',
            'telepon' => '089876543211',
            'alamat' => 'Meja Resepsionis B',
            'status' => 'aktif'
        ]);

        Pengguna::create([
            'nama' => 'Citra Nonaktif',
            'email' => 'citra@petugas.library.com',
            'password' => Hash::make('password123'),
            'peran' => 'petugas',
            'telepon' => '089876543212',
            'alamat' => 'Cuti Panjang',
            'status' => 'nonaktif' // Sesuai request ada status nonaktif
        ]);

        // 3. ANGGOTA (15 Orang)
        // Convention: [name]@[domain]
        // 5 Nonaktif, 10 Aktif
        $faker = Faker::create('id_ID');
        $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];

        // 5 Anggota Nonaktif
        for ($i = 1; $i <= 5; $i++) {
            $firstName = $faker->firstName;
            Pengguna::create([
                'nama' => "$firstName (Nonaktif)",
                'email' => strtolower($firstName) . $i . '@' . $faker->randomElement($domains),
                'password' => Hash::make('password123'),
                'peran' => 'anggota',
                'telepon' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'status' => 'nonaktif'
            ]);
        }

        // 10 Anggota Aktif
        for ($i = 1; $i <= 10; $i++) {
            $firstName = $faker->firstName; // e.g Eko
            Pengguna::create([
                'nama' => $firstName . " Anggota",
                'email' => strtolower($firstName) . $i . '@' . $faker->randomElement($domains),
                'password' => Hash::make('password123'),
                'peran' => 'anggota',
                'telepon' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'status' => 'aktif'
            ]);
        }
    }
}