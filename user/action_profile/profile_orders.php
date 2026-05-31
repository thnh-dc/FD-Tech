<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$user_id = $user_id ?? ($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] == 'cancel_order') {
        $order_id = (int)$_POST['order_id'];

        try {
            $pdo->beginTransaction();

            $stmt_order = $pdo->prepare("SELECT id, status, used_points FROM orders WHERE id = ? AND user_id = ? LIMIT 1 FOR UPDATE");
            $stmt_order->execute([$order_id, $user_id]);
            $order_cancel = $stmt_order->fetch(PDO::FETCH_ASSOC);

            if (!$order_cancel) throw new Exception('Không tìm thấy đơn hàng!');
            if (!in_array($order_cancel['status'], ['pending', 'processing'])) throw new Exception('Không thể hủy đơn hàng này hoặc trạng thái đơn đã thay đổi!');

            $used_points = (int)($order_cancel['used_points'] ?? 0);
            $cancel_note = ' | Người dùng hủy đơn';

            if ($used_points > 0) $cancel_note .= ', đã hoàn lại ' . $used_points . ' FDp';

            $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled', note = CONCAT(IFNULL(note, ''), ?), updated_at = NOW() WHERE id = ? AND user_id = ? AND status IN ('pending', 'processing')");

            if ($stmt->execute([$cancel_note, $order_id, $user_id]) && $stmt->rowCount() > 0) {

                $st_get_items = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $st_get_items->execute([$order_id]);
                $cancelled_items = $st_get_items->fetchAll(PDO::FETCH_ASSOC);

                if (empty($cancelled_items)) throw new Exception('Không tìm thấy chi tiết sản phẩm của đơn hàng.');

                $st_update_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");

                foreach ($cancelled_items as $item) {
                    $st_update_stock->execute([$item['quantity'], $item['product_id']]);
                }

                if ($used_points > 0) {
                    $st_refund_point = $pdo->prepare("UPDATE users SET point = COALESCE(point, 0) + ? WHERE id = ?");
                    $st_refund_point->execute([$used_points, $user_id]);

                    $st_refund_history = $pdo->prepare("INSERT INTO fd_point_transactions(user_id, order_id, type, points, description) VALUES (?, ?, 'refund', ?, ?)");
                    $st_refund_history->execute([$user_id, $order_id, $used_points, 'Hoàn lại ' . $used_points . ' FDp do người dùng hủy đơn hàng #FD-' . $order_id]);
                }

                $pdo->commit();

                $_SESSION['noti_message'] = $used_points > 0 
                    ? 'Đã hủy đơn hàng, hoàn trả số lượng vào kho và hoàn lại ' . $used_points . ' FDp!' 
                    : 'Đã hủy đơn hàng và hoàn trả số lượng vào kho thành công!';

                $_SESSION['noti_type'] = 'success';
            } else {
                $pdo->rollBack();
                $_SESSION['noti_message'] = 'Không thể hủy đơn hàng này hoặc trạng thái đơn đã thay đổi!';
                $_SESSION['noti_type'] = 'error';
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();

            $_SESSION['noti_message'] = $e->getMessage();
            $_SESSION['noti_type'] = 'error';
        }

        header("Location: profile.php?action=orders");
        exit();
    } 
    
    elseif ($_POST['action'] == 'confirm_received') {
        $order_id = (int)$_POST['order_id'];

        try {
            $pdo->beginTransaction();

            $stmt_order = $pdo->prepare("SELECT id, total_amount, status FROM orders WHERE id = ? AND user_id = ? LIMIT 1 FOR UPDATE");
            $stmt_order->execute([$order_id, $user_id]);
            $order_done = $stmt_order->fetch(PDO::FETCH_ASSOC);

            if (!$order_done) throw new Exception('Không tìm thấy đơn hàng!');
            if (!in_array($order_done['status'], ['shipped', 'shipping'])) throw new Exception('Không thể xác nhận đơn hàng này.');

            $stmt = $pdo->prepare("UPDATE orders SET status = 'completed', updated_at = NOW() WHERE id = ? AND user_id = ? AND status IN ('shipped', 'shipping')");
            $stmt->execute([$order_id, $user_id]);

            if ($stmt->rowCount() <= 0) throw new Exception('Không thể xác nhận đơn hàng này.');

            $earned_point = (int)floor(((float)$order_done['total_amount']) / 10000);

            if ($earned_point > 0) {
                $st_add_point = $pdo->prepare("UPDATE users SET point = COALESCE(point, 0) + ? WHERE id = ?");
                $st_add_point->execute([$earned_point, $user_id]);

                $st_earn_history = $pdo->prepare("INSERT INTO fd_point_transactions(user_id, order_id, type, points, description) VALUES (?, ?, 'earn', ?, ?)");
                $st_earn_history->execute([$user_id, $order_id, $earned_point, 'Tích ' . $earned_point . ' FDp từ đơn hàng hoàn thành #FD-' . $order_id]);
            }

            $pdo->commit();

            $_SESSION['noti_message'] = $earned_point > 0 
                ? 'Xác nhận đã nhận hàng thành công. Bạn được cộng ' . $earned_point . ' FDp!' 
                : 'Xác nhận đã nhận hàng thành công. Cảm ơn bạn đã mua sắm!';

            $_SESSION['noti_type'] = 'success';

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();

            $_SESSION['noti_message'] = $e->getMessage();
            $_SESSION['noti_type'] = 'error';
        }

        header("Location: profile.php?action=orders");
        exit();
    }
}

