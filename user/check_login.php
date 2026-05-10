<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Oppss, bạn chưa đăng nhập rồi!');
            window.location.href = '../auth/login.php';
          </script>";
    exit();
}