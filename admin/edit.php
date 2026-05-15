<?php
session_start();
include '../config/database.php';
require_once __DIR__ . '/check_admin.php';

// Lấy ID sản phẩm
$id = $_GET['id'] ?? $_POST['id'] ?? 0;

// Lấy thông tin sản phẩm
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: list_products.php");
    exit;
}

// Lấy danh sách tag hiện tại
$stmt_tags = $pdo->prepare("SELECT tag_id FROM product_tags WHERE product_id = ?");
$stmt_tags->execute([$id]);
$current_tags = $stmt_tags->fetchAll(PDO::FETCH_COLUMN);

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $image_url = trim($_POST['image_url'] ?? '');

    if ($image_url === '') {
        $image_url = $product['image_url'];
    }

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../upload/product_image/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($_FILES['product_image']['type'], $allowed_types)) {
            die("Chỉ cho phép JPG, PNG!");
        }

        $ext = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_url = $file_name;
        }
    }

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

$img = $product['image_url'];
$src = (filter_var($img, FILTER_VALIDATE_URL)) ? $img : "../upload/product_image/" . $img;

if (empty($img)) {
    $src = "../assets/images/logo-fd.jpg";
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>

    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/style_dashboard.css">
    <link rel="stylesheet" href="../assets/css/style_add_product.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<div class="dashboard-layout">

    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">

        <div class="top-navbar">
            <h1 class="page-title">Sửa sản phẩm</h1>

            <div class="admin-profile">
                <span class="text-muted">
                    Xin chào, <b>Admin</b>
                </span>
                <img src="../assets/images/logo-fd.jpg" alt="Admin">
            </div>
        </div>

        <div class="dashboard-container">
            <div class="card">
                <h3>
                    <i class="fa-solid fa-pen-to-square"></i>
                    Chỉnh sửa sản phẩm
                </h3>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">

                    <div class="form-group">
                        <label class="form-label">Tên sản phẩm</label>
                        <input
                            name="name"
                            value="<?= htmlspecialchars($product['name']) ?>"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label tag-label">🏷️ Gắn nhãn sản phẩm</label>

                        <div class="tag-box">
                            <label class="tag-option">
                                <input
                                    type="checkbox"
                                    name="tags[]"
                                    value="1"
                                    <?= in_array(1, $current_tags) ? 'checked' : '' ?>
                                >
                                <span class="tag-badge tag-featured">
                                    <i class="fa-solid fa-star"></i>
                                    Sản phẩm nổi bật
                                </span>
                            </label>

                            <label class="tag-option">
                                <input
                                    type="checkbox"
                                    name="tags[]"
                                    value="2"
                                    <?= in_array(2, $current_tags) ? 'checked' : '' ?>
                                >
                                <span class="tag-badge tag-sale">
                                    <i class="fa-solid fa-bolt"></i>
                                    Flash sale
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ảnh hiện tại</label>

                        <div class="current-image-box">
                            <img
                                src="<?= htmlspecialchars($src) ?>"
                                class="current-product-image"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                onerror="this.src='../assets/images/logo-fd.jpg'"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Link ảnh online</label>
                        <input
                            type="url"
                            name="image_url"
                            class="form-control"
                            placeholder="https://i.ibb.co/..."
                            value="<?= filter_var($product['image_url'], FILTER_VALIDATE_URL) ? htmlspecialchars($product['image_url']) : '' ?>"
                        >
                        <p class="image-help">
                            Có thể dán link ảnh online hoặc upload ảnh bên dưới.
                            Nếu chọn cả hai, ảnh upload local sẽ được ưu tiên.
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Upload ảnh local</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                        <p class="image-help">Để trống nếu muốn giữ nguyên ảnh cũ.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Giá (₫)</label>
                        <input
                            name="price"
                            type="number"
                            step="any"
                            min="0"
                            value="<?= $product['price'] ?>"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tồn kho</label>
                        <input
                            name="stock"
                            type="number"
                            value="<?= $product['stock_quantity'] ?>"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-control">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <button class="btn btn-primary">
                        <i class="fa-solid fa-save"></i>
                        Cập nhật sản phẩm
                    </button>

                    <a href="list_products.php" class="btn btn-cancel">Hủy</a>
                </form>
            </div>
        </div>

    </main>

</div>
<script src="../assets/js/script_dashboard.js"></script>
</body>
</html>