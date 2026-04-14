<?php
require_once '../../config/database.php';

$error = ""; $success = "";
// Lấy danh mục để hiện trong thẻ select
$categories = $conn->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cat_id = $_POST['category_id'];
    $desc = $_POST['description'];
    
    // Bắt lỗi nhập số âm
    if ($price < 0 || $stock < 0) {
        $error = "Giá tiền và số lượng không được âm!";
    } else {
        // Xử lý Upload file
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'png', 'jpeg'];
        
        if (!in_array($ext, $allowed)) {
            $error = "Chỉ chấp nhận file ảnh JPG, PNG.";
        } else {
            $new_name = time() . "_" . uniqid() . "." . $ext; // Chống trùng tên file
            if (move_uploaded_file($file['tmp_name'], "../../assets/images/" . $new_name)) {
                $sql = "INSERT INTO products (name, category_id, price, stock_quantity, image_url, description) VALUES (?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$name, $cat_id, $price, $stock, $new_name, $desc]);
                $success = "Thêm sản phẩm thành công!";
            } else {
                $error = "Không thể tải ảnh lên server.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm mới</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F4F6F9; padding: 50px; }
        .form-card { max-width: 700px; margin: auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #0B2A4A; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-save { background: #0B2A4A; color: white; border: none; padding: 15px 30px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; }
        #preview { width: 100%; max-width: 200px; margin-top: 10px; display: none; border-radius: 8px; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .alert-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Thêm sản phẩm mới</h2>
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="group">
                <label>Tên sản phẩm</label>
                <input type="text" name="name" required placeholder="Ví dụ: Tai nghe Gaming Razer">
            </div>
            <div class="group" style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <label>Giá bán (VNĐ)</label>
                    <input type="number" name="price" required>
                </div>
                <div style="flex: 1;">
                    <label>Số lượng kho</label>
                    <input type="number" name="stock" required>
                </div>
            </div>
            <div class="group">
                <label>Danh mục sản phẩm</label>
                <select name="category_id">
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="group">
                <label>Ảnh minh họa</label>
                <input type="file" name="image" id="imgFile" accept="image/*" required>
                <img id="preview" src="">
            </div>
            <div class="group">
                <label>Mô tả chi tiết</label>
                <textarea name="description" rows="5"></textarea>
            </div>
            <button type="submit" class="btn-save">Xác nhận thêm sản phẩm</button>
            <p style="text-align: center;"><a href="list.php">Quay lại danh sách</a></p>
        </form>
    </div>

    <script>
        // Preview ảnh trước khi upload
        document.getElementById('imgFile').onchange = e => {
            const [file] = e.target.files;
            if (file) {
                const pre = document.getElementById('preview');
                pre.src = URL.createObjectURL(file);
                pre.style.display = 'block';
            }
        }
    </script>
</body>
</html>