<?php
include 'includes/db.php';
include 'includes/auth.php';
require_login();

// Jika 'id' tidak ada di URL, alihkan ke dashboard
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_event = intval($_GET['id']);

// Menggunakan prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT * FROM events WHERE id_event = ?");
$stmt->bind_param("i", $id_event);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// Jika event tidak ditemukan, tampilkan pesan dan hentikan script
if (!$event) {
    // Anda bisa membuat halaman error yang lebih baik
    die("Event tidak ditemukan.");
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah = intval($_POST['jumlah_tiket']);
    $id_user = $_SESSION['id_user'];

    // Cek apakah user sudah punya tiket pending untuk event ini
    $cek_stmt = $conn->prepare("SELECT * FROM tickets WHERE id_user=? AND id_event=? AND status='pending'");
    $cek_stmt->bind_param("ii", $id_user, $id_event);
    $cek_stmt->execute();
    $cek_result = $cek_stmt->get_result();

    if ($cek_result->num_rows > 0) {
        $error_message = "Anda sudah memiliki pesanan yang belum dibayar untuk event ini.";
    } elseif ($event['kuota'] < $jumlah) {
        $error_message = "Kuota tidak mencukupi. Sisa kuota saat ini: " . $event['kuota'];
    } else {
        $harga_total = $event['harga_tiket'] * $jumlah;
        $qr = uniqid(); // Generate QR code unik

        $conn->begin_transaction();
        try {
            // Masukkan data tiket baru
            $insert_stmt = $conn->prepare("INSERT INTO tickets (id_user, id_event, jumlah_tiket, total_bayar, status, qr_code) VALUES (?, ?, ?, ?, 'pending', ?)");
            $insert_stmt->bind_param("iiids", $id_user, $id_event, $jumlah, $harga_total, $qr);
            $insert_stmt->execute();

            // Update kuota event
            $update_stmt = $conn->prepare("UPDATE events SET kuota = kuota - ? WHERE id_event = ?");
            $update_stmt->bind_param("ii", $jumlah, $id_event);
            $update_stmt->execute();
            
            $conn->commit();

            // Alihkan ke halaman tiket saya setelah berhasil
            header("Location: tiket_saya.php?pesan=berhasil");
            exit;
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $error_message = "Terjadi kesalahan saat memproses pesanan Anda.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event - <?= htmlspecialchars($event['judul_event']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<nav class="mb-4">
    <span>Halo, <?= htmlspecialchars($_SESSION['nama']) ?> | </span>
    <a href="dashboard.php">Dashboard</a> |
    <a href="tiket_saya.php">Tiket Saya</a> |
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="admin/kelola_event.php">Kelola Event</a> |
        <a href="admin/validasi_tiket.php">Validasi Tiket</a> |
        <a href="admin/laporan.php">Laporan Penjualan</a> |
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>

<h2 class="mb-3">Detail Event</h2>

<?php if ($error_message): ?>
    <div class="alert alert-danger"><?= $error_message ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($event['judul_event']) ?></h5>
        <p class="card-text">
            <strong>Kategori:</strong> <?= htmlspecialchars($event['kategori']) ?><br>
            <strong>Lokasi:</strong> <?= htmlspecialchars($event['lokasi']) ?><br>
            <strong>Jadwal:</strong> <?= date('d M Y H:i', strtotime($event['jadwal'])) ?><br>
            <strong>Harga:</strong> Rp <?= number_format($event['harga_tiket'], 0, ',', '.') ?><br>
            <strong>Sisa Kuota:</strong> <?= $event['kuota'] ?>
        </p>

        <?php if ($event['kuota'] > 0): ?>
        <form method="post" class="mt-4">
            <div class="mb-3">
                <label for="jumlah_tiket" class="form-label">Jumlah Tiket:</label>
                <input type="number" id="jumlah_tiket" name="jumlah_tiket" class="form-control" style="width: 150px;" value="1" min="1" max="<?= $event['kuota'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" onclick="this.disabled=true; this.form.submit();">Pesan Tiket</button>
        </form>
        <?php else: ?>
        <p class="text-danger mt-4"><strong>Mohon maaf, tiket untuk event ini sudah habis.</strong></p>
        <?php endif; ?>
    </div>
</div>

<a href="dashboard.php" class="btn btn-secondary mt-3">Kembali ke Daftar Event</a>

</body>
</html>