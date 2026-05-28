<?php
session_start();
require_once '../../config/database.php';

$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id <= 0) {
    header("Location: ../../auth/login.php");
    exit();
}

$address = trim($_POST['address'] ?? '');
$selectedItems = $_POST['selected_items'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'cod';
$use_points = isset($_POST['use_points']) ? (int)$_POST['use_points'] : 0;

/*
    Quy đổi điểm:
    1 FDp = 100đ
    10 FDp = 1.000đ
*/
$point_value = 100;

if ($use_points < 0) {
    $use_points = 0;
}

if (empty($selectedItems)) {
    header("Location: ../cart.php?error=no_items");
    exit();
}

$selectedArray = array_filter(explode(',', $selectedItems));

if (empty($selectedArray)) {
    header("Location: ../cart.php?error=no_items");
    exit();
}

if (!in_array($payment_method, ['cod', 'bank'])) {
    $payment_method = 'cod';
}

if ($payment_method === 'bank') {
    $payment_note = 'Chuyển khoản ngân hàng';
    $order_status = 'pending';
    $payment_status = 'unpaid';
} else {
    $payment_note = 'Thanh toán khi nhận hàng';
    $order_status = 'processing';
    $payment_status = 'unpaid';
}

$placeholders = implode(',', array_fill(0, count($selectedArray), '?'));

$stmt = $pdo->prepare("
    SELECT 
        c.product_id,
        c.quantity,
        c.id,
        p.price,
        p.discount_price,
        p.stock_quantity,
        COALESCE(NULLIF(p.discount_price, 0), p.price) AS display_price,
        p.name AS product_name,
        p.image_url AS product_image
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    AND c.id IN ($placeholders)
");

$params = array_merge([$user_id], $selectedArray);
$stmt->execute($params);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cartItems)) {
    header("Location: ../cart.php?error=cart_empty");
    exit();
}

$total = 0;

foreach ($cartItems as $item) {
    if ((int)$item['quantity'] > (int)$item['stock_quantity']) {
        $_SESSION['noti_message'] = 'Sản phẩm ' . $item['product_name'] . ' không đủ tồn kho.';
        $_SESSION['noti_type'] = 'error';

        header("Location: ../cart.php");
        exit();
    }

    $total += (float)$item['display_price'] * (int)$item['quantity'];
}

try {
    $pdo->beginTransaction();

    /*
        Khóa dòng user để tránh lỗi nếu người dùng đặt nhiều đơn cùng lúc.
    */
    $stmtUser = $pdo->prepare("
        SELECT point
        FROM users
        WHERE id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Không tìm thấy thông tin người dùng.');
    }

    $current_point = (int)($user['point'] ?? 0);

    /*
        Số điểm tối đa được dùng không được vượt quá:
        - điểm hiện có của user
        - tổng tiền đơn hàng sau quy đổi
    */
    $max_points_by_total = (int)floor($total / $point_value);
    $max_usable_points = min($current_point, $max_points_by_total);

    if ($use_points > $max_usable_points) {
        throw new Exception('Số điểm sử dụng không hợp lệ hoặc vượt quá số điểm hiện có.');
    }

    $point_discount = $use_points * $point_value;
    $final_total = max($total - $point_discount, 0);

    if ($use_points > 0) {
        $payment_note .= ' | Đã dùng ' . $use_points . ' FDp, giảm ' . number_format($point_discount, 0, ',', '.') . 'đ';

        $stmtMinusPoint = $pdo->prepare("
            UPDATE users
            SET point = COALESCE(point, 0) - ?
            WHERE id = ?
            AND COALESCE(point, 0) >= ?
        ");
        $stmtMinusPoint->execute([$use_points, $user_id, $use_points]);

        if ($stmtMinusPoint->rowCount() <= 0) {
            throw new Exception('Không thể trừ điểm. Vui lòng kiểm tra lại điểm hiện có.');
        }
    }

    /*
        Lưu used_points vào orders để sau này hủy đơn thì hoàn lại chính xác.
    */
    $stmtOrder = $pdo->prepare("
        INSERT INTO orders(
            user_id,
            total_amount,
            status,
            shipping_address,
            note,
            payment_method,
            payment_status,
            used_points
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmtOrder->execute([
        $user_id,
        $final_total,
        $order_status,
        $address,
        $payment_note,
        $payment_method,
        $payment_status,
        $use_points
    ]);

    $order_id = $pdo->lastInsertId();

    if ($payment_method === 'bank') {
        $payment_code = 'FDTECH' . $order_id;

        $bank_note = 'Chuyển khoản ngân hàng - Nội dung: ' . $payment_code;

        if ($use_points > 0) {
            $bank_note .= ' | Đã dùng ' . $use_points . ' FDp, giảm ' . number_format($point_discount, 0, ',', '.') . 'đ';
        }

        $stmtPaymentCode = $pdo->prepare("
            UPDATE orders
            SET 
                payment_code = ?,
                note = ?
            WHERE id = ?
        ");

        $stmtPaymentCode->execute([
            $payment_code,
            $bank_note,
            $order_id
        ]);
    }

    $stmtItem = $pdo->prepare("
        INSERT INTO order_items(
            order_id,
            product_id,
            quantity,
            price,
            product_name,
            product_image
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmtUpdateStock = $pdo->prepare("
        UPDATE products
        SET stock_quantity = stock_quantity - ?
        WHERE id = ?
        AND stock_quantity >= ?
    ");

    foreach ($cartItems as $item) {
        $product_id = (int)$item['product_id'];
        $quantity = (int)$item['quantity'];

        $stmtUpdateStock->execute([
            $quantity,
            $product_id,
            $quantity
        ]);

        if ($stmtUpdateStock->rowCount() <= 0) {
            throw new Exception("Sản phẩm " . $item['product_name'] . " không đủ tồn kho.");
        }

        $stmtItem->execute([
            $order_id,
            $product_id,
            $quantity,
            $item['display_price'],
            $item['product_name'],
            $item['product_image']
        ]);
    }

    $deletePlaceholders = implode(',', array_fill(0, count($selectedArray), '?'));

    $stmtDelete = $pdo->prepare("
        DELETE FROM cart_items
        WHERE user_id = ?
        AND id IN ($deletePlaceholders)
    ");

    $deleteParams = array_merge([$user_id], $selectedArray);
    $stmtDelete->execute($deleteParams);

    $pdo->commit();

    if ($payment_method === 'bank') {
        header("Location: bank_payment.php?order_id=" . $order_id);
        exit();
    }

    header("Location: ../checkout.php?status=success&order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['noti_message'] = $e->getMessage();
    $_SESSION['noti_type'] = 'error';

    header("Location: ../cart.php");
    exit();
}
?>