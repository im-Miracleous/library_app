<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // TABEL PENGGUNA
        Schema::create('pengguna', function (Blueprint $table) {
            $table->string('id_pengguna', 20)->primary(); // PK: id_pengguna
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('login_attempts')->default(0);
            $table->timestamp('lockout_time')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->enum('peran', ['owner', 'admin', 'petugas', 'anggota']);
            $table->string('telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('foto_profil')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Trigger Pengguna (Update nama kolom id -> id_pengguna)
        DB::unprepared(file_get_contents(database_path('sql/triggers/tr_pengguna_id_insert.sql')));
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_pengguna_id_insert');
        Schema::dropIfExists('pengguna');
    }
};
