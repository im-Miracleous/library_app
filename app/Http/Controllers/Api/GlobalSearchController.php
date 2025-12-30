<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Pengguna;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([
                'buku' => [],
                'anggota' => []
            ]);
        }

        // Search Books (Judul OR ISBN)
        $buku = Buku::where('judul', 'like', "%{$query}%")
            ->orWhere('isbn', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id_buku', 'judul', 'penulis', 'isbn']);

        // Search Members (Nama OR Email) - only role 'anggota'
        $anggota = Pengguna::where('peran', 'anggota')
            ->where(function ($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id_pengguna', 'nama', 'email']);

        return response()->json([
            'buku' => $buku,
            'anggota' => $anggota
        ]);
    }
}
