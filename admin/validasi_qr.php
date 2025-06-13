<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') die('Akses ditolak');

if (!isset($_GET['q'])) {
  echo "Data QR tidak valid.";
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Validasi Tiket QR - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="container mt-5">
  <h2>Validasi Tiket via QR</h2>
  <div id="reader" style="width:400px;"></div>
  <div id="result" class="mt-3"></div>
  <a href="../dashboard.php" class="btn btn-secondary mt-3">Kembali</a>

  <script>
    function onScanSuccess(decodedText) {
      document.getElementById('result').innerHTML = "Memeriksa tiket...";
      fetch('proses_validasi_qr.php?q=' + encodeURIComponent(decodedText))
        .then(response => response.text())
        .then(data => {
          document.getElementById('result').innerHTML = data;
          if(data.includes("✅")) {
            // stop scanner setelah berhasil
            html5QrcodeScanner.clear();
          }
        });
    }

    function onScanFailure(error) {
      //console.warn(`Scan failed: ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
  </script>
</body>
</html>
