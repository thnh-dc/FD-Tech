<?php
session_start();
require_once 'check_login.php'; // Chắc chắn user đã login
include '../config/database.php'; // Gọi file cấu hình PDO của bạn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $action_type = $_POST['action_type']; // 'add_to_cart' hoặc 'buy_now'

    // 1. Kiểm tra xem sản phẩm đã có trong giỏ hàng của user chưa
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch();

    $cart_item_id = 0;

    if ($item) {
        // Đã có -> Cộng dồn số lượng
        $new_qty = $item['quantity'] + $quantity;
        $update = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update->execute([$new_qty, $item['id']]);
        $cart_item_id = $item['id'];
    } else {
        // Chưa có -> Thêm mới
        $insert = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->execute([$user_id, $product_id, $quantity]);
        $cart_item_id = $pdo->lastInsertId(); // Lấy ID của dòng vừa thêm
    }

    // 2. Xử lý điều hướng dựa trên nút người dùng bấm
    if ($action_type === 'add_to_cart') {
        // Trở về trang giỏ hàng
        header("Location: cart.php");
        exit;
    } elseif ($action_type === 'buy_now') {
        // Trang checkout.php của bạn đang đợi biến $_POST['selected_items']
        // Nên ta in ra một form ẩn và dùng JS tự động submit ngay lập tức
        echo "
        <form id='redirectForm' action='checkout.php' method='POST'>
            <input type='hidden' name='selected_items' value='{$cart_item_id}'>
        </form>
        <script>
            document.getElementById('redirectForm').submit();
        </script>
        ";
        exit;
    }
}
?>