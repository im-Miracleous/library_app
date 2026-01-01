<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // TABEL DENDA
        Schema::create('denda', function (Blueprint $table) {
            $table->id('id_denda'); // PK: id_denda

            $table->foreignId('id_detail_peminjaman')->constrained('detail_peminjaman', 'id_detail_peminjaman')->onDelete('cascade');

            $table->enum('jenis_denda', ['terlambat', 'hilang', 'rusak']);
            $table->decimal('jumlah_denda', 10, 2);
            $table->enum('status_bayar', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->date('tanggal_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denda');
    }
};
