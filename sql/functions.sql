DELIMITER //
CREATE FUNCTION cek_kuota(eid INT) RETURNS BOOLEAN
BEGIN
  DECLARE sisa INT;
  SELECT kuota INTO sisa FROM events WHERE id_event = eid;
  RETURN sisa > 0;
END;
//
DELIMITER ;
