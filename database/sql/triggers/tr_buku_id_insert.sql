DROP TRIGGER IF EXISTS tr_buku_id_insert;
CREATE TRIGGER tr_buku_id_insert BEFORE INSERT ON buku FOR EACH ROW
BEGIN
    DECLARE cat_code CHAR(2);
    DECLARE next_no INT;

    -- Ambil ID kategori (Integer) dan format jadi 2 digit string (misal 5 -> '05')
    SET cat_code = LPAD(NEW.id_kategori, 2, '0');

    SET next_no = (
        SELECT IFNULL(MAX(CAST(RIGHT(id_buku, 3) AS UNSIGNED)), 0) + 1 
        FROM buku 
        WHERE id_kategori = NEW.id_kategori
    );

    SET NEW.id_buku = CONCAT('B-', cat_code, '-', LPAD(next_no, 3, '0'));

    -- Cek Stok Awal (Insert Logic)
    -- Jika stok 0, otomatis set ke 'habis', KECUALI jika di-explicit set 'tidak_tersedia'
    IF NEW.stok_tersedia <= 0 AND NEW.status != 'tidak_tersedia' THEN
        SET NEW.status = 'habis';
    END IF;
END
