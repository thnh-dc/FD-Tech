<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /FD-Tech/auth/login.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: /FD-Tech/user/index.php");
    exit();
}
?>