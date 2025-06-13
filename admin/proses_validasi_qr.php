<?php
include 'includes/db.php';

if (!isset($_GET['kode'])) {
    echo "Kode QR tidak ditemukan.";
    exit;
}

$kode_qr = $_GET['kode'];

$stmt = $conn->prepare("SELECT t.*, e.judul_event FROM tickets t JOIN events e ON t.id_event = e.id_event WHERE t.qr_code = ?");
$stmt->bind_param("s", $kode_qr);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Tiket tidak ditemukan.";
    exit;
}

$tiket = $result->fetch_assoc();

if ($tiket['status'] !== 'paid') {
    echo "Tiket belum dibayar atau tidak valid.";
    exit;
}

// Jika lolos semua pengecekan, tampilkan data
echo "<h3>Tiket Valid</h3>";
echo "<p>Event: " . htmlspecialchars($tiket['judul_event']) . "</p>";
echo "<p>Jumlah Tiket: " . $tiket['jumlah_tiket'] . "</p>";
echo "<p>Status: " . $tiket['status'] . "</p>";
