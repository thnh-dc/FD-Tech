<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/config/database.php');

$id = $_GET['id'] ?? 0;

if(!$id){
    header("Location: list.php");
    exit;
}

$stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if($product && !empty($product['image'])){
    $file = $_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/assets/images/' . $product['image'];
    if(file_exists($file)){
        unlink($file);
    }
}

$stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
$stmt->execute([$id]);

header("Location: list.php?msg=Đã xóa!");
exit;