<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Sleep;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            $this->command->info("Cleaning storage...");

            Sleep::for(3)->seconds(); 

            try {
                File::cleanDirectory(storage_path('app/public'));
                $this->command->info("Storage cleaned successfully!\n");
            } catch (\Exception $e) {
                $this->command->error("Failed to clean storage: " . $e->getMessage() . "\n");
            }

            Sleep::for(1)->seconds();
        }

        try {
            $this->call([
                PengaturanSeeder::class,
                KategoriSeeder::class,
                PenggunaSeeder::class,
                BukuSeeder::class,
                PeminjamanSeeder::class,
                PengunjungSeeder::class,
            ]);

            $this->command->info("Database seeded successfully!\n");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed database: " . $e->getMessage() . "\n");
        }
    }
}