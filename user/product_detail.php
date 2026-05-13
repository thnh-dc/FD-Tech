<?php
    session_start();
    require_once '../auth/user_only.php';
    require_once '../config/database.php';

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("
    SELECT 
        id,
        name,
        price,
        stock_quantity,
        image_url,
        description
    FROM products 
    WHERE id = :id
    LIMIT 1
");

$stmt->execute(['id' => $id]);
$sp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sp) {
    die("<h2 class='text-center'>Sản phẩm không tồn tại!</h2>");
}

    $custom_css = '<link rel="stylesheet" href="../assets/css/style_product_detail.css">';
    include '../includes/header.php';
?>

<main class="container product-detail-container">
    <div class="container">
        <h2 class="section-title">Chi tiết sản phẩm</h2>
    </div>

    <div class="product-layout">

        <div class="product-image-section">
            <?php 
                $img_src = !empty($sp['image_url']) 
                    ? $sp['image_url'] 
                    : 'https://via.placeholder.com/500x500?text=Chua+Co+Anh';
            ?>
            <img src="<?= htmlspecialchars($img_src); ?>" alt="<?= htmlspecialchars($sp['name']); ?>">
        </div>

        <div class="product-info-section">
            <h1><?= htmlspecialchars($sp['name'] ?? 'Đang cập nhật'); ?></h1>

            <div class="product-price">
                <?= number_format($sp['price'] ?? 0, 0, ',', '.'); ?> VNĐ
            </div>

            <p class="product-desc">
                <?= $sp['description'] ?? 'Đang cập nhật mô tả...' ?>
            </p>

            <form action="../user/action_product_detail/action_product.php" method="POST" class="product-form">
                <input type="hidden" name="product_id" value="<?= $id; ?>">

                <div class="quantity-group">
                    <label><B>Tồn kho: </B><?= $sp['stock_quantity'] ?? '0' ?></label>
                </div>
                <div class="quantity-group">
                    <label><B>Số lượng mua:</B></label>
                    <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                </div>

                <div class="product-actions">
                    <button type="submit" name="action_type" value="add_to_cart" class="btn-outline">
                        THÊM VÀO GIỎ HÀNG
                    </button>

                    <button type="submit" name="action_type" value="buy_now" class="btn btn-primary btn-buy">
                        MUA NGAY
                    </button>
                </div>
            </form>
        </div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>