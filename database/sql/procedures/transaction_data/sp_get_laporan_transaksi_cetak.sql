DROP PROCEDURE IF EXISTS sp_get_laporan_transaksi_cetak;

CREATE PROCEDURE sp_get_laporan_transaksi_cetak(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_status VARCHAR(20),
    IN p_search VARCHAR(255)
)
BEGIN
    -- Variable Declarations
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_peminjaman VARCHAR(20);
    DECLARE v_tanggal_pinjam DATE;
    DECLARE v_tanggal_jatuh_tempo DATE;
    DECLARE v_status_transaksi VARCHAR(20);
    DECLARE v_nama_anggota VARCHAR(255);
    DECLARE v_email_anggota VARCHAR(255);
    DECLARE v_buku_list TEXT;
    
    -- Declare Cursor
    DECLARE cur_transaksi CURSOR FOR 
        SELECT 
            p.id_peminjaman, 
            p.tanggal_pinjam, 
            p.tanggal_jatuh_tempo, 
            p.status_transaksi,
            u.nama,
            u.email
        FROM peminjaman p
        JOIN pengguna u ON p.id_pengguna = u.id_pengguna
        WHERE DATE(p.tanggal_pinjam) BETWEEN p_start_date AND p_end_date
        AND (p_status IS NULL OR p_status = '' OR p.status_transaksi = p_status)
        AND (p_search IS NULL OR p_search = '' OR p.id_peminjaman LIKE CONCAT('%', p_search, '%') OR u.nama LIKE CONCAT('%', p_search, '%'))
        ORDER BY p.tanggal_pinjam DESC;

    -- Declare Not Found Handler
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Create Temporary Table to Store Results
    DROP TEMPORARY TABLE IF EXISTS temp_laporan_cetak;
    CREATE TEMPORARY TABLE temp_laporan_cetak (
        id_peminjaman VARCHAR(20),
        tanggal_pinjam DATE,
        tanggal_jatuh_tempo DATE,
        status_transaksi VARCHAR(20),
        nama_anggota VARCHAR(255),
        email_anggota VARCHAR(255),
        daftar_buku TEXT
    );

    -- Open Cursor
    OPEN cur_transaksi;

    read_loop: LOOP
        FETCH cur_transaksi INTO v_id_peminjaman, v_tanggal_pinjam, v_tanggal_jatuh_tempo, v_status_transaksi, v_nama_anggota, v_email_anggota;
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Logic inside the loop: Get books for this transaction (simulating complex row building)
        SELECT GROUP_CONCAT(CONCAT(b.judul, ' (', dp.jumlah, ')') SEPARATOR ', ')
        INTO v_buku_list
        FROM detail_peminjaman dp
        JOIN buku b ON dp.id_buku = b.id_buku
        WHERE dp.id_peminjaman = v_id_peminjaman;

        -- Insert into temp table
        INSERT INTO temp_laporan_cetak VALUES (v_id_peminjaman, v_tanggal_pinjam, v_tanggal_jatuh_tempo, v_status_transaksi, v_nama_anggota, v_email_anggota, COALESCE(v_buku_list, '-'));
        
    END LOOP;

    -- Close Cursor
    CLOSE cur_transaksi;

    -- Select Final Results
    SELECT * FROM temp_laporan_cetak;
    
    -- Cleanup
    DROP TEMPORARY TABLE IF EXISTS temp_laporan_cetak;
END;
