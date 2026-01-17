CREATE OR REPLACE VIEW v_peminjaman_list AS
SELECT 
    p.id_peminjaman,
    u.nama AS nama_anggota,
    u.email AS email_anggota,
    p.status_transaksi,
    p.tanggal_pinjam,
    p.tanggal_jatuh_tempo,
    p.is_extended,         
    p.created_at,
    (SELECT COUNT(*) FROM detail_peminjaman dp WHERE dp.id_peminjaman = p.id_peminjaman) as total_buku,
    (SELECT COUNT(*) FROM detail_peminjaman dp WHERE dp.id_peminjaman = p.id_peminjaman AND dp.status_buku = 'dikembalikan') as total_dikembalikan
FROM peminjaman p
JOIN pengguna u ON p.id_pengguna = u.id_pengguna;