DROP PROCEDURE IF EXISTS sp_get_kepegawaian;
CREATE PROCEDURE sp_get_kepegawaian(
    IN p_search VARCHAR(255),
    IN p_sort_col VARCHAR(50),
    IN p_sort_dir VARCHAR(10),
    IN p_limit INT,
    IN p_offset INT,
    IN p_peran VARCHAR(20),
    IN p_viewer_role VARCHAR(20),
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
    
    -- Visibility Logic: Only Owner can see Owner
    IF p_peran IS NULL OR p_peran = '' THEN
        IF p_viewer_role = 'owner' THEN
            SET @peran_filter = ' AND peran IN ("owner", "admin", "petugas")';
        ELSE
            SET @peran_filter = ' AND peran IN ("admin", "petugas")';
        END IF;
    ELSE
        -- Specific role filter
        IF p_peran = 'owner' AND p_viewer_role != 'owner' THEN
            SET @peran_filter = ' AND 1=0'; -- Not allowed
        ELSE
            SET @peran_filter = CONCAT(' AND peran = "', p_peran, '"');
        END IF;
    END IF;

    -- Count query with peran filter
    SET @count_sql = CONCAT(
        'SELECT COUNT(*) INTO @temp_total 
         FROM pengguna 
         WHERE 1=1',
        @peran_filter,
        ' AND (nama LIKE ? OR email LIKE ? OR id_pengguna LIKE ?)'
    );
    
    PREPARE count_stmt FROM @count_sql;
    EXECUTE count_stmt USING @search_param, @search_param, @search_param;
    DEALLOCATE PREPARE count_stmt;
    SET p_total = @temp_total;

    -- Data query with peran filter
    SET @sql = CONCAT(
        'SELECT * FROM pengguna WHERE 1=1',
        @peran_filter,
        ' AND (nama LIKE ? OR email LIKE ? OR id_pengguna LIKE ?) 
         ORDER BY ', p_sort_col, ' ', p_sort_dir, 
        ' LIMIT ', p_limit, ' OFFSET ', p_offset
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt USING @search_param, @search_param, @search_param;
    DEALLOCATE PREPARE stmt;
END
