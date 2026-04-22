<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/header.php'); ?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h2 style="font-size: var(--text-xl); margin-bottom: 30px; color: white; padding: 0 12px;">FD TECH ADMIN</h2>
        <nav class="sidebar-menu">
            <a href="/FD-Tech/admin/dashboard.php" class="active" style="display:flex; align-items:center; gap:10px; padding:12px; background:rgba(255,255,255,0.1); border-radius:8px; color:white; text-decoration:none; margin-bottom:5px;">
                <span>📊</span> Bảng điều khiển
            </a>
            <a href="/FD-Tech/admin/products/list.php" style="display:flex; align-items:center; gap:10px; padding:12px; color:white; text-decoration:none; transition: 0.3s;">
                <span>📦</span> Quản lý sản phẩm
            </a>
            </nav>
    </aside>

    <main class="admin-main">
        <nav class="breadcrumb" style="margin-bottom: 20px; font-size: var(--text-sm); color: var(--text-muted);">
            Admin / <span style="color: var(--primary); font-weight: 600;">Dashboard</span>
        </nav>

        <header style="margin-bottom: 30px;">
            <h1 style="font-size: var(--text-2xl); margin-bottom: 5px;">Chào buổi sáng, Admin!</h1>
            <p style="color: var(--text-muted);">Đây là tình hình kinh doanh của FD Tech hôm nay.</p>
        </header>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="card stat-item">
                <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Doanh thu (Tháng)</span>
                <h2 style="margin: 10px 0; font-size: 24px;">45.200.000 đ</h2>
                <small style="color: #28a745; font-weight: 600;">↑ 12.5% so với tháng trước</small>
            </div>
            
            <div class="card stat-item">
                <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Đơn hàng mới</span>
                <h2 style="margin: 10px 0; font-size: 24px;">128</h2>
                <small style="color: var(--primary); font-weight: 600;">+5 đơn đang chờ xử lý</small>
            </div>

            <div class="card stat-item">
                <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Tổng sản phẩm</span>
                <h2 style="margin: 10px 0; font-size: 24px;">1.042</h2>
                <small style="color: var(--text-muted);">24 danh mục hoạt động</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            
            <section class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">Đơn hàng gần đây</h3>
                    <a href="#" style="font-size: 13px; color: var(--primary); text-decoration: none;">Xem tất cả →</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>MÃ ĐƠN</th>
                            <th>KHÁCH HÀNG</th>
                            <th>TRẠNG THÁI</th>
                            <th>TỔNG TIỀN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: 600;">#ORD-7702</td>
                            <td>Nguyễn Văn A</td>
                            <td><span class="badge" style="background: #e6f7ff; color: #1890ff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Hoàn tất</span></td>
                            <td style="font-weight: 600;">1.200.000đ</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">#ORD-7705</td>
                            <td>Trần Thị B</td>
                            <td><span class="badge" style="background: #fff7e6; color: #fa8c16; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Đang xử lý</span></td>
                            <td style="font-weight: 600;">850.000đ</td>
                        </tr>
                    </tbody>
                </table>
            </section>
            
            <section class="card" style="background: #fffbe6; border: 1px solid #ffe58f;">
                <h3 style="color: #856404; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                    🔔 Thông báo hệ thống
                </h3>
                <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                    <div style="padding-bottom: 10px; border-bottom: 1px solid #fadb14;">
                        <p style="margin: 0; font-weight: 600; font-size: 14px;">Kho hàng:</p>
                        <p style="margin: 5px 0 0; font-size: 13px; color: #555;">Laptop Dell XPS còn lại 2 chiếc.</p>
                    </div>
                    <div>
                        <p style="margin: 0; font-weight: 600; font-size: 14px;">Bảo trì:</p>
                        <p style="margin: 5px 0 0; font-size: 13px; color: #555;">Hệ thống sẽ backup vào lúc 2h sáng mai.</p>
                    </div>
                </div>
            </section>

        </div>
    </main>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/footer.php'); ?>