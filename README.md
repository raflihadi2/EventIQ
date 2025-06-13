# 🎫 EventIQ: Sistem Manajemen Tiket Event Berbasis Web

# 👨‍💻 Anggota Kelompok
##### M. Rafli Hadi (2217051166)
##### Dyandra Ayu KSP (2217051168)
##### Elgi Kurnia Sandi (2217051169)

## 🧾 Deskripsi Proyek
**EventIQ** adalah sistem pemesanan tiket berbasis web yang memungkinkan pengguna untuk memesan tiket, memvalidasi tiket menggunakan QR Code, dan admin dapat mengelola event serta laporan penjualan. Sistem ini dibangun menggunakan PHP native dan MySQL serta memenuhi seluruh ketentuan Ujian Akhir Praktik (UAP) yang ditetapkan.

![image](https://github.com/user-attachments/assets/61b8fa85-0d6d-4b6d-b181-fc2b48cc1734)

## 🔐 Fitur Utama
- Registrasi dan Login pengguna
- Daftar event & detail event
- Pemesanan tiket (otomatis generate QR code)
- Validasi tiket oleh admin
- Manajemen event & laporan
- Hak akses pengguna: `admin`, `pengguna`

## 💻 Teknologi yang Digunakan
- PHP (native)
- MySQL / MariaDB
- JavaScript, HTML, CSS
- QR Code Generator (phpqrcode)
- Task Scheduler (cronjob)
- GitHub untuk dokumentasi

## ✅ Implementasi Ketentuan UAP

| Fitur               | File / Struktur Implementasi               | Penjelasan                                                                 |
|---------------------|---------------------------------------------|----------------------------------------------------------------------------|
| **Transaction**     | `pesan_tiket` di `bayar.php`                | Membuat data pembelian + total + status atomik                            |
| **Procedure**       | `sql` - `CREATE PROCEDURE pesan_tiket`     | Prosedur pemesanan tiket dengan kalkulasi total otomatis                   |
| **Function**        | `sql` - `CREATE FUNCTION cek_kuota`        | Fungsi pengecekan ketersediaan tiket untuk event tertentu                  |
| **Trigger**         | `sql` - `CREATE TRIGGER kurangi_kuota`     | Mengurangi kuota event secara otomatis setelah insert data tiket           |
| **Backup**          | `sql/struktur.sql`                         | File dump database + struktur lengkap                                      |
| **Task Scheduler**  | `cron` (opsional script): `mysqldump`      | Backup otomatis terjadwal setiap hari/minggu melalui cronjob              |

## 🗃️ Struktur Folder

eventiq/

├── admin/ # Halaman dashboard admin

├── assets/ # CSS, JS

├── includes/ # QR Generator, koneksi, auth, dll.

├── sql/ # Berisi dump database, function, procedure

├── bayar.php # Proses transaksi

├── validasi.php # Proses validasi QR tiket

├── login.php, register.php, index.php, dll.

## 🧠 Stored Procedures
Stored procedure bertindak seperti SOP internal yang menetapkan alur eksekusi berbagai operasi penting di sistem pemesanan tiket ini. Procedure ini disimpan langsung di lapisan database, sehingga dapat menjamin konsistensi, efisiensi, dan keamanan eksekusi, terutama dalam sistem terdistribusi atau multi-user.
![image](https://github.com/user-attachments/assets/0ee0dfa1-fee2-48b4-9461-4eb958f551f9)


## 🧠 Database Detail

```sql
🔁 Stored Procedure
CREATE PROCEDURE pesan_tiket(IN uid INT, IN eid INT, IN jumlah INT)
BEGIN
  DECLARE harga DECIMAL(10,2);
  DECLARE total DECIMAL(10,2);

  SELECT harga_tiket INTO harga FROM events WHERE id_event = eid;
  SET total = harga * jumlah;

  INSERT INTO tickets(id_user, id_event, jumlah_tiket, total_bayar, status)
  VALUES (uid, eid, jumlah, total, 'pending');
END
🧮 Function
CREATE FUNCTION cek_kuota(eid INT) RETURNS TINYINT(1)
BEGIN
  DECLARE sisa INT;
  SELECT kuota INTO sisa FROM events WHERE id_event = eid;
  RETURN sisa > 0;
END
🔂 Trigger
CREATE TRIGGER kurangi_kuota AFTER INSERT ON tickets FOR EACH ROW
BEGIN
  UPDATE events SET kuota = kuota - NEW.jumlah_tiket
  WHERE id_event = NEW.id_event;
END
```
## 🚨 Trigger

Triggers `before_update_event_cek_kuota_harga` dan `before_insert_event_cek_kuota_harga` digunakan untuk mencegah hasil negatif saat tiket dimasukkan dan dibeli. Seperti palang pintu yang hanya terbuka jika syarat tertentu terpenuhi, trigger mencegah input data yang tidak valid atau berisiko merusak integritas sistem.
![image](https://github.com/user-attachments/assets/c8283608-b4bc-4b83-911a-5bf30efdda74)

## 🔓 Login & Role
Login: login.php

Role:

admin: Akses manajemen event, validasi, laporan

pengguna: Pemesanan tiket & riwayat

## ▶️ Cara Menjalankan
Clone/extract ke folder htdocs (XAMPP) atau www (Laragon)

Import database via phpMyAdmin dari sql/struktur.sql

Jalankan server lokal (localhost/eventiq)

📍 Penjelasan Highlight Ketentuan
| Ketentuan   | File Terkait       | Implementasi Singkat                                |
| ----------- | ------------------ | --------------------------------------------------- |
| Transaction | `bayar.php`        | Transaksi data tiket (insert + hitung total)        |
| Procedure   | `sql`              | `pesan_tiket` menyederhanakan proses pemesanan      |
| Function    | `sql`              | `cek_kuota` return boolean untuk ketersediaan tiket |
| Trigger     | `sql`              | `kurangi_kuota` update otomatis kuota setelah beli  |
| Backup      | `sql/struktur.sql` | File dump siap diimpor ulang                        |
| Scheduler   | Cron job           | `mysqldump` rutin otomatis setiap hari              |
