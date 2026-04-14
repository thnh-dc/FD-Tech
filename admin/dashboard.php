<?php
require_once '../config/database.php';
// Truy vấn dữ liệu thống kê
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn();
$total_users = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển Admin - FD Tech</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0B2A4A; --secondary: #23B5D3; --bg-light: #F4F6F9;
            --text-dark: #333333; --white: #FFFFFF; --shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); margin: 0; color: var(--text-dark); }
        .wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--primary); color: var(--white); padding: 30px 20px; }
        .sidebar h2 { font-size: 24px; margin-bottom: 40px; text-align: center; }
        .sidebar nav a { display: block; color: #cbd5e0; padding: 12px; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .sidebar nav a:hover, .sidebar nav a.active { background: rgba(255,255,255,0.1); color: var(--white); }

        /* Main Content */
        .main { flex: 1; padding: 40px; }
        .header { margin-bottom: 30px; }
        .header h1 { font-size: 32px; color: var(--primary); margin: 0; }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; }
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: var(--shadow); border-left: 5px solid var(--secondary); }
        .card h3 { font-size: 14px; color: #6C757D; text-transform: uppercase; margin: 0; }
        .card .value { font-size: 28px; font-weight: 700; margin: 10px 0; color: var(--primary); }
    </style>
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h2>FD-TECH</h2>
            <nav>
                <a href="dashboard.php" class="active">🏠 Tổng quan</a>
                <a href="products/list.php">📦 Quản lý sản phẩm</a>
                <a href="../index.php">🌐 Xem website</a>
            </nav>
        </aside>
        <main class="main">
            <div class="header">
                <h1>Hệ thống quản trị</h1>
                <p>Chào mừng bạn quay trở lại, Admin!</p>
            </div>
            <div class="stats-grid">
                <div class="card">
                    <h3>Tổng sản phẩm</h3>
                    <div class="value"><?= number_format($total_products) ?></div>
                </div>
                <div class="card" style="border-left-color: #28A745;">
                    <h3>Doanh thu (VNĐ)</h3>
                    <div class="value"><?= number_format($total_revenue ?? 0) ?>đ</div>
                </div>
                <div class="card" style="border-left-color: #FFC107;">
                    <h3>Đơn hàng thành công</h3>
                    <div class="value"><?= number_format($total_orders) ?></div>
                </div>
                <div class="card" style="border-left-color: #DC3545;">
                    <h3>Khách hàng</h3>
                    <div class="value"><?= number_format($total_users) ?></div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>