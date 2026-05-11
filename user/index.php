<?php 
    include '../includes/header.php'; 
    include '../includes/db.php'; 
?>

<link rel="stylesheet" href="../assets/css/index.css">
<div class="banner-container">
    <div class="banner-track" id="bannerTrack">
        <div class="banner-slide"><img src="../assets/images/banner.jpg"></div>
        <div class="banner-slide"><img src="../assets/images/banner1.jpg"></div>
    </div>
</div>
<div class="container"> 
    
    <h2 id="san-pham-noi-bat" class="section-title">⭐ Sản phẩm nổi bật</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 8");
        while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<span class="badge-hot">HOT</span>';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.$row['image_url'].'">';
            echo '<h3>'.$row['name'].'</h3>';
            echo '<p class="price">'.number_format($row['price']).' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="khuyen-mai" class="section-title">🔥 Flash Sale - Giá Sốc</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY price ASC LIMIT 8");
        while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<span class="badge-sale">SALE</span>';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.$row['image_url'].'">';
            echo '<h3>'.$row['name'].'</h3>';
            echo '<p class="price">'.number_format($row['price']).' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="man-hinh-may-tinh" class="section-title">🖥️ Màn hình máy tính</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products WHERE category_id = 3 LIMIT 8");
        while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.$row['image_url'].'">';
            echo '<h3>'.$row['name'].'</h3>';
            echo '<p class="price">'.number_format($row['price']).' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="loa-tai-nghe" class="section-title">🎧 Loa - Tai nghe</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products WHERE category_id IN (4, 5) LIMIT 8");
        while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.$row['image_url'].'">';
            echo '<h3>'.$row['name'].'</h3>';
            echo '<p class="price">'.number_format($row['price']).' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="ban-phim-co" class="section-title">⌨️ Bàn phím cơ</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products WHERE category_id = 6 LIMIT 8");
        while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.$row['image_url'].'">';
            echo '<h3>'.$row['name'].'</h3>';
            echo '<p class="price">'.number_format($row['price']).' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="chuot" class="section-title">🖱️ Chuột Gaming</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products WHERE category_id = 7 LIMIT 8");
        while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.$row['image_url'].'">';
            echo '<h3>'.$row['name'].'</h3>';
            echo '<p class="price">'.number_format($row['price']).' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="phu-kien-khac" class="section-title">🔌 Phụ kiện khác</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products WHERE category_id = 8 LIMIT 8");
        while($row = $stmt->fetch()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id='.$row['id'].'" style="text-decoration:none; color:#333;">';
            echo '<img src="'.$row['image_url'].'">';
            echo '<h3>'.$row['name'].'</h3>';
            echo '<p class="price">'.number_format($row['price']).' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

</div>

<script src="../assets/js/banner.js"></script>
<?php include '../includes/footer.php'; ?>