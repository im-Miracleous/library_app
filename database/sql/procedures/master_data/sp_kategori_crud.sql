DROP PROCEDURE IF EXISTS sp_create_kategori;
CREATE PROCEDURE sp_create_kategori(
    IN p_nama_kategori VARCHAR(255),
    IN p_deskripsi TEXT
)
BEGIN
    INSERT INTO kategori (nama_kategori, deskripsi, created_at, updated_at)
    VALUES (p_nama_kategori, p_deskripsi, NOW(), NOW());
END;

DROP PROCEDURE IF EXISTS sp_update_kategori;
CREATE PROCEDURE sp_update_kategori(
    IN p_id_kategori BIGINT,
    IN p_nama_kategori VARCHAR(255),
    IN p_deskripsi TEXT
)
BEGIN
    UPDATE kategori
    SET 
        nama_kategori = p_nama_kategori,
        deskripsi = p_deskripsi,
        updated_at = NOW()
    WHERE id_kategori = p_id_kategori;
END;

DROP PROCEDURE IF EXISTS sp_delete_kategori;
CREATE PROCEDURE sp_delete_kategori(
    IN p_id_kategori BIGINT
)
BEGIN
    DELETE FROM kategori WHERE id_kategori = p_id_kategori;
END;
