<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            
            if (!Schema::hasColumn('peminjaman', 'id_petugas')) {
                $table->string('id_petugas', 50)->nullable()->after('id_pengguna');
            }
            
           
            if (!Schema::hasColumn('peminjaman', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('keterangan');
            }
        });

       
        $procedure = "
            DROP PROCEDURE IF EXISTS sp_reject_peminjaman;
            
            CREATE PROCEDURE sp_reject_peminjaman(
                IN p_id_peminjaman VARCHAR(20),
                IN p_id_petugas VARCHAR(50),
                IN p_alasan TEXT
            )
            BEGIN
                -- 1. Kembalikan Stok Buku (Karena status 'menunggu_verifikasi' biasanya stok sudah di-hold)
                -- Kita update stok buku yang ada di detail peminjaman ini
                UPDATE buku b
                JOIN detail_peminjaman dp ON b.id_buku = dp.id_buku
                SET b.stok_tersedia = b.stok_tersedia + 1
                WHERE dp.id_peminjaman = p_id_peminjaman;

                -- 2. Update Status Transaksi, Petugas, dan Alasan
                UPDATE peminjaman
                SET status_transaksi = 'ditolak',
                    id_petugas = p_id_petugas,
                    alasan_penolakan = p_alasan,
                    updated_at = NOW()
                WHERE id_peminjaman = p_id_peminjaman;
            END;
        ";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'id_petugas')) {
                $table->dropColumn('id_petugas');
            }
            if (Schema::hasColumn('peminjaman', 'alasan_penolakan')) {
                $table->dropColumn('alasan_penolakan');
            }
        });
    }
};