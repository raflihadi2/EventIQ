<?php
include '../includes/db.php';
include '../includes/auth.php';
require_login();
require_admin();

$res = $conn->query("SELECT e.judul_event, SUM(t.jumlah_tiket) as total_tiket, SUM(t.total_bayar) as total_pendapatan 
                     FROM tickets t 
                     JOIN events e ON t.id_event = e.id_event 
                     GROUP BY t.id_event 
                     ORDER BY total_pendapatan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
</head>
<body>
    <h2>Laporan Penjualan Tiket</h2>
    <p><a href="../dashboard.php">← Kembali ke Dashboard</a></p>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Judul Event</th>
                <th>Total Tiket Terjual</th>
                <th>Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['judul_event']) ?></td>
                    <td><?= $row['total_tiket'] ?></td>
                    <td><?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
