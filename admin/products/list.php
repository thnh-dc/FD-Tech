<?php include('../../includes/header.php'); ?>
<div class="admin-main" style="max-width: 1200px; margin: 0 auto;">
    <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
        <div>
            <nav style="font-size: 12px; color: var(--text-muted);">QUẢN LÝ / SẢN PHẨM</nav>
            <h1 style="color: var(--primary);">Kho hàng sản phẩm</h1>
        </div>
        <a href="add.php" class="btn btn-primary">+ THÊM SẢN PHẨM</a>
    </header>

    <div class="card" style="display: flex; gap: 15px; align-items: center;">
        <input type="text" class="form-control" style="flex:2" placeholder="Tìm kiếm...">
        <select class="form-control" style="flex:1"><option>Tất cả danh mục</option></select>
        <button class="btn btn-secondary">Lọc</button>
    </div>

    <div class="card" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>SẢN PHẨM</th><th>GIÁ</th><th>TỒN KHO</th><th>TRẠNG THÁI</th><th style="text-align:right">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Laptop Asus ROG</td>
                    <td style="font-weight:700; color:var(--danger)">28.500.000đ</td>
                    <td>15 chiếc</td>
                    <td><span class="badge-success">● Đang bán</span></td>
                    <td style="text-align:right">
                        <a href="edit.php?id=1" style="color:var(--secondary); font-weight:600; text-decoration:none;">Sửa</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>