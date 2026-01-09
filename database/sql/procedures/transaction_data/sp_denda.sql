DROP PROCEDURE IF EXISTS sp_bayar_denda;
CREATE PROCEDURE sp_bayar_denda(
    IN p_id_denda BIGINT,
    IN p_metode_pembayaran VARCHAR(50),
    IN p_keterangan TEXT
)
BEGIN
    UPDATE denda
    SET 
        status_bayar = 'lunas',
        tanggal_bayar = NOW(),
        -- metode_bayar = p_metode_pembayaran, -- Removed: Column not in schema
        keterangan = p_keterangan,
        updated_at = NOW()
    WHERE id_denda = p_id_denda;
END;
