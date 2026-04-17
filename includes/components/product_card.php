<div class="product-card">
    <a href="product_detail.php?id=<?php echo $item['id']; ?>">
        <div class="product-img-wrap">
            <img src="../assets/images/<?php echo htmlspecialchars($item['image_url'] ?: 'default.jpg'); ?>" alt="Ảnh">
        </div>
        <p style="font-size: 12px; color: var(--text-muted); font-weight: bold; text-transform: uppercase;">
            <?php echo htmlspecialchars($item['category_name']); ?>
        </p>
        <h3 style="font-size: 16px; margin: 8px 0; color: var(--text-dark); height: 40px; overflow: hidden;">
            <?php echo htmlspecialchars($item['name']); ?>
        </h3>
        <p style="font-size: 20px; font-weight: bold; color: var(--primary); margin-bottom: 16px;">
            <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
        </p>
    </a>
    <button class="btn-primary">Thêm vào giỏ</button>
</div>