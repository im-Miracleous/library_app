DROP TRIGGER IF EXISTS tr_pulihkan_stok_delete;
CREATE TRIGGER tr_pulihkan_stok_delete
AFTER DELETE ON detail_peminjaman
FOR EACH ROW
BEGIN
    -- Jika buku yang dihapus statusnya masih 'dipinjam', kembalikan stoknya
    IF OLD.status_buku = 'dipinjam' THEN
        UPDATE buku 
        SET stok_tersedia = stok_tersedia + OLD.jumlah 
        WHERE id_buku = OLD.id_buku;
    END IF;
END
