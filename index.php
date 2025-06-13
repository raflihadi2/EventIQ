<?php
include 'includes/db.php';
session_start();
if (isset($_SESSION['id_user'])) {
  header("Location: dashboard.php");
  exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $conn->real_escape_string($_POST['email']);
  $password = $_POST['password'];

  $result = $conn->query("SELECT * FROM users WHERE email='$email'");
  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['id_user'] = $user['id_user'];
      $_SESSION['nama'] = $user['nama'];
      $_SESSION['role'] = $user['role'];
      header("Location: dashboard.php");
      exit();
    } else {
      $error = "Password salah!";
    }
  } else {
    $error = "Email tidak ditemukan!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Login - EventiQ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Login EventiQ</h2>
  <?php if($error): ?>
  <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="post">
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
    <p class="mt-2">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
  </form>
</body>
</html>
