<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '../../auth/check_admin.php';
require_once '../user/action_checkout/auto_cancel_unpaid_orders.php';

autoCancelUnpaidBankOrders($pdo, 15);

$orders = [];
$user_filter = trim($_GET['user_id'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$total_orders = 0;
$total_pages = 1;

try {
    if ($user_filter !== '') {
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $stmtCount->execute([$user_filter]);
        $total_orders = (int)$stmtCount->fetchColumn();

        $sql = "SELECT o.*, u.username
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
                LIMIT $limit OFFSET $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_filter]);
    } else {
        $stmtCount = $pdo->query("SELECT COUNT(*) FROM orders");
        $total_orders = (int)$stmtCount->fetchColumn();

        $sql = "SELECT o.*, u.username
                FROM orders o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT $limit OFFSET $offset";

        $stmt = $pdo->query($sql);
    }

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_pages = max(1, (int)ceil($total_orders / $limit));

    if ($page > $total_pages) {
        $page = $total_pages;
    }

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>

<?php
$page_title = 'Quản lý đơn hàng';
$page_icon = 'fa-solid fa-cart-shopping';
$custom_css = '
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_list_oder.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_notification.css">
';

include 'includes/header.php';
?>

            <div class="container dashboard-container">
                <section class="section-block">
                    <div class="card shadow-card" style="background: var(--bg-main); padding: var(--space-lg); border-radius: var(--radius-md);">
                        <form method="GET" class="filter-form">
                            <input type="number" name="user_id" placeholder="Nhập User ID..." value="<?= htmlspecialchars($user_filter) ?>">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                            <?php if ($user_filter !== ''): ?>
                                <a href="list_order.php" class="btn btn-secondary">Bỏ lọc</a>
                            <?php endif; ?>
                        </form>

                        <div class="order-list-meta">
                            Tổng số đơn: <b><?= number_format($total_orders, 0, ',', '.') ?></b>
                        </div>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>User ID</th>
                                    <th>User Name</th>
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
                                            <td class="user"><?= htmlspecialchars($row['user_id']) ?></td>
                                            <td class="user"><?= htmlspecialchars($row['username']) ?></td>
                                            <td class="price-highlight">
                                                <?= number_format($row['total_amount'], 0, ',', '.') ?>₫
                                            </td>
                                            <td>
                                                <?php 
                                                    $status = $row['status'];
                                                    $badge_class = 'badge-info';
                                                    $status_vi = $status;

                                                    if ($status == 'pending') { $badge_class = 'badge-warning'; $status_vi = 'Chờ thanh toán'; }
                                                    elseif ($status == 'processing') { $badge_class = 'badge-warning'; $status_vi = 'Đang xử lí'; }
                                                    elseif ($status == 'shipped') { $badge_class = 'badge-depending'; $status_vi = 'Đang vận chuyển'; }
                                                    elseif ($status == 'completed') { $badge_class = 'badge-success'; $status_vi = 'Hoàn thành'; }
                                                    elseif ($status == 'cancelled') { $badge_class = 'badge-danger'; $status_vi = 'Đã hủy'; }
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= $status_vi ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>

                                            <td style="position: relative;">
                                                <div class="action-buttons">
                                                    <a href="action_list_order/order_detail.php?id=<?= $row['id'] ?>" class="btn btn-primary" title="Xem chi tiết đơn hàng">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>

                                                    <button class="btn btn-primary btn-action" data-id="<?= $row['id'] ?>">
                                                        <i class="fa-solid fa-rotate"></i> Cập nhật
                                                    </button>

                                                    <div class="action-menu">
                                                        <button data-status="processing">Đang xử lý</button>
                                                        <button data-status="shipped">Đang giao</button>
                                                        <button data-status="completed">Hoàn thành</button>
                                                        <button data-status="cancelled">Hủy</button>
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

                        <?php if ($total_pages > 1): ?>
                            <div class="order-pagination">
                                <?php
                                    $queryBase = [];
                                    if ($user_filter !== '') {
                                        $queryBase['user_id'] = $user_filter;
                                    }
                                ?>

                                <?php if ($page > 1): ?>
                                    <?php $queryBase['page'] = $page - 1; ?>
                                    <a class="page-link" href="list_order.php?<?= http_build_query($queryBase) ?>">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($total_pages, $page + 2);
                                ?>

                                <?php if ($startPage > 1): ?>
                                    <?php $queryBase['page'] = 1; ?>
                                    <a class="page-link" href="list_order.php?<?= http_build_query($queryBase) ?>">1</a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="page-dots">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php $queryBase['page'] = $i; ?>
                                    <a class="page-link <?= $i == $page ? 'active' : '' ?>" href="list_order.php?<?= http_build_query($queryBase) ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($endPage < $total_pages): ?>
                                    <?php if ($endPage < $total_pages - 1): ?>
                                        <span class="page-dots">...</span>
                                    <?php endif; ?>
                                    <?php $queryBase['page'] = $total_pages; ?>
                                    <a class="page-link" href="list_order.php?<?= http_build_query($queryBase) ?>"><?= $total_pages ?></a>
                                <?php endif; ?>

                                <?php if ($page < $total_pages): ?>
                                    <?php $queryBase['page'] = $page + 1; ?>
                                    <a class="page-link" href="list_order.php?<?= http_build_query($queryBase) ?>">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="../assets/js/script_dashboard.js"></script>
    <script src="../assets/js/script_list_order_admin.js"></script>
</body>
</html>