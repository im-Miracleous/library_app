<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('notifikasi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We do not strictly need to recreate the table in down() for this context
        // as we are correcting the architecture. 
        // But if needed, we could recreate it here.
    }
};
