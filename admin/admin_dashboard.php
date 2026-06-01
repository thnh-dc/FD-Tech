<?php
session_start();
require_once '../config/database.php';
require_once __DIR__ . '/check_admin.php';

$totalRevenue = 0;
$totalImportCost = 0;
$netProfit = 0;
$newOrdersCount = 0;
$totalProductsCount = 0;
$chartData = [];

try {
    $stmtRevenue = $pdo->prepare("
        SELECT COALESCE(SUM(total_amount), 0) AS total_revenue 
        FROM orders 
        WHERE status = 'completed'
    ");
    $stmtRevenue->execute();
    $revenueRow = $stmtRevenue->fetch(PDO::FETCH_ASSOC);
    $totalRevenue = (float)($revenueRow['total_revenue'] ?? 0);

    $stmtImports = $pdo->prepare("
        SELECT COALESCE(SUM(total_amount), 0) AS total_import_cost 
        FROM import_orders 
        WHERE status = 'completed'
    ");
    $stmtImports->execute();
    $importRow = $stmtImports->fetch(PDO::FETCH_ASSOC);
    $totalImportCost = (float)($importRow['total_import_cost'] ?? 0);

    $netProfit = $totalRevenue - $totalImportCost;

    $stmtOrders = $pdo->prepare("
        SELECT COUNT(id) AS new_orders 
        FROM orders 
        WHERE status = 'pending'
    ");
    $stmtOrders->execute();
    $ordersRow = $stmtOrders->fetch(PDO::FETCH_ASSOC);
    $newOrdersCount = (int)($ordersRow['new_orders'] ?? 0);

    $stmtProducts = $pdo->prepare("
        SELECT COUNT(id) AS total_products 
        FROM products
    ");
    $stmtProducts->execute();
    $productsRow = $stmtProducts->fetch(PDO::FETCH_ASSOC);
    $totalProductsCount = (int)($productsRow['total_products'] ?? 0);

    $stmtChart = $pdo->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month,
            COALESCE(SUM(total_amount), 0) AS revenue
        FROM orders
        WHERE status = 'completed'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month
    ");
    $stmtChart->execute();
    $chartData = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

$labels = [];
$data = [];

foreach ($chartData as $row) {
    $labels[] = $row['month'];
    $data[] = (float)$row['revenue'];
}

$page_title = 'Thống kê';
$page_icon = 'fa-solid fa-chart-pie';

include 'includes/header.php';
?>

        <div class="container dashboard-container">

            <section class="section-block">
                <div class="stats-grid">

                    <div class="stat-card">
                        <div class="stat-icon stat-icon-revenue">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>

                        <div class="stat-info">
                            <span class="text-muted">Tổng doanh thu</span>
                            <h3><?= number_format($totalRevenue, 0, ',', '.') ?>₫</h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon stat-icon-import">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>

                        <div class="stat-info">
                            <span class="text-muted">Chi phí vốn nhập</span>
                            <h3><?= number_format($totalImportCost, 0, ',', '.') ?>₫</h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon <?= $netProfit >= 0 ? 'stat-icon-profit' : 'stat-icon-loss' ?>">
                            <i class="fa-solid <?= $netProfit >= 0 ? 'fa-chart-line' : 'fa-arrow-trend-down' ?>"></i>
                        </div>

                        <div class="stat-info">
                            <span class="text-muted">Lợi nhuận thuần</span>
                            <h3 class="<?= $netProfit >= 0 ? 'profit-text' : 'loss-text' ?>">
                                <?= number_format($netProfit, 0, ',', '.') ?>₫
                            </h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon stat-icon-orders">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>

                        <div class="stat-info">
                            <span class="text-muted">Đơn hàng mới</span>
                            <h3><?= number_format($newOrdersCount, 0, ',', '.') ?> đơn</h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon stat-icon-products">
                            <i class="fa-solid fa-cubes"></i>
                        </div>

                        <div class="stat-info">
                            <span class="text-muted">Sản phẩm đang bán</span>
                            <h3><?= number_format($totalProductsCount, 0, ',', '.') ?> món</h3>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block dashboard-chart-section">
                <div class="card stat-chart-placeholder">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Biểu đồ doanh thu</h3>
                            <p class="text-muted">Doanh thu các đơn hàng hoàn thành trong 6 tháng gần nhất</p>
                        </div>
                    </div>

                    <canvas id="revenueChart"></canvas>
                </div>
            </section>

        </div>

    </main>
</div>

<script>
    const revenueLabels = <?= json_encode($labels) ?>;
    const revenueData = <?= json_encode($data) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/FD-Tech/assets/js/script_dashboard.js"></script>
</body>
</html>