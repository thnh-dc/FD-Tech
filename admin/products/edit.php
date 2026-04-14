<?php
require_once '../../config/database.php';
$id = $_GET['id'] ?? null;
if (!$id) header("Location: list.php");

// Lấy thông tin sản phẩm hiện tại
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
$categories = $conn->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $img_name = $product['image_url']; // Mặc định dùng ảnh cũ
    
    // Nếu có chọn ảnh mới
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $img_name = time() . "_updated." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/images/" . $img_name);
    }

    $sql = "UPDATE products SET name=?, category_id=?, price=?, stock_quantity=?, image_url=?, description=? WHERE id=?";
    $conn->prepare($sql)->execute([
        $_POST['name'], $_POST['category_id'], $_POST['price'], 
        $_POST['stock'], $img_name, $_POST['description'], $id
    ]);
    header("Location: list.php");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Sửa sản phẩm</title>
<link rel="stylesheet" href="style_giong_add_php.css"> </head>
<body>
    <div class="form-card">
        <h2>Chỉnh sửa sản phẩm</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            <p>Ảnh hiện tại:</p>
            <img src="../../assets/images/<?= $product['image_url'] ?>" width="150" style="border-radius: 8px;">
            <input type="file" name="image">
            <button type="submit" class="btn-save">Cập nhật thay đổi</button>
        </form>
    </div>
</body>
</html>