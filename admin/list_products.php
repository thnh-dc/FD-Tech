<?php
session_start();
include '../config/database.php';

$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("
SELECT p.*, c.name as cat_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.name LIKE ?
ORDER BY p.id DESC
");
$stmt->execute(["%$search%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">

<link rel="stylesheet" href="../assets/css/style_chung.css">
<link rel="stylesheet" href="../assets/css/style_dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.admin-table tr:hover {
    background: #f9fbff;
}

.admin-table img {
    border-radius: 8px;
    border: 1px solid #eee;
}

.alert-success {
    background: #e6f9f0;
    color: #1cc88a;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 600;
}

.btn i {
    transition: 0.2s;
}
.btn:hover i {
    transform: scale(1.2);
}
</style>

</head>

<body>

<div class="dashboard-layout">
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">

<div class="top-navbar">
    <h1 class="page-title">Quản lí sản phẩm</h1>
    <div class="admin-profile">
        <span>Admin</span>
        <img src="../assets/images/logo-fd.jpg">
    </div>
</div>

<div class="dashboard-container">

<?php if(isset($_GET['msg'])): ?>
<div class="alert-success">
<i class="fa-solid fa-circle-check"></i> <?= $_GET['msg'] ?>
</div>
<?php endif; ?>

<div class="card">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">

<form method="GET" style="display:flex;gap:10px;">
<input type="text" name="search" placeholder="Tìm sản phẩm..."
value="<?= htmlspecialchars($search) ?>" class="form-control">
<button class="btn btn-primary"><i class="fa fa-search"></i></button>
</form>

<a href="add.php" class="btn btn-primary">
<i class="fa-solid fa-plus"></i> Thêm
</a>

</div>

<table class="admin-table">

<tr>
<th>Ảnh</th>
<th>Tên</th>
<th>Danh mục</th>
<th>Giá</th>
<th>Tồn</th>
<th></th>
</tr>

<?php foreach($products as $p): ?>
<tr>

<td>
<img src="../assets/images/<?= !empty($p['image']) ? $p['image'] : 'default.png' ?>" width="50">
</td>

<td><?= htmlspecialchars($p['name']) ?></td>

<td><?= htmlspecialchars($p['cat_name']) ?></td>

<td><?= number_format($p['price']) ?>đ</td>

<td>
<?php
$stock = $p['stock_quantity'];
$class = $stock == 0 ? 'badge-danger' : ($stock < 5 ? 'badge-warning' : 'badge-success');
$text = $stock == 0 ? 'Hết' : ($stock < 5 ? 'Sắp hết' : 'Còn');
?>
<span class="badge <?= $class ?>">
<?= $text ?> (<?= $stock ?>)
</span>
</td>

<td style="display:flex;gap:8px;">

<a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-primary">
<i class="fa-solid fa-pen"></i>
</a>

<a href="delete.php?id=<?= $p['id'] ?>"
onclick="return confirm('Xóa sản phẩm?')"
class="btn btn-secondary">
<i class="fa-solid fa-trash"></i>
</a>

</td>

</tr>
<?php endforeach; ?>

</table>

</div>
</div>

</main>
</div>

</body>
</html>