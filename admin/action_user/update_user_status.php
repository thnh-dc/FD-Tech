<?php
session_start();
require_once '../../config/database.php';
require_once __DIR__ . '/../check_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../list_users.php");
    exit();
}

$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
$status = $_POST['status'] ?? '';

$allowed_status = ['active', 'blocked'];

if ($user_id <= 0 || !in_array($status, $allowed_status)) {
    $_SESSION['flash_msg'] = 'Dữ liệu không hợp lệ!';
    header("Location: ../list_users.php");
    exit();
}

try {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET status = ?
        WHERE id = ? AND role = 'user'
    ");
    $stmt->execute([$status, $user_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['flash_msg'] = $status === 'blocked'
            ? 'Đã khóa tài khoản người dùng!'
            : 'Đã mở khóa tài khoản người dùng!';
    } else {
        $_SESSION['flash_msg'] = 'Không tìm thấy người dùng hoặc trạng thái không thay đổi!';
    }

} catch (PDOException $e) {
    $_SESSION['flash_msg'] = 'Lỗi hệ thống khi cập nhật trạng thái người dùng!';
}

header("Location: ../list_users.php");
exit();