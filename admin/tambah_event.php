<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') die('Akses ditolak');

if (!isset($_GET['q'])) {
  echo "Data QR tidak valid.";
  exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $judul = $conn->real_escape_string($_POST['judul_event']);
  $kategori = $conn->real_escape_string($_POST['kategori']);
  $lokasi = $conn->real_escape_string($_POST['lokasi']);
  $jadwal = $conn->real_escape_string($_POST['jadwal']);
  $kuota = intval($_POST['kuota']);
  $harga = intval($_POST['harga_tiket']);

  if (empty($judul) || empty($kategori) || empty($lokasi) || empty($jadwal) || $kuota < 1 || $harga < 1) {
    $error = "Semua field harus diisi dengan benar";
  } else {
    $conn->query("INSERT INTO events (judul_event, kategori, lokasi, jadwal, kuota, harga_tiket) VALUES ('$judul', '$kategori', '$lokasi', '$jadwal', $kuota, $harga)");
    header("Location: kelola_event.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Tambah Event - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Tambah Event Baru</h2>
  <?php if($error): ?>
  <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="post">
    <div class="mb-3">
      <label>Judul Event</label>
      <input type="text" name="judul_event" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Kategori</label>
      <input type="text" name="kategori" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Lokasi</label>
      <input type="text" name="lokasi" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Jadwal</label>
      <input type="datetime-local" name="jadwal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Kuota</label>
      <input type="number" name="kuota" class="form-control" min="1" required>
    </div>
    <div class="mb-3">
      <label>Harga Tiket (Rp)</label>
      <input type="number" name="harga_tiket" class="form-control" min="1" required>
    </div>
    <button type="submit" class="btn btn-primary">Tambah Event</button>
  </form>
  <a href="kelola_event.php" class="btn btn-secondary mt-3">Kembali</a>
</body>
</html>
