DROP TRIGGER IF EXISTS tr_kembalikan_stok_buku;
CREATE TRIGGER tr_kembalikan_stok_buku
AFTER UPDATE ON detail_peminjaman
FOR EACH ROW
BEGIN
    -- Jika status berubah dari 'dipinjam' ke 'dikembalikan'/'hilang' (logic sederhana)
    -- Jika dikembalikan, stok nambah. Jika hilang, stok tetap berkurang (karena fisik hilang), 
    -- tapi secara sistem 'dipinjam' selesai. 
    -- Asumsi: 'dikembalikan' = barang balik ke rak. 'hilang' = barang ga balik.
    
    IF OLD.status_buku = 'dipinjam' AND NEW.status_buku = 'dikembalikan' THEN
        UPDATE buku 
        SET stok_tersedia = stok_tersedia + OLD.jumlah 
        WHERE id_buku = NEW.id_buku;
    END IF;
END
