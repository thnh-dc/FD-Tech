<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '/../auth/check_admin.php';

$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

try {
    $countProcessing = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn();
    $countShipped = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'shipped'")->fetchColumn();
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
            os.carrier_name,
            os.tracking_number,
            os.shipping_cost,
            os.estimated_delivery,
            os.notes,
            os.updated_at AS shipping_updated_at
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_shipping os ON o.id = os.order_id
        WHERE o.status IN ('processing', 'shipped', 'completed')
    ";

    $params = [];

    if ($search !== '') {
        $sql .= " AND (o.id LIKE ? OR u.username LIKE ? OR os.tracking_number LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($status_filter !== '') {
        $sql .= " AND o.status = ?";
        $params[] = $status_filter;
    }

    $sql .= " ORDER BY 
                FIELD(o.status, 'processing', 'shipped', 'completed'),
                o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

function statusText($status) {
    return match ($status) {
        'processing' => 'Đang xử lý',
        'shipped' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'pending' => 'Chờ thanh toán',
        default => $status
    };
}

function statusClass($status) {
    return match ($status) {
        'processing' => 'badge-processing',
        'shipped' => 'badge-shipped',
        'completed' => 'badge-completed',
        'cancelled' => 'badge-cancelled',
        default => 'badge-default'
    };
}

$page_title = 'Quản lí vận chuyển';
$page_icon = 'fa-solid fa-truck-fast';
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/style_shipping_orders.css">';

include 'includes/header.php';
?>

<div class="shipping-wrapper">

    <div class="shipping-stats">
        <div class="shipping-stat-card">
            <span>Đang xử lý</span>
            <strong><?= $countProcessing ?></strong>
        </div>

        <div class="shipping-stat-card">
            <span>Đang giao</span>
            <strong><?= $countShipped ?></strong>
        </div>

        <div class="shipping-stat-card">
            <span>Hoàn thành</span>
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
                    <option value="processing" <?= $status_filter === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                    <option value="shipped" <?= $status_filter === 'shipped' ? 'selected' : '' ?>>Đang giao</option>
                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
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
                        $isShipped = $row['status'] === 'shipped';
                    ?>

                    <tr class="<?= $isCompleted ? 'row-completed' : '' ?>">
                        <form method="POST" action="action_shipping/update_shipping.php">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">

                            <td>
                                <strong>#FD-<?= htmlspecialchars($row['order_id']) ?></strong>

                                <a 
                                    href="action_list_order/order_detail.php?id=<?= $row['order_id'] ?>" 
                                    class="detail-link"
                                >
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
                                <?= number_format($row['total_amount'], 0, ',', '.') ?>₫
                            </td>

                            <td>
                                <span class="status-badge <?= statusClass($row['status']) ?>">
                                    <?= statusText($row['status']) ?>
                                </span>

                                <select name="order_status" class="form-control small-select status-select" <?= $isCompleted ? 'disabled' : '' ?>>
                                    <option value="processing" <?= $row['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                    <option value="shipped" <?= $row['status'] === 'shipped' ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="completed" <?= $row['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                </select>

                                <?php if ($isCompleted): ?>
                                    <input type="hidden" name="order_status" value="completed">
                                <?php endif; ?>
                            </td>

                            <td>
                                <select name="carrier_name" class="form-control small-select" <?= $isCompleted ? 'disabled' : '' ?> required>
                                    <option value="">-- Chọn --</option>
                                    <?php
                                    $carriers = ['GHTK', 'GHN', 'Viettel Post', 'J&T Express', 'VNPost', 'Ninja Van'];
                                    foreach ($carriers as $carrier):
                                    ?>
                                        <option value="<?= $carrier ?>" <?= $row['carrier_name'] === $carrier ? 'selected' : '' ?>>
                                            <?= $carrier ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <?php if ($isCompleted): ?>
                                    <input type="hidden" name="carrier_name" value="<?= htmlspecialchars($row['carrier_name'] ?? '') ?>">
                                <?php endif; ?>
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="tracking_number"
                                    class="form-control tracking-input"
                                    value="<?= htmlspecialchars($row['tracking_number'] ?? '') ?>"
                                    placeholder="VD: GHN123456"
                                    <?= $isCompleted ? 'readonly' : '' ?>
                                    required
                                >

                                <input
                                    type="number"
                                    name="shipping_cost"
                                    class="form-control shipping-cost"
                                    value="<?= htmlspecialchars($row['shipping_cost'] ?? 0) ?>"
                                    placeholder="Phí ship"
                                    <?= $isCompleted ? 'readonly' : '' ?>
                                >
                            </td>

                            <td>
                                <input
                                    type="date"
                                    name="estimated_delivery"
                                    class="form-control"
                                    value="<?= htmlspecialchars($row['estimated_delivery'] ?? '') ?>"
                                    <?= $isCompleted ? 'readonly' : '' ?>
                                >

                                <textarea
                                    name="notes"
                                    class="form-control shipping-note"
                                    placeholder="Ghi chú..."
                                    <?= $isCompleted ? 'readonly' : '' ?>
                                ><?= htmlspecialchars($row['notes'] ?? '') ?></textarea>

                                <?php if (!empty($row['shipping_updated_at'])): ?>
                                    <div class="updated-time">
                                        Cập nhật: <?= date('d/m/Y H:i', strtotime($row['shipping_updated_at'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($isCompleted): ?>
                                    <button type="button" class="btn btn-disabled" disabled>
                                        Đã khóa
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-primary btn-save-shipping">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        Lưu
                                    </button>
                                <?php endif; ?>
                            </td>
                        </form>
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