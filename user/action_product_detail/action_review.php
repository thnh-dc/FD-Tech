<?php
session_start();
require_once '../../config/database.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không hợp lệ.'
    ]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['noti_message'] = 'Oppss, bạn chưa đăng nhập rồi!';
    $_SESSION['noti_type'] = 'error';
    echo json_encode([
        'success' => false,
        'message' => 'Oppss, bạn chưa đăng nhập rồi!',
        'redirect' => '/FD-Tech/auth/login.php'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 5;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Sản phẩm không hợp lệ.'
    ]);
    exit;
}
if ($rating < 1 || $rating > 5) {
    echo json_encode([
        'success' => false,
        'message' => 'Mức đánh giá không hợp lệ.'
    ]);
    exit;
}
if ($comment === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng nhập nội dung đánh giá.'
    ]);
    exit;
}

try {
    $stmtProduct = $pdo->prepare("
        SELECT id FROM products WHERE id = ? LIMIT 1
    ");
    $stmtProduct->execute([$product_id]);
    $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại.'
        ]);
        exit;
    }

    $stmtCheck = $pdo->prepare("
        SELECT COUNT(*) 
        FROM order_items oi 
        JOIN orders o ON oi.order_id = o.id 
        WHERE o.user_id = :user_id 
        AND oi.product_id = :product_id 
        AND o.status = 'completed'
    ");
    $stmtCheck->execute([
        'user_id' => $user_id,
        'product_id' => $product_id
    ]);
    if ($stmtCheck->fetchColumn() == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Chỉ khách hàng đã mua và thanh toán sản phẩm này mới có quyền đánh giá.'
        ]);
        exit;
    }

    $image_url = null;
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['review_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (in_array($file['type'], $allowed_types)) {
            $upload_dir = '../../upload/review_image/'; 
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'review_' . time() . '_' . uniqid() . '.' . $ext; 
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $image_url = 'upload/review_image/' . $filename;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể lưu ảnh tải lên máy chủ. Vui lòng thử lại.'
                ]);
                exit;
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Định dạng ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF hoặc WEBP.'
            ]);
            exit;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO product_reviews (
            product_id, user_id, rating, comment, image_url, status
        ) 
        VALUES (
            :product_id, :user_id, :rating, :comment, :image_url, 'show'
        )
    ");
    $stmt->execute([
        'product_id' => $product_id,
        'user_id' => $user_id,
        'rating' => $rating,
        'comment' => $comment,
        'image_url' => $image_url
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Cảm ơn bạn đã gửi đánh giá!'
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi lưu trữ dữ liệu.'
    ]);
    exit;
}