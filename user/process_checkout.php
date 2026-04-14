<?php
session_start();
require_once '../config/database.php';

// Kiểm tra xem có gửi form và giỏ hàng có đồ không
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['cart'])) {
    
    // Lấy thông tin từ form
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method']; // 'cod' hoặc 'bank_transfer'
    
    // Ghép thông tin giao hàng thành 1 chuỗi (Theo cấu trúc bảng orders trong file báo cáo)
    $shipping_address = "$fullname - $phone - $address";
    
    // Lấy user_id đang đăng nhập (Giả sử là 1 nếu khách vãng lai, tùy logic auth của bạn)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; 

    // Tính tổng tiền giỏ hàng
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // BẮT ĐẦU TRANSACTION CỰC KỲ QUAN TRỌNG
    try {
        // Tắt chế độ autocommit, bắt đầu ghi nhận giao dịch
        $pdo->beginTransaction();

        // ---------------------------------------------------------
        // BƯỚC 1: INSERT DỮ LIỆU VÀO BẢNG orders
        // ---------------------------------------------------------
        $sql_order = "INSERT INTO orders (user_id, total_amount, status, shipping_address, created_at) 
                      VALUES (?, ?, 'pending', ?, NOW())";
        $stmt_order = $pdo->prepare($sql_order);
        $stmt_order->execute([$user_id, $total_amount, $shipping_address]);

        // Lấy ID đơn hàng vừa tạo xong bằng hàm của PDO
        $order_id = $pdo->lastInsertId();

        // Chuẩn bị sẵn 2 câu SQL cho Bước 2 và Bước 3
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $pdo->prepare($sql_item);

        $sql_update_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
        $stmt_update_stock = $pdo->prepare($sql_update_stock);

        // ---------------------------------------------------------
        // BƯỚC 2 & 3: LẶP QUA GIỎ HÀNG ĐỂ THÊM CHI TIẾT & TRỪ KHO
        // ---------------------------------------------------------
        foreach ($_SESSION['cart'] as $product_id => $item) {
            
            // Bước 2: Thêm vào bảng order_items
            $stmt_item->execute([
                $order_id, 
                $product_id, 
                $item['quantity'], 
                $item['price']
            ]);

            // Bước 3: Cập nhật trừ kho bảng products
            // Điều kiện AND stock_quantity >= ? để đảm bảo không bị kho âm
            $stmt_update_stock->execute([
                $item['quantity'], 
                $product_id,
                $item['quantity'] // Tham số cho điều kiện WHERE
            ]);

            // Kiểm tra xem có trừ kho thành công không (nếu = 0 tức là hết hàng lúc khách vừa bấm mua)
            if ($stmt_update_stock->rowCount() == 0) {
                throw new Exception("Sản phẩm '{$item['name']}' đã hết hàng hoặc không đủ số lượng!");
            }
        }

        // NẾU MỌI THỨ TRÓT LỌT -> CHỐT GIAO DỊCH LƯU VÀO CSDL VĨNH VIỄN
        $pdo->commit();

        // Làm trống giỏ hàng
        unset($_SESSION['cart']);

        // Chuyển hướng sang trang Thành công
        header("Location: done.php");
        exit();

    } catch (Exception $e) {
        // NẾU CÓ BẤT KỲ LỖI GÌ (Code sai, CSDL sập, Hết hàng...) -> HOÀN TÁC TOÀN BỘ (ROLLBACK)
        // Đảm bảo không có chuyện đơn hàng tạo ra mà không có chi tiết, hoặc tiền trừ mà kho không trừ
        $pdo->rollBack();
        
        // Lưu lỗi vào Session để hiển thị ra màn hình cho user biết
        $_SESSION['error_message'] = "Đặt hàng thất bại: " . $e->getMessage();
        header("Location: checkout.php");
        exit();
    }
}
?>