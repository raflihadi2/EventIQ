<?php
include '../includes/db.php';
include '../includes/auth.php';
require_login();
require_admin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Validasi Tiket - Admin EventiQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
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
    <h2>Validasi Tiket (Scan QR Code)</h2>
    <a href="../dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <div id="qr-reader" style="width:500px;"></div>
    <div id="qr-result" class="mt-3"></div>

    <script>
    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('qr-result').innerHTML = `<b>QR Code terdeteksi:</b> ${decodedText}`;
        // Kirim ke server untuk validasi via fetch API
        fetch('validasi_tiket_proses.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ qr_code: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Validasi berhasil: " + data.message);
            } else {
                alert("Validasi gagal: " + data.message);
            }
            html5QrcodeScanner.clear(); // reset scanner
        });
    }
    function onScanError(error) {
        // bisa abaikan
    }
    let html5QrcodeScanner = new Html5Qrcode("qr-reader");
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        onScanSuccess,
        onScanError
    );
    </script>
</body>
</html>
