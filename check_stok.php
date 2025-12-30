<?php

use Illuminate\Support\Facades\DB;

$buku = DB::table('buku')->select('id_buku', 'judul', 'stok_total', 'stok_tersedia')->get();
$peminjaman = DB::table('detail_peminjaman as dp')
    ->join('peminjaman as p', 'dp.id_peminjaman', '=', 'p.id_peminjaman')
    ->select('dp.id_buku', 'dp.jumlah', 'dp.status_buku', 'p.status_transaksi')
    ->get();

echo "=== BUKU ===\n";
foreach ($buku as $b) {
    echo "{$b->id_buku} - {$b->judul}: {$b->stok_tersedia}/{$b->stok_total}\n";
}

echo "\n=== PEMINJAMAN AKTIF ===\n";
foreach ($peminjaman as $p) {
    echo "{$p->id_buku} - Jumlah: {$p->jumlah} - Status: {$p->status_buku} - Transaksi: {$p->status_transaksi}\n";
}
