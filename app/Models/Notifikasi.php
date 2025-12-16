<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    // KONFIGURASI ID CUSTOM
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_kadaluwarsa' => 'date',
        'dibaca' => 'boolean',
    ];

    // RELASI
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}