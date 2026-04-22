<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/config/database.php');
include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/header.php');

$id = $_GET['id'] ?? $_POST['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: list.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];

    $name = trim($_POST['name']);
    $description = trim($_POST['description']) ?: '';
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];

    if (!$name || $price < 0 || $stock < 0) {
        $error = "Dữ liệu không hợp lệ!";
    } else {

        $imageName = $product['image'];

        if (!empty($_FILES['image']['name'])) {

            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (!in_array($ext, $allowed)) {
                $error = "Ảnh không hợp lệ!";
            } else {

                // 🔥 XÓA ẢNH CŨ
                if (!empty($product['image'])) {
                    $oldFile = $_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/assets/images/' . $product['image'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                // upload ảnh mới
                $imageName = uniqid('prod_') . '.' . $ext;

                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    $_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/assets/images/' . $imageName
                );
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("
                UPDATE products 
                SET name=?, description=?, price=?, stock_quantity=?, category_id=?, image=? 
                WHERE id=?
            ");

            $stmt->execute([$name,$description,$price,$stock,$category_id,$imageName,$id]);

            header("Location: list.php?msg=Sửa thành công");
            exit;
        }
    }
}
?>

<div class="admin-main">
<h1>Sửa sản phẩm</h1>

<?php if($error): ?>
<div class="card" style="background:#f8d7da;padding:10px;">
<?= $error ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card">

<input type="hidden" name="id" value="<?= $product['id'] ?>">

<input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="form-control"><br>

<input type="number" name="price" value="<?= $product['price'] ?>" required class="form-control"><br>

<input type="number" name="stock" value="<?= $product['stock_quantity'] ?>" required class="form-control"><br>

<select name="category_id" class="form-control">
<?php foreach($categories as $c): ?>
<option value="<?= $c['id'] ?>" <?= ($product['category_id']==$c['id'])?'selected':'' ?>>
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endforeach; ?>
</select><br>

<textarea name="description" class="form-control"><?= htmlspecialchars($product['description'] ?? '') ?></textarea><br>

<?php $img = !empty($product['image']) ? $product['image'] : 'default.png'; ?>
<img src="/FD-Tech/assets/images/<?= $img ?>" width="120"><br><br>

<input type="file" name="image" class="form-control"><br>

<button class="btn btn-primary">Cập nhật</button>

</form>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/footer.php'); ?>