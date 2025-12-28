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
            $table->string('kode_peminjaman')->unique();

            $table->string('id_pengguna', 20);
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');

            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status_transaksi', ['berjalan', 'selesai'])->default('berjalan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Trigger Peminjaman (Update nama kolom id -> id_peminjaman)
        // Updated: Logic diperbarui untuk mendukung insert manual (misal via SP)
        // Jika ID sudah ada, skip generate.
        DB::unprepared("
            CREATE TRIGGER tr_peminjaman_id_insert BEFORE INSERT ON peminjaman FOR EACH ROW
            BEGIN
                DECLARE date_code CHAR(10);
                DECLARE next_no INT;

                -- Logic: Only generate ID if it is passed as NULL or empty string
                IF NEW.id_peminjaman IS NULL OR NEW.id_peminjaman = '' THEN
                    SET date_code = DATE_FORMAT(NOW(), '%Y-%m-%d');

                    SET next_no = (
                        SELECT IFNULL(MAX(CAST(RIGHT(id_peminjaman, 3) AS UNSIGNED)), 0) + 1 
                        FROM peminjaman 
                        WHERE DATE(created_at) = CURDATE()
                    );

                    SET NEW.id_peminjaman = CONCAT('P-', date_code, LPAD(next_no, 3, '0'));
                END IF;
                
                -- Ensure kode_peminjaman is set
                IF NEW.kode_peminjaman IS NULL OR NEW.kode_peminjaman = '' THEN
                    SET NEW.kode_peminjaman = NEW.id_peminjaman;
                END IF;
            END
        ");

        // TABEL DETAIL PEMINJAMAN
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id('id_detail_peminjaman'); // PK: id_detail_peminjaman

            $table->string('id_peminjaman', 30);
            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade');

            $table->string('id_buku', 20);
            $table->foreign('id_buku')->references('id_buku')->on('buku')->onDelete('cascade');

            $table->integer('jumlah')->default(1);
            $table->enum('status_buku', ['dipinjam', 'dikembalikan', 'terlambat', 'hilang'])->default('dipinjam');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->timestamps();
        });

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

        // 7. TABEL NOTIFIKASI (Integer AI)
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
        DB::unprepared('DROP TRIGGER IF EXISTS tr_peminjaman_id_insert');

        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('denda');
        Schema::dropIfExists('detail_peminjaman');
        Schema::dropIfExists('peminjaman');
    }
};