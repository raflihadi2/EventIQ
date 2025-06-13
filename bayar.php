<?php
include 'includes/db.php';
include 'includes/auth.php';
require_login();

if (!isset($_GET['id'])) {
    echo "ID tiket tidak valid.";
    exit;
}

$id_tiket = intval($_GET['id']);
$id_user = $_SESSION['id_user'];

// Ambil data tiket
$stmt = $conn->prepare("SELECT * FROM tickets WHERE id_tiket = ? AND id_user = ? AND status = 'pending'");
$stmt->bind_param("ii", $id_tiket, $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Tiket tidak ditemukan atau sudah dibayar.";
    exit;
}

$tiket = $result->fetch_assoc();
$id_event = $tiket['id_event'];
$jumlah = $tiket['jumlah_tiket'];

// Ambil data event untuk harga dan kuota
$stmt_event = $conn->prepare("SELECT harga_tiket, kuota FROM events WHERE id_event = ?");
$stmt_event->bind_param("i", $id_event);
$stmt_event->execute();
$result_event = $stmt_event->get_result();
$event = $result_event->fetch_assoc();

$harga = $event['harga_tiket'];
$kuota = $event['kuota'];
$total_bayar = $harga * $jumlah;

// Cek apakah kuota cukup
if ($jumlah > $kuota) {
    echo "Stok tiket tidak mencukupi.";
    exit;
}

// Update tiket jadi 'valid' + isi total_bayar
$stmt = $conn->prepare("UPDATE tickets SET status='valid', total_bayar=? WHERE id_tiket=?");
$stmt->bind_param("di", $total_bayar, $id_tiket);
$stmt->execute();

// Generate QR code
include 'includes/generate_qr.php';
$qr_text = "TIKET-ID-$id_tiket";
$qr_filename = "tiket_$id_tiket";
$qr_path = buatQR($qr_text, $qr_filename);

// Simpan path QR code ke database
$stmt = $conn->prepare("UPDATE tickets SET qr_code=? WHERE id_tiket=?");
$stmt->bind_param("si", $qr_path, $id_tiket);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Berhasil</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
        .btn-kembali {
            display: inline-block;
            padding: 6px 12px;
            background-color: #ccc;
            color: #000;
            text-decoration: none;
            border: 1px solid #999;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h2>Pembayaran Berhasil</h2>
    <p>Tiket Anda telah berhasil dibayar.</p>
    <p><strong>Total Bayar:</strong> Rp <?= number_format($total_bayar, 0, ',', '.') ?></p>
    <p><strong>QR Code:</strong></p>
    <img src="<?= htmlspecialchars($qr_path) ?>" alt="QR Code Tiket" width="150">
    
    <br><br>
    <a href="tiket_saya.php" class="btn-kembali">Kembali ke Tiket Saya</a>
</body>
</html>

