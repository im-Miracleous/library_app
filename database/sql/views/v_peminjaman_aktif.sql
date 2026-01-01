CREATE OR REPLACE VIEW v_peminjaman_aktif AS
SELECT 
    p.id_peminjaman,
    u.nama AS nama_peminjam,
    p.tanggal_pinjam,
    p.tanggal_jatuh_tempo,
    dp.id_buku,
    b.judul AS judul_buku,
    dp.status_buku
FROM peminjaman p
JOIN pengguna u ON p.id_pengguna = u.id_pengguna
JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
JOIN buku b ON dp.id_buku = b.id_buku
WHERE p.status_transaksi = 'berjalan'
AND dp.status_buku = 'dipinjam';
