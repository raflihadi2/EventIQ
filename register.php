<?php
include "includes/db.php";
session_start();

$error = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password != $password2) {
        $error = "Password tidak sama";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter";
    } else {
        $cek = $conn->prepare("SELECT id_user FROM users WHERE email=?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $error = "Email sudah terdaftar";
        } else {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'pengguna')");
            $insert->bind_param("sss", $nama, $email, $pass_hash);
            if ($insert->execute()) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Gagal daftar, coba lagi";
            }
        }
        $cek->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Register - EventiQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Register</h2>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required minlength="6">
        </div>
        <div class="mb-3">
            <label>Ulangi Password</label>
            <input type="password" name="password2" class="form-control" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary">Daftar</button>
        <a href="login.php" class="btn btn-link">Sudah punya akun? Login</a>
    </form>
</body>
</html>
