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

$order_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$new_status = $_POST['status'] ?? '';

$allowed = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

$statusText = [
    'pending' => 'Chờ xác nhận',
    'processing' => 'Đang xử lý',
    'shipped' => 'Đang giao hàng',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy'
];

if ($order_id <= 0 || !in_array($new_status, $allowed)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dữ liệu cập nhật không hợp lệ.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmtOrder = $pdo->prepare("
        SELECT 
            id,
            user_id,
            total_amount,
            status,
            used_points
        FROM orders
        WHERE id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmtOrder->execute([$order_id]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Không tìm thấy đơn hàng.');
    }

    $old_status = $order['status'];
    $user_id = (int) $order['user_id'];
    $total_amount = (float) $order['total_amount'];
    $used_points = (int)($order['used_points'] ?? 0);

    if ($old_status === $new_status) {
        throw new Exception('Trạng thái đơn hàng không thay đổi.');
    }

    if ($old_status === 'cancelled' && $new_status !== 'cancelled') {
        throw new Exception('Đơn hàng đã hủy không thể chuyển sang trạng thái khác.');
    }

    if ($old_status === 'completed' && $new_status === 'cancelled') {
        throw new Exception('Đơn hàng đã hoàn thành không thể hủy.');
    }

    if ($old_status === 'completed' && $new_status !== 'completed') {
        throw new Exception('Đơn hàng đã hoàn thành không thể chuyển ngược trạng thái.');
    }

    $refunded_point = 0;

    /*
        Nếu admin hủy đơn:
        - Hoàn lại tồn kho
        - Hoàn lại điểm đã dùng nếu orders.used_points > 0
    */
    if ($new_status === 'cancelled' && $old_status !== 'cancelled') {
        $stmtItems = $pdo->prepare("
            SELECT product_id, quantity
            FROM order_items
            WHERE order_id = ?
        ");
        $stmtItems->execute([$order_id]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            throw new Exception('Không tìm thấy chi tiết sản phẩm của đơn hàng.');
        }

        $stmtReturnStock = $pdo->prepare("
            UPDATE products
            SET stock_quantity = stock_quantity + ?
            WHERE id = ?
        ");

        foreach ($items as $item) {
            $stmtReturnStock->execute([
                (int) $item['quantity'],
                (int) $item['product_id']
            ]);
        }

        if ($used_points > 0) {
            $stmtRefundPoint = $pdo->prepare("
                UPDATE users
                SET point = COALESCE(point, 0) + ?
                WHERE id = ?
            ");
            $stmtRefundPoint->execute([$used_points, $user_id]);

            $refunded_point = $used_points;
        }
    }

    /*
        Tích điểm khi đơn chuyển sang Hoàn thành.
        Tỷ lệ: 10.000đ = 1 điểm.
    */
    $earned_point = 0;

    if ($new_status === 'completed' && $old_status !== 'completed') {
        $money_per_point = 10000;
        $earned_point = (int) floor($total_amount / $money_per_point);

        if ($earned_point > 0) {
            $stmtAddPoint = $pdo->prepare("
                UPDATE users
                SET point = COALESCE(point, 0) + ?
                WHERE id = ?
            ");
            $stmtAddPoint->execute([$earned_point, $user_id]);
        }
    }

    $note_append = '';

    if ($new_status === 'cancelled') {
        $note_append = ' | Admin hủy đơn';

        if ($refunded_point > 0) {
            $note_append .= ', đã hoàn lại ' . $refunded_point . ' FDp';
        }
    }

    $stmtUpdate = $pdo->prepare("
        UPDATE orders
        SET 
            status = ?,
            note = CONCAT(IFNULL(note, ''), ?),
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmtUpdate->execute([
        $new_status,
        $note_append,
        $order_id
    ]);

    $pdo->commit();

    $message = 'Đã cập nhật đơn hàng sang trạng thái: ' . $statusText[$new_status];

    if ($earned_point > 0) {
        $message .= '. Người dùng được cộng ' . $earned_point . ' điểm tiêu dùng.';
    }

    if ($refunded_point > 0) {
        $message .= '. Đã hoàn lại ' . $refunded_point . ' điểm tiêu dùng.';
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'status' => $new_status,
        'status_text' => $statusText[$new_status],
        'earned_point' => $earned_point,
        'refunded_point' => $refunded_point
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>