<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    // KONFIGURASI ID CUSTOM
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];

    // Casting tanggal agar otomatis jadi object Carbon (mudah diformat)
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];

    // RELASI
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id');
    }

    public function denda()
    {
        return $this->hasOne(Denda::class, 'peminjaman_id');
    }
}