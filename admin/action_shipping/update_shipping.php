<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../check_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../shipping_orders.php");
    exit;
}

$order_id = (int)($_POST['order_id'] ?? 0);
$order_status = $_POST['order_status'] ?? '';
$carrier_name = trim($_POST['carrier_name'] ?? '');
$tracking_number = trim($_POST['tracking_number'] ?? '');
$shipping_cost = (float)($_POST['shipping_cost'] ?? 0);
$estimated_delivery = $_POST['estimated_delivery'] ?? null;
$notes = trim($_POST['notes'] ?? '');

$allowed_status = ['processing', 'shipped', 'completed', 'cancelled'];

if ($order_id <= 0 || !in_array($order_status, $allowed_status) || $carrier_name === '' || $tracking_number === '') {
    header("Location: ../shipping_orders.php?error=Dữ liệu không hợp lệ");
    exit;
}

if ($estimated_delivery === '') {
    $estimated_delivery = null;
}

try {
    $pdo->beginTransaction();

    $stmtCheck = $pdo->prepare("SELECT id FROM order_shipping WHERE order_id = ?");
    $stmtCheck->execute([$order_id]);
    $shipping_id = $stmtCheck->fetchColumn();

    if ($shipping_id) {
        $stmt = $pdo->prepare("
            UPDATE order_shipping
            SET carrier_name = ?,
                tracking_number = ?,
                shipping_cost = ?,
                estimated_delivery = ?,
                notes = ?
            WHERE order_id = ?
        ");

        $stmt->execute([
            $carrier_name,
            $tracking_number,
            $shipping_cost,
            $estimated_delivery,
            $notes,
            $order_id
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO order_shipping
            (order_id, carrier_name, tracking_number, shipping_cost, estimated_delivery, notes)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $order_id,
            $carrier_name,
            $tracking_number,
            $shipping_cost,
            $estimated_delivery,
            $notes
        ]);
    }

    $stmtOrder = $pdo->prepare("
        UPDATE orders
        SET status = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmtOrder->execute([
        $order_status,
        $order_id
    ]);

    $pdo->commit();

    header("Location: ../shipping_orders.php?msg=Cập nhật vận chuyển thành công");
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("Lỗi cập nhật vận chuyển: " . $e->getMessage());
}