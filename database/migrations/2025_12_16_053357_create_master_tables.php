<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABEL KATEGORI
        Schema::create('kategori', function (Blueprint $table) {
            $table->string('id', 10)->primary(); // Format: C-00
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Trigger Kategori
        DB::unprepared("
            CREATE TRIGGER tr_kategori_id_insert BEFORE INSERT ON kategori FOR EACH ROW
            BEGIN
                DECLARE next_no INT;
                SET next_no = (SELECT IFNULL(MAX(CAST(SUBSTRING(id, 3) AS UNSIGNED)), 0) + 1 FROM kategori);
                SET NEW.id = CONCAT('C-', LPAD(next_no, 2, '0'));
            END
        ");

        // 2. TABEL PENGGUNA
        Schema::create('pengguna', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // Format: U-XYY000
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('peran', ['admin', 'petugas', 'anggota']);
            $table->string('nim')->nullable();
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->rememberToken();
            $table->timestamps();
        });

        // Trigger Pengguna
        DB::unprepared("
            CREATE TRIGGER tr_pengguna_id_insert BEFORE INSERT ON pengguna FOR EACH ROW
            BEGIN
                DECLARE role_code CHAR(1);
                DECLARE year_code CHAR(2);
                DECLARE next_no INT;
                
                IF NEW.peran = 'admin' THEN SET role_code = 'A';
                ELSEIF NEW.peran = 'petugas' THEN SET role_code = 'S';
                ELSE SET role_code = 'M';
                END IF;

                SET year_code = DATE_FORMAT(NOW(), '%y');

                SET next_no = (
                    SELECT IFNULL(MAX(CAST(RIGHT(id, 3) AS UNSIGNED)), 0) + 1 
                    FROM pengguna 
                    WHERE SUBSTRING(id, 3, 1) = role_code 
                    AND SUBSTRING(id, 4, 2) = year_code
                );

                SET NEW.id = CONCAT('U-', role_code, year_code, LPAD(next_no, 3, '0'));
            END
        ");

        // 3. TABEL BUKU
        Schema::create('buku', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // Format: B-XX-000
            
            $table->string('kategori_id', 10);
            $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('cascade');

            $table->string('kode_dewey');
            $table->string('isbn')->unique();
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->integer('tahun_terbit');
            $table->integer('stok_total');
            $table->integer('stok_tersedia');
            $table->text('deskripsi')->nullable();
            $table->string('gambar_sampul')->nullable();
            $table->enum('status', ['tersedia', 'rusak', 'hilang'])->default('tersedia');
            $table->timestamps();
        });

        // Trigger Buku
        DB::unprepared("
            CREATE TRIGGER tr_buku_id_insert BEFORE INSERT ON buku FOR EACH ROW
            BEGIN
                DECLARE cat_code CHAR(2);
                DECLARE next_no INT;

                SET cat_code = SUBSTRING(NEW.kategori_id, 3, 2);

                SET next_no = (
                    SELECT IFNULL(MAX(CAST(RIGHT(id, 3) AS UNSIGNED)), 0) + 1 
                    FROM buku 
                    WHERE kategori_id = NEW.kategori_id
                );

                SET NEW.id = CONCAT('B-', cat_code, '-', LPAD(next_no, 3, '0'));
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_buku_id_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_pengguna_id_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_kategori_id_insert');
        
        Schema::dropIfExists('buku');
        Schema::dropIfExists('pengguna');
        Schema::dropIfExists('kategori');
    }
};