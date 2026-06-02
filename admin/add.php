<?php
session_start();
include '../config/database.php';
require_once __DIR__ . '../../auth/check_admin.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "../upload/product_image/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_url = trim($_POST['image_url'] ?? '');

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($_FILES['product_image']['type'], $allowed_types)) {
            die("Chỉ cho phép định dạng ảnh JPG, PNG!");
        }

        $ext = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "." . $ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_url = $file_name;
        }
    }

    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    $tags = $_POST['tags'] ?? [];

    $discount_price = null;
    if (in_array('2', $tags) && !empty($_POST['discount_price'])) {
        $discount_price = (float)$_POST['discount_price'];
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO products
            (category_id, name, price, discount_price, stock_quantity, image_url, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $category_id,
            $name,
            $price,
            $discount_price,
            $stock_quantity,
            $image_url,
            $description
        ]);

        $product_id = $pdo->lastInsertId();

        if (!empty($tags)) {
            $stmt_tag = $pdo->prepare("
                INSERT INTO product_tags(product_id, tag_id)
                VALUES (?, ?)
            ");

            foreach ($tags as $tag_id) {
                $tag_id = (int)$tag_id;

                if (in_array($tag_id, [1, 2])) {
                    $stmt_tag->execute([$product_id, $tag_id]);
                }
            }
        }

        if (!empty($_POST['spec_names']) && !empty($_POST['spec_values'])) {
            $spec_names = $_POST['spec_names'];
            $spec_values = $_POST['spec_values'];

            $stmt_spec = $pdo->prepare("
                INSERT INTO product_specs(product_id, spec_name, spec_value, sort_order)
                VALUES (?, ?, ?, ?)
            ");

            $sort_order = 1;

            for ($i = 0; $i < count($spec_names); $i++) {
                $s_name = trim($spec_names[$i]);
                $s_val = trim($spec_values[$i] ?? '');

                if (!empty($s_name)) {
                    $stmt_spec->execute([
                        $product_id,
                        $s_name,
                        $s_val,
                        $sort_order
                    ]);

                    $sort_order++;
                }
            }
        }

        if (isset($_FILES['gallery_images'])) {
            $gallery_dir = "../upload/product_gallery/";

            if (!is_dir($gallery_dir)) {
                mkdir($gallery_dir, 0777, true);
            }

            $stmt_gal = $pdo->prepare("
                INSERT INTO product_images(product_id, image_url)
                VALUES (?, ?)
            ");

            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] == 0) {
                    $g_type = $_FILES['gallery_images']['type'][$key];

                    if (in_array($g_type, ['image/jpeg', 'image/png', 'image/jpg'])) {
                        $g_ext = pathinfo($_FILES["gallery_images"]["name"][$key], PATHINFO_EXTENSION);
                        $g_file_name = time() . "_" . $key . "." . $g_ext;

                        if (move_uploaded_file($tmp_name, $gallery_dir . $g_file_name)) {
                            $stmt_gal->execute([$product_id, $g_file_name]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: list_products.php?msg=Thêm sản phẩm thành công");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi hệ thống: " . $e->getMessage());
    }
}

$page_title = 'Thêm sản phẩm mới';
$page_icon = 'fa-solid fa-plus';
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/add.css">';

include 'includes/header.php';
?>

<div class="add-product-container">
    <h2><i class="fa-solid fa-box-open"></i> Thêm sản phẩm linh kiện mới</h2>

    <form action="add.php" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label><i class="fa-solid fa-pen"></i> Tên sản phẩm</label>
                <input type="text" name="name" class="form-control" placeholder="Nhập tên linh kiện..." required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-layer-group"></i> Danh mục sản phẩm</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-tags"></i> Giá bán gốc (₫)</label>
                <input type="number" name="price" class="form-control" placeholder="Ví dụ: 1500000" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-cubes"></i> Số lượng nhập kho ban đầu</label>
                <input type="number" name="stock_quantity" class="form-control" placeholder="Ví dụ: 50" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image"></i> Ảnh đại diện sản phẩm</label>
                <input type="file" name="product_image" class="form-control" accept="image/*">
                <input type="text" name="image_url" class="form-control" placeholder="Hoặc dán URL ảnh có sẵn..." style="margin-top: 8px;">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-images"></i> Album ảnh phụ (Có thể chọn nhiều ảnh)</label>
                <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
            </div>

            <div class="form-group full-width">
                <label><i class="fa-solid fa-star"></i> Nhãn trạng thái hiển thị</label>

                <div class="tags-group">
                    <label class="tag-checkbox">
                        <input type="checkbox" name="tags[]" value="1">
                        Sản phẩm nổi bật
                    </label>

                    <label class="tag-checkbox">
                        <input type="checkbox" name="tags[]" value="2" id="flash_sale_checkbox">
                        Flash Sale
                    </label>
                </div>

                <div id="flash-sale-price-group" style="display: none;">
                    <label style="color: #be123c;">
                        <i class="fa-solid fa-bolt"></i> Giá Flash Sale đặc biệt (₫)
                    </label>
                    <input type="number" name="discount_price" class="form-control" placeholder="Nhập mức giá ưu đãi khi chạy Flash Sale...">
                </div>
            </div>

            <div class="form-group full-width">
                <label><i class="fa-solid fa-file-lines"></i> Mô tả chi tiết sản phẩm</label>
                <textarea name="description" class="form-control" placeholder="Nhập mô tả tính năng, hiệu năng chi tiết của sản phẩm..."></textarea>
            </div>
        </div>

        <div class="specs-section">
            <h3><i class="fa-solid fa-sliders"></i> Cấu hình thông số kỹ thuật chi tiết</h3>

            <div id="specs-wrapper">
                <div class="spec-item">
                    <input type="text" name="spec_names[]" class="form-control" placeholder="Tên thông số (VD: Chipset)" style="flex: 1;">
                    <input type="text" name="spec_values[]" class="form-control" placeholder="Giá trị (VD: AMD X670)" style="flex: 2;">
                    <button type="button" class="btn btn-danger remove-spec-btn" style="padding: 0 15px; border-radius: 4px;">Xóa</button>
                </div>
            </div>

            <button type="button" id="add-spec-btn" class="btn btn-success" style="margin-top: 12px;">
                <i class="fa-solid fa-circle-plus"></i> Thêm dòng thông số mới
            </button>
        </div>

        <div class="form-actions">
            <a href="list_products.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Lưu sản phẩm
            </button>
        </div>
    </form>
</div>

</main>
</div>

<script src="../assets/js/script_dashboard.js"></script>
<script src="../assets/js/add.js"></script>
</body>
</html>