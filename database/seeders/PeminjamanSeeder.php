<?php
namespace Database\Seeders;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Pengguna;
use App\Models\Buku;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $anggota = Pengguna::where('email', 'siti@student.com')->first();
        $buku = Buku::where('judul', 'Laskar Pelangi')->first();

        if ($anggota && $buku) {
            Peminjaman::create([
                'id_pengguna' => $anggota->id_pengguna,
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(7), 
                'status_transaksi' => 'berjalan', 
                'keterangan' => 'Peminjaman rutin'
            ]);

            $peminjaman = Peminjaman::where('id_pengguna', $anggota->id_pengguna)
                                ->orderBy('created_at', 'desc')
                                ->first();

            if ($peminjaman) {
                DetailPeminjaman::create([
                    'id_peminjaman' => $peminjaman->id_peminjaman,
                    'id_buku' => $buku->id_buku,
                    'jumlah' => 1,
                    'status_buku' => 'dipinjam' 
                ]);
                $buku->decrement('stok_tersedia');
            }
        }
    }
}