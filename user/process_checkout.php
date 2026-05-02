<?php
session_start();
require_once '../config/database.php';

$user_id = $_SESSION['user_id'] ?? 0;

// lấy dữ liệu form
$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method'];

// lấy giỏ hàng
$stmt = $pdo->prepare("
    SELECT c.product_id, c.quantity, p.price
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// tính tổng
$total = 0;
foreach($cartItems as $item){
    $total += $item['price'] * $item['quantity'];
}

// bắt đầu transaction
$pdo->beginTransaction();

try {

    // 1. lưu orders
    $stmt = $pdo->prepare("
        INSERT INTO orders(user_id, total_amount, shipping_address)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $total, $address]);

    $order_id = $pdo->lastInsertId();

    // 2. lưu order_items
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items(order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    foreach($cartItems as $item){
        $stmtItem->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    // 3. xoá giỏ hàng
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // commit
    $pdo->commit();

    // redirect thành công
    header("Location: checkout.php?status=success");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Lỗi: " . $e->getMessage();
}