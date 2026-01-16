<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class PengunjungController extends Controller
{
    public function index(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $limit = max(1, (int) $request->input('limit', 10));
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

        if ($request->ajax() && !$request->has('filter')) {
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

        // --- CHART DATA LOGIC ---
        // --- CHART DATA LOGIC ---
        $filter = $request->input('filter', 'today'); // default to today
        $endDate = Carbon::now();
        if ($filter === 'today') {
            $startDate = Carbon::today();
        } elseif ($filter === 'month') {
            $startDate = Carbon::now()->subDays(30);
        } else { // week
            $startDate = Carbon::now()->subDays(7);
        }

        $pengunjungQuery = Pengunjung::select('jenis_pengunjung', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('jenis_pengunjung')
            ->get();

        // Initialize Categories
        $groupedCounts = [
            'Personal & Akademik' => 0,
            'Organisasi & Komunitas' => 0,
            'Instansi & Perusahaan' => 0,
            'Kunjungan Khusus' => 0,
        ];

        // Map Results to Categories
        foreach ($pengunjungQuery as $row) {
            $category = 'Personal & Akademik'; // Default
            $jenis = $row->jenis_pengunjung;

            if (in_array($jenis, ['Organisasi Internal Kampus', 'Organisasi / Komunitas Luar', 'Yayasan / Nonprofit / NGO'])) {
                $category = 'Organisasi & Komunitas';
            } elseif (in_array($jenis, ['Pemerintahan / Dinas', 'Korporasi / Perusahaan Swasta'])) {
                $category = 'Instansi & Perusahaan';
            } elseif (in_array($jenis, ['Tamu Undangan / VIP', 'Media / Jurnalis', 'Lainnya'])) {
                $category = 'Kunjungan Khusus';
            }

            $groupedCounts[$category] += $row->count;
        }

        $chartData = [
            'labels' => array_keys($groupedCounts),
            'data' => array_values($groupedCounts)
        ];

        if ($request->ajax() && $request->has('filter')) {
            return response()->json($chartData);
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

        return view('admin.sirkulasi.pengunjung.index', compact('pengunjung', 'chartData', 'filter'));
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