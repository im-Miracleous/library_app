DROP TRIGGER IF EXISTS tr_kembalikan_stok_buku;
CREATE TRIGGER tr_kembalikan_stok_buku
AFTER UPDATE ON detail_peminjaman
FOR EACH ROW
BEGIN
    -- 1. Berkurang saat status berubah dari 'diajukan' ke 'dipinjam' (Peminjaman disetujui)
    IF OLD.status_buku = 'diajukan' AND NEW.status_buku = 'dipinjam' THEN
        UPDATE buku 
        SET stok_tersedia = stok_tersedia - NEW.jumlah 
        WHERE id_buku = NEW.id_buku;
    END IF;

    -- 2. Bertambah saat status berubah dari 'dipinjam' ke 'dikembalikan' (Pengembalian buku)
    IF OLD.status_buku = 'dipinjam' AND NEW.status_buku = 'dikembalikan' THEN
        UPDATE buku 
        SET stok_tersedia = stok_tersedia + OLD.jumlah 
        WHERE id_buku = NEW.id_buku;
    END IF;
END
