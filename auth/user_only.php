<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    echo "Bạn đang đăng nhập bằng tài khoản admin. Vui lòng đăng xuất để truy cập trang người dùng.";
    exit();
}
?>