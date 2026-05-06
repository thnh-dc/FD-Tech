<?php
include '../config/database.php';

$id = $_GET['id'] ?? 0;

if($id){
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$id]);
}

header("Location: list_product.php?msg=Đã xóa thành công");
exit;