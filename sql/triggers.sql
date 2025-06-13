DELIMITER //
CREATE TRIGGER kurangi_kuota
AFTER INSERT ON tickets
FOR EACH ROW
BEGIN
  UPDATE events SET kuota = kuota - NEW.jumlah_tiket
  WHERE id_event = NEW.id_event;
END;
//
DELIMITER ;
