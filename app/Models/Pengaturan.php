<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'nama_perpustakaan',
        'logo_path',
        'denda_per_hari',
        'batas_peminjaman_hari',
        'maksimal_buku_pinjam',
    ];

    protected $casts = [
        'denda_per_hari' => 'decimal:2',
        'batas_peminjaman_hari' => 'integer',
        'maksimal_buku_pinjam' => 'integer',
    ];
}
