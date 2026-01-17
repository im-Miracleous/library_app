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
            'database/sql/procedures/transaction_data/sp_get_laporan_transaksi_cetak.sql',
            'database/sql/procedures/transaction_data/sp_get_laporan_denda_cetak.sql',
            'database/sql/procedures/transaction_data/sp_get_laporan_kunjungan_cetak.sql',
            'database/sql/procedures/master_data/sp_get_laporan_inventaris_cetak.sql',
            'database/sql/procedures/transaction_data/sp_get_buku_terpopuler_cetak.sql',
            'database/sql/procedures/transaction_data/sp_get_anggota_teraktif_cetak.sql',
        ];

        foreach ($procedures as $path) {
            DB::unprepared(file_get_contents(base_path($path)));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $procedureNames = [
            'sp_get_laporan_transaksi_cetak',
            'sp_get_laporan_denda_cetak',
            'sp_get_laporan_kunjungan_cetak',
            'sp_get_laporan_inventaris_cetak',
            'sp_get_buku_terpopuler_cetak',
            'sp_get_anggota_teraktif_cetak',
        ];

        foreach ($procedureNames as $name) {
            DB::unprepared("DROP PROCEDURE IF EXISTS {$name}");
        }
    }
};
