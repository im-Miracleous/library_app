DROP PROCEDURE IF EXISTS sp_get_anggota;
CREATE PROCEDURE sp_get_anggota(
    IN p_search VARCHAR(255),
    IN p_sort_col VARCHAR(50),
    IN p_sort_dir VARCHAR(10),
    IN p_limit INT,
    IN p_offset INT,
    IN p_status VARCHAR(20),
    OUT p_total INT
)
BEGIN
    -- Default values
    IF p_search IS NULL THEN SET p_search = ''; END IF;
    IF p_sort_col IS NULL OR p_sort_col = '' THEN SET p_sort_col = 'created_at'; END IF;
    IF p_sort_dir IS NULL OR p_sort_dir = '' THEN SET p_sort_dir = 'DESC'; END IF;
    IF p_limit IS NULL THEN SET p_limit = 10; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;

    SET @search_param = CONCAT('%', p_search, '%');
    SET @status_filter = IF(p_status IS NULL OR p_status = '', '', CONCAT(' AND status = "', p_status, '"'));

    -- Count query with status filter
    SET @count_sql = CONCAT(
        'SELECT COUNT(*) INTO @temp_total 
         FROM pengguna 
         WHERE peran = "anggota" 
         AND (nama LIKE ? OR email LIKE ? OR id_pengguna LIKE ?)',
        @status_filter
    );
    
    PREPARE count_stmt FROM @count_sql;
    EXECUTE count_stmt USING @search_param, @search_param, @search_param;
    DEALLOCATE PREPARE count_stmt;
    SET p_total = @temp_total;

    -- Data query with active loans count and status filter
    SET @sql = CONCAT(
        'SELECT *, 
            (SELECT COUNT(*) FROM detail_peminjaman dp JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman WHERE p.id_pengguna = pengguna.id_pengguna AND p.status_transaksi = "berjalan" AND dp.status_buku = "dipinjam") AS active_loans 
         FROM pengguna 
         WHERE peran = "anggota" 
         AND (nama LIKE ? OR email LIKE ? OR id_pengguna LIKE ?)',
        @status_filter,
        ' ORDER BY ', p_sort_col, ' ', p_sort_dir, 
        ' LIMIT ', p_limit, ' OFFSET ', p_offset
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt USING @search_param, @search_param, @search_param;
    DEALLOCATE PREPARE stmt;
END
