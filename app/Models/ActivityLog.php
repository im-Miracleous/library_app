<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_pengguna', 
        'action', 
        'description', 
        'ip_address', 
        'user_agent'
    ];

    // --- RELASI
    public function pengguna()
    {
       
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    // --- FUNGSI (Helper)
    public static function record($action, $desc)
    {
        self::create([
            'id_pengguna' => Auth::check() ? Auth::user()->id_pengguna : null,
            'action'      => $action,
            'description' => $desc,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::header('User-Agent'),
        ]);
    }
}