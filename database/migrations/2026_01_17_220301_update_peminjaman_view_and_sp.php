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
       
        DB::unprepared("DROP VIEW IF EXISTS v_peminjaman_aktif");
        DB::unprepared("DROP VIEW IF EXISTS v_peminjaman_list");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_peminjaman_list");

        
        
        $viewPath = base_path('database/sql/views/v_peminjaman_list.sql'); 
        DB::unprepared(file_get_contents($viewPath));

       
        $spPath = base_path('database/sql/procedures/transaction_data/sp_get_peminjaman_list.sql');
        
       
        if (file_exists($spPath)) {
            DB::unprepared(file_get_contents($spPath));
        } else {
           
            throw new \Exception("File SP tidak ketemu. Pastikan path ini benar: " . $spPath);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_peminjaman_list");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_peminjaman_list");
    }
};