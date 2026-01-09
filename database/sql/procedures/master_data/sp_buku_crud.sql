DROP PROCEDURE IF EXISTS sp_create_buku;
CREATE PROCEDURE sp_create_buku(
    IN p_id_kategori BIGINT,
    IN p_kode_dewey VARCHAR(255),
    IN p_isbn VARCHAR(255),
    IN p_judul VARCHAR(255),
    IN p_penulis VARCHAR(255),
    IN p_penerbit VARCHAR(255),
    IN p_tahun_terbit INT,
    IN p_stok_total INT,
    IN p_stok_tersedia INT,
    IN p_deskripsi TEXT,
    IN p_gambar_sampul VARCHAR(255)
)
BEGIN
    INSERT INTO buku (
        id_kategori, kode_dewey, isbn, judul, penulis, penerbit, 
        tahun_terbit, stok_total, stok_tersedia, deskripsi, 
        gambar_sampul, status, created_at, updated_at
    ) VALUES (
        p_id_kategori, p_kode_dewey, p_isbn, p_judul, p_penulis, p_penerbit, 
        p_tahun_terbit, p_stok_total, p_stok_tersedia, p_deskripsi, 
        p_gambar_sampul, 'tersedia', NOW(), NOW()
    );
END;

DROP PROCEDURE IF EXISTS sp_update_buku;
CREATE PROCEDURE sp_update_buku(
    IN p_id_buku VARCHAR(20),
    IN p_id_kategori BIGINT,
    IN p_kode_dewey VARCHAR(255),
    IN p_isbn VARCHAR(255),
    IN p_judul VARCHAR(255),
    IN p_penulis VARCHAR(255),
    IN p_penerbit VARCHAR(255),
    IN p_tahun_terbit INT,
    IN p_stok_total INT,
    IN p_stok_tersedia INT,
    IN p_stok_rusak INT,
    IN p_stok_hilang INT,
    IN p_deskripsi TEXT,
    IN p_gambar_sampul VARCHAR(255),
    IN p_status ENUM('tersedia', 'tidak_tersedia')
)
BEGIN
    UPDATE buku 
    SET 
        id_kategori = p_id_kategori,
        kode_dewey = p_kode_dewey,
        isbn = p_isbn,
        judul = p_judul,
        penulis = p_penulis,
        penerbit = p_penerbit,
        tahun_terbit = p_tahun_terbit,
        stok_total = p_stok_total,
        stok_tersedia = p_stok_tersedia,
        stok_rusak = p_stok_rusak,
        stok_hilang = p_stok_hilang,
        deskripsi = p_deskripsi,
        gambar_sampul = IF(p_gambar_sampul IS NOT NULL, p_gambar_sampul, gambar_sampul),
        status = p_status,
        updated_at = NOW()
    WHERE id_buku = p_id_buku;
END;

DROP PROCEDURE IF EXISTS sp_delete_buku;
CREATE PROCEDURE sp_delete_buku(
    IN p_id_buku VARCHAR(20)
)
BEGIN
    DELETE FROM buku WHERE id_buku = p_id_buku;
END;
