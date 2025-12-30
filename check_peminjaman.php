<?php

use Illuminate\Support\Facades\DB;

echo "=== SEMUA DETAIL PEMINJAMAN BUKU B-01-001 ===\n";
$details = DB::table('detail_peminjaman as dp')
    ->join('peminjaman as p', 'dp.id_peminjaman', '=', 'p.id_peminjaman')
    ->where('dp.id_buku', 'B-01-001')
    ->select('dp.*', 'p.status_transaksi', 'p.id_pengguna')
    ->get();

foreach ($details as $d) {
    echo "ID Peminjaman: {$d->id_peminjaman}\n";
    echo "ID Pengguna: {$d->id_pengguna}\n";
    echo "Jumlah: {$d->jumlah}\n";
    echo "Status Buku: {$d->status_buku}\n";
    echo "Status Transaksi: {$d->status_transaksi}\n";
    echo "---\n";
}

echo "\n=== PERHITUNGAN ===\n";
$dipinjam = DB::table('detail_peminjaman as dp')
    ->join('peminjaman as p', 'dp.id_peminjaman', '=', 'p.id_peminjaman')
    ->where('dp.id_buku', 'B-01-001')
    ->where('dp.status_buku', 'dipinjam')
    ->where('p.status_transaksi', 'berjalan')
    ->sum('dp.jumlah');

echo "Total dipinjam (berjalan): {$dipinjam}\n";
echo "Stok total: 10\n";
echo "Stok tersedia seharusnya: " . (10 - $dipinjam) . "\n";
echo "Stok tersedia aktual di DB: 8\n";
echo "Selisih: " . ((10 - $dipinjam) - 8) . "\n";
