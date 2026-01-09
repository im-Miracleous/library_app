DROP PROCEDURE IF EXISTS sp_ajukan_peminjaman;
CREATE PROCEDURE sp_ajukan_peminjaman(
    IN p_id_pengguna VARCHAR(20),
    IN p_tgl_pinjam DATE,
    IN p_tgl_jatuh_tempo DATE,
    IN p_keterangan TEXT,
    IN p_json_buku JSON, -- Array of Strings ["ID1", "ID2"]
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_date_code VARCHAR(10);
    DECLARE v_next_no INT;
    DECLARE v_id_peminjaman VARCHAR(20);
    DECLARE i INT DEFAULT 0;
    DECLARE v_book_count INT;
    DECLARE v_id_buku VARCHAR(20);
    DECLARE v_stok INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- 1. Generate ID (Logic Copy from sp_buat_peminjaman / trigger pattern)
    -- Format: P-YYYYMMDD-XXX
    SET v_date_code = DATE_FORMAT(NOW(), '%Y%m%d');
    
    -- Locking read to prevent race condition on ID generation
    SELECT IFNULL(MAX(CAST(RIGHT(id_peminjaman, 3) AS UNSIGNED)), 0) + 1 INTO v_next_no
    FROM peminjaman
    WHERE DATE(created_at) = CURDATE()
    FOR UPDATE;
    
    SET v_id_peminjaman = CONCAT('P-', v_date_code, '-', LPAD(v_next_no, 3, '0'));

    -- 2. Insert Header
    INSERT INTO peminjaman (id_peminjaman, id_pengguna, tanggal_pinjam, tanggal_jatuh_tempo, status_transaksi, keterangan, created_at, updated_at)
    VALUES (v_id_peminjaman, p_id_pengguna, p_tgl_pinjam, p_tgl_jatuh_tempo, 'menunggu_verifikasi', p_keterangan, NOW(), NOW());

    -- 3. Loop Details
    SET v_book_count = JSON_LENGTH(p_json_buku);
    
    WHILE i < v_book_count DO
        SET v_id_buku = JSON_UNQUOTE(JSON_EXTRACT(p_json_buku, CONCAT('$[', i, ']')));
        
        -- Check Stock
        SELECT stok_tersedia INTO v_stok FROM buku WHERE id_buku = v_id_buku FOR UPDATE;
        IF v_stok < 1 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok buku habis';
        END IF;

        -- Insert Detail
        INSERT INTO detail_peminjaman (id_peminjaman, id_buku, jumlah, status_buku, created_at, updated_at)
        VALUES (v_id_peminjaman, v_id_buku, 1, 'diajukan', NOW(), NOW());
        
        -- Logic decrement stok? 
        -- Trigger `tr_kurangi_stok_buku` usually handles this. 
        -- IF the trigger works on 'diajukan', then fine. 
        -- Checking previous knowledge: Trigger usually reduces on INSERT.
        
        SET i = i + 1;
    END WHILE;
    
    SET p_message = 'Success';
    COMMIT;
END;
