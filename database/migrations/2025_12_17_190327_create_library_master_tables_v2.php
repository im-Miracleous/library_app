<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // TABEL KATEGORI
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('id_kategori'); // PK: id_kategori
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // TABEL PENGGUNA
        Schema::create('pengguna', function (Blueprint $table) {
            $table->string('id_pengguna', 20)->primary(); // PK: id_pengguna
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('login_attempts')->default(0);
            $table->timestamp('lockout_time')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->enum('peran', ['admin', 'petugas', 'anggota']);
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('foto_profil')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Trigger Pengguna (Update nama kolom id -> id_pengguna)
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
                    SELECT IFNULL(MAX(CAST(RIGHT(id_pengguna, 3) AS UNSIGNED)), 0) + 1 
                    FROM pengguna 
                    WHERE SUBSTRING(id_pengguna, 3, 1) = role_code 
                    AND SUBSTRING(id_pengguna, 4, 2) = year_code
                );

                SET NEW.id_pengguna = CONCAT('U-', role_code, year_code, LPAD(next_no, 3, '0'));
            END
        ");

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
            $table->text('deskripsi')->nullable();
            $table->string('gambar_sampul')->nullable();
            $table->enum('status', ['tersedia', 'rusak', 'hilang'])->default('tersedia');
            $table->timestamps();
        });

        // Trigger Buku (Update nama kolom id -> id_buku)
        DB::unprepared("
            CREATE TRIGGER tr_buku_id_insert BEFORE INSERT ON buku FOR EACH ROW
            BEGIN
                DECLARE cat_code CHAR(2);
                DECLARE next_no INT;

                -- Ambil ID kategori (Integer) dan format jadi 2 digit string (misal 5 -> '05')
                SET cat_code = LPAD(NEW.id_kategori, 2, '0');

                SET next_no = (
                    SELECT IFNULL(MAX(CAST(RIGHT(id_buku, 3) AS UNSIGNED)), 0) + 1 
                    FROM buku 
                    WHERE id_kategori = NEW.id_kategori
                );

                SET NEW.id_buku = CONCAT('B-', cat_code, '-', LPAD(next_no, 3, '0'));
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_buku_id_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS tr_pengguna_id_insert');

        Schema::dropIfExists('buku');
        Schema::dropIfExists('pengguna');
        Schema::dropIfExists('kategori');
    }
};