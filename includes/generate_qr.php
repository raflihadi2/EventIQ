<?php
require_once 'phpqrcode/qrlib.php'; // download from https://sourceforge.net/projects/phpqrcode/

function buatQR($text, $filename) {
    $folder = 'qrcodes/';
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
    $filepath = $folder . $filename . '.png';
    QRcode::png($text, $filepath, QR_ECLEVEL_L, 4);
    return $filepath;
}
?>
