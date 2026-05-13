<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /FD-Tech/auth/login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    echo "Bạn không phải admin";
    exit();
}
?>