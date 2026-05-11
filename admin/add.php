<?php
session_start();
include '../config/database.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý Upload Ảnh
    $image_url = ""; // Mặc định trống
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../upload/product_image/";
        
        // Tạo tên file duy nhất: thời gian + tên gốc
        $file_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        // Di chuyển file từ bộ nhớ tạm vào thư mục đích
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_url = $file_name; // Lưu tên file vào DB
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO products(name, price, stock_quantity, category_id, description, image_url)
        VALUES(?,?,?,?,?,?)
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['category_id'],
        $_POST['description'],
        $image_url
    ]);

    header("Location: list_products.php?msg=Thêm thành công");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="dashboard-layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-navbar">
            <h1 class="page-title">Thêm sản phẩm</h1>
            <div class="admin-profile">
                <span>Admin</span>
                <img src="../assets/images/logo-fd.jpg">
            </div>
        </div>
        <div class="dashboard-container">
            <div class="card">
                <h3><i class="fa-solid fa-plus"></i> Thêm sản phẩm mới</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Tên sản phẩm</label>
                        <input name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hình ảnh sản phẩm</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Giá (₫)</label>
                        <input name="price" type="number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tồn kho</label>
                        <input name="stock" type="number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-control">
                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <button class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Lưu sản phẩm
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>