<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class PengunjungController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search');
        $sort = $request->input('sort') ?: 'created_at';
        $direction = $request->input('direction') ?: 'desc';

        // Call SP 
        $data = DB::select('CALL sp_get_pengunjung(?, ?, ?, ?, ?, @total)', [
            $search,
            $sort,
            $direction,
            $limit,
            $offset
        ]);
        $total = DB::select('SELECT @total as total')[0]->total;

        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'total' => $total,
                'links' => (string) (new LengthAwarePaginator(
                    [],
                    $total,
                    $limit,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                ))->links()
            ]);
        }

        // Hydrate raw results to Models
        $items = Pengunjung::hydrate($data);

        // Manual Pagination for View
        $pengunjung = new LengthAwarePaginator(
            $items,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.sirkulasi.pengunjung.index', compact('pengunjung'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengunjung' => 'required|string|max:255',
            // PERUBAHAN DISINI: Hapus 'in:...' ganti jadi 'string|max:255'
            'jenis_pengunjung' => 'required|string|max:255', 
            'keperluan' => 'nullable|string',
            'id_pengguna' => 'nullable|exists:pengguna,id_pengguna',
        ]);

        Pengunjung::create($request->all());

        return redirect()->route('pengunjung.index')->with('success', 'Data pengunjung berhasil dicatat.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pengunjung' => 'required|string|max:255',
            // PERUBAHAN DISINI JUGA: Hapus 'in:...' ganti jadi 'string|max:255'
            'jenis_pengunjung' => 'required|string|max:255',
            'keperluan' => 'nullable|string',
        ]);

        $pengunjung = Pengunjung::findOrFail($id);
        $pengunjung->update($request->all());

        return redirect()->route('pengunjung.index')->with('success', 'Data pengunjung berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengunjung = Pengunjung::findOrFail($id);
        $pengunjung->delete();

        return redirect()->route('pengunjung.index')->with('success', 'Data pengunjung berhasil dihapus.');
    }
}