<?php
session_start();
include '../config/database.php';
require_once __DIR__ . '/check_admin.php';

$search = $_GET['search'] ?? '';
$category_id = $_GET['category_id'] ?? ''; 

try {
    // Lấy danh sách danh mục cho ô Select
    $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn bị câu SQL cơ bản
    $sql = "SELECT 
                p.id,
                p.name,
                p.price,
                p.stock_quantity,
                p.image_url,
                p.description,
                c.name AS cat_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE ?";
    
    $params = ["%$search%"];

    // Nếu người dùng có chọn danh mục, nối thêm điều kiện AND vào câu SQL
    if (!empty($category_id)) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }

    $sql .= " ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Lỗi query: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
<meta charset="UTF-8">

<title>Quản lý sản phẩm</title>

<link rel="stylesheet" href="../assets/css/style_chung.css">
<link rel="stylesheet" href="../assets/css/style_dashboard.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

.product-wrapper{
    padding: var(--space-xl);
}

.product-card{
    background: white;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-card);
    overflow: hidden;
}

.product-header{
    padding: 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid #eee;
}

.search-form{
    display:flex;
    gap:10px;
}

.search-form input{
    width:280px;
}

.admin-table{
    width:100%;
    border-collapse: collapse;
}

.admin-table th{
    background:#f8fafc;
    padding:18px;
    text-align:left;
}

.admin-table td{
    padding:18px;
    border-top:1px solid #eee;
}

.admin-table tr:hover{
    background:#fafafa;
}

.product-image{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:12px;
}

.stock-badge{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.in-stock{
    background:#dcfce7;
    color:#166534;
}

.low-stock{
    background:#fef3c7;
    color:#92400e;
}

.out-stock{
    background:#fee2e2;
    color:#991b1b;
}

.action-group{
    display:flex;
    gap:10px;
}

.btn-action{
    width:38px;
    height:38px;
    border:none;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    text-decoration:none;
}

.btn-edit{
    background:#2563eb;
}

.btn-delete{
    background:#dc2626;
}

.empty-box{
    padding:50px;
    text-align:center;
    color:#999;
}

</style>

</head>

<body>

<div class="dashboard-layout">

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">

<div class="top-navbar">

<h1 class="page-title">
<i class="fa-solid fa-box-open"></i>
Quản lý sản phẩm
</h1>

<div class="admin-profile">
    <span class="text-muted">
        Xin chào, <b>Admin</b>
    </span>

    <img src="../assets/images/logo-fd.jpg">
</div>

</div>

<div class="product-wrapper">

<div class="product-card">

<div class="product-header">

<form method="GET" class="search-form">

<input
type="text"
name="search"
class="form-control"
placeholder="Tìm sản phẩm..."
value="<?= htmlspecialchars($search) ?>"
>

<select name="category_id" class="form-control" style="width: 180px;" onchange="this.form.submit()">
    <option value="">Tất cả danh mục</option>
    <?php foreach($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
        </option>
    <?php endforeach; ?>
</select>

<button class="btn btn-primary">
<i class="fa-solid fa-search"></i>
</button>

</form>

<a href="add.php" class="btn btn-primary">
<i class="fa-solid fa-plus"></i>
Thêm sản phẩm
</a>

</div>

<table class="admin-table">

<thead>

<tr>
<th>Hình ảnh</th>
<th>Tên sản phẩm</th>
<th>Danh mục</th>
<th>Giá</th>
<th>Tồn kho</th>
<th>Thao tác</th>
</tr>

</thead>

<tbody>

<?php if(count($products) > 0): ?>

<?php foreach($products as $p): ?>

<tr>

<td>
<?php 
    $img = $p['image_url'];
    $src = (strpos($img, 'http') !== false) ? $img : "../upload/product_image/".$img;
    if(empty($img)) $src = "../assets/images/logo-fd.jpg";
?>
<img
src="<?= htmlspecialchars($src) ?>"
class="product-image"
onerror="this.src='../assets/images/logo-fd.jpg'"
>

</td>

<td>

<strong>
<?= htmlspecialchars($p['name']) ?>
</strong>

</td>

<td>

<?= htmlspecialchars($p['cat_name'] ?? 'Chưa có') ?>

</td>

<td style="font-weight:700;color:#dc2626;">

<?= number_format($p['price'],0,',','.') ?>₫

</td>

<td>

<?php

$stock = $p['stock_quantity'];

if($stock <= 0){
    $class = "out-stock";
}
elseif($stock < 10){
    $class = "low-stock";
}
else{
    $class = "in-stock";
}

?>

<span class="stock-badge <?= $class ?>">

<?= $stock ?> sản phẩm

</span>

</td>

<td>

<div class="action-group">

<a
href="edit.php?id=<?= $p['id'] ?>"
class="btn-action btn-edit"
>
<i class="fa-solid fa-pen"></i>
</a>

<a
href="delete.php?id=<?= $p['id'] ?>"
class="btn-action btn-delete"
onclick="return confirm('Bạn có chắc muốn xóa?')"
>
<i class="fa-solid fa-trash"></i>
</a>

</div>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="6">

<div class="empty-box">

<i class="fa-solid fa-box-open"
style="font-size:50px;margin-bottom:15px;"></i>

<h3>Không có sản phẩm</h3>

</div>

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</main>

</div>

</body>
</html>