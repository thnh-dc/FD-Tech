<?php
require_once '../config/database.php';

// 1. Kiểm tra ID sản phẩm có hợp lệ không
if (!isset($_GET['id'])) { 
    header("Location: product_list.php"); 
    exit(); 
}

$id = (int)$_GET['id'];

// 2. Truy vấn chi tiết sản phẩm và tên danh mục
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = :id";

$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Nếu sản phẩm không tồn tại, quay về trang danh sách
if (!$product) { 
    header("Location: product_list.php"); 
    exit(); 
}

// 4. Nhúng file giao diện
include 'views/product_detail_view.php';
?>