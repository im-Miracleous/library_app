DROP TRIGGER IF EXISTS tr_peminjaman_id_insert;
CREATE TRIGGER tr_peminjaman_id_insert BEFORE INSERT ON peminjaman FOR EACH ROW
BEGIN
    DECLARE date_code CHAR(10);
    DECLARE next_no INT;

    -- Logic: Only generate ID if it is passed as NULL or empty string
    IF NEW.id_peminjaman IS NULL OR NEW.id_peminjaman = '' THEN
        SET date_code = DATE_FORMAT(NOW(), '%Y%m%d');

        SET next_no = (
            SELECT IFNULL(MAX(CAST(RIGHT(id_peminjaman, 3) AS UNSIGNED)), 0) + 1 
            FROM peminjaman 
            WHERE DATE(created_at) = CURDATE()
        );

        SET NEW.id_peminjaman = CONCAT('P-', date_code, '-', LPAD(next_no, 3, '0'));
    END IF;
END
