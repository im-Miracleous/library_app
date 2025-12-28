<?php

namespace App\Http\Controllers;

use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DendaController extends Controller
{
    /**
     * Update the specified fine status (Payment).
     */
    public function update(Request $request, $id)
    {
        $denda = Denda::findOrFail($id);

        if ($denda->status_bayar == 'lunas') {
            return back()->with('error', 'Denda ini sudah lunas.');
        }

        $denda->update([
            'status_bayar' => 'lunas',
            'tanggal_bayar' => Carbon::now(),
            'keterangan' => $denda->keterangan . ' (Dibayar pada ' . Carbon::now()->format('Y-m-d H:i') . ')'
        ]);

        return back()->with('success', 'Pembayaran denda berhasil dicatat.');
    }
}
