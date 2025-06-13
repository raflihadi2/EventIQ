DELIMITER //
CREATE PROCEDURE pesan_tiket(IN uid INT, IN eid INT, IN jumlah INT)
BEGIN
  DECLARE harga DECIMAL(10,2);
  DECLARE total DECIMAL(10,2);

  SELECT harga_tiket INTO harga FROM events WHERE id_event = eid;
  SET total = harga * jumlah;

  INSERT INTO tickets(id_user, id_event, jumlah_tiket, total_bayar, status)
  VALUES (uid, eid, jumlah, total, 'pending');
END;
//
DELIMITER ;
