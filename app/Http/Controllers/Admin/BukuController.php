<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        // Parameter Default
        $search = $request->input('search', '');
        $sortCol = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // Panggil Stored Procedure
        $results = \Illuminate\Support\Facades\DB::select(
            'CALL sp_get_buku(?, ?, ?, ?, ?, @total)',
            [$search, $sortCol, $sortDir, $limit, $offset]
        );

        // Ambil Total Data
        $totalResult = \Illuminate\Support\Facades\DB::select('SELECT @total as total');
        $total = $totalResult[0]->total ?? 0;

        // Hydrate
        $items = Buku::hydrate($results);

        // Manual Paginator
        $buku = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // API Response (AJAX)
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $items,
                'total' => $total,
                'links' => (string) $buku->links()
            ]);
        }

        $kategoriList = Kategori::all();
        $totalBuku = Buku::count();
        $totalStok = Buku::sum('stok_total');

        return view('admin.buku.index', compact('buku', 'kategoriList', 'totalBuku', 'totalStok'));
    }

    public function searchGoogleBooks(Request $request)
    {
        $query = $request->input('q');
        
        if (!$query) {
            return response()->json(['error' => 'Query parameter required'], 400);
        }

        try {
            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => $query,
                'maxResults' => 10,
                'langRestrict' => 'id,en'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $books = [];

                if (isset($data['items'])) {
                    foreach ($data['items'] as $item) {
                        $volumeInfo = $item['volumeInfo'] ?? [];
                        $industryIdentifiers = $volumeInfo['industryIdentifiers'] ?? [];
                        
                        // Extract ISBN
                        $isbn = '';
                        foreach ($industryIdentifiers as $identifier) {
                            if (in_array($identifier['type'] ?? '', ['ISBN_13', 'ISBN_10'])) {
                                $isbn = $identifier['identifier'] ?? '';
                                break;
                            }
                        }

                        $books[] = [
                            'title' => $volumeInfo['title'] ?? 'No Title',
                            'authors' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown',
                            'publisher' => $volumeInfo['publisher'] ?? '',
                            'publishedDate' => $volumeInfo['publishedDate'] ?? '',
                            'description' => $volumeInfo['description'] ?? '',
                            'isbn' => $this->formatIsbn($isbn),
                            'thumbnail' => $volumeInfo['imageLinks']['thumbnail'] ?? '',
                            'categories' => isset($volumeInfo['categories']) ? implode(', ', $volumeInfo['categories']) : ''
                        ];
                    }
                }

                return response()->json(['books' => $books]);
            }

            \Log::error('Google Books API Error: ' . $response->body());
            return response()->json(['error' => 'Failed to fetch data from Google Books'], 500);
        } catch (\Exception $e) {
            \Log::error('Google Books Search Exception: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'isbn' => 'nullable|string|unique:buku,isbn',
            'stok_total' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'kode_dewey' => 'nullable|string',
            'gambar_sampul' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Stok tersedia awal = stok total
        $validated['stok_tersedia'] = $validated['stok_total'];

        // Handle File Upload
        if ($request->hasFile('gambar_sampul')) {
            $path = $request->file('gambar_sampul')->store('covers', 'public');
            $validated['gambar_sampul'] = $path;
        } elseif ($request->filled('gambar_sampul_url')) {
            // Download image from URL (Google Books)
            try {
                $imageUrl = $request->input('gambar_sampul_url');
                $imageContent = Http::get($imageUrl)->body();
                
                // Generate unique filename
                $extension = 'jpg';
                $filename = 'covers/' . uniqid() . '_' . time() . '.' . $extension;
                
                // Store image
                Storage::disk('public')->put($filename, $imageContent);
                $validated['gambar_sampul'] = $filename;
            } catch (\Exception $e) {
                // If download fails, continue without image
                \Log::warning('Failed to download book cover: ' . $e->getMessage());
            }
        }

        Buku::create($validated);

        return redirect()->back()->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show($id)
    {
        $buku = Buku::findOrFail($id);
        return response()->json($buku);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'required|integer',
            'isbn' => 'nullable|string|unique:buku,isbn,' . $id . ',id_buku',
            'stok_total' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,tidak_tersedia',
            'kode_dewey' => 'nullable|string',
            'stok_rusak' => 'nullable|integer|min:0',
            'stok_hilang' => 'nullable|integer|min:0',
            'gambar_sampul' => 'nullable|image|max:2048',
        ]);

        // Hitung Stock Tersedia Baru (Formula: Tersedia = Total - Dipinjam - Rusak - Hilang)
        // Kita butuh 'Dipinjam' saat ini, yang bisa didapat dari snapshot sebelumnya.
        // Dipinjam = OldTotal - OldTersedia - OldRusak - OldHilang

        $currentDipinjam = $buku->stok_total - $buku->stok_tersedia - $buku->stok_rusak - $buku->stok_hilang;


        $currentDipinjam = max(0, $currentDipinjam); // Safety

        // Handle Image Update or Removal
        if ($request->hasFile('gambar_sampul')) {
            // Delete old image if exists
            if ($buku->gambar_sampul && Storage::disk('public')->exists($buku->gambar_sampul)) {
                Storage::disk('public')->delete($buku->gambar_sampul);
            }
            // Store new image
            $path = $request->file('gambar_sampul')->store('covers', 'public');
            $validated['gambar_sampul'] = $path;
        } elseif ($request->input('remove_gambar_sampul') == '1') {
            // Delete image if requested (Draft Delete)
            if ($buku->gambar_sampul && Storage::disk('public')->exists($buku->gambar_sampul)) {
                Storage::disk('public')->delete($buku->gambar_sampul);
            }
            $validated['gambar_sampul'] = null;
        }

        $newTotal = $request->stok_total;
        $newRusak = $request->input('stok_rusak', $buku->stok_rusak); // Default ke old value jika tidak ada input
        $newHilang = $request->input('stok_hilang', $buku->stok_hilang); // Default ke old value

        // Calculate New Available
        $newTersedia = $newTotal - $currentDipinjam - $newRusak - $newHilang;

        if ($newTersedia < 0) {
            return back()->with('error', 'Update gagal. Stok Total tidak cukup untuk menutupi buku yang sedang dipinjam/rusak/hilang.');
        }

        $validated['stok_rusak'] = $newRusak;
        $validated['stok_hilang'] = $newHilang;
        $validated['stok_tersedia'] = $newTersedia;

        // Update data
        $buku->update($validated);

        return redirect()->back()->with('success', 'Data buku & stok berhasil diperbarui.');
    }

    private function formatIsbn($isbn)
    {
        // Hapus karakter non-digit dan dash
        $cleanIsbn = preg_replace('/[^0-9]/', '', $isbn);
        
        // Cek jika 13 digit
        if (strlen($cleanIsbn) === 13) {
            // Format khusus Indonesia (Kelompok 602 dan 979)
            // Pola umum: 978-GGG-PPP-JJJ-C (3-3-3-3-1)
            // Contoh user: 978-602-401-581-7
            if (preg_match('/^978(602|979|623)(\d{3})(\d{3})(\d{1})$/', $cleanIsbn, $matches)) {
                return "978-{$matches[1]}-{$matches[2]}-{$matches[3]}-{$matches[4]}";
            }
        }
        
        return $isbn;
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->gambar_sampul && Storage::disk('public')->exists($buku->gambar_sampul)) {
            Storage::disk('public')->delete($buku->gambar_sampul);
        }

        $buku->delete();
        return redirect()->back()->with('success', 'Buku berhasil dihapus.');
    }
}