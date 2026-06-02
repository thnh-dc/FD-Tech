<?php
    session_start();
    require_once '../auth/user_only.php';
    require_once '../config/database.php';
    $custom_css='
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="../assets/css/index.css">';
    include '../includes/header.php'; 
    
?>
<div id="advPopup" class="adv-popup-overlay">
    <div class="adv-popup-content">
        <span class="adv-popup-close" onclick="closePopup(event)"><i class="fa-solid fa-xmark"></i></span>
        <img src="" alt="FD_Tech Siêu Sale Công Nghệ" class="adv-popup-img" style="cursor: pointer;">
    </div>
</div>

<div class="banner-container">
    <div class="banner-track" id="bannerTrack"></div>
</div>
<div class="container"> 
    
    <h2 id="san-pham-noi-bat" class="section-title"><i class="fa-solid fa-star" style="color: #ffca08;"></i> Sản phẩm nổi bật</h2>
        <div class="product-grid">
            <?php
            $tag_id_noi_bat = 1; 

            $sql = "SELECT p.* FROM products p 
                    INNER JOIN product_tags pt ON p.id = pt.product_id 
                    WHERE pt.tag_id = ? 
                    LIMIT 8";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$tag_id_noi_bat]);

            $base_url = "/FD-Tech/";

            while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<span class="badge-hot">HOT</span>';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.(strpos($row['image_url'], 'http') === 0 
                ? $row['image_url'] 
                : 'http://localhost/FD-Tech/upload/product_image/'.$row['image_url']).'">';
            echo '<h3>'.$row['name'].'</h3>';
            if (isset($row['discount_price']) && $row['discount_price'] > 0) {
                echo '<div class="price-container">';
                echo '<p class="price">'.number_format($row['discount_price']).' ₫</p>';
                echo '<p class="old-price">'.number_format($row['price']).' ₫</p>';
                echo '</div>';
            } else {
        echo '<div class="price-container">';
        echo '<p class="price">'.number_format($row['price']).' ₫</p>';
        echo '</div>';
    }
        echo '<div class="stock-container">';
        if (isset($row['stock_quantity']) && $row['stock_quantity'] > 0) {
            echo '<span class="status-in-stock"><i class="fa-solid fa-check"></i> Còn hàng</span> <span class="stock-count">(Số lượng: '.$row['stock_quantity'].')</span>';
        } else {
            echo '<span class="status-out-of-stock"><i class="fa-solid fa-xmark"></i> Hết hàng</span>';
        }
        echo '</div>';

        echo '</a></div>';
            }
            ?>
        </div>  

    <h2 id="khuyen-mai" class="section-title"><i class="fa-solid fa-fire" style="color: #ff3838;"></i> Flash Sale - Giá Sốc</h2>
        <div class="product-grid">
            <?php
            $tag_id_flash_sale = 2; 

            $sql_sale = "SELECT p.* FROM products p 
                        INNER JOIN product_tags pt ON p.id = pt.product_id 
                        WHERE pt.tag_id = ? 
                        LIMIT 8";
                    
            $stmt_sale = $pdo->prepare($sql_sale);
            $stmt_sale->execute([$tag_id_flash_sale]);
            $base_url = "/FD-Tech/";

            while($row = $stmt_sale->fetch()) { 
                echo '<div class="product-card">';
                echo '<span class="badge-sale">SALE</span>'; 
                echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
                echo '<img src="'.(strpos($row['image_url'], 'http') === 0 
                    ? $row['image_url'] 
                    : 'http://localhost/FD-Tech/upload/product_image/'.$row['image_url']).'">';
                    echo '<h3>'.$row['name'].'</h3>';
                    if (isset($row['discount_price']) && $row['discount_price'] > 0) {
                        echo '<div class="price-container">';
                        echo '<p class="price">'.number_format($row['discount_price']).' ₫</p>';
                        echo '<p class="old-price">'.number_format($row['price']).' ₫</p>';
                        echo '</div>';
                   } else {
                echo '<div class="price-container">';
                echo '<p class="price">'.number_format($row['price']).' ₫</p>';
                echo '</div>';
            }

                echo '<div class="stock-container">';
                if (isset($row['stock_quantity']) && $row['stock_quantity'] > 0) {
                    echo '<span class="status-in-stock"><i class="fa-solid fa-check"></i> Còn hàng</span> <span class="stock-count">(Số lượng: '.$row['stock_quantity'].')</span>';
                } else {
                    echo '<span class="status-out-of-stock"><i class="fa-solid fa-xmark"></i> Hết hàng</span>';
                }
                echo '</div>';

            echo '</a></div>';
            }
            ?>
            </div>
            
            <h2 id="san-pham-dang-ban" class="section-title"><i class="fa-solid fa-cart-shopping" style="color: #007bff;"></i> Sản phẩm đang bán</h2>
        <div class="product-grid">
            <?php
            $sql_random = "SELECT * FROM products 
                           ORDER BY RAND() 
                           LIMIT 8";
                    
            $stmt_random = $pdo->query($sql_random);

            while($row = $stmt_random->fetch()) { 
                echo '<div class="product-card">';
                echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
                echo '<img src="'.(strpos($row['image_url'], 'http') === 0 
                    ? $row['image_url'] 
                    : 'http://localhost/FD-Tech/upload/product_image/'.$row['image_url']).'">';
                    echo '<h3>'.$row['name'].'</h3>';
                    if (isset($row['discount_price']) && $row['discount_price'] > 0) {
                        echo '<div class="price-container">';
                        echo '<p class="price">'.number_format($row['discount_price']).' ₫</p>';
                        echo '<p class="old-price">'.number_format($row['price']).' ₫</p>';
                        echo '</div>';
                    } else {
                        echo '<div class="price-container">';
                        echo '<p class="price">'.number_format($row['price']).' ₫</p>';
                        echo '</div>';
                    }

                    echo '<div class="stock-container">';
                    if (isset($row['stock_quantity']) && $row['stock_quantity'] > 0) {
                        echo '<span class="status-in-stock"><i class="fa-solid fa-check"></i> Còn hàng</span> <span class="stock-count">(Số lượng: '.$row['stock_quantity'].')</span>';
                    } else {
                        echo '<span class="status-out-of-stock"><i class="fa-solid fa-xmark"></i> Hết hàng</span>';
                    }
                    echo '</div>';

                    echo '</a></div>';
            }
            ?>
        </div>
</div>

<script src="../assets/js/index.js"></script>

<?php include '../includes/ai_assistant_widget.php'; ?>
<?php include '../includes/footer.php'; ?>