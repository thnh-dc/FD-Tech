<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '/check_admin.php';
require_once '../user/action_checkout/auto_cancel_unpaid_orders.php';

autoCancelUnpaidBankOrders($pdo, 15);
try {
    $user_filter = $_GET['user_id'] ?? '';

    if ($user_filter != '') {
        $sql = "SELECT o.*, u.username, os.carrier_name, os.tracking_number
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_shipping os ON o.id = os.order_id
                WHERE o.user_id = ?
                ORDER o.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_filter]);
    } else {
        $sql = "SELECT o.*, u.username, os.carrier_name, os.tracking_number
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_shipping os ON o.id = os.order_id
                ORDER BY o.created_at DESC";

        $stmt = $pdo->query($sql);
    }

    $orders = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>

<?php
$page_title = 'Quản lí đơn hàng';
$page_icon = 'fa-solid fa-cart-shopping';
// Nhúng thêm file CSS Vận chuyển chuyên dụng tách biệt
$custom_css = '
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_list_oder.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_notification.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_order_shipping.css">
';

include 'includes/header.php';
?>

<div class="container dashboard-container">
    <section class="section-block">
        <div class="card shadow-card" style="background: var(--bg-main); padding: var(--space-lg); border-radius: var(--radius-md);">
            <table class="data-table">
                <form method="GET" class="filter-form">
                    <input type="number" name="user_id" placeholder="Nhập User ID..." value="<?= $_GET['user_id'] ?? '' ?>">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </form>
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>User Name</th>
                        <th>Thông Tin Vận Chuyển</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Đặt</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $row): ?>
                            <tr>
                                <td>#FD-<?= $row['id'] ?></td>
                                <td class="user"><?= htmlspecialchars($row['username']) ?></td>
                                <td>
                                    <?php if(!empty($row['tracking_number'])): ?>
                                        <small style="display:block; font-weight:700; color:#1e293b;">
                                            <i class="fa-solid fa-truck-ramp-box"></i> <?= htmlspecialchars($row['carrier_name']) ?>
                                        </small>
                                        <small style="color:#64748b; font-variant-numeric: tabular-nums;">
                                            Mã vận đơn: <strong><?= htmlspecialchars($row['tracking_number']) ?></strong>
                                        </small>
                                    <?php else: ?>
                                        <span style="color:#cbd5e1; font-style:italic; font-size:0.85rem;">Chưa bàn giao đơn vị vận chuyển</span>
                                    <?php endif; ?>
                                </td>
                                <td class="price-highlight">
                                    <?= number_format($row['total_amount'], 0, ',', '.') ?>₫
                                </td>
                                <td>
                                    <?php 
                                        $status = $row['status'];
                                        $badge_class = 'badge-info';
                                        $status_vi = $status;

                                        // Thiết lập số bước active cho Timeline dựa trên status
                                        $step_count = 1; 
                                        if ($status == 'pending') { $badge_class = 'badge-warning'; $status_vi = 'Chờ thanh toán'; $step_count = 1; }
                                        elseif ($status == 'processing') { $badge_class = 'badge-warning'; $status_vi = 'Đang xử lí'; $step_count = 2; }
                                        elseif ($status == 'shipped') { $badge_class = 'badge-depending'; $status_vi = 'Đang vận chuyển'; $step_count = 3; }
                                        elseif ($status == 'completed') { $badge_class = 'badge-success'; $status_vi = 'Hoàn thành'; $step_count = 4; }
                                        elseif ($status == 'cancelled') { $badge_class = 'badge-danger'; $status_vi = 'Đã hủy'; $step_count = 0; }
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= $status_vi ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                <td style="position: relative;">
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-toggle" data-id="<?= $row['id'] ?>">Xem chi tiết</button>
                                        <button class="btn btn-primary btn-action" data-id="<?= $row['id'] ?>">Cập nhật</button>
                                        <div class="action-menu">
                                            <button data-status="processing">Đang xử lý</button>
                                            <button data-status="shipped">Đang giao</button>
                                            <button data-status="completed">Hoàn thành</button>
                                            <button data-status="cancelled">Hủy</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr class="order-detail-row" id="detail-<?= $row['id'] ?>" style="display:none;">
                                <td colspan="7">
                                    <div class="order-detail-content" style="padding: 15px; background: #fff; border-radius: 6px;">
                                        </div>

                                    <div class="shipping-logistics-container" style="background:#fff; padding: 20px; border-top: 1px dashed #e2e8f0;">
                                        
                                        <h4 style="margin: 0 0 15px 0; color: #334155;"><i class="fa-solid fa-map-location-dot"></i> Lộ trình vận chuyển đơn hàng</h4>
                                        <?php if($status !== 'cancelled'): ?>
                                            <div class="shipping-timeline">
                                                <div class="shipping-timeline-bar" style="width: <?= (($step_count - 1) / 3) * 100 ?>%;"></div>
                                                
                                                <div class="timeline-step <?= $step_count >= 1 ? 'active' : '' ?>">
                                                    <div class="timeline-icon"><i class="fa-solid fa-receipt"></i></div>
                                                    <div class="timeline-text">Đã đặt hàng</div>
                                                </div>
                                                <div class="timeline-step <?= $step_count >= 2 ? 'active' : '' ?>">
                                                    <div class="timeline-icon"><i class="fa-solid fa-box-open"></i></div>
                                                    <div class="timeline-text">Đóng gói xong</div>
                                                </div>
                                                <div class="timeline-step <?= $step_count >= 3 ? 'active' : '' ?>">
                                                    <div class="timeline-icon"><i class="fa-solid fa-truck-fast"></i></div>
                                                    <div class="timeline-text">Đang giao hàng</div>
                                                </div>
                                                <div class="timeline-step <?= $step_count >= 4 ? 'active' : '' ?>">
                                                    <div class="timeline-icon"><i class="fa-solid fa-house-chimney-user"></i></div>
                                                    <div class="timeline-text">Thành công</div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p style="color: var(--danger); font-weight: 700; font-size: 0.9rem;"><i class="fa-solid fa-circle-xmark"></i> Đơn hàng này đã bị hủy bỏ, tiến trình giao vận bị ngắt.</p>
                                        <?php endif; ?>

                                        <?php
                                            $stmt_ship = $pdo->prepare("SELECT * FROM order_shipping WHERE order_id = ?");
                                            $stmt_ship->execute([$row['id']]);
                                            $ship_data = $stmt_ship->fetch();
                                        ?>

                                        <div class="shipping-update-box">
                                            <h4><i class="fa-solid fa-pen-to-square"></i> Cập nhật thông tin vận đơn từ đối tác (GHTK / GHN / VNPost...)</h4>
                                            <form class="shipping-submit-form">
                                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                                <div class="shipping-form-grid">
                                                    <div class="shipping-form-group">
                                                        <label>Đơn vị vận chuyển</label>
                                                        <select name="carrier_name" required>
                                                            <option value="Giao Hàng Tiết Kiệm (GHTK)" <?= ($ship_data['carrier_name'] ?? '') == 'Giao Hàng Tiết Kiệm (GHTK)' ? 'selected' : '' ?>>Giao Hàng Tiết Kiệm (GHTK)</option>
                                                            <option value="Giao Hàng Nhanh (GHN)" <?= ($ship_data['carrier_name'] ?? '') == 'Giao Hàng Nhanh (GHN)' ? 'selected' : '' ?>>Giao Hàng Nhanh (GHN)</option>
                                                            <option value="Viettel Post" <?= ($ship_data['carrier_name'] ?? '') == 'Viettel Post' ? 'selected' : '' ?>>Viettel Post</option>
                                                            <option value="VNPost (Bưu điện VN)" <?= ($ship_data['carrier_name'] ?? '') == 'VNPost (Bưu điện VN)' ? 'selected' : '' ?>>VNPost (Bưu điện VN)</option>
                                                            <option value="J&T Express" <?= ($ship_data['carrier_name'] ?? '') == 'J&T Express' ? 'selected' : '' ?>>J&T Express</option>
                                                        </select>
                                                    </div>
                                                    <div class="shipping-form-group">
                                                        <label>Mã vận đơn (Tracking ID)</label>
                                                        <input type="text" name="tracking_number" placeholder="Ví dụ: GHTK12938492" value="<?= htmlspecialchars($ship_data['tracking_number'] ?? '') ?>" required>
                                                    </div>
                                                    <div class="shipping-form-group">
                                                        <label>Phí vận chuyển thực tế (₫)</label>
                                                        <input type="number" name="shipping_cost" value="<?= (int)($ship_data['shipping_cost'] ?? 0) ?>">
                                                    </div>
                                                    <div class="shipping-form-group">
                                                        <label>Ngày dự kiến nhận</label>
                                                        <input type="date" name="estimated_delivery" value="<?= $ship_data['estimated_delivery'] ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="shipping-form-group">
                                                    <label>Lộ trình bưu cục ghi chú</label>
                                                    <input type="text" name="notes" placeholder="Ví dụ: Đã rời kho tổng Hà Nội, đang chuyển tới bưu cục đích..." value="<?= htmlspecialchars($ship_data['notes'] ?? '') ?>">
                                                </div>
                                                <div style="text-align: right; margin-top: 15px;">
                                                    <button type="submit" class="btn btn-primary btn-submit-shipping" style="padding: 8px 20px; font-size: 0.85rem;">
                                                        <i class="fa-solid fa-floppy-disk"></i> Lưu thông tin vận đơn
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">Chưa có đơn hàng nào được ghi nhận.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

</main>
</div>

<script src="../assets/js/script_dashboard.js"></script>
<script src="/FD-Tech/assets/js/script_order_shipping.js"></script>
</body>
</html>