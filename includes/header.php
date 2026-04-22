<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FD Tech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/FD-Tech/assets/css/header.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/footer.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_chung.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_admin.css">
    <?php
        if (isset($custom_css)) {
            echo $custom_css;
        }
    ?>
</head>
<body>
<?php
    $brand_name = "FD"; 
    $cart_count = 0;   
    
    // Kiểm tra xem URL hiện tại có chứa thư mục /admin/ hay không
    $is_admin = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false);
?>

<?php if (!$is_admin): ?>
    <div class="main-header">
        <div class="container header-flex">
            <div class="logo">
                <a href="http://localhost/FD-Tech/user/index.php">
                    <img src="../assets/images/logo-fd.jpg" alt="FD TECH" style="height: 50px;"> 
                    <span style="font-size: 24px; font-weight: bold; color: #333;">FD<span style="color: #00a8ff;">TECH</span></span>
                </a>
            </div>

            <ul class="main-menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li class="has-child"><a href="#">Sản phẩm <i class="fas fa-chevron-down"></i></a></li>
                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Khuyến mãi</a></li>
                <li><a href="#footer-contact">Liên hệ</a></li>
            </ul>

            <div class="header-auth">
                <a href="http://localhost/FD-Tech/auth/login.php" class="auth-link">
                    <i class="fas fa-user-circle"></i> Đăng nhập
                </a>
                <span class="divider">|</span>
                <a href="http://localhost/FD-Tech/auth/register.php" class="auth-link">Đăng ký</a>
            </div>
        </div>
    </div>

    <div class="bottom-header">
        <div class="container header-flex">
            <div class="category-wrapper">
                <div class="category-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i> 
                    <span>DANH MỤC SẢN PHẨM</span> 
                    <i class="fas fa-chevron-down"></i>
                </div>

                <div class="category-dropdown" id="categoryDropdown">
                    <div class="category-list">
                        <?php 
                        $categories = [
                            ['id' => 'khuyen-mai', 'name' => 'Khuyến mãi'],
                            ['id' => 'san-pham-ban-chay', 'name' => 'Sản phẩm bán chạy'],
                            ['id' => 'xay-dung-cau-hinh', 'name' => 'Xây dựng cấu hình'],
                            ['id' => 'man-hinh-may-tinh', 'name' => 'MÀN HÌNH MÁY TÍNH'],
                            ['id' => 'loa-tai-nghe', 'name' => 'LOA - TAI NGHE'],
                            ['id' => 'ban-phim-co', 'name' => 'BÀN PHÍM CƠ'],
                            ['id' => 'chuot', 'name' => 'CHUỘT'],
                            ['id' => 'phu-kien-khac', 'name' => 'PHỤ KIỆN KHÁC'],
                        ];
                        foreach ($categories as $category): ?>
                            <a href="products.php?category=<?php echo $category['id']; ?>" class="category-item">
                                <span><?php echo $category['name']; ?></span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="search-box">
                <input type="text" placeholder="Nội dung tìm kiếm">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>

            <div class="header-icons">
                <a href="#"><i class="far fa-heart"></i></a>
                <a href="/FD-Tech/user/cart.php" class="cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="count">0</span>
                    <span class="cart-text">Giỏ hàng</span>
                </a>
            </div>
        </div>
    </div>
    <script src="../assets/js/navbar.js"></script>
<?php endif; ?> 
</body>
</html>