<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. VIEW: v_peminjaman_aktif
        DB::unprepared(file_get_contents(database_path('sql/views/v_peminjaman_aktif.sql')));

        // 2. FUNCTION: fn_hitung_denda
        DB::unprepared(file_get_contents(database_path('sql/functions/fn_hitung_denda.sql')));

        // 3. TRIGGER: tr_kurangi_stok_buku
        DB::unprepared(file_get_contents(database_path('sql/triggers/tr_kurangi_stok_buku.sql')));

        // 4. TRIGGER: tr_kembalikan_stok_buku
        DB::unprepared(file_get_contents(database_path('sql/triggers/tr_kembalikan_stok_buku.sql')));

        // 5. STORED PROCEDURE: sp_buat_peminjaman (Transaction & Rollback)
        DB::unprepared(file_get_contents(database_path('sql/procedures/sp_buat_peminjaman.sql')));

        // 6. STORED PROCEDURE with CURSOR: sp_cek_keterlambatan
        DB::unprepared(file_get_contents(database_path('sql/procedures/sp_cek_keterlambatan.sql')));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_peminjaman_aktif");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_hitung_denda");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_kurangi_stok_buku");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_kembalikan_stok_buku");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_buat_peminjaman");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_cek_keterlambatan");
    }
};
