DROP PROCEDURE IF EXISTS sp_get_kategori;
CREATE PROCEDURE sp_get_kategori(
    IN p_search VARCHAR(255),
    IN p_sort_col VARCHAR(50),
    IN p_sort_dir VARCHAR(10),
    IN p_limit INT,
    IN p_offset INT,
    OUT p_total INT
)
BEGIN
    IF p_search IS NULL THEN SET p_search = ''; END IF;
    IF p_sort_col IS NULL OR p_sort_col = '' THEN SET p_sort_col = 'created_at'; END IF;
    IF p_sort_dir IS NULL OR p_sort_dir = '' THEN SET p_sort_dir = 'DESC'; END IF;
    IF p_limit IS NULL THEN SET p_limit = 10; END IF;
    IF p_offset IS NULL THEN SET p_offset = 0; END IF;

    SET @search_param = CONCAT('%', p_search, '%');

    SELECT COUNT(*) INTO p_total 
    FROM kategori
    WHERE id_kategori LIKE @search_param
    OR nama_kategori LIKE @search_param;

    SET @sql = CONCAT(
        'SELECT * FROM kategori 
         WHERE id_kategori LIKE ? 
         OR nama_kategori LIKE ? 
         ORDER BY ', p_sort_col, ' ', p_sort_dir, 
        ' LIMIT ', p_limit, ' OFFSET ', p_offset
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt USING @search_param, @search_param;
    DEALLOCATE PREPARE stmt;
END
