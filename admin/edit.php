<?php
session_start();
include '../config/database.php';

$id = $_GET['id'] ?? $_POST['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: list_products.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $image_url = $product['image_url']; 

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../upload/product_image/";
        $file_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_url = $file_name; 
        }
    }

    $stmt = $pdo->prepare("
        UPDATE products 
        SET name=?, price=?, stock_quantity=?, category_id=?, description=?, image_url=? 
        WHERE id=?
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['category_id'],
        $_POST['description'],
        $image_url,
        $_POST['id']
    ]);

    header("Location: list_products.php?msg=Sửa thành công");
    exit;
}
?>

<!DOCTYPE html>
<html>
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
</div>

<div class="dashboard-container">

<div class="card">

<h3><i class="fa-solid fa-pen"></i> Sửa sản phẩm</h3>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $product['id'] ?>">

<div class="form-group">
<label class="form-label">Tên</label>
<input name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
</div>

<div class="form-group">
<label class="form-label">Hình ảnh</label>
<div style="margin-bottom: 10px;">
    <?php 
        $img = $product['image_url'];
        $src = (strpos($img, 'http') !== false) ? $img : "../upload/product_image/".$img;
        if(empty($img)) $src = "../assets/images/logo-fd.jpg";
    ?>
    <img src="<?= htmlspecialchars($src) ?>" alt="Ảnh sản phẩm" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;" onerror="this.src='../assets/images/logo-fd.jpg'">
</div>
<input type="file" name="product_image" class="form-control" accept="image/*">
<small style="color: #666; margin-top: 5px; display: block;">* Bỏ trống nếu muốn giữ nguyên ảnh cũ</small>
</div>

<div class="form-group">
<label class="form-label">Giá</label>
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
<option value="<?= $c['id'] ?>" <?= $product['category_id']==$c['id']?'selected':'' ?>>
<?= $c['name'] ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="form-group">
<label class="form-label">Mô tả</label>
<textarea name="description" class="form-control"><?= $product['description'] ?></textarea>
</div>

<button class="btn btn-primary">
<i class="fa-solid fa-pen"></i> Cập nhật
</button>

</form>

</div>
</div>

</main>
</div>

</body>
</html>