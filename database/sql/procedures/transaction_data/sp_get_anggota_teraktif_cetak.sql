DROP PROCEDURE IF EXISTS sp_get_anggota_teraktif_cetak;

CREATE PROCEDURE sp_get_anggota_teraktif_cetak(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_nama VARCHAR(255);
    DECLARE v_email VARCHAR(255);
    DECLARE v_total INT;
    
    DECLARE cur_anggota CURSOR FOR 
        SELECT 
            u.nama,
            u.email,
            COUNT(p.id_peminjaman) as total_transaksi
        FROM peminjaman p
        JOIN pengguna u ON p.id_pengguna = u.id_pengguna
        WHERE DATE(p.tanggal_pinjam) BETWEEN p_start_date AND p_end_date
        GROUP BY u.id_pengguna
        ORDER BY total_transaksi DESC
        LIMIT 50; -- Limit to top 50

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    DROP TEMPORARY TABLE IF EXISTS temp_anggota_top_cetak;
    CREATE TEMPORARY TABLE temp_anggota_top_cetak (
        nama_anggota VARCHAR(255),
        email_anggota VARCHAR(255),
        total_transaksi INT
    );

    OPEN cur_anggota;

    read_loop: LOOP
        FETCH cur_anggota INTO v_nama, v_email, v_total;
        IF done THEN
            LEAVE read_loop;
        END IF;

        INSERT INTO temp_anggota_top_cetak VALUES (v_nama, v_email, v_total);
    END LOOP;

    CLOSE cur_anggota;

    SELECT * FROM temp_anggota_top_cetak;
    DROP TEMPORARY TABLE IF EXISTS temp_anggota_top_cetak;
END;
