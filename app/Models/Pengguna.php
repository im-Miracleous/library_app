<?php

namespace App\Models;

// PENTING: Gunakan Authenticatable agar bisa login
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. Sesuaikan dengan nama tabel di PHPMyAdmin
    protected $table = 'pengguna';

    // 2. Sesuaikan Primary Key (karena bukan 'id' biasa)
    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    // 3. AGAR DATA BISA DISIMPAN (PENTING!)
    // Kita pakai $fillable supaya aman dan jelas kolom apa saja yang boleh diisi
    protected $fillable = [
        'id_pengguna',
        'nama',
        'email',
        'password',
        'peran',
        'telepon',
        'alamat',
        'otp_code',       // <--- KOLOM BARU
        'otp_expires_at', // <--- KOLOM BARU
        'login_attempts',
        'lockout_time',
        'is_locked'
    ];

    protected $hidden = ['password', 'remember_token'];

    // 4. SETTING TIPE DATA (CASTS)
    protected $casts = [
        'password' => 'hashed',
        'lockout_time' => 'datetime',
        'is_locked' => 'boolean',
        'otp_expires_at' => 'datetime',
    ];


    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_pengguna', 'id_pengguna');
    }

    // Legacy notifikasi relationship removed in favor of standard Laravel Notifications
    // public function notifikasi()
    // {
    //     return $this->hasMany(Notifikasi::class, 'id_pengguna', 'id_pengguna');
    // }
}