<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perpustakaan');
            $table->string('logo_path')->nullable();
            $table->decimal('denda_per_hari', 10, 2)->default(0);
            $table->integer('batas_peminjaman_hari')->default(7);
            $table->integer('maksimal_buku_pinjam')->default(3);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
