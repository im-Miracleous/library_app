DROP TRIGGER IF EXISTS tr_kurangi_stok_buku;
CREATE TRIGGER tr_kurangi_stok_buku
AFTER INSERT ON detail_peminjaman
FOR EACH ROW
BEGIN
    IF NEW.status_buku = 'dipinjam' THEN
        UPDATE buku 
        SET stok_tersedia = stok_tersedia - NEW.jumlah 
        WHERE id_buku = NEW.id_buku;
    END IF;
END
