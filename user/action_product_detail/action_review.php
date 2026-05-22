<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 1;

if ($product_id === 0 || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không đầy đủ.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, user_id, rating, comment) VALUES (:product_id, :user_id, :rating, :comment)");
    $stmt->execute([
        'product_id' => $product_id,
        'user_id' => $user_id,
        'rating' => $rating,
        'comment' => $comment
    ]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi lưu trữ dữ liệu.']);
}