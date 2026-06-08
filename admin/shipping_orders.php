<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '/../auth/check_admin.php';

$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

try {
    $countPreparing = (int)$pdo->query("
        SELECT COUNT(*) 
        FROM orders o
        LEFT JOIN order_shipping os ON o.id = os.order_id
        WHERE o.status = 'shipped'
        AND (os.shipping_status IS NULL OR os.shipping_status = 'preparing')
    ")->fetchColumn();

    $countShipping = (int)$pdo->query("
        SELECT COUNT(*) 
        FROM orders o
        JOIN order_shipping os ON o.id = os.order_id
        WHERE o.status = 'shipped'
        AND os.shipping_status = 'shipping'
    ")->fetchColumn();

    $countDelivered = (int)$pdo->query("
        SELECT COUNT(*) 
        FROM orders o
        JOIN order_shipping os ON o.id = os.order_id
        WHERE o.status = 'shipped'
        AND os.shipping_status = 'delivered'
    ")->fetchColumn();

    $countCompleted = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn();

    $sql = "
        SELECT 
            o.id AS order_id,
            o.user_id,
            o.total_amount,
            o.status,
            o.shipping_address,
            o.created_at,
            u.username,
            os.shipping_status,
            os.carrier_name,
            os.tracking_number,
            os.shipping_cost,
            os.estimated_delivery,
            os.delivered_at,
            os.notes,
            os.updated_at AS shipping_updated_at
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_shipping os ON o.id = os.order_id
        WHERE o.status IN ('shipped', 'completed')
    ";

    $params = [];

    if ($search !== '') {
        $sql .= " AND (o.id LIKE ? OR u.username LIKE ? OR os.tracking_number LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($status_filter !== '') {
        if ($status_filter === 'preparing') {
            $sql .= " AND o.status = 'shipped' AND (os.shipping_status IS NULL OR os.shipping_status = 'preparing')";
        } elseif ($status_filter === 'shipping') {
            $sql .= " AND o.status = 'shipped' AND os.shipping_status = 'shipping'";
        } elseif ($status_filter === 'delivered') {
            $sql .= " AND o.status = 'shipped' AND os.shipping_status = 'delivered'";
        } elseif ($status_filter === 'completed') {
            $sql .= " AND o.status = 'completed'";
        }
    }

    $sql .= " ORDER BY 
                FIELD(o.status, 'shipped', 'completed'),
                FIELD(COALESCE(os.shipping_status, 'preparing'), 'preparing', 'shipping', 'delivered'),
                o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

function shippingStatusText($order_status, $shipping_status) {
    if ($order_status === 'shipped' && ($shipping_status === null || $shipping_status === '' || $shipping_status === 'preparing')) return 'Đang chuẩn bị';
    if ($order_status === 'shipped' && $shipping_status === 'shipping') return 'Đang giao hàng';
    if ($order_status === 'shipped' && $shipping_status === 'delivered') return 'Đã giao hàng';
    if ($order_status === 'completed') return 'Đã hoàn thành';
    if ($order_status === 'cancelled') return 'Đã hủy';
    return $order_status;
}

function statusClass($order_status, $shipping_status = '') {
    if ($order_status === 'shipped' && ($shipping_status === null || $shipping_status === '' || $shipping_status === 'preparing')) return 'badge-processing';
    if ($order_status === 'shipped' && $shipping_status === 'shipping') return 'badge-shipped';
    if ($order_status === 'shipped' && $shipping_status === 'delivered') return 'badge-completed';
    if ($order_status === 'completed') return 'badge-completed';
    if ($order_status === 'cancelled') return 'badge-cancelled';
    return 'badge-default';
}

function formatMoney($value) {
    return number_format((float)$value, 0, ',', '.') . '₫';
}

function formatDateVN($date) {
    if (empty($date)) return 'Chưa có';
    return date('d/m/Y', strtotime($date));
}

function formatDateTimeVN($datetime) {
    if (empty($datetime)) return 'Chưa có';
    return date('d/m/Y H:i', strtotime($datetime));
}

$page_title = 'Quản lí vận chuyển';
$page_icon = 'fa-solid fa-truck-fast';
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/style_shipping_orders.css">';

include 'includes/header.php';
?>

<div class="shipping-wrapper">

    <?php if (isset($_GET['msg'])): ?>
        <div class="shipping-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="shipping-alert error">
            <i class="fa-solid fa-circle-xmark"></i>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <div class="shipping-stats">
        <div class="shipping-stat-card">
            <span>Đang chuẩn bị</span>
            <strong><?= $countPreparing ?></strong>
        </div>

        <div class="shipping-stat-card">
            <span>Đang giao hàng</span>
            <strong><?= $countShipping ?></strong>
        </div>

        <div class="shipping-stat-card">
            <span>Đã giao hàng</span>
            <strong><?= $countDelivered ?></strong>
        </div>

        <div class="shipping-stat-card">
            <span>Đã hoàn thành</span>
            <strong><?= $countCompleted ?></strong>
        </div>
    </div>

    <div class="shipping-card">
        <div class="shipping-header">
            <form method="GET" class="shipping-filter">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Tìm mã đơn, username, mã vận đơn..."
                    value="<?= htmlspecialchars($search) ?>"
                >

                <select name="status" class="form-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="preparing" <?= $status_filter === 'preparing' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                    <option value="shipping" <?= $status_filter === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>Đã giao hàng</option>
                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                </select>

                <button class="btn btn-primary">
                    <i class="fa-solid fa-search"></i> Lọc
                </button>

                <?php if ($search !== '' || $status_filter !== ''): ?>
                    <a href="shipping_orders.php" class="btn btn-secondary">Bỏ lọc</a>
                <?php endif; ?>
            </form>
        </div>

        <table class="shipping-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Đơn vị VC</th>
                    <th>Mã vận đơn / Phí ship</th>
                    <th>Dự kiến / Ghi chú</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $row): ?>
                    <?php
                        $isCompleted = $row['status'] === 'completed';
                        $shipping_status = $row['shipping_status'] ?? 'preparing';
                        $isPreparing = $row['status'] === 'shipped' && ($shipping_status === '' || $shipping_status === 'preparing');
                        $isShipping = $row['status'] === 'shipped' && $shipping_status === 'shipping';
                        $isDeliveredWaitingConfirm = $row['status'] === 'shipped' && $shipping_status === 'delivered';
                        $form_id = 'shipping-form-' . $row['order_id'];

                        $carrier_name = $row['carrier_name'] ?: 'FD Express';
                        $tracking_number = $row['tracking_number'] ?: 'Chưa có mã vận đơn';
                        $shipping_cost = (float)($row['shipping_cost'] ?? 0);
                        $estimated_delivery = $row['estimated_delivery'] ?? '';
                        $notes = $row['notes'] ?? '';
                    ?>

                    <tr class="<?= $isCompleted ? 'row-completed' : '' ?>">

                        <td>
                            <form id="<?= $form_id ?>" method="POST" action="action_shipping/update_shipping.php"></form>

                            <input form="<?= $form_id ?>" type="hidden" name="order_id" value="<?= (int)$row['order_id'] ?>">

                            <strong>#FD-<?= htmlspecialchars($row['order_id']) ?></strong>

                            <a href="action_list_order/order_detail.php?id=<?= $row['order_id'] ?>" class="detail-link">
                                Chi tiết
                            </a>
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($row['username'] ?? 'Không rõ') ?></strong>
                            <div class="shipping-address">
                                <?= nl2br(htmlspecialchars($row['shipping_address'] ?? '')) ?>
                            </div>
                        </td>

                        <td class="price">
                            <?= formatMoney($row['total_amount']) ?>
                        </td>

                        <td>
                            <span class="status-badge <?= statusClass($row['status'], $shipping_status) ?>">
                                <?= shippingStatusText($row['status'], $shipping_status) ?>
                            </span>

                            <select
                                form="<?= $form_id ?>"
                                name="order_status"
                                class="form-control small-select status-select"
                                <?= $isCompleted ? 'disabled' : '' ?>
                            >
                                <option value="preparing" <?= $isPreparing ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                <option value="shipped" <?= $isShipping ? 'selected' : '' ?>>Đang giao hàng</option>
                                <option value="delivered" <?= $isDeliveredWaitingConfirm ? 'selected' : '' ?>>Đã giao hàng</option>
                            </select>
                        </td>

                        <td>
                            <div class="shipping-db-info">
                                <strong><?= htmlspecialchars($carrier_name) ?></strong>
                            </div>
                        </td>

                        <td>
                            <div class="shipping-db-info">
                                <strong><?= htmlspecialchars($tracking_number) ?></strong>
                                <span>Phí ship: <?= formatMoney($shipping_cost) ?></span>
                            </div>
                        </td>

                        <td>
                            <div class="shipping-db-info">
                                <strong>Dự kiến: <?= formatDateVN($estimated_delivery) ?></strong>

                                <?php if (!empty($notes)): ?>
                                    <span><?= htmlspecialchars($notes) ?></span>
                                <?php else: ?>
                                    <span>Không có ghi chú</span>
                                <?php endif; ?>

                                <?php if (!empty($row['delivered_at'])): ?>
                                    <span>Đã giao: <?= formatDateTimeVN($row['delivered_at']) ?></span>
                                <?php elseif (!empty($row['shipping_updated_at'])): ?>
                                    <span>Cập nhật: <?= formatDateTimeVN($row['shipping_updated_at']) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td>
                            <?php if ($isCompleted): ?>
                                <button type="button" class="btn btn-disabled" disabled>
                                    Đã khóa
                                </button>
                            <?php else: ?>
                                <button
                                    form="<?= $form_id ?>"
                                    type="submit"
                                    formnovalidate
                                    class="btn btn-primary btn-save-shipping"
                                >
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Lưu trạng thái
                                </button>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="empty-row">Không có đơn hàng nào cần vận chuyển.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</div>

<script src="/FD-Tech/assets/js/script_dashboard.js"></script>
<script src="/FD-Tech/assets/js/script_shipping_orders.js"></script>
</body>
</html>