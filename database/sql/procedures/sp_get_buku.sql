DROP PROCEDURE IF EXISTS sp_get_buku;
CREATE PROCEDURE sp_get_buku(
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
    FROM buku b
    JOIN kategori k ON b.id_kategori = k.id_kategori
    WHERE b.id_buku LIKE @search_param 
    OR b.judul LIKE @search_param 
    OR b.penulis LIKE @search_param 
    OR b.penerbit LIKE @search_param
    OR b.isbn LIKE @search_param;

    SET @sql = CONCAT(
        'SELECT b.*, k.nama_kategori 
         FROM buku b
         JOIN kategori k ON b.id_kategori = k.id_kategori
         WHERE b.id_buku LIKE ? 
         OR b.judul LIKE ? 
         OR b.penulis LIKE ? 
         OR b.penerbit LIKE ? 
         OR b.isbn LIKE ?
         ORDER BY ', p_sort_col, ' ', p_sort_dir, 
        ' LIMIT ', p_limit, ' OFFSET ', p_offset
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt USING @search_param, @search_param, @search_param, @search_param, @search_param;
    DEALLOCATE PREPARE stmt;
END
