<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FD Tech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_chung.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/header.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/footer.css">
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_ai_assistant.css">
    <?php
    if (isset($custom_css)) {
        echo $custom_css;
    }
    ?>
</head>

<body>
    <div class="main-header">
        <div class="container header-flex">
            <div class="logo">
                <a href="/FD-Tech/user/index.php">
                    <img src="/FD-Tech/assets/images/logo-FD.jpg" alt="FD TECH" style="height: 50px;">
                    <span style="font-size: 24px; font-weight: bold; color: #333;">FD<span
                            style="color: #00a8ff;">TECH</span></span>
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
                            // --- LOGIC HEADER TỰ ĐỘNG LẤY ẢNH TỪ DATABASE ---
                            $avatar_name = '';

                            // Tự động quét Database để lấy ảnh mới nhất (không cần phụ thuộc file profile)
                            if (isset($pdo) && isset($_SESSION['user_id'])) {
                                try {
                                    $stmt_avt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
                                    $stmt_avt->execute([$_SESSION['user_id']]);
                                    $avt_data = $stmt_avt->fetch(PDO::FETCH_ASSOC);

                                    if ($avt_data && !empty($avt_data['avatar'])) {
                                        $avatar_name = $avt_data['avatar'];
                                        $_SESSION['avatar'] = $avatar_name; // Cập nhật lại session dự phòng
                                    }
                                } catch (Exception $e) {
                                    // Bỏ qua nếu có lỗi
                                }
                            } elseif (isset($_SESSION['avatar'])) {
                                $avatar_name = $_SESSION['avatar'];
                            }

                            // Đường dẫn vật lý trên ổ cứng để PHP kiểm tra (Có dấu / sau __DIR__)
                            $server_file_path = __DIR__ . "/../upload/avatar_user/" . $avatar_name;

                            // Đường dẫn URL bắt đầu bằng /FD-Tech/ để luôn lấy đúng thư mục gốc
                            $browser_img_url = "/FD-Tech/upload/avatar_user/" . $avatar_name;

                            // Kiểm tra ảnh có tồn tại thật trên server không
                            if (!empty($avatar_name) && file_exists($server_file_path)) {
                                $header_avatar_url = $browser_img_url;
                            } else {
                                $username_for_avatar = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
                                $initials = mb_strtoupper(mb_substr($username_for_avatar, 0, 2, 'UTF-8'), 'UTF-8');
                                $header_avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=random&color=fff&size=128";
                            }
                            ?>
                            <img src="<?php echo $header_avatar_url; ?>" alt="AVT" class="user-avatar-img"
                                style="object-fit: cover; border-radius: 50%; width: 35px; height: 35px;">
                            <span class="user-name-text"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="fas fa-caret-down"></i>
                        </div>

                        <ul class="user-dropdown-menu">
                            <li><a href="/FD-Tech/user/profile.php"><i class="fas fa-user-cog"></i> Tài khoản của tôi</a>
                            </li>
                            <li><a href="/FD-Tech/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/FD-Tech/auth/login.php" class="auth-link"><i class="fas fa-user-circle"></i> Đăng nhập</a>
                    <span class="divider">|</span>
                    <a href="/FD-Tech/auth/register.php" class="auth-link">Đăng ký</a>
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
                            $categories = [
                                ['id' => '1', 'name' => 'LAPTOP'],
                                ['id' => '2', 'name' => 'LINH KIỆN'],
                                ['id' => '3', 'name' => 'MÀN HÌNH MÁY TÍNH'],
                                ['id' => '4', 'name' => 'TAI NGHE'],
                                ['id' => '5', 'name' => 'LOA'],
                                ['id' => '6', 'name' => 'BÀN PHÍM'],
                                ['id' => '7', 'name' => 'CHUỘT'],
                                ['id' => '8', 'name' => 'PHỤ KIỆN KHÁC'],
                            ];
                            foreach ($categories as $category): ?>
                                <a href="/FD-Tech/user/product_list.php?cat=<?php echo $category['id']; ?>&cname=<?php echo urlencode($category['name']); ?>"
                                    class="category-item">
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
                    <?php
                    $header_notifications = [];

                    if (isset($pdo) && isset($_SESSION['user_id'])) {
                        try {
                            $stmt_header_noti = $pdo->prepare("
                                SELECT id, status, updated_at
                                FROM orders
                                WHERE user_id = ?
                                ORDER BY updated_at DESC
                                LIMIT 5
                            ");
                            $stmt_header_noti->execute([$_SESSION['user_id']]);
                            $header_notifications = $stmt_header_noti->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            $header_notifications = [];
                        }
                    }

                    if (!function_exists('getHeaderNotificationText')) {
                        function getHeaderNotificationText($noti_order) {
                            $noti_order_id = str_pad($noti_order['id'], 0, '0', STR_PAD_LEFT);
                            $noti_time = date('H:i - d/m/Y', strtotime($noti_order['updated_at']));

                            switch ($noti_order['status']) {
                                case 'pending':
                                    return "Đơn hàng <strong>#FD{$noti_order_id}</strong> chưa được thanh toán, bạn vui lòng thanh toán trong vòng 15 phút nếu không đơn hàng sẽ bị hủy! <em>($noti_time)</em>";

                                case 'processing':
                                    return "Đơn hàng <strong>#FD{$noti_order_id}</strong> đang được xử lý. <em>($noti_time)</em>";

                                case 'shipped':
                                case 'shipping':
                                    return "Đơn hàng <strong>#FD{$noti_order_id}</strong> đã được giao cho đơn vị vận chuyển để giao hàng đến bạn ! <em>($noti_time)</em>";

                                case 'completed':
                                    return "Đơn hàng <strong>#FD{$noti_order_id}</strong> đã giao thành công. Cảm ơn bạn! <em>($noti_time)</em>";

                                case 'cancelled':
                                    return "Đơn hàng <strong>#FD{$noti_order_id}</strong> đã bị hủy. <em>($noti_time)</em>";

                                default:
                                    return "Đơn hàng <strong>#FD{$noti_order_id}</strong> vừa được cập nhật trạng thái. <em>($noti_time)</em>";
                            }
                        }
                    }
                    ?>

                    <div class="header-noti-wrapper">
                        <a href="/FD-Tech/user/profile.php?action=notifications" class="header-noti-icon">
                            <i class="far fa-bell"></i>

                            <?php if (!empty($header_notifications)): ?>
                                <span class="header-noti-dot"></span>
                            <?php endif; ?>
                        </a>

                        <div class="header-noti-popup">
                            <div class="header-noti-title">
                                <strong>Thông báo của tôi</strong>
                                <span>Cập nhật mới nhất</span>
                            </div>

                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <div class="header-noti-empty">
                                    Vui lòng đăng nhập để xem thông báo.
                                </div>
                            <?php elseif (empty($header_notifications)): ?>
                                <div class="header-noti-empty">
                                    Bạn chưa có thông báo nào.
                                </div>
                            <?php else: ?>
                                <ul class="header-noti-list">
                                    <?php foreach ($header_notifications as $header_noti_order): ?>
                                        <li class="header-noti-item">
                                            <i class="fas fa-angle-right"></i>
                                            <span><?= getHeaderNotificationText($header_noti_order) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <a href="/FD-Tech/user/profile.php?action=notifications" class="header-noti-view-all">
                                    Xem tất cả thông báo
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="/FD-Tech/user/cart.php" class="cart-icon">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-text">Giỏ hàng</span>
                    </a>
                </div>
            </div>
        </div>