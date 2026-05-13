<?php
    session_start();
    require_once '../auth/user_only.php';
    require_once '../config/database.php';

    $cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
    $products = [];
    $category_name = '';

    $menu_categories = [
        1 => 'LAPTOP',
        2 => 'LINH KIỆN',
        3 => 'MÀN HÌNH MÁY TÍNH',
        4 => 'TAI NGHE',
        5 => 'LOA',
        6 => 'BÀN PHÍM',
        7 => 'CHUỘT',
        8 => 'PHỤ KIỆN KHÁC'
    ];

    try {
        if ($cat > 0) {
            if (isset($menu_categories[$cat])) {
                $category_name = $menu_categories[$cat];
            } else {
                $stmt_cat = $pdo->prepare("SELECT name FROM categories WHERE id = :cat");
                $stmt_cat->execute(['cat' => $cat]);
                $cat_data = $stmt_cat->fetch(PDO::FETCH_ASSOC);
                if ($cat_data && !empty($cat_data['name'])) {
                    $category_name = $cat_data['name'];
                }
            }

            $stmt = $pdo->prepare("SELECT id, name, price, image_url, description FROM products WHERE category_id = :cat ORDER BY id DESC");
            $stmt->execute(['cat' => $cat]);
        } else {
            $stmt = $pdo->prepare("SELECT id, name, price, image_url, description FROM products ORDER BY id DESC");
            $stmt->execute();
        }

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("<h3 style='color:red; text-align:center;'>Lỗi truy vấn SQL: " . $e->getMessage() . "</h3>");
    }

    $custom_css = '<link rel="stylesheet" href="../assets/css/style_product_list.css">';

    include '../includes/header.php'; 
?>

<main class="container">
    <h2 class="section-title">
        <?php if ($category_name !== ''): ?>
            <span>DANH MỤC: <?= htmlspecialchars(mb_strtoupper($category_name, 'UTF-8')) ?></span>
        <?php else: ?>
            <span>TẤT CẢ SẢN PHẨM</span>
        <?php endif; ?>
    </h2>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $row): ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?= $row['id'] ?>">

                        <?php if(isset($row['is_promotion']) && $row['is_promotion'] == 1): ?>
                            <span class="product-badge">SALE</span>
                        <?php endif; ?>

                        <img src="<?= htmlspecialchars($row['image_url']) ?>">

                        <h3><?= htmlspecialchars($row['name']) ?></h3>

                        <p class="price">
                            <?= number_format($row['price'], 0, ',', '.') ?> VNĐ
                        </p>

                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="product-empty">Chưa có sản phẩm nào trong danh mục này.</p>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>