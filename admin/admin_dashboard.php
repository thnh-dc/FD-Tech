<?php
session_start();
include '../config/database.php';

try {
    // 1. Tính tổng doanh thu
    $stmtRevenue = $pdo->prepare("SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = 'completed'");
    $stmtRevenue->execute();
    $revenueRow = $stmtRevenue->fetch(PDO::FETCH_ASSOC);
    $totalRevenue = $revenueRow['total_revenue'] ?? 0; // Trả về 0 nếu chưa có doanh thu

    // 2. Đếm số lượng đơn hàng mới
    $stmtOrders = $pdo->prepare("SELECT COUNT(id) as new_orders FROM orders WHERE status = 'pending'");
    $stmtOrders->execute();
    $ordersRow = $stmtOrders->fetch(PDO::FETCH_ASSOC);
    $newOrdersCount = $ordersRow['new_orders'] ?? 0;

    // 3. Đếm số lượng sản phẩm đang bán
    $stmtProducts = $pdo->prepare("SELECT COUNT(id) as total_products FROM products");
    $stmtProducts->execute();
    $productsRow = $stmtProducts->fetch(PDO::FETCH_ASSOC);
    $totalProductsCount = $productsRow['total_products'] ?? 0;

    // 4.Truy vấn để render biểu đồ
    $stmtChart = $pdo->prepare("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(total_amount) as revenue
        FROM orders
        WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month");
    $stmtChart->execute();
    $chartData = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Xử lý lỗi nếu không kết nối được database
    echo "Lỗi truy vấn: " . $e->getMessage();
}
//tách dữ liệu
$labels = [];
$data = [];

foreach ($chartData as $row) {
    $labels[] = $row['month'];
    $data[] = $row['revenue'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FD Tech</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/style_dashboard.css">
</head>
<body>

    <div class="dashboard-layout">
        <?php include 'includes/sidebar.php'; ?>
        <main class="main-content">
            <div class="top-navbar">
                <h1 class="page-title">Tổng quan Thống kê</h1>
                <div class="admin-profile">
                    <span class="text-muted">Xin chào, <b>Admin</b></span>
                    <img src="../assets/images/logo-fd.jpg" alt="Avatar">
                </div>
            </div>

            <div class="container dashboard-container">
                <div class="stats-grid">
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="color: var(--primary); background: #e6f0fa;">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                        <div class="stat-info">
                            <span class="text-muted">Tổng doanh thu</span>
                            <h3><?= number_format($totalRevenue, 0, ',', '.') ?>₫</h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="color: var(--secondary); background: #e0f7fc;">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <div class="stat-info">
                            <span class="text-muted">Đơn hàng mới</span>
                            <h3><?= $newOrdersCount ?> đơn hàng</h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="color: var(--success); background: #e6f4ea;">
                            <i class="fa-solid fa-cubes"></i>
                        </div>
                        <div class="stat-info">
                            <span class="text-muted">Sản phẩm đang bán</span>
                            <h3><?= $totalProductsCount ?> sản phẩm</h3>
                        </div>
                    </div>

                </div>

                <section class="section-block" style="margin-top: var(--space-xl);">
                    <div class="card stat-chart-placeholder" style="background: var(--bg-main); padding: var(--space-lg); border-radius: var(--radius-md); box-shadow: var(--shadow-card); min-height: 300px;">
                        <h3 style="margin-bottom: var(--space-md); color: var(--primary);">Biểu đồ doanh thu </h3>
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
    <script src="../assets/js/script_dashboard.js"></script>
</body>
</html>