$current_status = $_GET['status'] ?? 'all';

try {
    if ($current_status == 'all') {
        $stmt = $pdo->prepare("SELECT o.*, u.phone AS user_phone FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.user_id = ? ORDER BY o.created_at DESC");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("SELECT o.*, u.phone AS user_phone FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.user_id = ? AND o.status = ? ORDER BY o.created_at DESC");
        $stmt->execute([$user_id, $current_status]);
    }

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}

if (!function_exists('translateOrderStatus')) {
    function translateOrderStatus($status)
    {
        $labels = [
            'pending' => 'Chờ thanh toán',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];

        $text = $labels[$status] ?? $status;

        return '<span class="status-badge-text">' . htmlspecialchars($text) . '</span>';
    }
}
?>

<link rel="stylesheet" href="../assets/css/style_profile_order.css">

<div class="profile-orders-header">
    <h2>Đơn hàng của tôi</h2>
    <p>Theo dõi tình trạng các đơn hàng đã đặt</p>
</div>

<div class="order-filter-tabs">
    <a href="profile.php?action=orders&status=all" class="<?= $current_status == 'all' ? 'active' : '' ?>">Tất cả</a>
    <a href="profile.php?action=orders&status=pending" class="<?= $current_status == 'pending' ? 'active' : '' ?>">Chờ thanh toán</a> 
    <a href="profile.php?action=orders&status=processing" class="<?= $current_status == 'processing' ? 'active' : '' ?>">Đang xử lý</a>
    <a href="profile.php?action=orders&status=shipped" class="<?= $current_status == 'shipped' ? 'active' : '' ?>">Đang giao</a>
    <a href="profile.php?action=orders&status=completed" class="<?= $current_status == 'completed' ? 'active' : '' ?>">Đã giao</a>
    <a href="profile.php?action=orders&status=cancelled" class="<?= $current_status == 'cancelled' ? 'active' : '' ?>">Đã hủy</a>
</div>

<?php if (empty($orders)): ?>
    <div class="order-empty-state">
        <p>Bạn chưa có đơn hàng nào ở trạng thái này.</p>
    </div>
