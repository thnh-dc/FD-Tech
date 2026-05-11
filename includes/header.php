<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FD Tech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/index.css">
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
?>
    <div class="main-header">
    <div class="container header-flex">
        <div class="logo">
            <a href="index.php">
                <img src="../assets/images/logo-FD.jpg" alt="FD TECH" style="height: 50px;"> 
                <span style="font-size: 24px; font-weight: bold; color: #333;">FD<span style="color: #00a8ff;">TECH</span></span>
            </a>
        </div>

        <ul class="main-menu">
        <li><a href="index.php">Trang chủ</a></li>
        
        <li><a href="#">Tin tức</a></li>
        <li><a href="index.php#khuyen-mai">Khuyến mãi</a></li>
        <li><a href="#footer-contact">Liên hệ</a></li>
        </ul>

      <div class="header-auth">
    <?php 
    if (isset($_SESSION['user_id'])): 
    ?>
        <div class="user-profile-wrapper">
            <div class="user-profile-toggle">
                <?php 
                    $avatar = !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'default-avatar.png';
                ?>
                <img src="../assets/images/<?php echo $avatar; ?>" alt="AVT" class="user-avatar-img">
                <span class="user-name-text"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <i class="fas fa-caret-down"></i>
            </div>
            
            <ul class="user-dropdown-menu">
                <li><a href="../user/profile.php"><i class="fas fa-user-cog"></i> Tài khoản của tôi</a></li>
                <li><a href="../user/profile_order.php"><i class="fas fa-shopping-bag"></i> Đơn mua</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </div>
    <?php else: ?>
        <a href="../auth/login.php" class="auth-link"><i class="fas fa-user-circle"></i> Đăng nhập</a>
        <span class="divider">|</span>
        <a href="../auth/register.php" class="auth-link">Đăng ký</a>
    <?php endif; ?>
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
                            // BẢNG MAP ID: CHÚ Ý SỬA CÁC SỐ 3, 4, 5 CHO KHỚP VỚI DATABASE
                            $categories = [
                            ['id' => '1', 'name' => 'LAPTOP'],
                            ['id' => '2', 'name' => 'LINH KIỆN'],
                            ['id' => '3', 'name' => 'MÀN HÌNH MÁY TÍNH'],
                            ['id' => '4', 'name' => 'TAI NGHE'],
                            ['id'=> '5', 'name'=> 'LOA'],
                            ['id' => '6', 'name' => 'BÀN PHÍM'],
                            ['id' => '7', 'name' => 'CHUỘT'],
                            ['id' => '8', 'name' => 'PHỤ KIỆN KHÁC'],
                       ];
                        foreach ($categories as $category): ?>
                            <a href="/FD-Tech/user/product_list.php?cat=<?php echo $category['id']; ?>&cname=<?php echo urlencode($category['name']); ?>" class="category-item">
                                <span><?php echo $category['name']; ?></span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <form action="search.php" method="GET" class="search-box">
            <input type="text" name="query" placeholder="Nội dung tìm kiếm" required>
            <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <div class="header-icons">
    <a href="#"><i class="far fa-heart"></i></a>
    <a href="/FD-Tech/user/cart.php" class="cart-icon">
        <i class="fas fa-shopping-bag"></i> 
        <span class="cart-text">Giỏ hàng</span>
    </a>
</div>
        </div>
    </div>