<?php
session_start();

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];

    switch ($action) {
        case 'add':
            // Lấy thông tin sản phẩm từ Form (hoặc truy vấn CSDL nếu cần bảo mật giá)
            $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if (isset($_SESSION['cart'][$product_id])) {
                // Nếu đã có trong giỏ -> Tăng số lượng
                $_SESSION['cart'][$product_id]['quantity'] += $qty;
            } else {
                // Nếu chưa có -> Thêm mới vào mảng
                $_SESSION['cart'][$product_id] = [
                    'id' => $product_id,
                    'name' => $_POST['product_name'],
                    'price' => $_POST['price'],
                    'quantity' => $qty,
                    // Lưu ý: Nên truyền thêm giới hạn tồn kho (stock) để check max quantity ở frontend
                ];
            }
            break;

        case 'update':
            // Cập nhật lại số lượng khi người dùng bấm nút +/-
            $new_qty = (int)$_POST['quantity'];
            if ($new_qty > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $new_qty;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            break;

        case 'remove':
            // Xóa phần tử khỏi mảng Session
            unset($_SESSION['cart'][$product_id]);
            break;
    }

    // Chuyển hướng về lại trang giỏ hàng sau khi xử lý xong
    header("Location: cart.php");
    exit();
}
?>