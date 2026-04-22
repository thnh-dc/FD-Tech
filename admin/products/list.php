<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/config/database.php');
include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/header.php'); 

$search = $_GET['search'] ?? '';
$cat_id = $_GET['category_id'] ?? '';

$sql = "SELECT p.*, c.name as cat_name FROM products p 
LEFT JOIN categories c ON p.category_id = c.id 
WHERE p.name LIKE ?";

$params = ["%$search%"];

if($cat_id){
    $sql .= " AND p.category_id = ?";
    $params[] = $cat_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="admin-main">

<?php if(isset($_GET['msg'])): ?>
<div class="card" style="background:#d4edda;padding:10px;">
<?= htmlspecialchars($_GET['msg']) ?>
</div>
<?php endif; ?>

<h1>Danh sách sản phẩm</h1>

<a href="add.php" class="btn btn-primary">+ Thêm</a>

<form method="GET" class="card">
<input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control">

<select name="category_id" class="form-control">
<option value="">Tất cả</option>
<?php foreach($categories as $c): ?>
<option value="<?= $c['id'] ?>" <?= ($cat_id==$c['id'])?'selected':'' ?>>
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endforeach; ?>
</select>

<button class="btn btn-secondary">Lọc</button>
</form>

<table class="admin-table">
<tr><th>Ảnh</th><th>Tên</th><th>Danh mục</th><th>Giá</th><th>Tồn</th><th></th></tr>

<?php foreach($products as $p): ?>
<?php $img = !empty($p['image']) ? $p['image'] : 'default.png'; ?>
<tr>
<td><img src="/FD-Tech/assets/images/<?= $img ?>" width="60"></td>
<td><?= htmlspecialchars($p['name']) ?></td>
<td><?= htmlspecialchars($p['cat_name']) ?></td>
<td><?= number_format($p['price']) ?>đ</td>
<td><?= $p['stock_quantity'] ?></td>
<td>
<a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-primary">Sửa</a>
<a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Xóa?')" class="btn btn-secondary">Xóa</a>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/footer.php'); ?>