<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations - Create all stored procedures for data retrieval
     */
    public function up(): void
    {
        // 1. Stored Procedure: sp_get_anggota (with status filter)
        DB::unprepared(<<<'SQL'
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
            (SELECT COUNT(*) FROM peminjaman p WHERE p.id_pengguna = pengguna.id_pengguna AND status_transaksi = "berjalan") AS active_loans 
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
SQL
        );

        // 2. Stored Procedure: sp_get_kepegawaian (with peran filter)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS sp_get_kepegawaian;
CREATE PROCEDURE sp_get_kepegawaian(
    IN p_search VARCHAR(255),
    IN p_sort_col VARCHAR(50),
    IN p_sort_dir VARCHAR(10),
    IN p_limit INT,
    IN p_offset INT,
    IN p_peran VARCHAR(20),
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
    SET @peran_filter = IF(p_peran IS NULL OR p_peran = '', ' AND peran IN ("admin", "petugas")', CONCAT(' AND peran = "', p_peran, '"'));

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
SQL
        );

        // 3. Stored Procedure: sp_get_buku
        DB::unprepared(<<<'SQL'
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
SQL
        );

        // 4. Stored Procedure: sp_get_kategori
        DB::unprepared(<<<'SQL'
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
SQL
        );
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_anggota');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_kepegawaian');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_buku');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_kategori');
    }
};
