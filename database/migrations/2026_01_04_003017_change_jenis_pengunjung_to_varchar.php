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
        
        DB::statement("ALTER TABLE pengunjung MODIFY COLUMN jenis_pengunjung VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        
        DB::statement("ALTER TABLE pengunjung MODIFY COLUMN jenis_pengunjung ENUM('umum','anggota','petugas','admin') NOT NULL");
    }
};
