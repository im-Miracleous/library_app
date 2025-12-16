<?php

namespace App\Models;

// PENTING: Gunakan Authenticatable agar bisa login, bukan Model biasa
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna'; // Nama tabel kustom

    // KONFIGURASI ID CUSTOM (WAJIB)
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id']; // ID dijaga, jangan diisi manual

    // Field yang disembunyikan saat return JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casting tipe data
    protected $casts = [
        'password' => 'hashed',
    ];

    // RELASI (Sesuai ERD)
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'pengguna_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'pengguna_id');
    }
}