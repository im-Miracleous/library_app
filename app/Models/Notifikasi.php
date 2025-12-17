<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;
    protected $table = 'notifikasi';
    
    protected $primaryKey = 'id_notifikasi';
    
    protected $guarded = ['id_notifikasi'];
    protected $casts = [
        'tanggal_kadaluwarsa' => 'date', 
        'dibaca' => 'boolean'
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}