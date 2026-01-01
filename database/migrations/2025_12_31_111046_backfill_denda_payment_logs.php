<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dendas = \App\Models\Denda::where('status_bayar', 'lunas')->get();

        foreach ($dendas as $denda) {
            if (strpos($denda->keterangan, '(Dibayar pada') === false) {
                // Use tanggal_bayar if available, otherwise use updated_at
                $date = $denda->tanggal_bayar ?
                    \Carbon\Carbon::parse($denda->tanggal_bayar) :
                    $denda->updated_at;

                $log = ' (Dibayar pada ' . $date->format('Y-m-d H:i') . ')';
                $denda->keterangan = trim($denda->keterangan) . $log;
                $denda->save();
            }
        }
    }

    public function down(): void
    {
        // Optional: Clean up? Better to leave as is since it's just helpful text.
        // But for consistency:
        $dendas = \App\Models\Denda::all();
        foreach ($dendas as $denda) {
            $denda->keterangan = trim(preg_replace('/\s*\(Dibayar pada.*?\)/i', '', $denda->keterangan));
            $denda->save();
        }
    }
};
