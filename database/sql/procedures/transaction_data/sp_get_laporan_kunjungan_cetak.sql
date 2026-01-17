DROP PROCEDURE IF EXISTS sp_get_laporan_kunjungan_cetak;

CREATE PROCEDURE sp_get_laporan_kunjungan_cetak(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_search VARCHAR(255)
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_tanggal DATETIME;
    DECLARE v_nama_pengunjung VARCHAR(255);
    DECLARE v_jenis_pengunjung VARCHAR(50);
    DECLARE v_keperluan VARCHAR(255);
    
    DECLARE cur_kunjungan CURSOR FOR 
        SELECT 
            created_at,
            nama_pengunjung,
            jenis_pengunjung,
            keperluan
        FROM pengunjung
        WHERE DATE(created_at) BETWEEN p_start_date AND p_end_date
        AND (p_search IS NULL OR p_search = '' OR nama_pengunjung LIKE CONCAT('%', p_search, '%') OR keperluan LIKE CONCAT('%', p_search, '%'))
        ORDER BY created_at DESC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    DROP TEMPORARY TABLE IF EXISTS temp_laporan_kunjungan_cetak;
    CREATE TEMPORARY TABLE temp_laporan_kunjungan_cetak (
        tanggal DATETIME,
        nama_pengunjung VARCHAR(255),
        jenis_pengunjung VARCHAR(50),
        keperluan VARCHAR(255)
    );

    OPEN cur_kunjungan;

    read_loop: LOOP
        FETCH cur_kunjungan INTO v_tanggal, v_nama_pengunjung, v_jenis_pengunjung, v_keperluan;
        IF done THEN
            LEAVE read_loop;
        END IF;

        INSERT INTO temp_laporan_kunjungan_cetak VALUES (v_tanggal, v_nama_pengunjung, v_jenis_pengunjung, v_keperluan);
    END LOOP;

    CLOSE cur_kunjungan;

    SELECT * FROM temp_laporan_kunjungan_cetak;
    DROP TEMPORARY TABLE IF EXISTS temp_laporan_kunjungan_cetak;
END;