<?php else: ?>
    <div>
        <?php foreach ($orders as $order):
            $st_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $st_items->execute([$order['id']]);
            $items = $st_items->fetchAll(PDO::FETCH_ASSOC);
            $first_name = !empty($items) ? $items[0]['product_name'] : 'Đơn hàng #' . $order['id'];
            ?>

            <div class="order-card">
                <div class="order-summary">
                    <div class="order-summary-left">
                        <span class="order-summary-title"><?= htmlspecialchars($first_name) ?></span>
                        <div class="order-summary-status"><?= translateOrderStatus($order['status']) ?></div>
                    </div>
                    
                    <div class="order-summary-price">
                        <span>Giá tiền: </span>
                        <strong><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong>
                    </div>

                    <button type="button" class="btn-toggle-detail" onclick="toggleOrder(<?= $order['id'] ?>, this)">
                        Xem chi tiết
                    </button>
                </div>

                <div id="detail-<?= $order['id'] ?>" class="order-detail-box" style="display: none;">
                    <div class="order-info-row">
                        <span>Mã đơn: <strong>#<?= $order['id'] ?></strong></span>
                        <span>Ngày đặt: <strong><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong></span>
                    </div>

                    <?php 
                        $display_phone = !empty($order['user_phone']) ? $order['user_phone'] : 'Chưa cập nhật SĐT'; 
                        $display_address = !empty($order['shipping_address']) ? $order['shipping_address'] : 'Chưa cập nhật địa chỉ';
                        $payment_method = $order['payment_method'] ?? 'cod';
                        $display_payment = ($payment_method === 'bank') ? 'Chuyển khoản ngân hàng (Qua cổng mã QR)' : 'Thanh toán khi nhận hàng (COD)';
                    ?>

                    <div class="order-customer-info">
                        <div>
                            <strong>Số điện thoại:</strong> <span><?= htmlspecialchars($display_phone) ?></span>
                        </div>
                        <div>
                            <strong>Địa chỉ giao hàng:</strong> <span><?= htmlspecialchars($display_address) ?></span>
                        </div>
                    </div>

                    <?php foreach ($items as $it):
                        $img_link = !empty($it['product_image']) ? $it['product_image'] : '';
                        $p_name = !empty($it['product_name']) ? $it['product_name'] : 'Sản phẩm không xác định';
                        
                        if (!empty($img_link) && !filter_var($img_link, FILTER_VALIDATE_URL) && strpos($img_link, 'upload/') !== 0) {
                            $img_link = "../upload/product_image/" . $img_link;
                        }
                        ?>

                        <div class="order-item">
                            <div class="order-item-img">
                                <img src="<?= htmlspecialchars($img_link) ?>" alt="Product" onerror="this.src='../assets/images/logo-fd.jpg'">
                            </div>
                            <div class="order-item-info">
                                <div class="order-item-name"><?= htmlspecialchars($p_name) ?></div>
                                <div class="order-item-meta">
                                    Số lượng: <?= $it['quantity'] ?> | Giá: <span class="order-item-price"><?= number_format($it['price'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="order-payment-method-box">
                        <strong>Phương thức thanh toán:</strong> <span><?= htmlspecialchars($display_payment) ?></span>
                    </div>

                    <?php if (!empty($order['used_points']) && (int)$order['used_points'] > 0): ?>
                        <div class="order-payment-method-box">
                            <strong>FD Point đã sử dụng:</strong> <span><?= number_format((int)$order['used_points'], 0, ',', '.') ?> FDp</span>
                        </div>
                    <?php endif; ?>

                    <div class="order-footer">
                        <div class="order-total-text">
                            <span>Tổng thanh toán: </span>
                            <span class="order-total-amount"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="order-actions-group">
                            <?php if ($order['status'] == 'pending' || $order['status'] == 'processing'): ?>
                                <form action="" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #<?= $order['id'] ?> này không? Thao tác này sẽ tự động hoàn lại sản phẩm vào kho hàng và hoàn lại FDp đã dùng nếu có.');">
                                    <input type="hidden" name="action" value="cancel_order">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="btn-cancel-order">Hủy đơn hàng</button>
                                </form>
                                
                                <?php if ($order['status'] == 'pending' && ($order['payment_method'] ?? '') === 'bank'): ?>
                                    <a href="/FD-Tech/user/action_checkout/bank_payment.php?order_id=<?= $order['id'] ?>" class="btn-pay-now">Thanh toán ngay</a>
                                <?php endif; ?>

                            <?php elseif ($order['status'] == 'shipped' || $order['status'] == 'shipping'): ?>
                                <form action="" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn xác nhận đã nhận được đơn hàng #<?= $order['id'] ?> này thành công?');">
                                    <input type="hidden" name="action" value="confirm_received">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="btn-confirm-received">Đã nhận được hàng</button>
                                </form>

                            <?php elseif ($order['status'] == 'completed'): ?>
                                <a href="../user/action_checkout/bill.php?id=<?= $order['id'] ?>" target="_blank" style="background-color: #1a9bb8; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                                    &#128442; Xuất hóa đơn
                                </a>
                                
                                <a href="../user/action_profile/request_service.php?order_id=<?= $order['id'] ?>" style="background-color: #db4437; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
                                    &#10226; Hoàn hàng / Bảo hành
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function toggleOrder(id, btn) {
    var box = document.getElementById('detail-' + id);
    if (box.style.display === "none" || box.style.display === "") {
        box.style.display = "block";
        btn.innerText = "Đóng";
    } else {
        box.style.display = "none";
        btn.innerText = "Xem chi tiết";
    }
}
</script>

<?php include '../includes/notification.php'; ?>