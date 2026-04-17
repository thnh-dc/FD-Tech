<?php
require_once '../config/database.php';
// ... (GIỮ NGUYÊN TOÀN BỘ ĐOẠN PHP XỬ LÝ TRUY VẤN DỮ LIỆU Ở TRÊN CÙNG) ...
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm - FD Tech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../assets/css/components/product_card.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container layout-with-sidebar">
        
        <?php include '../includes/components/sidebar_filter.php'; ?>

        <main class="main-content">
            <h2 style="font-size: 24px; margin-bottom: 24px; color: var(--primary);">
                TẤT CẢ SẢN PHẨM <span style="font-size: 16px; color: var(--text-muted);"> (<?php echo $total_products; ?> kết quả)</span>
            </h2>
            
            <div id="ajaxProductGrid">
                <div class="loader-container" id="ajaxLoader"><div class="spinner"></div></div>
                
                <div id="productResults" class="product-grid">
                    <?php if (count($products) > 0): ?>
                        
                        <?php foreach ($products as $item): ?>
                            <?php include '../includes/components/product_card.php'; ?>
                        <?php endforeach; ?>
                        
                        <?php if($total_pages > 1): ?>
                            <div class="pagination" style="grid-column: 1/-1;">
                                <?php for($i=1; $i<=$total_pages; $i++): ?>
                                    <a href="#" class="page-item <?php echo ($page==$i)?'active':''; ?>" onclick="changePage(<?php echo $i; ?>, event)"><?php echo $i; ?></a>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div style="grid-column: 1/-1; padding: 40px; text-align: center; border: 1px solid var(--bg-light); border-radius: 8px;">
                            <p style="color: var(--danger); font-size: 18px; font-weight: bold;">Không tìm thấy sản phẩm nào!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/product.js"></script>
</body>
</html>