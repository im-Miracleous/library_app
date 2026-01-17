DROP TRIGGER IF EXISTS tr_update_status_stok;
CREATE TRIGGER tr_update_status_stok
BEFORE UPDATE ON buku
FOR EACH ROW
BEGIN
    IF NEW.stok_tersedia <= 0 THEN
        -- Jika stok habis, set status ke 'habis', KECUALI jika status di-set ke 'tidak_tersedia' (manual hide)
        IF NEW.status != 'tidak_tersedia' THEN
            SET NEW.status = 'habis';
        END IF;
    ELSEIF NEW.stok_tersedia > 0 THEN
        -- Jika stok tersedia kembali
        -- Jika status sebelumnya (atau input baru) adalah 'habis', kembalikan ke 'tersedia'
        -- Tapi jika status adalah 'tidak_tersedia' (manual hide), biarkan tetap 'tidak_tersedia'
        IF NEW.status = 'habis' THEN
            SET NEW.status = 'tersedia';
        END IF;
    END IF;
END