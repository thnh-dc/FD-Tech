<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- LẤY THÔNG TIN USER CHO SIDEBAR ---
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy xuất dữ liệu người dùng!");
}

// --- XỬ LÝ LỌC TRẠNG THÁI (NHƯ SHOPEE) ---
// Nhận trạng thái từ URL, mặc định là 'all' (Tất cả)
$current_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$allowed_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

try {
    // Nếu người dùng chọn 1 tab cụ thể
    if ($current_status !== 'all' && in_array($current_status, $allowed_statuses)) {
        $stmt_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND status = ? ORDER BY created_at DESC");
        $stmt_orders->execute([$user_id, $current_status]);
    } else {
        // Nếu chọn "Tất cả"
        $stmt_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt_orders->execute([$user_id]);
    }
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}

// Xử lý đường dẫn Avatar
$has_custom_avatar = !empty($user['avatar']) && file_exists("../upload/avatar_user/" . $user['avatar']);
if ($has_custom_avatar) {
    $avatar_url = "../upload/avatar_user/" . $user['avatar'];
} else {
    $initials = mb_strtoupper(mb_substr($user['username'], 0, 2, 'UTF-8'), 'UTF-8');
    $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=random&color=fff&size=128";
}
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="../assets/css/style_profile.css">

<style>
    /* Thanh Tabs phân loại */
    .shopee-tabs {
        display: flex;
        background: #fff;
        margin-bottom: 15px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        border-radius: 4px;
        overflow-x: auto;
        /* Cho phép cuộn ngang trên mobile */
    }

    .shopee-tab-item {
        flex: 1;
        text-align: center;
        padding: 15px 10px;
        color: #555;
        text-decoration: none;
        font-weight: 500;
        font-size: 15px;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .shopee-tab-item:hover {
        color: #ee4d2d;
    }

    .shopee-tab-item.active {
        color: #ee4d2d;
        border-bottom-color: #ee4d2d;
    }

    /* Thẻ Card cho từng đơn hàng */
    .order-card {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
        padding: 20px;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .order-id {
        font-weight: 600;
        color: #333;
    }

    .order-status {
        text-transform: uppercase;
        font-weight: 600;
        font-size: 13px;
    }

    .status-pending {
        color: #856404;
    }

    .status-processing {
        color: #d39e00;
    }

    .status-shipped {
        color: #004085;
    }

    .status-completed {
        color: #ee4d2d;
    }

    /* Màu đỏ đặc trưng Shopee khi hoàn thành */
    .status-cancelled {
        color: #721c24;
    }

    .order-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
    }

    .order-info p {
        margin: 5px 0;
        color: #777;
        font-size: 14px;
    }

    .order-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        border-top: 1px solid #eee;
        padding-top: 15px;
        gap: 20px;
    }

    .order-total-price {
        font-size: 20px;
        color: #ee4d2d;
        font-weight: bold;
    }

    .btn-action {
        background: #ee4d2d;
        color: #fff;
        padding: 8px 20px;
        border-radius: 2px;
        text-decoration: none;
        font-weight: 500;
        transition: 0.2s;
    }

    .btn-action:hover {
        background: #d73a1c;
        color: #fff;
    }

    .btn-outline {
        background: #fff;
        color: #555;
        border: 1px solid #ccc;
        padding: 8px 20px;
        border-radius: 2px;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-outline:hover {
        background: #f8f9fa;
    }
</style>

<div class="profile-wrapper">
    <div class="profile-container">

        <div class="profile-sidebar">
            <div class="user-brief">
                <img src="<?php echo $avatar_url; ?>" id="sidebar-avatar" alt="Avatar">
                <div>
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p style="font-size: 12px; color: #777;"><a href="profile.php"
                            style="color: #777; text-decoration: none;"><i class="fas fa-pencil-alt"></i> Sửa hồ sơ</a>
                    </p>
                </div>
            </div>
            <ul class="profile-menu">
                <li><a href="profile.php" class="menu-link"><i class="far fa-user"></i> Tài khoản của tôi</a></li>
                <li><a href="profile_order.php" class="menu-link active"><i class="fas fa-clipboard-list"></i> Đơn
                        mua</a></li>
                <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                    <a href="../auth/logout.php" style="color: #DB4437;"><i class="fas fa-sign-out-alt"></i> Đăng
                        xuất</a>
                </li>
            </ul>
        </div>

        <div class="profile-content">

            <div class="shopee-tabs">
                <a href="?status=all"
                    class="shopee-tab-item <?php echo $current_status == 'all' ? 'active' : ''; ?>">Tất cả</a>
                <a href="?status=pending"
                    class="shopee-tab-item <?php echo $current_status == 'pending' ? 'active' : ''; ?>">Chờ xác nhận</a>
                <a href="?status=processing"
                    class="shopee-tab-item <?php echo $current_status == 'processing' ? 'active' : ''; ?>">Đang xử
                    lý</a>
                <a href="?status=shipped"
                    class="shopee-tab-item <?php echo $current_status == 'shipped' ? 'active' : ''; ?>">Đang giao</a>
                <a href="?status=completed"
                    class="shopee-tab-item <?php echo $current_status == 'completed' ? 'active' : ''; ?>">Hoàn thành</a>
                <a href="?status=cancelled"
                    class="shopee-tab-item <?php echo $current_status == 'cancelled' ? 'active' : ''; ?>">Đã hủy</a>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-state" style="background: #fff; padding: 50px 0; border-radius: 4px;">
                    <i class="fas fa-file-invoice"
                        style="font-size: 50px; color: #ccc; margin-bottom: 15px; display: block;"></i>
                    <p style="color: #555;">Chưa có đơn hàng</p>
                </div>
            <?php else: ?>

                <?php foreach ($orders as $order): ?>
                    <?php
                    // Cấu hình chữ và màu cho từng trạng thái
                    $status_map = [
                        'pending' => ['text' => 'CHỜ XÁC NHẬN', 'class' => 'status-pending'],
                        'processing' => ['text' => 'ĐANG XỬ LÝ', 'class' => 'status-processing'],
                        'shipped' => ['text' => 'ĐANG GIAO', 'class' => 'status-shipped'],
                        'completed' => ['text' => 'HOÀN THÀNH', 'class' => 'status-completed'],
                        'cancelled' => ['text' => 'ĐÃ HỦY', 'class' => 'status-cancelled']
                    ];
                    $status = $status_map[$order['status']] ?? ['text' => 'KHÔNG RÕ', 'class' => ''];
                    ?>

                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">Mã Đơn Hàng:
                                #FD<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></span>
                            <span class="order-status <?php echo $status['class']; ?>">
                                <?php if ($order['status'] == 'completed')
                                    echo '<i class="fas fa-truck" style="color: #26aa99; margin-right: 5px;"></i> Giao hàng thành công | '; ?>
                                <?php echo $status['text']; ?>
                            </span>
                        </div>

                        <div class="order-body">
                            <div class="order-info">
                                <p><i class="far fa-clock"></i> Ngày đặt:
                                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> Giao đến:
                                    <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div style="color: #555; display: flex; align-items: center;">
                                Thành tiền: <span class="order-total-price"
                                    style="margin-left: 10px;">₫<?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <?php if ($order['status'] == 'completed'): ?>
                                    <a href="#" class="btn-action">Mua Lại</a>
                                <?php endif; ?>
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-outline">Xem Chi Tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>