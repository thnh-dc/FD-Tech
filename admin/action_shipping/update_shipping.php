<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../shipping_orders.php");
    exit;
}

$order_id = (int)($_POST['order_id'] ?? 0);
$order_status = $_POST['order_status'] ?? '';

$allowed_status = ['preparing', 'shipped', 'delivered'];

if ($order_id <= 0 || !in_array($order_status, $allowed_status)) {
    header("Location: ../shipping_orders.php?error=" . urlencode("Dữ liệu không hợp lệ"));
    exit;
}

try {
    $pdo->beginTransaction();

    $stmtOrder = $pdo->prepare("
        SELECT id, user_id, total_amount, status
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

    if ($old_status === 'processing') {
        throw new Exception('Đơn hàng chưa được chuyển cho đơn vị vận chuyển nên không thể cập nhật vận chuyển.');
    }

    if ($old_status === 'cancelled') {
        throw new Exception('Đơn hàng đã hủy, không thể cập nhật vận chuyển.');
    }

    if ($old_status === 'completed') {
        throw new Exception('Đơn hàng đã hoàn thành, không thể cập nhật vận chuyển.');
    }

    if ($old_status !== 'shipped') {
        throw new Exception('Chỉ có đơn hàng đã chuyển cho đơn vị vận chuyển mới được cập nhật vận chuyển.');
    }

    if ($order_status === 'preparing') {
        $new_order_status = 'shipped';
        $shipping_status = 'preparing';
        $delivered_at = null;
    } elseif ($order_status === 'shipped') {
        $new_order_status = 'shipped';
        $shipping_status = 'shipping';
        $delivered_at = null;
    } else {
        $new_order_status = 'shipped';
        $shipping_status = 'delivered';
        $delivered_at = date('Y-m-d H:i:s');
    }

    $stmtShipping = $pdo->prepare("
        SELECT id, carrier_name, tracking_number, shipping_cost, estimated_delivery
        FROM order_shipping
        WHERE order_id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmtShipping->execute([$order_id]);
    $shipping = $stmtShipping->fetch(PDO::FETCH_ASSOC);

    if ($shipping) {
        $stmtUpdateShipping = $pdo->prepare("
            UPDATE order_shipping
            SET shipping_status = ?,
                delivered_at = ?,
                updated_at = NOW()
            WHERE order_id = ?
        ");

        $stmtUpdateShipping->execute([
            $shipping_status,
            $delivered_at,
            $order_id
        ]);
    } else {
        $carrier_name = 'FD Express';
        $tracking_number = 'FDX' . date('ymd') . str_pad($order_id, 6, '0', STR_PAD_LEFT);
        $shipping_cost = 25000;
        $estimated_delivery = date('Y-m-d', strtotime('+4 days'));

        $stmtInsertShipping = $pdo->prepare("
            INSERT INTO order_shipping (
                order_id,
                shipping_status,
                carrier_name,
                tracking_number,
                shipping_cost,
                estimated_delivery,
                delivered_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtInsertShipping->execute([
            $order_id,
            $shipping_status,
            $carrier_name,
            $tracking_number,
            $shipping_cost,
            $estimated_delivery,
            $delivered_at
        ]);
    }

    $stmtUpdateOrder = $pdo->prepare("
        UPDATE orders
        SET status = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmtUpdateOrder->execute([
        $new_order_status,
        $order_id
    ]);

    $pdo->commit();

    if ($order_status === 'preparing') {
        $msg = 'Đơn hàng đang được chuẩn bị giao cho đơn vị vận chuyển';
    } elseif ($order_status === 'shipped') {
        $msg = 'Đơn hàng đã chuyển sang trạng thái đang giao hàng';
    } else {
        $msg = 'Đơn vị vận chuyển đã giao hàng thành công. Vui lòng xác nhận hoàn thành ở trang quản lí đơn hàng.';
    }

    header("Location: ../shipping_orders.php?msg=" . urlencode($msg));
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: ../shipping_orders.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>