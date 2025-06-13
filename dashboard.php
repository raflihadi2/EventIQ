<?php
include "includes/db.php";
include "includes/auth.php";
require_login();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard - EventiQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<nav class="mb-3">
    <span>Halo, <?= htmlspecialchars($_SESSION['nama']) ?> | </span>
    <a href="tiket_saya.php">Tiket Saya</a> |
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="admin/kelola_event.php">Kelola Event</a> |
        <a href="admin/validasi_tiket.php">Validasi Tiket</a> |
        <a href="admin/laporan.php">Laporan Penjualan</a> |
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>

<h2>Daftar Event</h2>
<div class="row">
<?php
$res = $conn->query("SELECT * FROM events WHERE kuota > 0 ORDER BY jadwal ASC");

if ($res->num_rows === 0): ?>
    <div class="alert alert-warning" role="alert">
        Belum ada event tersedia saat ini.
    </div>
<?php
else:
    while ($event = $res->fetch_assoc()):
?>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($event['judul_event']) ?></h5>
                <p class="card-text">
                    <strong>Kategori:</strong> <?= htmlspecialchars($event['kategori']) ?><br>
                    <strong>Lokasi:</strong> <?= htmlspecialchars($event['lokasi']) ?><br>
                    <strong>Jadwal:</strong> <?= date('d M Y H:i', strtotime($event['jadwal'])) ?><br>
                    <strong>Kuota Tersisa:</strong> <?= $event['kuota'] ?><br>
                    <strong>Harga:</strong> Rp <?= number_format($event['harga_tiket'],0,',','.') ?>
                </p>
                <a href="event_detail.php?id=<?= $event['id_event'] ?>" class="btn btn-primary">Pesan Tiket</a>
            </div>
        </div>
    </div>
<?php
    endwhile;
endif;
?>
</div>

</body>
</html>
