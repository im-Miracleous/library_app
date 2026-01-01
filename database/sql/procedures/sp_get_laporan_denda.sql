DROP PROCEDURE IF EXISTS sp_get_laporan_denda;
CREATE PROCEDURE sp_get_laporan_denda(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_status_bayar VARCHAR(20),
    IN p_search VARCHAR(255),
    IN p_sort_col VARCHAR(50),
    IN p_sort_dir VARCHAR(10),
    IN p_limit INT,
    IN p_offset INT,
    OUT p_total INT
)
BEGIN
    IF p_search IS NULL THEN SET p_search = ''; END IF;
    SET @search_param = CONCAT('%', p_search, '%');
    SET @start_date = p_start_date;
    SET @end_date = p_end_date;
    
    SET @where_clause = ' WHERE DATE(d.created_at) BETWEEN ? AND ? ';
    
    IF p_status_bayar IS NOT NULL AND p_status_bayar != '' THEN
        SET @where_clause = CONCAT(@where_clause, ' AND d.status_bayar = "', p_status_bayar, '" ');
    END IF;

    SET @where_clause = CONCAT(@where_clause, ' AND (p.id_peminjaman LIKE ? OR u.nama LIKE ? OR b.judul LIKE ?) ');

    SET @count_sql = 'SELECT COUNT(*) INTO @temp_total FROM denda d ';
    SET @count_sql = CONCAT(@count_sql, 'JOIN detail_peminjaman dp ON d.id_detail_peminjaman = dp.id_detail_peminjaman ');
    SET @count_sql = CONCAT(@count_sql, 'JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman ');
    SET @count_sql = CONCAT(@count_sql, 'JOIN pengguna u ON p.id_pengguna = u.id_pengguna ');
    SET @count_sql = CONCAT(@count_sql, 'JOIN buku b ON dp.id_buku = b.id_buku ');
    SET @count_sql = CONCAT(@count_sql, @where_clause);
    
    PREPARE count_stmt FROM @count_sql;
    EXECUTE count_stmt USING @start_date, @end_date, @search_param, @search_param, @search_param;
    DEALLOCATE PREPARE count_stmt;
    SET p_total = @temp_total;

    SET @sql = 'SELECT d.id_denda, d.created_at as tanggal_denda, p.id_peminjaman, u.nama as nama_anggota, ';
    SET @sql = CONCAT(@sql, 'b.judul as judul_buku, d.jenis_denda, d.jumlah_denda, d.status_bayar, d.keterangan ');
    SET @sql = CONCAT(@sql, 'FROM denda d ');
    SET @sql = CONCAT(@sql, 'JOIN detail_peminjaman dp ON d.id_detail_peminjaman = dp.id_detail_peminjaman ');
    SET @sql = CONCAT(@sql, 'JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman ');
    SET @sql = CONCAT(@sql, 'JOIN pengguna u ON p.id_pengguna = u.id_pengguna ');
    SET @sql = CONCAT(@sql, 'JOIN buku b ON dp.id_buku = b.id_buku ');
    SET @sql = CONCAT(@sql, @where_clause);
    IF p_sort_col IS NULL OR p_sort_col = '' THEN SET p_sort_col = 'tanggal_denda'; END IF;
    IF p_sort_dir IS NULL OR p_sort_dir = '' THEN SET p_sort_dir = 'DESC'; END IF;

    SET @sql = CONCAT(@sql, ' ORDER BY ', p_sort_col, ' ', p_sort_dir, ' LIMIT ', p_limit, ' OFFSET ', p_offset);

    PREPARE stmt FROM @sql;
    EXECUTE stmt USING @start_date, @end_date, @search_param, @search_param, @search_param;
    DEALLOCATE PREPARE stmt;
END
