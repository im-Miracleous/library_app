<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. VIEW: v_peminjaman_aktif
        DB::unprepared("
            CREATE OR REPLACE VIEW v_peminjaman_aktif AS
            SELECT 
                p.id_peminjaman,
                u.nama AS nama_peminjam,
                p.tanggal_pinjam,
                p.tanggal_jatuh_tempo,
                dp.id_buku,
                b.judul AS judul_buku,
                dp.status_buku
            FROM peminjaman p
            JOIN pengguna u ON p.id_pengguna = u.id_pengguna
            JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
            JOIN buku b ON dp.id_buku = b.id_buku
            WHERE p.status_transaksi = 'berjalan'
            AND dp.status_buku = 'dipinjam';
        ");

        // 2. FUNCTION: fn_hitung_denda
        DB::unprepared("
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
        ");

        // 3. TRIGGER: tr_kurangi_stok_buku
        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_kurangi_stok_buku;
            CREATE TRIGGER tr_kurangi_stok_buku
            AFTER INSERT ON detail_peminjaman
            FOR EACH ROW
            BEGIN
                IF NEW.status_buku = 'dipinjam' THEN
                    UPDATE buku 
                    SET stok_tersedia = stok_tersedia - NEW.jumlah 
                    WHERE id_buku = NEW.id_buku;
                END IF;
            END
        ");

        // 4. TRIGGER: tr_kembalikan_stok_buku
        DB::unprepared("
            DROP TRIGGER IF EXISTS tr_kembalikan_stok_buku;
            CREATE TRIGGER tr_kembalikan_stok_buku
            AFTER UPDATE ON detail_peminjaman
            FOR EACH ROW
            BEGIN
                -- Jika status berubah dari 'dipinjam' ke 'dikembalikan'/'hilang' (logic sederhana)
                -- Jika dikembalikan, stok nambah. Jika hilang, stok tetap berkurang (karena fisik hilang), 
                -- tapi secara sistem 'dipinjam' selesai. 
                -- Asumsi: 'dikembalikan' = barang balik ke rak. 'hilang' = barang ga balik.
                
                IF OLD.status_buku = 'dipinjam' AND NEW.status_buku = 'dikembalikan' THEN
                    UPDATE buku 
                    SET stok_tersedia = stok_tersedia + OLD.jumlah 
                    WHERE id_buku = NEW.id_buku;
                END IF;
            END
        ");

        // 5. STORED PROCEDURE: sp_buat_peminjaman (Transaction & Rollback)
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_buat_peminjaman;
            CREATE PROCEDURE sp_buat_peminjaman(
                IN p_id_pengguna VARCHAR(20),
                IN p_tgl_pinjam DATE,
                IN p_tgl_jatuh_tempo DATE,
                IN p_keterangan TEXT,
                IN p_json_buku JSON,
                OUT p_result_message VARCHAR(255)
            )
            BEGIN
                -- Declare variables
                DECLARE v_id_peminjaman VARCHAR(30);
                DECLARE v_date_code CHAR(10);
                DECLARE v_next_no INT;
                DECLARE i INT DEFAULT 0;
                DECLARE v_book_count INT;
                DECLARE v_id_buku VARCHAR(20);
                DECLARE v_stok INT;
                
                -- Error Handler: Rollback on exception
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_result_message = 'Transaction failed: Error occurred.';
                END;

                -- Start Transaction
                START TRANSACTION;

                    -- 1. Generate ID (Logic similar to Trigger)
                    SET v_date_code = DATE_FORMAT(NOW(), '%Y%m%d');
                    SET v_next_no = (
                        SELECT IFNULL(MAX(CAST(RIGHT(id_peminjaman, 3) AS UNSIGNED)), 0) + 1 
                        FROM peminjaman 
                        WHERE DATE(created_at) = CURDATE()
                    );
                    SET v_id_peminjaman = CONCAT('P-', v_date_code, '-', LPAD(v_next_no, 3, '0'));

                    -- 2. Insert Header
                    INSERT INTO peminjaman (id_peminjaman, id_pengguna, tanggal_pinjam, tanggal_jatuh_tempo, status_transaksi, keterangan, created_at, updated_at)
                    VALUES (v_id_peminjaman, p_id_pengguna, p_tgl_pinjam, p_tgl_jatuh_tempo, 'berjalan', p_keterangan, NOW(), NOW());

                    -- 3. Extract JSON length
                    SET v_book_count = JSON_LENGTH(p_json_buku);

                    -- 4. Loop through books
                    WHILE i < v_book_count DO
                        -- Get Book ID from JSON Array (e.g. ['B001', 'B002'])
                        SET v_id_buku = JSON_UNQUOTE(JSON_EXTRACT(p_json_buku, CONCAT('$[', i, ']')));
                        
                        -- Check Stock Lock (For Update)
                        SELECT stok_tersedia INTO v_stok FROM buku WHERE id_buku = v_id_buku FOR UPDATE;

                        IF v_stok < 1 THEN
                            -- Signal Error to trigger Rollback
                            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok buku habis';
                        END IF;

                        -- Insert Detail (Trigger tr_kurangi_stok_buku will run after this)
                        INSERT INTO detail_peminjaman (id_peminjaman, id_buku, jumlah, status_buku, created_at, updated_at)
                        VALUES (v_id_peminjaman, v_id_buku, 1, 'dipinjam', NOW(), NOW());

                        SET i = i + 1;
                    END WHILE;

                    SET p_result_message = 'Success';

                -- Commit Transaction
                COMMIT;
            END
        ");

        // 6. STORED PROCEDURE with CURSOR: sp_cek_keterlambatan
        DB::unprepared("
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
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_peminjaman_aktif");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_hitung_denda");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_kurangi_stok_buku");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_kembalikan_stok_buku");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_buat_peminjaman");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_cek_keterlambatan");
    }
};
