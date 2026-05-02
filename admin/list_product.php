<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lí sản phẩm - Admin</title>
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper" style="padding: 40px; background: var(--bg-light); min-height: 100vh;">
        <div style="max-width: 1200px; margin: 0 auto;">
            
            <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
                <div>
                    <nav style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">QUẢN LÝ / SẢN PHẨM</nav>
                    <h1 style="margin: 0; font-size: var(--text-2xl); color: var(--primary);">Kho hàng sản phẩm</h1>
                </div>
                <a href="add.php" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 10px rgba(11, 42, 74, 0.2);">
                    + THÊM SẢN PHẨM MỚI
                </a>
            </header>

            <div class="card" style="display: flex; gap: 15px; align-items: center; margin-bottom: 25px; padding: 20px;">
                <div style="flex: 2; position: relative;">
                    <input type="text" class="form-control" placeholder="Tìm theo tên sản phẩm hoặc mã SKU..." style="padding-left: 35px;">
                    <span style="position: absolute; left: 12px; top: 10px; color: var(--text-muted);">🔍</span>
                </div>
                <select class="form-control" style="flex: 1;">
                    <option>Tất cả danh mục</option>
                    <option>Laptop & Macbook</option>
                    <option>Linh kiện PC</option>
                    <option>Thiết bị ngoại vi</option>
                </select>
                <select class="form-control" style="flex: 1;">
                    <option>Sắp xếp: Mới nhất</option>
                    <option>Giá: Thấp đến Cao</option>
                    <option>Giá: Cao đến Thấp</option>
                </select>
                <button class="btn btn-secondary" style="padding: 10px 20px;">Lọc</button>
            </div>

            <div class="card" style="padding: 0; overflow: hidden;">
                <table class="admin-table" style="border-collapse: collapse;">
                    <thead style="background: #fcfcfc; border-bottom: 2px solid #f0f0f0;">
                        <tr style="text-align: left; color: var(--text-muted); font-size: 13px;">
                            <th style="padding: 20px;">SẢN PHẨM</th>
                            <th>DANH MỤC</th>
                            <th>GIÁ NIÊM YẾT</th>
                            <th>TỒN KHO</th>
                            <th>TRẠNG THÁI</th>
                            <th style="text-align: right; padding-right: 20px;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #f9f9f9;">
                            <td style="padding: 15px 20px; display: flex; align-items: center; gap: 15px;">
                                <div style="width: 50px; height: 50px; background: #eee; border-radius: 8px;"></div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-dark);">Laptop Asus ROG Strix G15</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">SKU: ASUS-7790-G15</div>
                                </div>
                            </td>
                            <td>Gaming Laptop</td>
                            <td style="font-weight: 700; color: var(--danger);">28.500.000 đ</td>
                            <td>15 chiếc</td>
                            <td><span style="color: var(--success); font-weight: 600; font-size: 13px;">● Đang bán</span></td>
                            <td style="text-align: right; padding-right: 20px;">
                                <a href="edit.php?id=1" style="text-decoration: none; color: var(--secondary); margin-right: 15px; font-weight: 600;">Sửa</a>
                                <a href="#" style="text-decoration: none; color: var(--danger); font-weight: 600;">Xóa</a>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f9f9f9;">
                            <td style="padding: 15px 20px; display: flex; align-items: center; gap: 15px;">
                                <div style="width: 50px; height: 50px; background: #eee; border-radius: 8px;"></div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-dark);">Chuột Logitech G Pro X</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">SKU: LOGI-GPX-W</div>
                                </div>
                            </td>
                            <td>Phụ kiện</td>
                            <td style="font-weight: 700; color: var(--danger);">3.200.000 đ</td>
                            <td>08 chiếc</td>
                            <td><span style="color: var(--warning); font-weight: 600; font-size: 13px;">● Sắp hết</span></td>
                            <td style="text-align: right; padding-right: 20px;">
                                <a href="edit.php?id=2" style="text-decoration: none; color: var(--secondary); margin-right: 15px; font-weight: 600;">Sửa</a>
                                <a href="#" style="text-decoration: none; color: var(--danger); font-weight: 600;">Xóa</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div style="padding: 20px; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; color: var(--text-muted);">Hiển thị 1 đến 10 trong số 142 sản phẩm</span>
                    <div style="display: flex; gap: 5px;">
                        <button style="padding: 5px 12px; border: 1px solid #ddd; background: white; border-radius: 4px;">Trước</button>
                        <button style="padding: 5px 12px; background: var(--primary); color: white; border: none; border-radius: 4px;">1</button>
                        <button style="padding: 5px 12px; border: 1px solid #ddd; background: white; border-radius: 4px;">2</button>
                        <button style="padding: 5px 12px; border: 1px solid #ddd; background: white; border-radius: 4px;">Sau</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>