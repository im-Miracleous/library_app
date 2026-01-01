<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // TABEL DETAIL PEMINJAMAN
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id('id_detail_peminjaman'); // PK: id_detail_peminjaman

            $table->string('id_peminjaman', 30);
            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade');

            $table->string('id_buku', 20);
            $table->foreign('id_buku')->references('id_buku')->on('buku')->onDelete('cascade');

            $table->integer('jumlah')->default(1);
            $table->enum('status_buku', ['dipinjam', 'dikembalikan', 'terlambat', 'hilang', 'rusak', 'diajukan', 'ditolak'])->default('diajukan');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
