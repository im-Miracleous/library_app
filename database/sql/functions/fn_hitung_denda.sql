DROP FUNCTION IF EXISTS fn_hitung_denda;
CREATE FUNCTION fn_hitung_denda(
    tgl_jatuh_tempo DATE, 
    tgl_kembali DATE, 
    biaya_per_hari DECIMAL(10,2)
) 
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE jumlah_hari INT;
    DECLARE total_denda DECIMAL(10,2);
    
    SET jumlah_hari = DATEDIFF(tgl_kembali, tgl_jatuh_tempo);
    
    IF jumlah_hari > 0 THEN
        SET total_denda = jumlah_hari * biaya_per_hari;
    ELSE
        SET total_denda = 0;
    END IF;
    
    RETURN total_denda;
END
