<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DendaController extends Controller
{
    /**
     * Update the specified fine status (Payment).
     */
    public function edit($id)
    {
        $denda = Denda::findOrFail($id);
        return response()->json($denda);
    }

    public function update(Request $request, $id)
    {
        $denda = Denda::findOrFail($id);

        // helper to clean existing payment logs from keterangan
        $cleanKeterangan = function ($text) {
            return trim(preg_replace('/\s*\(Dibayar pada.*?\)/i', '', $text));
        };

        // Distinguish between Quick Payment (POST) and Full Edit (PUT)
        if ($request->isMethod('POST')) {
            // Quick Payment Logic (Accessible by all staff)
            if ($denda->status_bayar == 'lunas') {
                return back()->with('error', 'Denda ini sudah lunas.');
            }

            $currentKeterangan = $cleanKeterangan($denda->keterangan);
            $newKeterangan = $currentKeterangan . ' (Dibayar pada ' . Carbon::now()->format('Y-m-d H:i') . ')';

            // Use Stored Procedure
            \Illuminate\Support\Facades\DB::statement('CALL sp_bayar_denda(?, ?, ?)', [
                $id,
                'manual', // Default method
                $newKeterangan
            ]);

            return back()->with('success', 'Pembayaran denda berhasil dicatat.');
        }

        // Full Edit Logic (PUT) - Restricted to Owner
        if (auth()->user()->peran !== 'owner') {
            abort(403, 'Akses ditolak. Hanya Owner yang dapat mengedit data denda secara manual.');
        }

        $validated = $request->validate([
            'keterangan' => 'nullable|string',
            'status_bayar' => 'nullable|in:lunas,belum_bayar'
        ]);

        if ($request->status_bayar === 'lunas' && $denda->status_bayar !== 'lunas') {
            $validated['tanggal_bayar'] = Carbon::now();
            // Add payment log if not already present (and clean potential manual duplicates)
            $cleaned = $cleanKeterangan($validated['keterangan'] ?? '');
            $validated['keterangan'] = $cleaned . ' (Dibayar pada ' . Carbon::now()->format('Y-m-d H:i') . ')';
        } elseif ($request->status_bayar === 'belum_bayar' && $denda->status_bayar === 'lunas') {
            $validated['tanggal_bayar'] = null;
            // Remove payment log when reverting
            $validated['keterangan'] = $cleanKeterangan($validated['keterangan'] ?? '');
        }

        $denda->update($validated);
        return back()->with('success', 'Data denda berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->peran !== 'owner') {
            abort(403);
        }

        $denda = Denda::findOrFail($id);
        $denda->delete();

        return back()->with('success', 'Data denda berhasil dihapus.');
    }
}
