<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $procedures = [
            'master_data/sp_buku_crud.sql',
            'master_data/sp_kategori_crud.sql',
            'master_data/sp_pengguna_crud.sql',
            'transaction_data/sp_transaksi_sirkulasi.sql',
            'transaction_data/sp_denda.sql',
            'transaction_data/sp_ajukan_peminjaman.sql',
        ];

        foreach ($procedures as $file) {
            $path = database_path("sql/procedures/{$file}");
            if (file_exists($path)) {
                DB::unprepared(file_get_contents($path));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $drops = [
            'DROP PROCEDURE IF EXISTS sp_create_buku',
            'DROP PROCEDURE IF EXISTS sp_update_buku',
            'DROP PROCEDURE IF EXISTS sp_delete_buku',
            'DROP PROCEDURE IF EXISTS sp_create_kategori',
            'DROP PROCEDURE IF EXISTS sp_update_kategori',
            'DROP PROCEDURE IF EXISTS sp_delete_kategori',
            'DROP PROCEDURE IF EXISTS sp_create_pengguna',
            'DROP PROCEDURE IF EXISTS sp_update_pengguna',
            'DROP PROCEDURE IF EXISTS sp_delete_pengguna',
            'DROP PROCEDURE IF EXISTS sp_approve_peminjaman',
            'DROP PROCEDURE IF EXISTS sp_reject_peminjaman',
            'DROP PROCEDURE IF EXISTS sp_complete_peminjaman',
            'DROP PROCEDURE IF EXISTS sp_extend_peminjaman',
            'DROP PROCEDURE IF EXISTS sp_bayar_denda',
        ];

        foreach ($drops as $sql) {
            DB::unprepared($sql);
        }
    }
};
