DROP PROCEDURE IF EXISTS sp_get_buku_terpopuler_cetak;

CREATE PROCEDURE sp_get_buku_terpopuler_cetak(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_judul VARCHAR(255);
    DECLARE v_penulis VARCHAR(255);
    DECLARE v_total INT;
    
    DECLARE cur_buku CURSOR FOR 
        SELECT 
            b.judul,
            b.penulis,
            SUM(dp.jumlah) as total_dipinjam
        FROM detail_peminjaman dp
        JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
        JOIN buku b ON dp.id_buku = b.id_buku
        WHERE DATE(p.tanggal_pinjam) BETWEEN p_start_date AND p_end_date
        GROUP BY b.id_buku
        ORDER BY total_dipinjam DESC
        LIMIT 50; -- Limit to top 50 for report

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    DROP TEMPORARY TABLE IF EXISTS temp_buku_top_cetak;
    CREATE TEMPORARY TABLE temp_buku_top_cetak (
        judul VARCHAR(255),
        penulis VARCHAR(255),
        total_dipinjam INT
    );

    OPEN cur_buku;

    read_loop: LOOP
        FETCH cur_buku INTO v_judul, v_penulis, v_total;
        IF done THEN
            LEAVE read_loop;
        END IF;

        INSERT INTO temp_buku_top_cetak VALUES (v_judul, v_penulis, v_total);
    END LOOP;

    CLOSE cur_buku;

    SELECT * FROM temp_buku_top_cetak;
    DROP TEMPORARY TABLE IF EXISTS temp_buku_top_cetak;
END;
