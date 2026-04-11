<?php
$categories = [
    ['name' => 'Khuyến mãi',       'icon' => false, 'bg' => '#f4f4f4'],
    ['name' => 'Sản phẩm bán chạy', 'icon' => false],
    ['name' => 'Xây dựng cấu hình', 'icon' => false],
    ['name' => 'Gearshop PC',      'icon' => false],
    ['name' => 'MÀN HÌNH MÁY TÍNH', 'icon' => true],
    ['name' => 'BÀN GAMING',       'icon' => true],
    ['name' => 'GHẾ CÔNG THÁI HỌC', 'icon' => true],
    ['name' => 'BÀN PHÍM CUSTOM',   'icon' => true],
    ['name' => 'GHẾ GAMING',       'icon' => true],
    ['name' => 'PHỤ KIỆN KHÁC',    'icon' => true],
    ['name' => 'LINH KIỆN PC',     'icon' => true],
    ['name' => 'LOA - TAI NGHE',   'icon' => true],
    ['name' => 'BÀN PHÍM CƠ',      'icon' => true],
    ['name' => 'CHUỘT',            'icon' => true],
];
?>
<?php
    $brand_name = "FD"; 
    $cart_count = 3; // Giả lập số lượng giỏ hàng
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
                <a href="../index.php">
                    <img src="../assets/images/logo-fd.jpg" alt="FD TECH" style="height: 50px; vertical-align: middle;"> 
                    <span style="font-size: 24px; font-weight: bold; color: #333; vertical-align: middle;">FD<span style="color: #00a8ff;">TECH.VN</span></span>
                </a>
            </div>

            <ul class="main-menu">
                <li><a href="../index.php">Trang chủ</a></li>
                <li class="has-child">
                    <a href="#">Sản phẩm <i class="fas fa-chevron-down"></i></a>
                </li>
                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Khuyến mãi</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>

            <div class="header-contact">
                <i class="fas fa-headset" style="font-size: 25px; color: var(--secondary);"></i>
                <div class="contact-info">
                    <span class="p" style="display: block; font-weight: bold;">0.888.000.112</span>
                    <span class="e" style="font-size: 11px; color: var(--text-muted);">fdtechchannel@gmail.com</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-header">
        <div class="container header-flex">
            <div class="category-toggle">
                <i class="fas fa-bars"></i> Danh mục sản phẩm <i class="fas fa-chevron-down"></i>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Nội dung tìm kiếm">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="header-icons">
                <a href="#"><i class="far fa-heart"></i></a>
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="count"><?php echo isset($cart_count) ? $cart_count : 0; ?></span>
                    <span style="font-size: 14px; margin-left: 5px;">Giỏ hàng</span>
                </a>
            </div>
        </div>
    </div>
</nav>