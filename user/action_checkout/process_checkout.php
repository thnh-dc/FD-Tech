<?php
session_start();
require_once '../../config/database.php';
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id <= 0) {
    header("Location: ../../auth/login.php");
    exit();
}
$action = $_POST['action'] ?? '';
if ($action === 'confirm_bank_payment') {
    $pendingCheckout = $_SESSION['pending_bank_checkout'] ?? null;
    if (!$pendingCheckout) {
        header("Location: ../cart.php?error=no_payment_session");
        exit();
    }
    $address = $pendingCheckout['address'];
    $selectedItems = $pendingCheckout['selected_items'];
    $payment_method = 'bank';
    $payment_note = 'Chuyển khoản ngân hàng - Nội dung: ' . $pendingCheckout['payment_content'];
    $order_status = 'pending';
} else {
    $address = trim($_POST['address'] ?? '');
    $selectedItems = $_POST['selected_items'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'cod';
    if ($payment_method === 'bank') {
        if (empty($selectedItems)) {
            header("Location: ../cart.php?error=no_items");
            exit();
        }
        $payment_content = 'FD TECH - THANH TOAN DON HANG - ' . date('YmdHis');

        $_SESSION['pending_bank_checkout'] = [
            'selected_items' => $selectedItems,
            'address' => $address,
            'payment_content' => $payment_content
        ];
        header("Location:bank_payment.php");
        exit();
    }
    $payment_note = 'Thanh toán khi nhận hàng';
    $order_status = 'pending';
}
if (empty($selectedItems)) {
    header("Location: ../cart.php?error=no_items");
    exit();
}
$selectedArray = array_filter(explode(',', $selectedItems));

if (empty($selectedArray)) {
    header("Location: ../cart.php?error=no_items");
    exit();
}
$placeholders = implode(',', array_fill(0, count($selectedArray), '?'));

$stmt = $pdo->prepare("
    SELECT 
        c.product_id,
        c.quantity,
        p.price,
        c.id,
        p.name AS product_name,
        p.image_url AS product_image
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    AND c.id IN ($placeholders)
");
$params = array_merge([$user_id], $selectedArray);
$stmt->execute($params);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($cartItems)) {
    header("Location: ../cart.php?error=cart_empty");
    exit();
}
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("
        INSERT INTO orders(
            user_id,
            total_amount,
            status,
            shipping_address,
            note
        )
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        $total,
        $order_status,
        $address,
        $payment_note
    ]);
    $order_id = $pdo->lastInsertId();
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items(
            order_id,
            product_id,
            quantity,
            price,
            product_name,
            product_image
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    foreach ($cartItems as $item) {
        $stmtItem->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $item['product_name'],
            $item['product_image']
        ]);
    }
    $deletePlaceholders = implode(',', array_fill(0, count($selectedArray), '?'));
    $stmtDelete = $pdo->prepare("
        DELETE FROM cart_items
        WHERE user_id = ?
        AND id IN ($deletePlaceholders)
    ");
    $deleteParams = array_merge([$user_id], $selectedArray);
    $stmtDelete->execute($deleteParams);
    $pdo->commit();
    if ($payment_method === 'bank') {
        unset($_SESSION['pending_bank_checkout']);
    }
    header("Location: ../checkout.php?status=success&order_id=" . $order_id);
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Lỗi: " . $e->getMessage();
}
?>