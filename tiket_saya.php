<?php
include 'includes/db.php';
include 'includes/auth.php';
require_login();

$id_user = $_SESSION['id_user'];

$res = $conn->prepare("SELECT t.*, e.judul_event FROM tickets t 
                       JOIN events e ON t.id_event = e.id_event 
                       WHERE t.id_user=? 
                       ORDER BY t.id_tiket DESC");
$res->bind_param("i", $id_user);
$res->execute();
$result = $res->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Saya - EventiQ</title>
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
    <h2 class="mb-4">Tiket Saya</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">Belum ada tiket yang dipesan.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Tiket</th>
                    <th>Event</th>
                    <th>Jumlah</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>QR Code</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_tiket'] ?></td>
                        <td><?= htmlspecialchars($row['judul_event']) ?></td>
                        <td><?= $row['jumlah_tiket'] ?></td> <!-- jika kolommu bernama 'jumlah' -->
                        <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                        <td>
                            <?php if (!empty($row['qr_code']) && file_exists($row['qr_code'])): ?>
                                <img src="<?= htmlspecialchars($row['qr_code']) ?>" alt="QR Code" style="width:100px;">
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <a href="bayar.php?id=<?= $row['id_tiket'] ?>" class="btn btn-sm btn-success">Bayar Sekarang</a>
                            <?php else: ?>
                                <span class="text-success">✓ Dibayar</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
