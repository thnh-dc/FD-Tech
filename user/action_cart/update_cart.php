<?php
require_once '../../config/database.php';

$id = $_POST['id'];
$change = $_POST['change'];

// lấy số lượng hiện tại
$stmt = $pdo->prepare("SELECT quantity, product_id FROM cart_items WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();

$newQty = $item['quantity'] + $change;
if($newQty < 1) $newQty = 1;

// cập nhật
$update = $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=?");
$update->execute([$newQty, $id]);

// lấy giá
$stmt2 = $pdo->prepare("SELECT price FROM products WHERE id=?");
$stmt2->execute([$item['product_id']]);
$price = $stmt2->fetchColumn();

echo json_encode([
    'success' => true,
    'quantity' => $newQty,
    'subtotal' => number_format($price * $newQty)
]);