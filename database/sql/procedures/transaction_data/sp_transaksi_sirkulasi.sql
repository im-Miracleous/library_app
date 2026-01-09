DROP PROCEDURE IF EXISTS sp_approve_peminjaman;
CREATE PROCEDURE sp_approve_peminjaman(
    IN p_id_peminjaman VARCHAR(30),
    IN p_id_petugas VARCHAR(20)
)
BEGIN
    UPDATE peminjaman
    SET 
        status_transaksi = 'berjalan',
        id_petugas = p_id_petugas,
        updated_at = NOW()
    WHERE id_peminjaman = p_id_peminjaman;
END;

DROP PROCEDURE IF EXISTS sp_reject_peminjaman;
CREATE PROCEDURE sp_reject_peminjaman(
    IN p_id_peminjaman VARCHAR(30),
    IN p_id_petugas VARCHAR(20),
    IN p_alasan TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL; -- Propagate error to caller
    END;

    START TRANSACTION;
        UPDATE peminjaman
        SET 
            status_transaksi = 'ditolak',
            id_petugas = p_id_petugas,
            alasan_penolakan = p_alasan,
            updated_at = NOW()
        WHERE id_peminjaman = p_id_peminjaman;
    COMMIT;
END;

DROP PROCEDURE IF EXISTS sp_complete_peminjaman;
CREATE PROCEDURE sp_complete_peminjaman(
    IN p_id_peminjaman VARCHAR(30),
    IN p_tgl_kembali DATETIME,
    IN p_kondisi_buku JSON -- Array of {id_detail, id_buku, kondisi, denda_amount, keterangan}
)
BEGIN
    DECLARE v_id_detail BIGINT;
    DECLARE v_id_buku VARCHAR(20);
    DECLARE v_kondisi VARCHAR(20);
    DECLARE v_denda_item DECIMAL(10,2);
    DECLARE v_keterangan TEXT;
    DECLARE i INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_sisa INT;
    
    -- Error Handler that propagates the error
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;
        
        -- 1. Loop JSON Kondisi & Update Details
        SET v_count = JSON_LENGTH(p_kondisi_buku);
        
        WHILE i < v_count DO
            SET v_id_detail = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_kondisi_buku, CONCAT('$[', i, '].id_detail'))) AS UNSIGNED);
            SET v_id_buku = JSON_UNQUOTE(JSON_EXTRACT(p_kondisi_buku, CONCAT('$[', i, '].id_buku')));
            SET v_kondisi = JSON_UNQUOTE(JSON_EXTRACT(p_kondisi_buku, CONCAT('$[', i, '].kondisi')));
            SET v_denda_item = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_kondisi_buku, CONCAT('$[', i, '].denda_amount'))) AS DECIMAL(10,2));
            SET v_keterangan = JSON_UNQUOTE(JSON_EXTRACT(p_kondisi_buku, CONCAT('$[', i, '].keterangan')));
            
            -- Update Detail
            -- Trigger 'tr_kembalikan_stok_buku' will handle stock adjustment automatically based on status_buku change
            UPDATE detail_peminjaman 
            SET status_buku = CASE 
                                WHEN v_kondisi = 'baik' THEN 'dikembalikan'
                                ELSE v_kondisi 
                              END,
                tanggal_kembali_aktual = p_tgl_kembali,
                updated_at = NOW()
            WHERE id_detail_peminjaman = v_id_detail;
            
            -- 2. Create Denda linked to Detail
            IF v_denda_item > 0 THEN
                INSERT INTO denda (id_detail_peminjaman, jenis_denda, jumlah_denda, status_bayar, keterangan, created_at, updated_at)
                VALUES (v_id_detail, 
                        CASE 
                            WHEN v_kondisi = 'baik' THEN 'terlambat'
                            ELSE v_kondisi 
                        END, 
                        v_denda_item, 'belum_bayar', v_keterangan, NOW(), NOW());
            END IF;
            
            SET i = i + 1;
        END WHILE;
        
        -- 3. Check remaining items (Partial Return Logic)
        SELECT COUNT(*) INTO v_sisa 
        FROM detail_peminjaman 
        WHERE id_peminjaman = p_id_peminjaman AND status_buku = 'dipinjam';
        
        IF v_sisa = 0 THEN
            UPDATE peminjaman 
            SET status_transaksi = 'selesai', 
                updated_at = NOW()
            WHERE id_peminjaman = p_id_peminjaman;
        END IF;
        
    COMMIT;
END;

DROP PROCEDURE IF EXISTS sp_extend_peminjaman;
CREATE PROCEDURE sp_extend_peminjaman(
    IN p_id_peminjaman VARCHAR(30),
    IN p_new_due_date DATE
)
BEGIN
    UPDATE peminjaman
    SET tanggal_jatuh_tempo = p_new_due_date,
        is_extended = 1,
        updated_at = NOW()
    WHERE id_peminjaman = p_id_peminjaman;
END;
