<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // TABEL NOTIFIKASI
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi'); // PK: id_notifikasi

            $table->string('id_pengguna', 20);
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');

            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', ['info', 'pengingat', 'denda', 'tersedia']);
            $table->boolean('dibaca')->default(false);
            $table->date('tanggal_kadaluwarsa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
