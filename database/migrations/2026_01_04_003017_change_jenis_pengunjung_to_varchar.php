<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kita pakai Raw SQL biar aman dan tidak butuh install doctrine/dbal
        // Ini akan mengubah kolom jadi VARCHAR(255) yang bisa menampung teks apa saja
        DB::statement("ALTER TABLE pengunjung MODIFY COLUMN jenis_pengunjung VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM (sesuaikan dengan nilai lamamu kalau perlu rollback)
        DB::statement("ALTER TABLE pengunjung MODIFY COLUMN jenis_pengunjung ENUM('umum','anggota','petugas','admin') NOT NULL");
    }
};
