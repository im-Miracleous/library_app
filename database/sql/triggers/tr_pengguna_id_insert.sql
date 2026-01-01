DROP TRIGGER IF EXISTS tr_pengguna_id_insert;
CREATE TRIGGER tr_pengguna_id_insert BEFORE INSERT ON pengguna FOR EACH ROW
BEGIN
    DECLARE role_code CHAR(1);
    DECLARE year_code CHAR(2);
    DECLARE next_no INT;
    
    IF NEW.peran = 'owner' THEN SET role_code = 'R';
    ELSEIF NEW.peran = 'admin' THEN SET role_code = 'A';
    ELSEIF NEW.peran = 'petugas' THEN SET role_code = 'S';
    ELSE SET role_code = 'M';
    END IF;

    SET year_code = DATE_FORMAT(NOW(), '%y');

    SET next_no = (
        SELECT IFNULL(MAX(CAST(RIGHT(id_pengguna, 3) AS UNSIGNED)), 0) + 1 
        FROM pengguna 
        WHERE SUBSTRING(id_pengguna, 3, 1) = role_code 
        AND SUBSTRING(id_pengguna, 4, 2) = year_code
    );

    SET NEW.id_pengguna = CONCAT('U-', role_code, year_code, LPAD(next_no, 3, '0'));
END
