<?php
require_once '../config/database.php';

// Xử lý PHP Backend: $_GET
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1"; 
$params = [];

if ($search !== '') {
    $sql .= " AND p.name LIKE :search";
    $params[':search'] = "%{$search}%";
}
if ($category_id > 0) {
    $sql .= " AND p.category_id = :category";
    $params[':category'] = $category_id;
}
$sql .= " ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catStmt = $conn->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>
