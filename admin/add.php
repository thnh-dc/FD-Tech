<?php
session_start();
include '../config/database.php';
require_once __DIR__ . '/check_admin.php';

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
            die("Chỉ cho phép JPG, PNG!");
        }

        $ext = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "." . $ext;

        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_url = $file_name;
        }
    }

    // --- XỬ LÝ GIÁ FLASH SALE ---
    $tags = $_POST['tags'] ?? [];
    $discount_price = (in_array('2', $tags) && !empty($_POST['discount_price'])) ? $_POST['discount_price'] : null;

    $stmt = $pdo->prepare("
        INSERT INTO products(name, price, discount_price, stock_quantity, category_id, description, image_url)
        VALUES(?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['price'],
        $discount_price,
        $_POST['stock'],
        $_POST['category_id'],
        $_POST['description'],
        $image_url
    ]);

    $product_id = $pdo->lastInsertId();

    if (!empty($_POST['tags'])) {
        $stmt_tags = $pdo->prepare("INSERT INTO product_tags (product_id, tag_id) VALUES (?, ?)");
        foreach ($_POST['tags'] as $tag_id) {
            $stmt_tags->execute([$product_id, $tag_id]);
        }
    }

    header("Location: list_products.php?msg=Thêm thành công");
    exit;
}
?>

<?php
$page_title = 'Thêm sản phẩm';
$page_icon = 'fa-solid fa-plus';
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/style_add_product.css">';

include 'includes/header.php';
?>

        <div class="dashboard-container">
            <div class="card">
                <h3>
                    <i class="fa-solid fa-plus"></i>
                    Thêm sản phẩm mới
                </h3>

                <form method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label class="form-label">Tên sản phẩm</label>
                        <input name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label tag-label">🏷️ Gắn nhãn sản phẩm</label>

                        <div class="tag-box">
                            <label class="tag-option">
                                <input type="checkbox" name="tags[]" value="1">
                                <span class="tag-badge tag-featured">
                                    <i class="fa-solid fa-star"></i>
                                    Sản phẩm nổi bật
                                </span>
                            </label>

                            <label class="tag-option">
                                <input type="checkbox" name="tags[]" value="2" id="flash-sale-checkbox">
                                <span class="tag-badge tag-sale">
                                    <i class="fa-solid fa-bolt"></i>
                                    Flash sale
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="flash-sale-price-group" style="display: none; background: #fff5f5; padding: 12px; border-radius: 6px; border: 1px solid #fee2e2;">
                        <label class="form-label" style="color: #dc2626; font-weight: bold;">Giá Flash Sale (₫)</label>
                        <input name="discount_price" type="number" step="any" min="0" class="form-control" placeholder="Nhập giá bán riêng cho Flash Sale...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Link ảnh online</label>
                        <input
                            type="url"
                            name="image_url"
                            class="form-control"
                            placeholder="https://i.ibb.co/..."
                        >
                        <p class="image-help">
                            Có thể dán link ảnh online hoặc upload ảnh local bên dưới.
                            Nếu chọn cả hai, ảnh upload local sẽ được ưu tiên.
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Upload ảnh local</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Giá (₫)</label>
                        <input name="price" type="number" step="any" min="0" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tồn kho</label>
                        <input name="stock" type="number" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-control">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>

                    <button class="btn btn-primary">
                        <i class="fa-solid fa-save"></i>
                        Lưu sản phẩm
                    </button>

                </form>
            </div>
        </div>

    </main>

</div>
<script src="../assets/js/script_dashboard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flashSaleCheckbox = document.getElementById('flash-sale-checkbox');
    const flashSalePriceGroup = document.getElementById('flash-sale-price-group');
    
    if (flashSaleCheckbox && flashSalePriceGroup) {
        flashSaleCheckbox.addEventListener('change', function() {
            if (this.checked) {
                flashSalePriceGroup.style.display = 'block';
            } else {
                flashSalePriceGroup.style.display = 'none';
                flashSalePriceGroup.querySelector('input').value = '';
            }
        });
    }
});
</script>
</body>
</html>