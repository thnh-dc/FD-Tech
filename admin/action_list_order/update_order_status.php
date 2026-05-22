<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../check_admin.php';

header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không hợp lệ.'
    ]);
    exit;
}
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status = $_POST['status'] ?? '';
$allowed = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
$statusText = [
    'pending' => 'Chờ xác nhận',
    'processing' => 'Đang xử lý',
    'shipped' => 'Đang giao hàng',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy'
];
if ($id <= 0 || !in_array($status, $allowed)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dữ liệu cập nhật không hợp lệ.'
    ]);
    exit;
}
try {
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$status, $id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Đã cập nhật đơn hàng sang trạng thái: ' . $statusText[$status],
            'status' => $status,
            'status_text' => $statusText[$status]
        ]);
        exit;
    }
    echo json_encode([
        'success' => false,
        'message' => 'Trạng thái đơn hàng không thay đổi hoặc không tìm thấy đơn hàng.'
    ]);
    exit;
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống, không thể cập nhật trạng thái đơn hàng.'
    ]);
    exit;
}