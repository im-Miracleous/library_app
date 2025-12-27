<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('pengaturan')) {
                $pengaturan = \App\Models\Pengaturan::firstOrCreate([], [
                    'nama_perpustakaan' => 'Perpustakaan Digital',
                    'denda_per_hari' => 1000,
                    'batas_peminjaman_hari' => 7,
                    'maksimal_buku_pinjam' => 3,
                ]);
                \Illuminate\Support\Facades\View::share('pengaturan', $pengaturan);
            }
        } catch (\Exception $e) {
            // Prevent crash during migration
        }
    }
}
