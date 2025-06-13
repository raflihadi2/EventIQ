CREATE TABLE users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  email VARCHAR(100),
  password VARCHAR(100),
  role ENUM('admin','pengguna')
);

CREATE TABLE events (
  id_event INT AUTO_INCREMENT PRIMARY KEY,
  judul_event VARCHAR(100),
  kategori VARCHAR(50),
  lokasi VARCHAR(100),
  jadwal DATETIME,
  kuota INT,
  harga_tiket DECIMAL(10,2)
);

CREATE TABLE tickets (
  id_tiket INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT,
  id_event INT,
  jumlah_tiket INT,
  total_bayar DECIMAL(10,2),
  qr_code VARCHAR(255),
  status ENUM('pending', 'valid'),
  FOREIGN KEY (id_user) REFERENCES users(id_user),
  FOREIGN KEY (id_event) REFERENCES events(id_event)
);

CREATE TABLE validasi (
  id_validasi INT AUTO_INCREMENT PRIMARY KEY,
  id_tiket INT,
  waktu_validasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_tiket) REFERENCES tickets(id_tiket)
);
