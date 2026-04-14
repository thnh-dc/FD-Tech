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
</head>
<body>

<?php
    $brand_name = "FD"; 
    $cart_count = 0;   
?>

<nav class="site-header">
    <div class="top-bar">
        <div class="container header-flex">
            <div class="top-left">Chào mừng bạn đến với FD-Tech.vn</div>
            <div class="top-right">
                <span><i class="fas fa-desktop"></i> Tuyển dụng</span>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <div class="main-header">
        <div class="container header-flex">
            <div class="logo">
                <a href="index.php">
                    <img src="../assets/images/logo-fd.jpg" alt="FD TECH" style="height: 50px;"> 
                    <span style="font-size: 24px; font-weight: bold; color: #333;">FD<span style="color: #00a8ff;">TECH.VN</span></span>
                </a>
            </div>

            <ul class="main-menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li class="has-child"><a href="#">Sản phẩm <i class="fas fa-chevron-down"></i></a></li>
                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Khuyến mãi</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>

            <div class="header-contact">
                <i class="fas fa-headset"></i>
                <div class="contact-info">
                    <span class="p">1900 10 00</span>
                    <span class="e">fdtech@gmail.com</span>
                </div>
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
                            ['id' => 'gearshop-pc', 'name' => 'Gearshop PC'],
                            ['id' => 'man-hinh-may-tinh', 'name' => 'MÀN HÌNH MÁY TÍNH'],
                            ['id' => 'ban-gaming', 'name' => 'BÀN GAMING'],
                            ['id' => 'ghe-cong-thai-hoc', 'name' => 'GHẾ CÔNG THÁI HỌC'],
                            ['id' => 'ban-phim-custom', 'name' => 'BÀN PHÍM CUSTOM'],
                            ['id' => 'ghe-gaming', 'name' => 'GHẾ GAMING'],
                            ['id' => 'phu-kien-khac', 'name' => 'PHỤ KIỆN KHÁC'],
                            ['id' => 'linh-kien-pc', 'name' => 'LINH KIỆN PC'],
                            ['id' => 'loa-tai-nghe', 'name' => 'LOA - TAI NGHE'],
                            ['id' => 'ban-phim-co', 'name' => 'BÀN PHÍM CƠ'],
                            ['id' => 'chuot', 'name' => 'CHUỘT'],
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
                    <i class="fas fa-shopping-bag"></i> <span class="count">0</span>
                    <span class="cart-text">Giỏ hàng</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<script src="../assets/js/navbar.js"></script>
</body>
</html>