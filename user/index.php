<?php
include '../includes/header.php';
include '../includes/db.php';
?>
<div class="banner-container">
    <div class="banner-track" id="bannerTrack">
        <div class="banner-slide"><img src="../assets/images/banner.jpg" alt="Slide 1"></div>
        <div class="banner-slide"><img src="../assets/images/banner1.jpg" alt="Slide 2"></div>
    </div>
</div>

<div class="container" style="margin-top: 30px;">

    <h2 id="san-pham-noi-bat" class="section-title">Sản phẩm nổi bật</h2>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        $sql = "SELECT * FROM products WHERE category_id = 'featured' LIMIT 8";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: #333;">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
            echo '<h3 style="font-size: 14px; margin-bottom: 8px;">' . $row['name'] . '</h3>';
            echo '<p style="color: #ee4d2d; font-weight: bold;">' . number_format($row['price']) . ' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="khuyen-mai" class="section-title">Flash Sale - Giá Sốc</h2>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        $sql_promo = "SELECT * FROM products WHERE category_id = 'sale_hot' AND is_promotion = 1 LIMIT 8";
        $result_promo = $conn->query($sql_promo);
        while ($row = $result_promo->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: #333;">';
            echo '<div style="position:relative;">';
            echo '<span style="position:absolute; top:5px; left:5px; background:red; color:white; padding:2px 8px; font-size:10px; border-radius:4px; z-index:1;">SALE</span>';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
            echo '</div>';
            echo '<h3 style="font-size: 14px; margin-bottom: 8px;">' . $row['name'] . '</h3>';
            echo '<p style="color: #ee4d2d; font-weight: bold;">' . number_format($row['price']) . ' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="man-hinh-may-tinh" class="section-title">Màn hình máy tính</h2>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        $sql_monitor = "SELECT * FROM products WHERE category_id = 'monitor' LIMIT 8";
        $result_monitor = $conn->query($sql_monitor);
        while ($row = $result_monitor->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: #333;">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
            echo '<h3 style="font-size: 14px; margin-bottom: 8px;">' . $row['name'] . '</h3>';
            echo '<p style="color: #ee4d2d; font-weight: bold;">' . number_format($row['price']) . ' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="loa-tai-nghe" class="section-title">Loa - Tai nghe</h2>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        $sql_audio = "SELECT * FROM products WHERE category_id = 'audio' LIMIT 8";
        $result_audio = $conn->query($sql_audio);
        while ($row = $result_audio->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: #333;">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
            echo '<h3 style="font-size: 14px; margin-bottom: 8px;">' . $row['name'] . '</h3>';
            echo '<p style="color: #ee4d2d; font-weight: bold;">' . number_format($row['price']) . ' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="ban-phim-co" class="section-title">Bàn phím cơ</h2>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        $sql_kb = "SELECT * FROM products WHERE category_id = 'keyboard' ORDER BY id DESC LIMIT 8";
        $result_kb = $conn->query($sql_kb);
        while ($row = $result_kb->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: #333;">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
            echo '<h3 style="font-size: 14px; margin-bottom: 8px;">' . $row['name'] . '</h3>';
            echo '<p style="color: #ee4d2d; font-weight: bold;">' . number_format($row['price']) . ' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="chuot" class="section-title">Chuột</h2>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        $sql_mouse = "SELECT * FROM products WHERE category_id = 'mouse' ORDER BY id DESC LIMIT 8";
        $result_mouse = $conn->query($sql_mouse);
        while ($row = $result_mouse->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '<a href="product_detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: #333;">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
            echo '<h3 style="font-size: 14px; margin-bottom: 8px;">' . $row['name'] . '</h3>';
            echo '<p style="color: #ee4d2d; font-weight: bold;">' . number_format($row['price']) . ' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>

    <h2 id="phu-kien-khac" class="section-title">Phụ kiện khác</h2>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <?php
        $sql_acc = "SELECT * FROM products WHERE category_id = 'accessory' ORDER BY id DESC LIMIT 16";
        $result_acc = $conn->query($sql_acc);
        while ($row = $result_acc->fetch_assoc()) {
            echo '<div class="product-card" style="position: relative;">';
            echo '<a href="product_detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: #333;">';
            if ($row['is_promotion'] == 1)
                echo '<span style="position:absolute; top:5px; left:5px; background:red; color:white; padding:2px 8px; font-size:10px; border-radius:4px; z-index:1;">SALE</span>';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_data']) . '" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">';
            echo '<h3 style="font-size: 14px; margin-bottom: 8px;">' . $row['name'] . '</h3>';
            echo '<p style="color: #ee4d2d; font-weight: bold;">' . number_format($row['price']) . ' ₫</p>';
            echo '</a></div>';
        }
        ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>