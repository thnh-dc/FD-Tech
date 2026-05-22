<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $user_id ?? ($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] == 'cancel_order') {
        $order_id = (int)$_POST['order_id'];
        try {
            // Kích hoạt Transaction để đảm bảo tính toàn vẹn của dữ liệu kho hàng
            $pdo->beginTransaction();

            // 1. Cập nhật trạng thái đơn hàng thành đã hủy (Chỉ cho phép hủy khi đang Chờ thanh toán hoặc Đang xử lý)
            $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status IN ('pending', 'processing')");
            
            if ($stmt->execute([$order_id, $user_id]) && $stmt->rowCount() > 0) {
                
                // 2. Lấy danh sách sản phẩm và số lượng tương ứng từ đơn hàng bị hủy
                $st_get_items = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $st_get_items->execute([$order_id]);
                $cancelled_items = $st_get_items->fetchAll(PDO::FETCH_ASSOC);

                // 3. Chạy vòng lặp cộng trả lại số lượng vào kho hàng (bảng products)
                $st_update_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
                foreach ($cancelled_items as $item) {
                    $st_update_stock->execute([$item['quantity'], $item['product_id']]);
                }
                
                // Xác nhận lưu mọi thay đổi vào Database thành công
                $pdo->commit(); 
                $_SESSION['noti_message'] = 'Đã hủy đơn hàng và hoàn trả số lượng vào kho thành công!';
                $_SESSION['noti_type'] = 'success';
            } else {
                // Nếu đơn hàng đã bị đổi trạng thái trước đó bởi Admin hoặc không hợp lệ
                $pdo->rollBack();
                $_SESSION['noti_message'] = 'Không thể hủy đơn hàng này hoặc trạng thái đơn đã thay đổi!';
                $_SESSION['noti_type'] = 'error';
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['noti_message'] = 'Lỗi hệ thống: Không thể xử lý hủy đơn lúc này!';
            $_SESSION['noti_type'] = 'error';
        }
        header("Location: profile.php?action=orders");
        exit();
    } 
    
    elseif ($_POST['action'] == 'confirm_received') {
        $order_id = (int)$_POST['order_id'];
        try {
            // Khách hàng bấm xác nhận khi đã nhận hàng
            $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND user_id = ? AND status IN ('shipped', 'shipping')");
            if ($stmt->execute([$order_id, $user_id])) {
                $_SESSION['noti_message'] = 'Xác nhận đã nhận hàng thành công. Cảm ơn bạn đã mua sắm!';
                $_SESSION['noti_type'] = 'success';
            }
        } catch (PDOException $e) {
            $_SESSION['noti_message'] = 'Lỗi khi xác nhận đơn hàng!';
            $_SESSION['noti_type'] = 'error';
        }
        header("Location: profile.php?action=orders");
        exit();
    }
}

$current_status = $_GET['status'] ?? 'all';

try {
    if ($current_status == 'all') {
        $stmt = $pdo->prepare("
            SELECT o.*, u.phone AS user_phone 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT o.*, u.phone AS user_phone 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = ? AND o.status = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id, $current_status]);
    }
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}

// Hàm hỗ trợ dịch trạng thái đơn hàng sang tiếng Việt hiển thị trực quan
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
            // Lấy toàn bộ danh sách sản phẩm thuộc đơn hàng hiện tại
            $st_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $st_items->execute([$order['id']]);
            $items = $st_items->fetchAll(PDO::FETCH_ASSOC);

            // Đặt tên tiêu đề đại diện hiển thị ngoài Card là tên sản phẩm đầu tiên
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

                <div id="detail-<?= $order['id'] ?>" class="order-detail-box">
                    
                    <div class="order-info-row">
                        <span>Mã đơn: <strong>#<?= $order['id'] ?></strong></span>
                        <span>Ngày đặt: <strong><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong></span>
                    </div>

                    <?php 
                        // Cấu hình hiển thị thông tin khách hàng nhận
                        $display_phone = !empty($order['user_phone']) ? $order['user_phone'] : 'Chưa cập nhật SĐT'; 
                        $display_address = !empty($order['shipping_address']) ? $order['shipping_address'] : 'Chưa cập nhật địa chỉ';
                        
                        // Định dạng hiển thị chuỗi phương thức thanh toán dựa theo cột payment_method
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
                        
                        // Định vị chuẩn đường dẫn thư mục lưu ảnh upload trên Localhost XAMPP
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

                    <div class="order-footer">
                        <div class="order-total-text">
                            <span>Tổng thanh toán: </span>
                            <span class="order-total-amount"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="order-actions-group">
                            <?php if ($order['status'] == 'pending' || $order['status'] == 'processing'): ?>
                                <form action="" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #<?= $order['id'] ?> này không? Thao tác này sẽ tự động hoàn lại sản phẩm vào kho hàng.');">
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