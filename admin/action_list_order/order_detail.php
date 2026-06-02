<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_admin.php';
require_once __DIR__ . '/../../user/action_checkout/auto_cancel_unpaid_orders.php';

autoCancelUnpaidBankOrders($pdo, 15);

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    header("Location: ../list_order.php");
    exit();
}

try {
    $stmtOrder = $pdo->prepare("
        SELECT 
            o.*,
            u.username,
            u.full_name,
            u.email,
            u.phone AS user_phone,
            u.address AS user_address
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
        LIMIT 1
    ");
    $stmtOrder->execute([$order_id]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: ../list_order.php");
        exit();
    }

    $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmtItems->execute([$order_id]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi truy vấn đơn hàng: " . $e->getMessage());
}

function formatMoney($value) {
    return number_format((float)$value, 0, ',', '.') . '₫';
}

function statusBadge($status) {
    $badge_class = 'badge-info';
    $status_vi = $status;

    if ($status == 'pending') { $badge_class = 'badge-warning'; $status_vi = 'Chờ thanh toán'; }
    elseif ($status == 'processing') { $badge_class = 'badge-warning'; $status_vi = 'Đang xử lí'; }
    elseif ($status == 'shipped') { $badge_class = 'badge-depending'; $status_vi = 'Đang vận chuyển'; }
    elseif ($status == 'completed') { $badge_class = 'badge-success'; $status_vi = 'Hoàn thành'; }
    elseif ($status == 'cancelled') { $badge_class = 'badge-danger'; $status_vi = 'Đã hủy'; }

    return '<span class="badge ' . $badge_class . '">' . htmlspecialchars($status_vi) . '</span>';
}

function paymentMethodText($method) {
    if ($method === 'bank') return 'Chuyển khoản ngân hàng';
    if ($method === 'cod') return 'Thanh toán khi nhận hàng';
    return $method ?: 'Chưa xác định';
}

function paymentStatusText($status) {
    if ($status === 'paid') return 'Đã thanh toán';
    if ($status === 'unpaid') return 'Chưa thanh toán';
    if ($status === 'pending') return 'Chờ thanh toán';
    if ($status === 'failed') return 'Thanh toán thất bại';
    return $status ?: 'Chưa xác định';
}

$subtotal = 0;

foreach ($items as $item) {
    $subtotal += ((float)$item['price'] * (int)$item['quantity']);
}

$used_points = (int)($order['used_points'] ?? 0);
$point_value = 100;
$point_discount = $used_points * $point_value;
$total_amount = (float)$order['total_amount'];
$total_discount = max($subtotal - $total_amount, 0);
$member_discount = max($total_discount - $point_discount, 0);

$page_title = 'Chi tiết đơn hàng ';
$page_icon = 'fa-solid fa-file-invoice';
$custom_css = '
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_order_detail.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_notification.css">
';

include '../includes/header.php';
?>

            <div class="container dashboard-container">
                <section class="section-block">

                    <div class="order-detail-header">
                        <div>
                            <h2>
                                <i class="fa-solid fa-file-invoice"></i>
                                Chi tiết đơn hàng #FD-<?= $order_id ?>
                            </h2>
                            <p>Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                        </div>

                        <div>
                            <?= statusBadge($order['status']) ?>
                        </div>
                    </div>

                    <div class="order-detail-grid">

                        <div class="order-detail-main">

                            <div class="order-detail-card">
                                <h3><i class="fa-solid fa-user"></i> Thông tin khách hàng</h3>

                                <div class="detail-info-grid">
                                    <div>
                                        <span>User ID</span>
                                        <strong><?= htmlspecialchars($order['user_id']) ?></strong>
                                    </div>

                                    <div>
                                        <span>Tên đăng nhập</span>
                                        <strong><?= htmlspecialchars($order['username']) ?></strong>
                                    </div>

                                    <div>
                                        <span>Họ tên</span>
                                        <strong><?= htmlspecialchars($order['full_name'] ?? 'Chưa cập nhật') ?></strong>
                                    </div>

                                    <div>
                                        <span>Email</span>
                                        <strong><?= htmlspecialchars($order['email'] ?? 'Chưa cập nhật') ?></strong>
                                    </div>

                                    <div>
                                        <span>Số điện thoại</span>
                                        <strong><?= htmlspecialchars($order['user_phone'] ?? 'Chưa cập nhật') ?></strong>
                                    </div>

                                    <div>
                                        <span>Tên tài khoản</span>
                                        <strong><?= htmlspecialchars($order['username']) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="order-detail-card">
                                <h3><i class="fa-solid fa-location-dot"></i> Thông tin nhận hàng</h3>

                                <div class="shipping-address-box">
                                    <?= nl2br(htmlspecialchars($order['shipping_address'] ?? 'Chưa có địa chỉ giao hàng')) ?>
                                </div>
                            </div>

                            <div class="order-detail-card">
                                <h3><i class="fa-solid fa-box-open"></i> Sản phẩm trong đơn</h3>

                                <?php if (empty($items)): ?>
                                    <p class="text-muted">Không có sản phẩm trong đơn hàng.</p>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <?php
                                            $img = $item['product_image'] ?? '';

                                            if (empty($img)) {
                                                $img_src = "/FD-Tech/assets/images/logo-fd.jpg";
                                            } elseif (filter_var($img, FILTER_VALIDATE_URL)) {
                                                $img_src = $img;
                                            } elseif (strpos($img, 'upload/product_image/') === 0) {
                                                $img_src = "/FD-Tech/" . $img;
                                            } else {
                                                $img_src = "/FD-Tech/upload/product_image/" . $img;
                                            }
                                        ?>

                                        <div class="order-detail-item">
                                            <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" onerror="this.src='/FD-Tech/assets/images/logo-fd.jpg'">

                                            <div class="order-detail-item-info">
                                                <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                                                <p>Số lượng: <?= (int)$item['quantity'] ?></p>
                                            </div>

                                            <div class="order-detail-item-price">
                                                <span><?= formatMoney($item['price']) ?></span>
                                                <strong><?= formatMoney((float)$item['price'] * (int)$item['quantity']) ?></strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="order-detail-card">
                                <h3><i class="fa-solid fa-credit-card"></i> Thanh toán</h3>

                                <div class="detail-info-grid">
                                    <div>
                                        <span>Hình thức thanh toán</span>
                                        <strong><?= htmlspecialchars(paymentMethodText($order['payment_method'] ?? '')) ?></strong>
                                    </div>

                                    <div>
                                        <span>Trạng thái thanh toán</span>
                                        <strong><?= htmlspecialchars(paymentStatusText($order['payment_status'] ?? '')) ?></strong>
                                    </div>

                                    <div>
                                        <span>Mã thanh toán</span>
                                        <strong><?= htmlspecialchars($order['payment_code'] ?? '-') ?></strong>
                                    </div>

                                    <div>
                                        <span>Thời gian thanh toán</span>
                                        <strong>
                                            <?= !empty($order['paid_at']) ? date('d/m/Y H:i', strtotime($order['paid_at'])) : 'Chưa thanh toán' ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($order['note'])): ?>
                                <div class="order-detail-card">
                                    <h3><i class="fa-solid fa-note-sticky"></i> Ghi chú đơn hàng</h3>
                                    <div class="shipping-address-box">
                                        <?= nl2br(htmlspecialchars($order['note'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="order-detail-side">

                            <div class="order-detail-card order-summary-card">
                                <h3><i class="fa-solid fa-calculator"></i> Thông tin thanh toán</h3>

                                <div class="summary-line">
                                    <span>Tạm tính sản phẩm</span>
                                    <strong><?= formatMoney($subtotal) ?></strong>
                                </div>

                                <div class="summary-line">
                                    <span>FD Point đã dùng</span>
                                    <strong><?= number_format($used_points, 0, ',', '.') ?> FDp</strong>
                                </div>

                                <div class="summary-line">
                                    <span>Giảm bằng FDp</span>
                                    <strong class="text-success">-<?= formatMoney($point_discount) ?></strong>
                                </div>

                                <div class="summary-line">
                                    <span>Ưu đãi thành viên / giảm giá khác</span>
                                    <strong class="text-success">-<?= formatMoney($member_discount) ?></strong>
                                </div>

                                <div class="summary-line summary-total">
                                    <span>Tổng thanh toán</span>
                                    <strong><?= formatMoney($total_amount) ?></strong>
                                </div>
                            </div>

                            <div class="order-detail-card">
                                <h3><i class="fa-solid fa-rotate"></i> Cập nhật trạng thái</h3>

                                <select id="orderStatusSelect" class="order-status-select">
                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ thanh toán</option>
                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                    <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Hủy</option>
                                </select>
                            </div>

                            <div class="order-detail-actions">
                                <button type="button" onclick="window.print()" class="btn btn-secondary">
                                    <i class="fa-solid fa-file-invoice"></i> In chi tiết đơn hàng
                                </button>

                                <button type="button" id="btnUpdateOrderStatus" data-id="<?= $order_id ?>" class="btn btn-primary">
                                    <i class="fa-solid fa-rotate"></i> Cập nhật
                                </button>

                                <button type="button" onclick="window.location.href='../list_order.php'" class="btn btn-danger">
                                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                                </button>
                            </div>

                        </div>

                    </div>

                </section>
            </div>
        </main>
    </div>
<script src="/FD-Tech/assets/js/script_dashboard.js"></script>
<script src="/FD-Tech/assets/js/script_order_detail.js"></script>

</body>
</html>