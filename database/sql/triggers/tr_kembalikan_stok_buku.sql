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

    -- 2. Bertambah saat status berubah dari 'dipinjam' ke 'dikembalikan' (Pengembalian buku normal)
    IF OLD.status_buku = 'dipinjam' AND NEW.status_buku = 'dikembalikan' THEN
        UPDATE buku 
        SET stok_tersedia = stok_tersedia + OLD.jumlah 
        WHERE id_buku = NEW.id_buku;
    END IF;

    -- 3. Bertambah stok rusak (Pengembalian buku rusak)
    IF OLD.status_buku = 'dipinjam' AND NEW.status_buku = 'rusak' THEN
        UPDATE buku 
        SET stok_rusak = stok_rusak + OLD.jumlah 
        WHERE id_buku = NEW.id_buku;
    END IF;

    -- 4. Bertambah stok hilang (Pengembalian buku hilang)
    IF OLD.status_buku = 'dipinjam' AND NEW.status_buku = 'hilang' THEN
        UPDATE buku 
        SET stok_hilang = stok_hilang + OLD.jumlah 
        WHERE id_buku = NEW.id_buku;
    END IF;
END
