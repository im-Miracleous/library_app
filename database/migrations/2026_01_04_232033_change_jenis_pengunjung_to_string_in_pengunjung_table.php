<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
    public function up()
    {
        Schema::table('pengunjung', function (Blueprint $table) {
  
            $table->string('jenis_pengunjung', 100)->change();
        });
    }

    public function down()
    {
        Schema::table('pengunjung', function (Blueprint $table) {
        });
    }
};
