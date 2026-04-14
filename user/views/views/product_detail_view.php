<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - FD Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container" style="margin-bottom: 64px;">
        
        <div style="padding: 16px 0; font-size: var(--text-sm); color: var(--text-muted);">
            <a href="index.php" style="color: var(--primary); font-weight: 600;">Trang chủ</a> / 
            <a href="product_list.php" style="color: var(--primary); font-weight: 600;">Sản phẩm</a> / 
            <span style="color: var(--text-dark);"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div class="product-detail-layout">
            
            <div class="zoom-wrapper" id="zoomContainer">
                <img src="../assets/images/<?php echo htmlspecialchars($product['image_url'] ?: 'default.jpg'); ?>" 
                     class="zoom-img" id="zoomImage" alt="Sản phẩm">
            </div>
            
            <div class="product-detail-info">
                <span style="display: inline-block; background: var(--bg-light); color: var(--secondary); padding: 4px 12px; border-radius: 20px; font-size: var(--text-xs); font-weight: 700; margin-bottom: 12px; text-transform: uppercase;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
                
                <h1 style="font-size: var(--text-2xl); color: var(--text-dark); font-weight: 700; margin-bottom: 16px; line-height: 1.3;">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>
                
                <div style="font-size: 36px; color: var(--primary); font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--bg-light); padding-bottom: 16px;">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?> ₫
                </div>
                
                <div style="font-size: var(--text-sm); margin-bottom: 24px;">
                    Tình trạng: 
                    <?php if($product['stock_quantity'] > 0): ?>
                        <span style="color: var(--success); font-weight: 700;">✓ Còn hàng (Sẵn <?php echo $product['stock_quantity']; ?> SP)</span>
                    <?php else: ?>
                        <span style="color: var(--danger); font-weight: 700;">✕ Hết hàng</span>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 32px; color: var(--text-dark); line-height: 1.6; font-size: var(--text-sm);">
                    <h3 style="font-size: var(--text-base); margin-bottom: 8px;">Đặc điểm nổi bật:</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <form action="cart.php" method="POST" style="display: flex; gap: 16px; align-items: stretch;">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="qty-selector">
                        <button type="button" class="qty-btn" id="btnMinus">-</button>
                        <input type="number" name="quantity" id="qtyInput" class="qty-input" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                        <button type="button" class="qty-btn" id="btnPlus">+</button>
                    </div>

                    <button type="submit" style="flex: 1; background: var(--secondary); color: var(--bg-main); border: none; border-radius: var(--radius-md); font-size: var(--text-base); font-weight: 700; cursor: pointer; transition: 0.3s;" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                        THÊM VÀO GIỎ HÀNG
                    </button>
                </form>
            </div>

        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>