<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations - Create all stored procedures for data retrieval
     */
    public function up(): void
    {
        // 1. Stored Procedure: sp_get_anggota (with status filter)
        DB::unprepared(file_get_contents(database_path('sql/procedures/master_data/sp_get_anggota.sql')));

        // 2. Stored Procedure: sp_get_kepegawaian (with peran filter)
        DB::unprepared(file_get_contents(database_path('sql/procedures/master_data/sp_get_kepegawaian.sql')));

        // 3. Stored Procedure: sp_get_buku
        DB::unprepared(file_get_contents(database_path('sql/procedures/master_data/sp_get_buku.sql')));

        // 4. Stored Procedure: sp_get_kategori
        DB::unprepared(file_get_contents(database_path('sql/procedures/master_data/sp_get_kategori.sql')));

        // 5. Stored Procedure: sp_get_laporan_denda
        DB::unprepared(file_get_contents(database_path('sql/procedures/transaction_data/sp_get_laporan_denda.sql')));

        // 6. Stored Procedure: sp_get_laporan_transaksi
        DB::unprepared(file_get_contents(database_path('sql/procedures/transaction_data/sp_get_laporan_transaksi.sql')));

        // 7. Stored Procedure: sp_get_peminjaman_list
        DB::unprepared(file_get_contents(database_path('sql/procedures/transaction_data/sp_get_peminjaman_list.sql')));

        // 8. Stored Procedure: sp_get_pengembalian_list
        DB::unprepared(file_get_contents(database_path('sql/procedures/transaction_data/sp_get_pengembalian_list.sql')));

        // 9. Stored Procedure: sp_get_pengunjung
        DB::unprepared(file_get_contents(database_path('sql/procedures/transaction_data/sp_get_pengunjung.sql')));
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_anggota');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_kepegawaian');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_buku');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_kategori');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_laporan_denda');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_laporan_transaksi');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_peminjaman_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_pengembalian_list');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_pengunjung');
    }
};
