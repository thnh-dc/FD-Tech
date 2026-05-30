<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../check_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức truy cập không hợp lệ.']);
    exit;
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$carrier_name = trim($_POST['carrier_name'] ?? '');
$tracking_number = trim($_POST['tracking_number'] ?? '');
$shipping_cost = isset($_POST['shipping_cost']) ? (float)$_POST['shipping_cost'] : 0.00;
$estimated_delivery = !empty($_POST['estimated_delivery']) ? $_POST['estimated_delivery'] : null;
$notes = trim($_POST['notes'] ?? '');

if ($order_id <= 0 || empty($carrier_name) || empty($tracking_number)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ Đơn vị vận chuyển và Mã vận đơn!']);
    exit;
}

try {
    // Kiểm tra xem đơn hàng đã có thông tin vận chuyển trước đó chưa
    $stmt = $pdo->prepare("SELECT id FROM order_shipping WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Nếu đã có thì tiến hành cập nhật bản ghi cũ
        $sql = "UPDATE order_shipping 
                SET carrier_name = ?, tracking_number = ?, shipping_cost = ?, estimated_delivery = ?, notes = ? 
                WHERE order_id = ?";
        $stmt_exec = $pdo->prepare($sql);
        $stmt_exec->execute([$carrier_name, $tracking_number, $shipping_cost, $estimated_delivery, $notes, $order_id]);
    } else {
        // Nếu chưa có thì chèn bản ghi mới hoàn toàn vào bảng vận chuyển
        $sql = "INSERT INTO order_shipping (order_id, carrier_name, tracking_number, shipping_cost, estimated_delivery, notes) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_exec = $pdo->prepare($sql);
        $stmt_exec->execute([$order_id, $carrier_name, $tracking_number, $shipping_cost, $estimated_delivery, $notes]);
    }

    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()]);
}
exit;