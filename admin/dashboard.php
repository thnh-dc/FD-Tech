<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/header.php'); ?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h2>FD TECH ADMIN</h2>
        <nav class="sidebar-menu">
            <a href="/FD-Tech/admin/dashboard.php" class="active">
                <span>📊</span> Bảng điều khiển
            </a>
            <a href="/FD-Tech/admin/products/list.php">
                <span>📦</span> Quản lý sản phẩm
            </a>
            <a href="#">
                <span>📜</span> Đơn hàng
            </a>
            <a href="#">
                <span>👤</span> Người dùng
            </a>
        </nav>
    </aside>

    <main class="admin-main">
        <nav class="breadcrumb" style="margin-bottom: 20px; font-size: var(--text-sm); color: var(--text-muted);">
            Admin / <span style="color: var(--primary); font-weight: 600;">Dashboard</span>
        </nav>

        <header style="margin-bottom: 30px;">
            <h1 style="font-size: 1.75rem; color: var(--text-main); margin-bottom: 5px;">Chào buổi sáng, Admin!</h1>
            <p style="color: var(--text-muted);">Đây là tình hình kinh doanh của FD Tech hôm nay.</p>
        </header>

        <div class="stats-grid">
            <div class="card stat-item">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--primary); text-transform: uppercase;">Doanh thu (Tháng)</span>
                <h2>45.200.000 đ</h2>
                <small style="color: var(--success); font-weight: 600;">↑ 12.5% so với tháng trước</small>
            </div>
            
            <div class="card stat-item" style="border-left-color: var(--success);">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--success); text-transform: uppercase;">Đơn hàng mới</span>
                <h2>128</h2>
                <small style="color: var(--primary); font-weight: 600;">+5 đơn đang chờ xử lý</small>
            </div>

            <div class="card stat-item" style="border-left-color: var(--warning);">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--warning); text-transform: uppercase;">Tổng sản phẩm</span>
                <h2>1.042</h2>
                <small style="color: var(--text-muted);">24 danh mục hoạt động</small>
            </div>

            <div class="card stat-item" style="border-left-color: var(--danger);">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--danger); text-transform: uppercase;">Cảnh báo kho</span>
                <h2>08</h2>
                <small style="color: var(--danger);">Sản phẩm sắp hết hàng</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <section class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0; color: var(--text-main);">Đơn hàng gần đây</h3>
                    <a href="#" style="font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 600;">Xem tất cả →</a>
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
                            <td style="font-weight: 600; color: var(--primary);">#ORD-7702</td>
                            <td>Nguyễn Văn A</td>
                            <td><span class="badge" style="background: #e6f7ff; color: #1890ff;">Hoàn tất</span></td>
                            <td style="font-weight: 700;">1.200.000đ</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--primary);">#ORD-7705</td>
                            <td>Trần Thị B</td>
                            <td><span class="badge" style="background: #fff7e6; color: #fa8c16;">Đang xử lý</span></td>
                            <td style="font-weight: 700;">850.000đ</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--primary);">#ORD-7709</td>
                            <td>Lê Văn C</td>
                            <td><span class="badge" style="background: #fff1f0; color: #f5222d;">Đã hủy</span></td>
                            <td style="font-weight: 700;">2.150.000đ</td>
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
                        <p style="margin: 0; font-weight: 700; font-size: 14px; color: #856404;">Kho hàng:</p>
                        <p style="margin: 5px 0 0; font-size: 13px; color: var(--text-main);">Laptop Dell XPS còn lại 2 chiếc.</p>
                    </div>
                    <div>
                        <p style="margin: 0; font-weight: 700; font-size: 14px; color: #856404;">Bảo trì:</p>
                        <p style="margin: 5px 0 0; font-size: 13px; color: var(--text-main);">Hệ thống sẽ backup vào lúc 2h sáng mai.</p>
                    </div>
                    <button class="btn btn-primary" style="width: 100%; margin-top: 10px;">Xem nhật ký</button>
                </div>
            </section>
        </div>
    </main>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/footer.php'); ?>