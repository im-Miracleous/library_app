DROP PROCEDURE IF EXISTS sp_buat_peminjaman;
CREATE PROCEDURE sp_buat_peminjaman(
    IN p_id_pengguna VARCHAR(20),
    IN p_tgl_pinjam DATE,
    IN p_tgl_jatuh_tempo DATE,
    IN p_keterangan TEXT,
    IN p_json_buku JSON,
    OUT p_result_message VARCHAR(255)
)
BEGIN
    -- Declare variables
    DECLARE v_id_peminjaman VARCHAR(30);
    DECLARE v_date_code CHAR(10);
    DECLARE v_next_no INT;
    DECLARE i INT DEFAULT 0;
    DECLARE v_book_count INT;
    DECLARE v_id_buku VARCHAR(20);
    DECLARE v_stok INT;
    
    -- Error Handler: Rollback on exception
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_message = 'Transaction failed: Error occurred.';
    END;

    -- Start Transaction
    START TRANSACTION;

        -- 1. Generate ID (Logic similar to Trigger)
        SET v_date_code = DATE_FORMAT(NOW(), '%Y%m%d');
        SET v_next_no = (
            SELECT IFNULL(MAX(CAST(RIGHT(id_peminjaman, 3) AS UNSIGNED)), 0) + 1 
            FROM peminjaman 
            WHERE DATE(created_at) = CURDATE()
        );
        SET v_id_peminjaman = CONCAT('P-', v_date_code, '-', LPAD(v_next_no, 3, '0'));

        -- 2. Insert Header
        INSERT INTO peminjaman (id_peminjaman, id_pengguna, tanggal_pinjam, tanggal_jatuh_tempo, status_transaksi, keterangan, created_at, updated_at)
        VALUES (v_id_peminjaman, p_id_pengguna, p_tgl_pinjam, p_tgl_jatuh_tempo, 'berjalan', p_keterangan, NOW(), NOW());

        -- 3. Extract JSON length
        SET v_book_count = JSON_LENGTH(p_json_buku);

        -- 4. Loop through books
        WHILE i < v_book_count DO
            -- Get Book ID from JSON Array (e.g. ['B001', 'B002'])
            SET v_id_buku = JSON_UNQUOTE(JSON_EXTRACT(p_json_buku, CONCAT('$[', i, ']')));
            
            -- Check Stock Lock (For Update)
            SELECT stok_tersedia INTO v_stok FROM buku WHERE id_buku = v_id_buku FOR UPDATE;

            IF v_stok < 1 THEN
                -- Signal Error to trigger Rollback
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok buku habis';
            END IF;

            -- Check if book already borrowed by this user (Active/Pending)
            IF EXISTS (
                SELECT 1 
                FROM detail_peminjaman dp
                JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
                WHERE p.id_pengguna = p_id_pengguna
                AND dp.id_buku = v_id_buku
                AND p.status_transaksi IN ('berjalan', 'menunggu_verifikasi')
                AND dp.status_buku = 'dipinjam'
            ) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User sedang meminjam buku ini';
            END IF;

            -- Insert Detail (Trigger tr_kurangi_stok_buku will run after this)
            INSERT INTO detail_peminjaman (id_peminjaman, id_buku, jumlah, status_buku, created_at, updated_at)
            VALUES (v_id_peminjaman, v_id_buku, 1, 'dipinjam', NOW(), NOW());

            SET i = i + 1;
        END WHILE;

        SET p_result_message = 'Success';

    -- Commit Transaction
    COMMIT;
END
