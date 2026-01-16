DROP TRIGGER IF EXISTS tr_update_status_stok;
CREATE TRIGGER tr_update_status_stok
BEFORE UPDATE ON buku
FOR EACH ROW
BEGIN
    IF NEW.stok_tersedia <= 0 THEN
        SET NEW.status = 'tidak_tersedia';
    ELSEIF NEW.stok_tersedia > 0 THEN
        SET NEW.status = 'tersedia';
    END IF;
END