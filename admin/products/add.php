<?php
    require_once '/FD-Tech/config/database.php';
    include '/FD-Tech/includes/header.php' ;

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']) ?: '';
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];

    // 🔥 CHECK TRÙNG TÊN
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name=?");
    $check->execute([$name]);
    $exists = $check->fetchColumn();

    if ($exists > 0) {
        $error = "Tên sản phẩm đã tồn tại!";
    }
    elseif (!$name || $price < 0 || $stock < 0 || !$category_id) {
        $error = "Dữ liệu không hợp lệ!";
    } else {

        $imageName = null;

        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (!in_array($ext, $allowed)) {
                $error = "Ảnh không hợp lệ!";
            } else {
                $imageName = uniqid('prod_') . '.' . $ext;

                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    $_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/assets/images/' . $imageName
                );
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, stock_quantity, category_id, image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([$name,$description,$price,$stock,$category_id,$imageName]);

            header("Location: list.php?msg=Thêm thành công");
            exit;
        }
    }
}
?>

<div class="admin-main">
<h1>Thêm sản phẩm</h1>

<?php if($error): ?>
<div class="card" style="background:#f8d7da;padding:10px;">
<?= $error ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card">

<input type="text" name="name" required class="form-control" placeholder="Tên"><br>

<input type="number" name="price" required class="form-control" placeholder="Giá"><br>

<input type="number" name="stock" required class="form-control" placeholder="Tồn kho"><br>

<select name="category_id" required class="form-control">
<option value="">--Danh mục--</option>
<?php foreach($categories as $c): ?>
<option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
<?php endforeach; ?>
</select><br>

<textarea name="description" class="form-control" placeholder="Mô tả"></textarea><br>

<input type="file" name="image" class="form-control"><br>

<button class="btn btn-primary">Thêm</button>

</form>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/footer.php'); ?>