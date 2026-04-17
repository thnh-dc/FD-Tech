<?php
require_once '../config/database.php';
// ... (GIỮ NGUYÊN TOÀN BỘ ĐOẠN PHP XỬ LÝ TRUY VẤN Ở TRÊN CÙNG) ...
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - FD Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/pages/product_detail.css">
    <link rel="stylesheet" href="../assets/css/components/product_card.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container" style="margin-bottom: 64px;">
        <div style="padding: 16px 0; font-size: var(--text-sm); color: var(--text-muted);">
            <a href="index.php" style="color: var(--primary); font-weight: 600;">Trang chủ</a> / 
            <a href="product_list.php?category=<?php echo $product['category_id']; ?>" style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($product['category_name']); ?></a> / 
            <span style="color: var(--text-dark);"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div class="product-detail-layout">
            <div class="zoom-wrapper" id="zoomContainer">
                <img src="../assets/images/<?php echo htmlspecialchars($product['image_url'] ?: 'default.jpg'); ?>" class="zoom-img" id="zoomImage">
            </div>
            
            <div class="product-detail-info">
                <span style="background: var(--bg-light); color: var(--secondary); padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
                <h1 style="font-size: 32px; margin: 16px 0; line-height: 1.4; color: var(--text-dark);"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div style="font-size: 40px; color: var(--primary); font-weight: 900; margin-bottom: 24px; border-bottom: 1px solid var(--bg-light); padding-bottom: 24px;">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?> ₫
                </div>
                </div>
        </div>

        <?php include '../includes/components/product_tabs.php'; ?>

        <?php if(count($related_products) > 0): ?>
        <h2 style="font-size: 24px; color: var(--primary); margin: 64px 0 24px;">SẢN PHẨM CÙNG DANH MỤC</h2>
        <div class="product-grid">
            <?php foreach($related_products as $rel): ?>
                <?php 
                    // Gán biến $item thành $rel để dùng chung file product_card.php
                    $item = $rel; 
                    include '../includes/components/product_card.php'; 
                ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/product.js"></script>
</body>
</html>