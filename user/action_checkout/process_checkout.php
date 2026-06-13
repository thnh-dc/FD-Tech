<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/fd_member_helper.php';

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) { header("Location: ../../auth/login.php"); exit(); }

$address = trim($_POST['address'] ?? '');
$selectedItems = $_POST['selected_items'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'cod';
$use_points = isset($_POST['use_points']) ? (int)$_POST['use_points'] : 0;
$point_value = 100;

$shipping_carrier = 'FD Express';
$shipping_cost_original = 25000;
$estimated_delivery = date('Y-m-d', strtotime('+4 days'));

if ($use_points < 0) $use_points = 0;
if (empty($selectedItems)) { header("Location: ../cart.php?error=no_items"); exit(); }

$selectedArray = array_filter(explode(',', $selectedItems));
if (empty($selectedArray)) { header("Location: ../cart.php?error=no_items"); exit(); }

if (!in_array($payment_method, ['cod', 'bank'])) $payment_method = 'cod';

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
$stmt = $pdo->prepare("SELECT c.product_id, c.quantity, c.id, p.price, p.discount_price, p.stock_quantity, COALESCE(NULLIF(p.discount_price, 0), p.price) AS display_price, p.name AS product_name, p.image_url AS product_image FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? AND c.id IN ($placeholders)");
$params = array_merge([$user_id], $selectedArray);
$stmt->execute($params);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cartItems)) { header("Location: ../cart.php?error=cart_empty"); exit(); }

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

    $stmtUser = $pdo->prepare("SELECT point, email FROM users WHERE id = ? LIMIT 1 FOR UPDATE");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if (!$user) throw new Exception('Không tìm thấy thông tin người dùng.');

    $period_points = getUserPeriodFDp($pdo, $user_id);
    $current_tier = getFDMemberTierByPoint($pdo, $period_points);
    $member_tier_name = $current_tier['tier_name'] ?? 'Đồng';
    $member_discount_percent = (float)($current_tier['discount_percent'] ?? 0);
    $member_free_shipping = ((int)($current_tier['free_shipping'] ?? 0) === 1);
    $shipping_cost = $member_free_shipping ? 0 : $shipping_cost_original;

    $current_point = (int)($user['point'] ?? 0);
    $max_points_by_total = (int)floor($total / $point_value);
    $max_usable_points = min($current_point, $max_points_by_total);

    if ($use_points > $max_usable_points) throw new Exception('Số FDp sử dụng không hợp lệ hoặc vượt quá số FDp hiện có.');

    $point_discount = $use_points * $point_value;
    $after_point_total = max($total - $point_discount, 0);
    $member_discount = (int)floor($after_point_total * $member_discount_percent / 100);
    $final_total = max($after_point_total - $member_discount + $shipping_cost, 0);

    $payment_note .= ' | Đơn vị vận chuyển: ' . $shipping_carrier . ', phí ship ' . number_format($shipping_cost, 0, ',', '.') . 'đ, giao dự kiến ' . date('d/m/Y', strtotime($estimated_delivery));

    if ($member_free_shipping) {
        $payment_note .= ' | Thành viên ' . $member_tier_name . ' được miễn phí vận chuyển, giảm ' . number_format($shipping_cost_original, 0, ',', '.') . 'đ phí ship';
    }

    if ($use_points > 0) {
        $payment_note .= ' | Đã dùng ' . $use_points . ' FDp, giảm ' . number_format($point_discount, 0, ',', '.') . 'đ';
        $stmtMinusPoint = $pdo->prepare("UPDATE users SET point = COALESCE(point, 0) - ? WHERE id = ? AND COALESCE(point, 0) >= ?");
        $stmtMinusPoint->execute([$use_points, $user_id, $use_points]);
        if ($stmtMinusPoint->rowCount() <= 0) throw new Exception('Không thể trừ FDp. Vui lòng kiểm tra lại FDp hiện có.');
    }

    if ($member_discount > 0) $payment_note .= ' | Ưu đãi FD Member hạng ' . $member_tier_name . ' giảm ' . number_format($member_discount, 0, ',', '.') . 'đ';

    $stmtOrder = $pdo->prepare("INSERT INTO orders(user_id, total_amount, status, shipping_address, note, payment_method, payment_status, used_points) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtOrder->execute([$user_id, $final_total, $order_status, $address, $payment_note, $payment_method, $payment_status, $use_points]);
    $order_id = $pdo->lastInsertId();

    $tracking_number = 'FDX' . date('ymd') . str_pad($order_id, 6, '0', STR_PAD_LEFT);

    $stmtCheckShippingStatusColumn = $pdo->query("SHOW COLUMNS FROM order_shipping LIKE 'shipping_status'");
    $hasShippingStatusColumn = $stmtCheckShippingStatusColumn && $stmtCheckShippingStatusColumn->fetch(PDO::FETCH_ASSOC);

    $stmtShipping = $pdo->prepare("
        INSERT INTO order_shipping (
                    order_id,
                    shipping_status,
                    carrier_name,
                    tracking_number,
                    shipping_cost,
                    estimated_delivery
                ) VALUES (?, 'preparing', ?, ?, ?, ?)
            ");

            $stmtShipping->execute([
                $order_id,
                $shipping_carrier,
                $tracking_number,
                $shipping_cost,
                $estimated_delivery
            ]);

    if ($use_points > 0) {
        $stmtPointHistory = $pdo->prepare("INSERT INTO fd_point_transactions(user_id, order_id, type, points, description) VALUES (?, ?, 'redeem', ?, ?)");
        $stmtPointHistory->execute([$user_id, $order_id, -$use_points, 'Dùng ' . $use_points . ' FDp để giảm ' . number_format($point_discount, 0, ',', '.') . 'đ cho đơn hàng #FD-' . $order_id]);
    }

    if ($payment_method === 'bank') {
        $payment_code = 'FDTECH' . $order_id;
        $bank_note = 'Chuyển khoản ngân hàng - Nội dung: ' . $payment_code;
        $bank_note .= ' | Đơn vị vận chuyển: ' . $shipping_carrier . ', mã vận đơn ' . $tracking_number . ', phí ship ' . number_format($shipping_cost, 0, ',', '.') . 'đ, giao dự kiến ' . date('d/m/Y', strtotime($estimated_delivery));

        if ($member_free_shipping) $bank_note .= ' | Thành viên ' . $member_tier_name . ' được miễn phí vận chuyển, giảm ' . number_format($shipping_cost_original, 0, ',', '.') . 'đ phí ship';
        if ($use_points > 0) $bank_note .= ' | Đã dùng ' . $use_points . ' FDp, giảm ' . number_format($point_discount, 0, ',', '.') . 'đ';
        if ($member_discount > 0) $bank_note .= ' | Ưu đãi FD Member hạng ' . $member_tier_name . ' giảm ' . number_format($member_discount, 0, ',', '.') . 'đ';

        $stmtPaymentCode = $pdo->prepare("UPDATE orders SET payment_code = ?, note = ? WHERE id = ?");
        $stmtPaymentCode->execute([$payment_code, $bank_note, $order_id]);
    }

    $stmtItem = $pdo->prepare("INSERT INTO order_items(order_id, product_id, quantity, price, product_name, product_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?");

    foreach ($cartItems as $item) {
        $product_id = (int)$item['product_id'];
        $quantity = (int)$item['quantity'];

        $stmtUpdateStock->execute([$quantity, $product_id, $quantity]);
        if ($stmtUpdateStock->rowCount() <= 0) throw new Exception("Sản phẩm " . $item['product_name'] . " không đủ tồn kho.");

        $stmtItem->execute([$order_id, $product_id, $quantity, $item['display_price'], $item['product_name'], $item['product_image']]);
    }

    $deletePlaceholders = implode(',', array_fill(0, count($selectedArray), '?'));
    $stmtDelete = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND id IN ($deletePlaceholders)");
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
    if ($pdo->inTransaction()) $pdo->rollBack();

    $_SESSION['noti_message'] = $e->getMessage();
    $_SESSION['noti_type'] = 'error';

    header("Location: ../cart.php");
    exit();
}
?>