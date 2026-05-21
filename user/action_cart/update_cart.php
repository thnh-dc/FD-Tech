<?php
session_start();
require_once '../../config/database.php';
header('Content-Type: application/json; charset=utf-8');
$user_id = $_SESSION['user_id'] ?? 0;
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$change = isset($_POST['change']) ? (int)$_POST['change'] : 0;
if ($user_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.'
    ]);
    exit;
}
if ($id <= 0 || !in_array($change, [-1, 1])) {
    echo json_encode([
        'success' => false,
        'message' => 'Dữ liệu không hợp lệ.'
    ]);
    exit;
}
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id, c.quantity, c.product_id, p.stock_quantity, p.price, p.discount_price,
            COALESCE(NULLIF(p.discount_price, 0), p.price) AS display_price
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.id = ?
        AND c.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$id, $user_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'
        ]);
        exit;
    }
    $newQty = $item['quantity'] + $change;
    if ($newQty < 1) {
        $newQty = 1;
    }
    if ($newQty > $item['stock_quantity']) {
        echo json_encode([
            'success' => false,
            'message' => 'Số lượng vượt quá tồn kho hiện có.'
        ]);
        exit;
    }
    $update = $pdo->prepare("
        UPDATE cart_items 
        SET quantity = ? 
        WHERE id = ? 
        AND user_id = ?
    ");
    $update->execute([$newQty, $id, $user_id]);
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật số lượng thành công.',
        'quantity' => $newQty,
        'price' => $item['display_price'],
        'subtotal' => $newQty * $item['display_price']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống, không thể cập nhật số lượng.'
    ]);
}
?>