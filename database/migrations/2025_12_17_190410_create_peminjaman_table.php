<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // TABEL PEMINJAMAN
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->string('id_peminjaman', 30)->primary(); // PK: id_peminjaman
            $table->string('id_pengguna', 20);
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status_transaksi', ['menunggu_verifikasi', 'ditolak', 'berjalan', 'selesai', 'dibatalkan'])->default('menunggu_verifikasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Trigger Peminjaman (Update nama kolom id -> id_peminjaman)
        DB::unprepared(file_get_contents(database_path('sql/triggers/tr_peminjaman_id_insert.sql')));
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_peminjaman_id_insert');
        Schema::dropIfExists('peminjaman');
    }
};
