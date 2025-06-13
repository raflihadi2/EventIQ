<?php
include '../includes/db.php';
include '../includes/auth.php';
require_login();
require_admin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['qr_code'])) {
    echo json_encode(['success' => false, 'message' => 'QR Code tidak ditemukan']);
    exit;
}

$qr_code = $conn->real_escape_string($data['qr_code']);
$res = $conn->query("SELECT * FROM tickets WHERE qr_code='$qr_code'");

if ($res->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Tiket tidak valid']);
    exit;
}

$tiket = $res->fetch_assoc();
if ($tiket['status'] == 'used') {
    echo json_encode(['success' => false, 'message' => 'Tiket sudah pernah digunakan']);
    exit;
}

$update = $conn->query("UPDATE tickets SET status='used' WHERE id_tiket=" . $tiket['id_tiket']);
if ($update) {
    echo json_encode(['success' => true, 'message' => 'Tiket valid dan sudah digunakan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update status tiket']);
}
?>
