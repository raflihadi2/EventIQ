<?php
include '../koneksi.php';
include '../includes/auth.php';
require_login();

$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$data_laporan = [];

if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $tanggal_awal_full = $tanggal_awal . ' 00:00:00';
    $tanggal_akhir_full = $tanggal_akhir . ' 23:59:59';

    $query = "SELECT 
                e.judul_event,
                SUM(t.jumlah_tiket) AS tiket_terjual,
                SUM(t.total_bayar) AS pendapatan
              FROM tickets t
              JOIN events e ON t.id_event = e.id_event
              WHERE t.tanggal_pesan BETWEEN ? AND ?
              GROUP BY t.id_event";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $tanggal_awal_full, $tanggal_akhir_full);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data_laporan[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Tiket - EventiQ</title>
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

    <h2 class="mb-4">Laporan Penjualan Tiket</h2>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>" class="form-control" required>
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </form>

    <?php if (!empty($tanggal_awal) && !empty($tanggal_akhir)): ?>
        <?php if (empty($data_laporan)): ?>
            <div class="alert alert-warning">Tidak ada data untuk rentang tanggal tersebut.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Judul Event</th>
                        <th>Tiket Terjual</th>
                        <th>Total Pendapatan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_laporan as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['judul_event']) ?></td>
                            <td><?= $row['tiket_terjual'] ?></td>
                            <td>Rp <?= number_format($row['pendapatan'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <a href="../dashboard.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>

</body>
</html>
