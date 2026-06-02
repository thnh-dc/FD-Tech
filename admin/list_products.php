<?php
session_start();
include '../config/database.php';
require_once __DIR__ . '../../auth/check_admin.php';

$search = $_GET['search'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$tag_id = $_GET['tag_id'] ?? '';

try {
    $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    $tags = $pdo->query("SELECT * FROM tags ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT 
                p.id,
                p.name,
                p.price,
                p.discount_price,
                p.image_url,
                p.description,
                c.name AS cat_name,
                GROUP_CONCAT(t.name ORDER BY t.id SEPARATOR ',') AS tag_names,
                GROUP_CONCAT(t.id ORDER BY t.id SEPARATOR ',') AS tag_ids
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_tags pt ON p.id = pt.product_id
            LEFT JOIN tags t ON pt.tag_id = t.id
            WHERE p.name LIKE ?";

    $params = ["%$search%"];

    if (!empty($category_id)) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }

    if (!empty($tag_id)) {
        $sql .= " AND EXISTS (
                    SELECT 1 
                    FROM product_tags pt_filter
                    WHERE pt_filter.product_id = p.id
                    AND pt_filter.tag_id = ?
                  )";
        $params[] = $tag_id;
    }

    $sql .= " GROUP BY 
                p.id,
                p.name,
                p.price,
                p.discount_price,
                p.image_url,
                p.description,
                c.name
              ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi query: " . $e->getMessage());
}

function getProductImage($image_url)
{
    if (empty($image_url)) {
        return "../assets/images/logo-fd.jpg";
    }

    if (filter_var($image_url, FILTER_VALIDATE_URL)) {
        return $image_url;
    }

    return "../upload/product_image/" . $image_url;
}

function renderProductTags($tag_ids)
{
    if (empty($tag_ids)) {
        return '<span style="color:#999;font-size:12px;">Chưa gắn tag</span>';
    }

    $ids = explode(',', $tag_ids);
    $html = '';

    foreach ($ids as $id) {
        if ((int)$id === 1) {
            $html .= '<span class="product-tag tag-featured"><i class="fa-solid fa-star"></i> Nổi bật</span>';
        }

        if ((int)$id === 2) {
            $html .= '<span class="product-tag tag-sale"><i class="fa-solid fa-bolt"></i> Flash Sale</span>';
        }
    }

    return $html;
}
?>

<?php
$page_title = 'Quản lý sản phẩm';
$page_icon = 'fa-solid fa-box-open';
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/style_list_product.css">';

include 'includes/header.php';
?>

<style>
    .price-old {
        display: block;
        color: #999;
        text-decoration: line-through;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .price-sale {
        display: block;
        color: #dc2626;
        font-weight: 800;
        font-size: 15px;
    }

    .price-normal {
        color: #dc2626;
        font-weight: 800;
    }

    .tag-list {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 6px;
    }

    .product-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .tag-featured {
        background: #fef3c7;
        color: #92400e;
    }

    .tag-sale {
        background: #fee2e2;
        color: #991b1b;
    }

    .search-form {
        flex-wrap: wrap;
    }

    .tag-select {
        min-width: 170px;
    }
</style>

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

                        <select name="category_id" class="form-control category-select" onchange="this.form.submit()">
                            <option value="">Tất cả danh mục</option>

                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="tag_id" class="form-control tag-select" onchange="this.form.submit()">
                            <option value="">Tất cả tag</option>

                            <?php foreach ($tags as $tag): ?>
                                <option value="<?= $tag['id'] ?>" <?= ($tag_id == $tag['id']) ? 'selected' : '' ?>>
                                    <?php if ($tag['id'] == 1): ?>
                                        Nổi bật
                                    <?php elseif ($tag['id'] == 2): ?>
                                        Flash Sale
                                    <?php else: ?>
                                        <?= htmlspecialchars($tag['name']) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button class="btn btn-primary">
                            <i class="fa-solid fa-search"></i>
                        </button>

                        <?php if ($search !== '' || $category_id !== '' || $tag_id !== ''): ?>
                            <a href="list_products.php" class="btn btn-secondary">
                                Bỏ lọc
                            </a>
                        <?php endif; ?>

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
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (count($products) > 0): ?>

                        <?php foreach ($products as $p): ?>

                            <?php
                                $image_src = getProductImage($p['image_url']);
                                $price = (float)$p['price'];
                                $discount_price = isset($p['discount_price']) ? (float)$p['discount_price'] : 0;
                                $has_sale = $discount_price > 0 && $discount_price < $price;
                            ?>

                            <tr>

                                <td>
                                    <img
                                        src="<?= htmlspecialchars($image_src) ?>"
                                        class="product-image"
                                        alt="<?= htmlspecialchars($p['name']) ?>"
                                        onerror="this.src='../assets/images/logo-fd.jpg'"
                                    >
                                </td>

                                <td>
                                    <strong><?= htmlspecialchars($p['name']) ?></strong>

                                    <div class="tag-list">
                                        <?= renderProductTags($p['tag_ids']) ?>
                                    </div>
                                </td>

                                <td>
                                    <?= htmlspecialchars($p['cat_name'] ?? 'Chưa có') ?>
                                </td>

                                <td class="product-price">
                                    <?php if ($has_sale): ?>
                                        <span class="price-old">
                                            <?= number_format($price, 0, ',', '.') ?>₫
                                        </span>

                                        <span class="price-sale">
                                            <?= number_format($discount_price, 0, ',', '.') ?>₫
                                        </span>
                                    <?php else: ?>
                                        <span class="price-normal">
                                            <?= number_format($price, 0, ',', '.') ?>₫
                                        </span>
                                    <?php endif; ?>
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
                            <td colspan="5">
                                <div class="empty-box">
                                    <i class="fa-solid fa-box-open"></i>
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
<script src="../assets/js/script_dashboard.js"></script>
</body>
</html>