<?php
require_once '../../config/database.php';
// Lấy danh sách sản phẩm kèm tên danh mục
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          ORDER BY p.id DESC";
$products = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm - Admin</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F4F6F9; padding: 30px; }
        .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .flex-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; }
        .btn-add { background: #0B2A4A; color: white; }
        .btn-edit { background: #FFC107; color: #333; font-size: 12px; }
        .btn-delete { background: #DC3545; color: white; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #F8F9FA; text-align: left; padding: 15px; border-bottom: 2px solid #dee2e6; color: #6C757D; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .badge { background: #E2E8F0; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="flex-header">
            <h2>Quản lý kho hàng</h2>
            <a href="add.php" class="btn btn-add">+ Thêm sản phẩm</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td><img src="../../assets/images/<?= htmlspecialchars($p['image_url']) ?>" class="img-thumb"></td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><span class="badge"><?= htmlspecialchars($p['category_name']) ?></span></td>
                    <td><?= number_format($p['price']) ?>đ</td>
                    <td><?= $p['stock_quantity'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-edit">Sửa</a>
                        <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-delete" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>