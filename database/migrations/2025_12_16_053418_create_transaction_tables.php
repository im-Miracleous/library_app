<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 4. TABEL PEMINJAMAN
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->string('id', 30)->primary(); // P-YYYY-MM-DD000
            $table->string('kode_peminjaman')->unique();
            
            $table->string('pengguna_id', 20);
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->onDelete('cascade');
            
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Trigger Peminjaman
        DB::unprepared("
            CREATE TRIGGER tr_peminjaman_id_insert BEFORE INSERT ON peminjaman FOR EACH ROW
            BEGIN
                DECLARE date_code CHAR(10);
                DECLARE next_no INT;

                SET date_code = DATE_FORMAT(NOW(), '%Y-%m-%d');

                SET next_no = (
                    SELECT IFNULL(MAX(CAST(RIGHT(id, 3) AS UNSIGNED)), 0) + 1 
                    FROM peminjaman 
                    WHERE DATE(created_at) = CURDATE()
                );

                SET NEW.id = CONCAT('P-', date_code, LPAD(next_no, 3, '0'));
                
                IF NEW.kode_peminjaman IS NULL OR NEW.kode_peminjaman = '' THEN
                    SET NEW.kode_peminjaman = NEW.id;
                END IF;
            END
        ");

        // 5. TABEL DETAIL PEMINJAMAN
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // DP-0000
            
            $table->string('peminjaman_id', 30);
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->onDelete('cascade');
            
            $table->string('buku_id', 20);
            $table->foreign('buku_id')->references('id')->on('buku')->onDelete('cascade');
            
            $table->integer('jumlah')->default(1);
            $table->enum('status', ['dipinjam', 'dikembalikan', 'hilang', 'rusak'])->default('dipinjam');
            $table->timestamps();
        });

        // Trigger Detail Peminjaman
        DB::unprepared("
            CREATE TRIGGER tr_detail_peminjaman_id_insert BEFORE INSERT ON detail_peminjaman FOR EACH ROW
            BEGIN
                DECLARE next_no INT;
                SET next_no = (SELECT IFNULL(MAX(CAST(SUBSTRING(id, 4) AS UNSIGNED)), 0) + 1 FROM detail_peminjaman);
                SET NEW.id = CONCAT('DP-', LPAD(next_no, 4, '0'));
            END
        ");

        // 6. TABEL DENDA
        Schema::create('denda', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // F-0000
            
            $table->string('peminjaman_id', 30);
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->onDelete('cascade');
            
            $table->enum('jenis_denda', ['terlambat', 'hilang', 'rusak']);
            $table->decimal('jumlah_denda', 10, 2);
            $table->enum('status_bayar', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->date('tanggal_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Trigger Denda
        DB::unprepared("
            CREATE TRIGGER tr_denda_id_insert BEFORE INSERT ON denda FOR EACH ROW
            BEGIN
                DECLARE next_no INT;
                SET next_no = (SELECT IFNULL(MAX(CAST(SUBSTRING(id, 3) AS UNSIGNED)), 0) + 1 FROM denda);
                SET NEW.id = CONCAT('F-', LPAD(next_no, 4, '0'));
            END
        ");

        // 7. TABEL NOTIFIKASI
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // N-0000
            
            $table->string('pengguna_id', 20);
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->onDelete('cascade');
            
            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', ['info', 'pengingat', 'denda', 'tersedia']);
            $table->boolean('dibaca')->default(false);
            $table->date('tanggal_kadaluwarsa')->nullable();
            $table->timestamps();
        });

        // Trigger Notifikasi
        DB::unprepared("
            CREATE TRIGGER tr_notifikasi_id_insert BEFORE INSERT ON notifikasi FOR EACH ROW
            BEGIN
                DECLARE next_no INT;
                SET next_no = (SELECT IFNULL(MAX(CAST(SUBSTRING(id, 3) AS UNSIGNED)), 0) + 1 FROM notifikasi);
                SET NEW.id = CONCAT('N-', LPAD(next_no, 4, '0'));
            END
        ");
    }

    public function down(): void
    {
        $triggers = ['tr_peminjaman_id_insert', 'tr_detail_peminjaman_id_insert', 'tr_denda_id_insert', 'tr_notifikasi_id_insert'];
        foreach ($triggers as $tr) {
            DB::unprepared("DROP TRIGGER IF EXISTS $tr");
        }
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('denda');
        Schema::dropIfExists('detail_peminjaman');
        Schema::dropIfExists('peminjaman');
    }
};