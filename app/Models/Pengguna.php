<?php

namespace App\Models;

// PENTING: Gunakan Authenticatable agar bisa login, bukan Model biasa
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = ['id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'password' => 'hashed',
        'lockout_time' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_pengguna', 'id_pengguna');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_pengguna', 'id_pengguna');
    }
}