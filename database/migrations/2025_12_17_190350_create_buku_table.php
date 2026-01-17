<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // TABEL BUKU
        Schema::create('buku', function (Blueprint $table) {
            $table->string('id_buku', 20)->primary(); // PK: id_buku
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onDelete('cascade');
            $table->string('kode_dewey');
            $table->string('isbn')->unique();
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->integer('tahun_terbit');
            $table->integer('stok_total');
            $table->integer('stok_tersedia');
            $table->integer('stok_rusak')->default(0);
            $table->integer('stok_hilang')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('gambar_sampul')->nullable();
            $table->enum('status', ['tersedia', 'tidak_tersedia', 'habis'])->default('tersedia');
            $table->timestamps();
        });

        // Trigger Buku
        DB::unprepared(file_get_contents(database_path('sql/triggers/tr_buku_id_insert.sql')));
        DB::unprepared(file_get_contents(database_path('sql/triggers/tr_update_status_stok.sql')));
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_buku_id_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_update_status_stok');
        Schema::dropIfExists('buku');
    }
};
