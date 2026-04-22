<?php include('../includes/header.php'); ?>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h2 style="font-size: var(--text-xl); margin-bottom: 30px;">FD TECH ADMIN</h2>
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="active" style="display:block; padding:12px; background:rgba(255,255,255,0.1); border-radius:8px; color:white; text-decoration:none;">📊 Bảng điều khiển</a>
            <a href="products/list.php" style="display:block; padding:12px; color:white; text-decoration:none;">📦 Quản lý sản phẩm</a>
        </nav>
    </aside>

    <main class="admin-main">
        <nav class="breadcrumb" style="margin-bottom: 20px; font-size: var(--text-sm); color: var(--text-muted);">
            Admin / <span style="color: var(--primary); font-weight: 600;">Dashboard</span>
        </nav>

        <header style="margin-bottom: 30px;">
            <h1 style="font-size: var(--text-2xl);">Chào buổi sáng, Admin!</h1>
            <p class="text-muted">Đây là tình hình kinh doanh hôm nay.</p>
        </header>

        <div class="stats-grid">
            <div class="card stat-item">
                <span class="text-muted" style="font-size:12px;">DOANH THU (THÁNG)</span>
                <h2>45.200.000 đ</h2>
                <small class="badge-success">↑ 12.5%</small>
            </div>
            <div class="card stat-item" style="border-top-color: var(--secondary);">
                <span class="text-muted" style="font-size:12px;">ĐƠN HÀNG MỚI</span>
                <h2>128</h2>
                <small class="badge-success">+5 đơn mới</small>
            </div>
            </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <section class="card">
                <h3>Đơn hàng gần đây</h3>
                <table class="admin-table">
                    <thead>
                        <tr><th>MÃ ĐƠN</th><th>KHÁCH HÀNG</th><th>TRẠNG THÁI</th><th>TỔNG TIỀN</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-7702</td>
                            <td>Nguyễn Văn A</td>
                            <td><span class="badge badge-success">Hoàn tất</span></td>
                            <td>1.200.000đ</td>
                        </tr>
                    </tbody>
                </table>
            </section>
            
            <section class="card" style="background: #fffbe6; border: 1px solid #ffe58f;">
                <h3 style="color: #856404;">🔔 Thông báo</h3>
                <p style="font-size:14px;">Kho hàng: Laptop Dell XPS còn 2 chiếc.</p>
            </section>
        </div>
    </main>
</div>
<?php include('../includes/footer.php'); ?>