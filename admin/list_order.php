<?php
session_start();
require_once '../config/database.php'; 

try {
    // Truy vấn lấy danh sách đơn hàng kèm tên khách hàng
    $sql = "SELECT o.*, u.username 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC";
    
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Admin FD Tech</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/style_dashboard.css">
    <link rel="stylesheet" href="../assets/css/style_sidebar.css">
    <link rel="stylesheet" href="../assets/css/style_list_oder.css">
</head>
<body>

    <div class="dashboard-layout">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-navbar">
                <h1 class="page-title">Danh Sách Đơn Hàng</h1>
                <div class="admin-profile">
                    <span class="text-muted">Xin chào, <b>Admin</b></span>
                    <img src="../assets/images/logo-fd.jpg" alt="Avatar">
                </div>
            </div>

            <div class="container dashboard-container">
                <section class="section-block">
                    <div class="card shadow-card" style="background: var(--bg-main); padding: var(--space-lg); border-radius: var(--radius-md);">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Khách Hàng</th>
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
                                            <td class="product-name"><?= htmlspecialchars($row['username']) ?></td>
                                            <td class="price-highlight">
                                                <?= number_format($row['total_amount'], 0, ',', '.') ?>₫
                                            </td>
                                            <td>
                                                <?php 
                                                    // Xử lý Badge dựa trên trạng thái (status) từ database
                                                    $status = $row['status'];
                                                    $badge_class = 'badge-info';
                                                    $status_vi = $status;

                                                    if ($status == 'pending') { $badge_class = 'badge-warning'; $status_vi = 'Chờ xử lý'; }
                                                    elseif ($status == 'shipped') { $badge_class = 'badge-warning'; $status_vi = 'Đang vận chuyển'; }
                                                    elseif ($status == 'completed') { $badge_class = 'badge-success'; $status_vi = 'Hoàn thành'; }
                                                    elseif ($status == 'cancelled') { $badge_class = 'badge-danger'; $status_vi = 'Đã hủy'; }
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= $status_vi ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                            <td>
                                                <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-primary" style="padding: 4px 12px; font-size: 12px;">Xem</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 20px;">Chưa có đơn hàng nào được ghi nhận.</td>
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
</body>
</html>