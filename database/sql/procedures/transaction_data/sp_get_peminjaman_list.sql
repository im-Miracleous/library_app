DROP PROCEDURE IF EXISTS sp_get_peminjaman_list;
CREATE PROCEDURE sp_get_peminjaman_list(
    IN p_search VARCHAR(255),
    IN p_status VARCHAR(20),
    IN p_sort_col VARCHAR(50),
    IN p_sort_dir VARCHAR(10),
    IN p_limit INT,
    IN p_offset INT,
    OUT p_total INT
)
BEGIN
    IF p_search IS NULL THEN SET p_search = ''; END IF;
    SET @search_param = CONCAT('%', p_search, '%');
    
    SET @where_clause = ' WHERE 1=1 ';

    IF p_status IS NOT NULL AND p_status != '' THEN
        SET @where_clause = CONCAT(@where_clause, ' AND p.status_transaksi = "', p_status, '" ');
    END IF;

    SET @where_clause = CONCAT(@where_clause, ' AND (p.id_peminjaman LIKE ? OR u.nama LIKE ?) ');

    -- Count
    SET @count_sql = 'SELECT COUNT(*) INTO @temp_total FROM peminjaman p JOIN pengguna u ON p.id_pengguna = u.id_pengguna';
    SET @count_sql = CONCAT(@count_sql, @where_clause);
    
    PREPARE count_stmt FROM @count_sql;
    EXECUTE count_stmt USING @search_param, @search_param;
    DEALLOCATE PREPARE count_stmt;
    SET p_total = @temp_total;

    SET @sql = 'SELECT p.*, p.alasan_penolakan, u.nama as nama_anggota, u.email as email_anggota, 
        (SELECT COUNT(*) FROM detail_peminjaman dp WHERE dp.id_peminjaman = p.id_peminjaman) as total_buku,
        (SELECT COUNT(*) FROM detail_peminjaman dp WHERE dp.id_peminjaman = p.id_peminjaman AND dp.status_buku = ''dikembalikan'') as total_dikembalikan
        FROM peminjaman p JOIN pengguna u ON p.id_pengguna = u.id_pengguna';
    SET @sql = CONCAT(@sql, @where_clause);
    
    IF p_sort_col IS NULL OR p_sort_col = '' THEN SET p_sort_col = 'created_at'; END IF;
    IF p_sort_dir IS NULL OR p_sort_dir = '' THEN SET p_sort_dir = 'DESC'; END IF;

    SET @sql = CONCAT(@sql, ' ORDER BY ', p_sort_col, ' ', p_sort_dir, ' LIMIT ', p_limit, ' OFFSET ', p_offset);

    PREPARE stmt FROM @sql;
    EXECUTE stmt USING @search_param, @search_param;
    DEALLOCATE PREPARE stmt;
END
