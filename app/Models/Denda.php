<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Denda extends Model
{
    use HasFactory;
    protected $table = 'denda';

    protected $primaryKey = 'id_denda';

    protected $guarded = ['id_denda'];
    protected $casts = ['tanggal_bayar' => 'date'];

    public function detail()
    {
        return $this->belongsTo(DetailPeminjaman::class, 'id_detail_peminjaman', 'id_detail_peminjaman');
    }
}