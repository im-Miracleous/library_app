DROP PROCEDURE IF EXISTS sp_get_laporan_inventaris_cetak;

CREATE PROCEDURE sp_get_laporan_inventaris_cetak(
    IN p_search VARCHAR(255),
    IN p_kategori INT
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_isbn VARCHAR(20);
    DECLARE v_judul VARCHAR(255);
    DECLARE v_penulis VARCHAR(255);
    DECLARE v_kategori VARCHAR(100);
    DECLARE v_stok_total INT;
    DECLARE v_stok_tersedia INT;
    DECLARE v_stok_rusak INT;
    DECLARE v_stok_hilang INT;
    
    DECLARE cur_inventaris CURSOR FOR 
        SELECT 
            b.isbn,
            b.judul,
            b.penulis,
            k.nama_kategori,
            b.stok_total,
            b.stok_tersedia,
            b.stok_rusak,
            b.stok_hilang
        FROM buku b
        JOIN kategori k ON b.id_kategori = k.id_kategori
        WHERE (p_search IS NULL OR p_search = '' OR b.judul LIKE CONCAT('%', p_search, '%') OR b.isbn LIKE CONCAT('%', p_search, '%'))
        AND (p_kategori IS NULL OR p_kategori = 0 OR b.id_kategori = p_kategori)
        ORDER BY b.judul ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    DROP TEMPORARY TABLE IF EXISTS temp_laporan_inventaris_cetak;
    CREATE TEMPORARY TABLE temp_laporan_inventaris_cetak (
        isbn VARCHAR(20),
        judul VARCHAR(255),
        penulis VARCHAR(255),
        kategori VARCHAR(100),
        stok_total INT,
        stok_tersedia INT,
        stok_rusak INT,
        stok_hilang INT
    );

    OPEN cur_inventaris;

    read_loop: LOOP
        FETCH cur_inventaris INTO v_isbn, v_judul, v_penulis, v_kategori, v_stok_total, v_stok_tersedia, v_stok_rusak, v_stok_hilang;
        IF done THEN
            LEAVE read_loop;
        END IF;

        INSERT INTO temp_laporan_inventaris_cetak VALUES (v_isbn, v_judul, v_penulis, v_kategori, v_stok_total, v_stok_tersedia, v_stok_rusak, v_stok_hilang);
    END LOOP;

    CLOSE cur_inventaris;

    SELECT * FROM temp_laporan_inventaris_cetak;
    DROP TEMPORARY TABLE IF EXISTS temp_laporan_inventaris_cetak;
END;
