<?php
session_start();

function require_login() {
    if (!isset($_SESSION['id_user'])) {
        header("Location: login.php");
        exit();
    }
}

function require_admin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }
}
?>
