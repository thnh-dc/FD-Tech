<?php
if (!isset($user_id)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user_id = $_SESSION['user_id'] ?? 0;
}

try {
    $stmt_point = $pdo->prepare("
        SELECT point
        FROM users
        WHERE id = ?
        LIMIT 1
    ");
    $stmt_point->execute([$user_id]);
    $user_point_data = $stmt_point->fetch(PDO::FETCH_ASSOC);
    $user_point = (int) ($user_point_data['point'] ?? 0);

    $stmt_orders = $pdo->prepare("
        SELECT *
        FROM orders
        WHERE user_id = ?
        AND status = 'completed'
        ORDER BY created_at DESC
    ");
    $stmt_orders->execute([$user_id]);
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    $total_orders = count($orders);
    $total_spent = 0;

    foreach ($orders as $order) {
        $total_spent += $order['total_amount'] ?? 0;
    }

    $months_data = [];

    for ($i = 5; $i >= 0; $i--) {
        $month_target = date('Y-m', strtotime("-$i months"));
        $month_label = "Tháng " . date('m/Y', strtotime("-$i months"));

        $months_data[$month_target] = [
            'label' => $month_label,
            'amount' => 0
        ];
    }

    foreach ($orders as $order) {
        $order_month = date('Y-m', strtotime($order['created_at']));

        if (isset($months_data[$order_month])) {
            $months_data[$order_month]['amount'] += $order['total_amount'];
        }
    }

} catch (PDOException $e) {
    $orders = [];
    $total_orders = 0;
    $total_spent = 0;
    $months_data = [];
    $user_point = 0;
}
?>

<link rel="stylesheet" href="../assets/css/style_profile_history_bought.css">

<div class="profile-header">
    <h2>Thống kê & Lịch sử mua hàng</h2>
    <p>Xem lại các sản phẩm bạn đã sở hữu, điểm tiêu dùng và biểu đồ chi tiêu</p>
</div>

<div class="profile-body-split">

    <div class="profile-form-area" class="profile-form-area-full">
        <div class="profile-form" class="profile-form-full">
            <h3 class="table-title">Danh sách đơn hàng đã mua</h3>

            <?php if (empty($orders)): ?>
                <div class="empty-orders-box">
                    <i class="fas fa-shopping-bag empty-orders-icon"></i>
                    <p>Bạn chưa có đơn hàng thành công nào trong lịch sử.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Ngày mua</th>
                                <th>Tổng tiền</th>
                                <th style="text-align: center;">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="order-code-text">
                                        #<?php echo htmlspecialchars($order['order_code'] ?? $order['id']); ?>
                                    </td>
                                    <td class="order-date-text">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="order-price-text">
                                        <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="status-badge-success">Thành công</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="profile-avatar-sidebar">
        <div class="avatar-preview-box-custom">

            <h3 class="sidebar-box-title">TỔNG QUAN CHI TIÊU</h3>

            <div class="center-stat-block">
                <p class="stat-label-sub">Tổng tích lũy mua sắm</p>
                <p class="stat-value-big">
                    <?php echo number_format($total_spent, 0, ',', '.'); ?><span class="stat-unit-small">đ</span>
                </p>
            </div>

            <hr class="dashed-line">

            <div class="sidebar-row-info">
                <span>Tổng đơn hàng đã mua:</span>
                <strong><?php echo $total_orders; ?> đơn</strong>
            </div>

            <div class="sidebar-row-info">
                <span>FD point hiện có:</span>
                <strong class="point-text-highlight">
                    <?php echo number_format($user_point, 0, ',', '.'); ?> FDp
                </strong>
            </div>

            <div class="point-tip-box">
                <p class="point-tip-text">FD point sẽ được tích lũy khi đơn hàng hoàn thành</p>
                <p><b>10.000đ = 1FDp</b></p>
            </div>

            <?php if (!empty($orders)): ?>
                <hr class="solid-line">

                <h4 class="monthly-chart-title">Chi tiêu 6 tháng gần nhất</h4>

                <div class="monthly-chart-list">
                    <?php foreach ($months_data as $month): ?>
                        <div class="sidebar-row-info">
                            <span><?php echo $month['label']; ?>:</span>
                            <strong class="order-price-text">
                                <?php echo number_format($month['amount'], 0, ',', '.'); ?>đ
                            </strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <button type="button" class="btn-upload btn-continue-shopping" onclick="window.location.href='../user/index.php'">
            <i class="fas fa-shopping-cart" style="margin-right: 5px;"></i> Tiếp tục mua sắm
        </button>
    </div>

</div>