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
        Schema::table('pengguna', function (Blueprint $table) {
            // Kita bungkus dalam pengecekan untuk menghindari duplikat error
            if (!Schema::hasColumn('pengguna', 'login_attempts')) {
                $table->integer('login_attempts')->default(0)->after('password');
            }
            if (!Schema::hasColumn('pengguna', 'lockout_time')) {
                $table->timestamp('lockout_time')->nullable()->after('password'); // After password biar aman posisinya
            }
            if (!Schema::hasColumn('pengguna', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('pengguna', 'login_attempts'))
                $columns[] = 'login_attempts';
            if (Schema::hasColumn('pengguna', 'lockout_time'))
                $columns[] = 'lockout_time';
            if (Schema::hasColumn('pengguna', 'is_locked'))
                $columns[] = 'is_locked';

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
