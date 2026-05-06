<?php
session_start();
include '../config/database.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO products(name, price, stock_quantity, category_id, description)
        VALUES(?,?,?,?,?)
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['category_id'],
        $_POST['description']
    ]);

    header("Location: list_product.php?msg=Thêm thành công");
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

<h3><i class="fa-solid fa-plus"></i> Thêm sản phẩm</h3>

<form method="POST">

<div class="form-group">
<label class="form-label">Tên</label>
<input name="name" class="form-control" required>
</div>

<div class="form-group">
<label class="form-label">Giá</label>
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
<textarea name="description" class="form-control"></textarea>
</div>

<button class="btn btn-primary">
<i class="fa-solid fa-plus"></i> Thêm
</button>

</form>

</div>

</div>

</main>
</div>

</body>
</html>