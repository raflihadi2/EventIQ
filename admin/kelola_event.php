<?php
include '../includes/db.php';
include '../includes/auth.php';
require_login();
require_admin();

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $lokasi = trim($_POST['lokasi']);
    $jadwal = $_POST['jadwal'];
    $kuota = intval($_POST['kuota']);
    $harga = intval($_POST['harga']);
    if ($judul == "" || $kategori == "" || $lokasi == "" || $jadwal == "" || $kuota <= 0 || $harga <= 0) {
        $error = "Semua field harus diisi dengan benar";
    } else {
        $stmt = $conn->prepare("INSERT INTO events (judul_event, kategori, lokasi, jadwal, kuota, harga_tiket) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $judul, $kategori, $lokasi, $jadwal, $kuota, $harga);
        if ($stmt->execute()) {
            header("Location: kelola_event.php");
            exit();
        } else {
            $error = "Gagal tambah event";
        }
    }
}

$events = $conn->query("SELECT * FROM events ORDER BY jadwal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Event - Admin EventiQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <nav class="mb-3">
    <span>Halo, <?= htmlspecialchars($_SESSION['nama']) ?> | </span>
    <a href="tiket_saya.php">Tiket Saya</a> |
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="kelola_event.php">Kelola Event</a> |
        <a href="validasi_tiket.php">Validasi Tiket</a> |
        <a href="laporan.php">Laporan Penjualan</a> |
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>
    <h2>Kelola Event</h2>
    <a href="../dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4">
        <div class="mb-3">
            <label>Judul Event</label>
            <input type="text" name="judul" class="form-control" required>
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
            <input type="number" name="harga" class="form-control" min="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Event</button>
    </form>

    <h3>Daftar Event</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Event</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Jadwal</th>
                <th>Kuota</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php while($ev = $events->fetch_assoc()): ?>
                <tr>
                    <td><?= $ev['id_event'] ?></td>
                    <td><?= htmlspecialchars($ev['judul_event']) ?></td>
                    <td><?= htmlspecialchars($ev['kategori']) ?></td>
                    <td><?= htmlspecialchars($ev['lokasi']) ?></td>
                    <td><?= date('d M Y H:i', strtotime($ev['jadwal'])) ?></td>
                    <td><?= $ev['kuota'] ?></td>
                    <td>Rp <?= number_format($ev['harga_tiket'],0,',','.') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
