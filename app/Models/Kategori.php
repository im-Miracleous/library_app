<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    // KONFIGURASI ID CUSTOM
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];

    // RELASI
    public function buku()
    {
        return $this->hasMany(Buku::class, 'kategori_id');
    }
}