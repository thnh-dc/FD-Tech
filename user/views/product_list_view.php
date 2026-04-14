
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm - FD Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div style="padding: 16px 0; font-size: var(--text-sm); color: var(--text-muted);">
            <a href="index.php" style="color: var(--primary); font-weight: 600;">Trang chủ</a> / <span>Sản phẩm</span>
        </div>

        <div class="layout-with-sidebar">
            
            <aside class="sidebar">
                <h2 class="sidebar-title">BỘ LỌC</h2>
                
                <form action="product_list.php" method="GET" id="filterForm">
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-size: var(--text-sm); margin-bottom: 8px;">Từ khóa tìm kiếm</h3>
                        <input type="text" name="search" class="search-input" placeholder="Tên phụ kiện..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>

                    <div style="margin-bottom: 24px;">
                        <h3 style="font-size: var(--text-sm); margin-bottom: 12px;">Danh mục thiết bị</h3>
                        <label class="custom-radio">
                            <input type="radio" name="category" value="0" <?php echo ($category_id == 0) ? 'checked' : ''; ?>> 
                            Tất cả sản phẩm
                        </label>
                        <?php foreach ($categories as $cat): ?>
                            <label class="custom-radio">
                                <input type="radio" name="category" value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'checked' : ''; ?>> 
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" style="width: 100%; background: var(--secondary); color: var(--bg-main); border: none; padding: 12px; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                        ÁP DỤNG
                    </button>
                </form>
            </aside>

            <main class="main-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
                    <h2 style="font-size: var(--text-xl); font-weight: 700; color: var(--primary);">
                        <?php echo ($search != '') ? "Kết quả cho: '".htmlspecialchars($search)."'" : "TẤT CẢ SẢN PHẨM"; ?>
                    </h2>
                    <span style="font-size: var(--text-sm); color: var(--text-muted);"><?php echo count($products); ?> sản phẩm</span>
                </div>
                
                <div class="product-grid" id="ajaxProductGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px;">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $item): ?>
                            <div class="product-card">
                                <a href="product_detail.php?id=<?php echo $item['id']; ?>">
                                    <div class="product-img-wrap">
                                        <img src="../assets/images/<?php echo htmlspecialchars($item['image_url'] ?: 'default.jpg'); ?>" alt="Ảnh SP">
                                    </div>
                                    <p style="font-size: var(--text-xs); color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase;"><?php echo htmlspecialchars($item['category_name']); ?></p>
                                    <h3 style="font-size: var(--text-base); font-weight: 600; color: var(--text-dark); margin-bottom: 12px; height: 40px; overflow: hidden;"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p style="font-size: var(--text-lg); font-weight: 700; color: var(--primary); margin-bottom: 16px;">
                                        <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
                                    </p>
                                </a>
                                <button style="width:100%; background: var(--bg-light); color: var(--secondary); border: 1px solid var(--secondary); padding: 10px; border-radius: var(--radius-md); font-weight: 600; cursor: pointer; transition: 0.3s;">
                                    Thêm vào giỏ
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: var(--bg-main); border-radius: var(--radius-md);">
                            <p style="color: var(--danger); font-size: var(--text-lg);">Không tìm thấy sản phẩm nào.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>