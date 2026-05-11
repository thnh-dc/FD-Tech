<?php
session_start();
include '../config/database.php';

// 1. Lấy ID sản phẩm
$id = $_GET['id'] ?? $_POST['id'] ?? 0;

// 2. Lấy thông tin sản phẩm từ bảng products
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: list_products.php");
    exit;
}

// 3. Lấy danh sách tags hiện tại của sản phẩm từ bảng product_tags
$stmt_tags = $pdo->prepare("SELECT tag_id FROM product_tags WHERE product_id = ?");
$stmt_tags->execute([$id]);
$current_tags = $stmt_tags->fetchAll(PDO::FETCH_COLUMN); // Trả về mảng ví dụ: [1, 2]

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// 4. Xử lý khi nhấn nút Cập nhật (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- XỬ LÝ ẢNH ---
    $image_url = $product['image_url']; // Mặc định giữ ảnh cũ

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../upload/product_image/";
        $file_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_url = $file_name; // Nếu up ảnh mới thành công thì đổi tên file
        }
    }

    // --- CẬP NHẬT BẢNG PRODUCTS ---
    $stmt_update = $pdo->prepare("
        UPDATE products 
        SET name=?, price=?, stock_quantity=?, category_id=?, description=?, image_url=? 
        WHERE id=?
    ");

    $stmt_update->execute([
        $_POST['name'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['category_id'],
        $_POST['description'],
        $image_url,
        $id
    ]);

    // --- XỬ LÝ TAGS (Xóa hết cũ - Ghi lại mới) ---
    $pdo->prepare("DELETE FROM product_tags WHERE product_id = ?")->execute([$id]);

    if (!empty($_POST['tags'])) {
        $stmt_ins_tag = $pdo->prepare("INSERT INTO product_tags (product_id, tag_id) VALUES (?, ?)");
        foreach ($_POST['tags'] as $tag_id) {
            $stmt_ins_tag->execute([$id, $tag_id]);
        }
    }

    header("Location: list_products.php?msg=Sửa thành công");
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
            <h1 class="page-title">Sửa sản phẩm</h1>
            <div class="admin-profile">
                <span>Admin</span>
                <img src="../assets/images/logo-fd.jpg">
            </div>
        </div>

        <div class="dashboard-container">
            <div class="card">
                <h3><i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa sản phẩm</h3>
                
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">

                    <div class="form-group">
                        <label class="form-label">Tên sản phẩm</label>
                        <input name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight: 700; color: #2563eb;">🏷️ Gắn nhãn sản phẩm</label>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e1; display: flex; gap: 25px;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="tags[]" value="1" <?= in_array(1, $current_tags) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span style="background: #fef3c7; color: #92400e; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                                    <i class="fa-solid fa-star"></i> Sản phẩm nổi bật
                                </span>
                            </label>
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="tags[]" value="2" <?= in_array(2, $current_tags) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span style="background: #fee2e2; color: #991b1b; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                                    <i class="fa-solid fa-bolt"></i> Flash sale
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hình ảnh sản phẩm</label>
                        <div style="margin-bottom: 10px;">
                            <?php 
                                $img = $product['image_url'];
                                $src = (strpos($img, 'http') !== false) ? $img : "../upload/product_image/".$img;
                                if(empty($img)) $src = "../assets/images/logo-fd.jpg";
                            ?>
                            <img src="<?= htmlspecialchars($src) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;" onerror="this.src='../assets/images/logo-fd.jpg'">
                        </div>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                        <small style="color: #666;">* Để trống nếu muốn giữ nguyên ảnh cũ</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Giá (₫)</label>
                        <input name="price" type="number" value="<?= $product['price'] ?>" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tồn kho</label>
                        <input name="stock" type="number" value="<?= $product['stock_quantity'] ?>" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-control">
                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>>
                                    <?= $c['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <button class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Cập nhật sản phẩm
                    </button>
                    <a href="list_products.php" class="btn" style="background: #64748b; color: white; margin-left: 10px; text-decoration: none; padding: 10px 20px; border-radius: 5px; display: inline-block;">Hủy</a>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>