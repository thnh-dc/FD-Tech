<?php
// Bắt đầu session để có thể thao tác với dữ liệu đăng nhập
session_start();

// Xóa tất cả các biến session (xóa thông tin người dùng đang đăng nhập)
session_unset();

// Hủy toàn bộ session
session_destroy();

// Chuyển hướng người dùng về trang chủ
// Lưu ý: Nếu trang chủ của bạn tên khác hoặc nằm ở thư mục khác, bạn nhớ đổi "../index.php" cho đúng nhé
header("Location: ../user/index.php");
exit();
?>