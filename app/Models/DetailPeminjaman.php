<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPeminjaman extends Model
{
    use HasFactory;
    protected $table = 'detail_peminjaman';

    protected $primaryKey = 'id_detail_peminjaman';

    protected $guarded = ['id_detail_peminjaman'];

    protected $casts = ['tanggal_kembali_aktual' => 'date'];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function denda()
    {
        return $this->hasMany(Denda::class, 'id_detail_peminjaman', 'id_detail_peminjaman');
    }
}