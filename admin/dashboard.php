<?php include('../includes/header.php'); ?>
<div class="admin-wrapper" style="display: flex; min-height: 100vh;">
    <aside style="width: 260px; background: var(--primary); color: white; padding: 20px;">
        <h2 style="font-size: var(--text-xl); margin-bottom: 30px;">FD TECH ADMIN</h2>
        <nav>
            <div style="padding: 12px; background: rgba(255,255,255,0.1); border-radius: 8px; margin-bottom: 10px;">📊 Bảng điều khiển</div>
            <div style="padding: 12px; margin-bottom: 10px; cursor: pointer;">📦 Quản lý sản phẩm</div>
            <div style="padding: 12px; margin-bottom: 10px; cursor: pointer;">📜 Đơn hàng</div>
            <div style="padding: 12px; margin-bottom: 10px; cursor: pointer;">👤 Khách hàng</div>
        </nav>
    </aside>

    <main style="flex: 1; padding: 30px; background: var(--bg-light);">
        <nav class="breadcrumb" style="margin-bottom: 20px; font-size: var(--text-sm); color: var(--text-muted);">
            Admin / <span style="color: var(--primary); font-weight: 600;">Dashboard</span>
        </nav>

        <header style="margin-bottom: 30px;">
            <h1 style="font-size: var(--text-2xl); color: var(--text-dark); margin: 0;">Chào buổi sáng, Admin!</h1>
            <p style="color: var(--text-muted);">Đây là tình hình kinh doanh của FD Tech hôm nay.</p>
        </header>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
            <div class="card" style="border-top: 4px solid var(--primary); display: flex; flex-direction: column; justify-content: center; height: 120px;">
                <span style="color: var(--text-muted); font-size: 12px; font-weight: 600;">DOANH THU (THÁNG)</span>
                <h2 style="margin: 8px 0; font-size: 28px;">45.200.000 đ</h2>
                <small style="color: var(--success);">↑ 12.5% so với tháng trước</small>
            </div>
            <div class="card" style="border-top: 4px solid var(--secondary); display: flex; flex-direction: column; justify-content: center;">
                <span style="color: var(--text-muted); font-size: 12px; font-weight: 600;">ĐƠN HÀNG MỚI</span>
                <h2 style="margin: 8px 0; font-size: 28px;">128</h2>
                <small style="color: var(--success);">+5 đơn đang chờ xử lý</small>
            </div>
            <div class="card" style="border-top: 4px solid var(--warning); display: flex; flex-direction: column; justify-content: center;">
                <span style="color: var(--text-muted); font-size: 12px; font-weight: 600;">TỔNG SẢN PHẨM</span>
                <h2 style="margin: 8px 0; font-size: 28px;">1.042</h2>
                <small style="color: var(--text-muted);">24 danh mục hoạt động</small>
            </div>
            <div class="card" style="border-top: 4px solid var(--danger); display: flex; flex-direction: column; justify-content: center;">
                <span style="color: var(--text-muted); font-size: 12px; font-weight: 600;">CẢNH BÁO KHO</span>
                <h2 style="margin: 8px 0; font-size: 28px; color: var(--danger);">08</h2>
                <small style="color: var(--danger);">Sản phẩm sắp hết hàng</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <section class="card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="margin: 0;">Đơn hàng gần đây</h3>
                    <a href="#" style="color: var(--secondary); font-size: 14px; text-decoration: none;">Xem tất cả →</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr style="text-align: left; color: var(--text-muted); font-size: 13px;">
                            <th>MÃ ĐƠN</th>
                            <th>KHÁCH HÀNG</th>
                            <th>TRẠNG THÁI</th>
                            <th>TỔNG TIỀN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 12px 0;">#ORD-7702</td>
                            <td>Nguyễn Văn A</td>
                            <td><span style="background: #e8f5e9; color: var(--success); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Hoàn tất</span></td>
                            <td style="font-weight: 600;">1.200.000đ</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0;">#ORD-7705</td>
                            <td>Trần Thị B</td>
                            <td><span style="background: #fff3e0; color: var(--warning); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Đang xử lý</span></td>
                            <td style="font-weight: 600;">850.000đ</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="card" style="background: #fffbe6; border: 1px solid #ffe58f;">
                <h3 style="margin-top: 0; color: #856404;">🔔 Thông báo hệ thống</h3>
                <ul style="padding: 0; list-style: none; font-size: 14px; color: #856404;">
                    <li style="margin-bottom: 15px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">
                        <strong>Kho hàng:</strong> Laptop Dell XPS còn lại 2 chiếc.
                    </li>
                    <li style="margin-bottom: 15px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">
                        <strong>Bảo trì:</strong> Hệ thống sẽ backup vào lúc 2h sáng mai.
                    </li>
                </ul>
            </section>
        </div>
    </main>
</div>
<?php include('../includes/footer.php'); ?>