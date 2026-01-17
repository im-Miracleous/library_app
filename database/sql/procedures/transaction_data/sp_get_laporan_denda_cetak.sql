DROP PROCEDURE IF EXISTS sp_get_laporan_denda_cetak;

CREATE PROCEDURE sp_get_laporan_denda_cetak(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_status_bayar VARCHAR(20),
    IN p_search VARCHAR(255)
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_denda VARCHAR(20);
    DECLARE v_nama_anggota VARCHAR(255);
    DECLARE v_tanggal_kembali DATE;
    DECLARE v_jenis_denda VARCHAR(50);
    DECLARE v_keterangan TEXT;
    DECLARE v_jumlah_denda DECIMAL(10,2);
    DECLARE v_status_bayar VARCHAR(20);
    
    DECLARE cur_denda CURSOR FOR 
        SELECT 
            d.id_denda,
            u.nama,
            DATE(dp.tanggal_kembali_aktual),
            d.jenis_denda,
            d.keterangan,
            d.jumlah_denda,
            d.status_bayar
        FROM denda d
        JOIN detail_peminjaman dp ON d.id_detail_peminjaman = dp.id_detail_peminjaman
        JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
        JOIN pengguna u ON p.id_pengguna = u.id_pengguna
        WHERE DATE(d.created_at) BETWEEN p_start_date AND p_end_date
        AND (p_status_bayar IS NULL OR p_status_bayar = '' OR d.status_bayar = p_status_bayar)
        AND (p_search IS NULL OR p_search = '' OR d.id_denda LIKE CONCAT('%', p_search, '%') OR u.nama LIKE CONCAT('%', p_search, '%'))
        ORDER BY d.id_denda DESC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    DROP TEMPORARY TABLE IF EXISTS temp_laporan_denda_cetak;
    CREATE TEMPORARY TABLE temp_laporan_denda_cetak (
        id_denda VARCHAR(20),
        nama_anggota VARCHAR(255),
        tanggal_kembali DATE,
        jenis_denda VARCHAR(50),
        keterangan TEXT,
        jumlah_denda DECIMAL(10,2),
        status_bayar VARCHAR(20)
    );

    OPEN cur_denda;

    read_loop: LOOP
        FETCH cur_denda INTO v_id_denda, v_nama_anggota, v_tanggal_kembali, v_jenis_denda, v_keterangan, v_jumlah_denda, v_status_bayar;
        IF done THEN
            LEAVE read_loop;
        END IF;

        INSERT INTO temp_laporan_denda_cetak VALUES (v_id_denda, v_nama_anggota, v_tanggal_kembali, v_jenis_denda, v_keterangan, v_jumlah_denda, v_status_bayar);
    END LOOP;

    CLOSE cur_denda;

    SELECT * FROM temp_laporan_denda_cetak;
    DROP TEMPORARY TABLE IF EXISTS temp_laporan_denda_cetak;
END;
