DROP PROCEDURE IF EXISTS sp_cek_keterlambatan;
CREATE PROCEDURE sp_cek_keterlambatan()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_peminjaman VARCHAR(30);
    DECLARE v_id_pengguna VARCHAR(20);
    DECLARE v_tgl_jatuh_tempo DATE;
    
    -- Cursor Declaration
    DECLARE cur_peminjaman CURSOR FOR 
        SELECT id_peminjaman, id_pengguna, tanggal_jatuh_tempo 
        FROM peminjaman 
        WHERE status_transaksi = 'berjalan' 
        AND tanggal_jatuh_tempo < CURDATE();
    
    -- Handler for NOT FOUND
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur_peminjaman;
    
    read_loop: LOOP
        FETCH cur_peminjaman INTO v_id_peminjaman, v_id_pengguna, v_tgl_jatuh_tempo;
        
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Check if notification already exists for today to avoid spam
        IF NOT EXISTS (
            SELECT 1 FROM notifikasi 
            WHERE id_pengguna = v_id_pengguna 
            AND judul LIKE CONCAT('%', v_id_peminjaman, '%')
            AND DATE(created_at) = CURDATE()
        ) THEN
            INSERT INTO notifikasi (id_pengguna, judul, pesan, tipe, created_at, updated_at)
            VALUES (
                v_id_pengguna, 
                CONCAT('Keterlambatan Peminjaman ', v_id_peminjaman),
                CONCAT('Buku yang anda pinjam telah melewati jatuh tempo (', v_tgl_jatuh_tempo, '). Harap segera kembalikan.'),
                'denda',
                NOW(),
                NOW()
            );
        END IF;
        
    END LOOP;
    
    CLOSE cur_peminjaman;
END
