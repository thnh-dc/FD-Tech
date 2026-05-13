<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Kết nối Database chuẩn PDO
require_once '../config/database.php';

// Kiểm tra nếu chưa đăng nhập thì đá về trang login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. LẤY THÔNG TIN USER TỪ DATABASE ---
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy xuất dữ liệu người dùng!");
}

// --- XỬ LÝ HỦY ĐƠN HÀNG ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel_order') {
    $order_id_to_cancel = $_POST['order_id'];

    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$order_id_to_cancel, $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['flash_msg'] = 'Đã hủy đơn hàng thành công!';
        } else {
            $_SESSION['flash_msg'] = 'Không thể hủy đơn hàng này!';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_msg'] = 'Lỗi hệ thống khi hủy đơn hàng.';
    }

    $_SESSION['active_tab'] = 'orders';
    header("Location: profile.php");
    exit();
}

// --- LẤY DANH SÁCH ĐƠN HÀNG CỦA USER ---
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}

// --- 2. XỬ LÝ LƯU DỮ LIỆU HỒ SƠ & MẬT KHẨU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_action'])) {

    // NẾU LÀ FORM CẬP NHẬT HỒ SƠ
    if ($_POST['form_action'] == 'update_profile') {
        $full_name = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        // Bắt thêm dữ liệu Giới tính, Ngày sinh, Địa chỉ từ Form
        $gender = $_POST['gender'];
        $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
        $address = trim($_POST['address']);

        $avatar_name = $user['avatar'] ?? '';
        $upload_error = '';

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['avatar']['name'];
            $filesize = $_FILES['avatar']['size'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $upload_error = 'Chỉ chấp nhận file ảnh định dạng JPG, JPEG hoặc PNG!';
            } elseif ($filesize > 10048576) {
                $upload_error = 'Dung lượng ảnh vượt quá 10MB!';
            } else {
                $new_filename = $user_id . "_" . time() . "." . $ext;
                $upload_dir = "../upload/avatar_user/";

                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                    $avatar_name = $new_filename;
                    if (!empty($user['avatar']) && file_exists($upload_dir . $user['avatar'])) {
                        unlink($upload_dir . $user['avatar']);
                    }
                } else {
                    $upload_error = 'Lỗi hệ thống không thể lưu file ảnh.';
                }
            }
        }

        if ($upload_error) {
            $_SESSION['flash_msg'] = $upload_error;
        } else {
            try {
                // Cập nhật câu lệnh SQL để lưu toàn bộ thông tin
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, date_of_birth = ?, address = ?, avatar = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $phone, $gender, $dob, $address, $avatar_name, $user_id]);

                $_SESSION['full_name'] = $full_name;
                $_SESSION['avatar'] = $avatar_name;
                $_SESSION['flash_msg'] = 'Cập nhật hồ sơ thành công!';
            } catch (PDOException $e) {
                $_SESSION['flash_msg'] = 'Lỗi cập nhật Database!';
            }
        }
        $_SESSION['active_tab'] = 'profile';
        header("Location: profile.php");
        exit();
    }

    // NẾU LÀ FORM ĐỔI MẬT KHẨU
    if ($_POST['form_action'] == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $_SESSION['flash_msg'] = 'Mật khẩu xác nhận không khớp!';
        } else {
            try {
                if (password_verify($current_password, $user['password'])) {
                    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hashed, $user_id]);
                    $_SESSION['flash_msg'] = 'Đổi mật khẩu thành công!';
                } else {
                    $_SESSION['flash_msg'] = 'Mật khẩu hiện tại không đúng!';
                }
            } catch (PDOException $e) {
                $_SESSION['flash_msg'] = 'Lỗi hệ thống khi đổi mật khẩu.';
            }
        }
        $_SESSION['active_tab'] = 'password';
        header("Location: profile.php");
        exit();
    }
}

// Xác định tab nào đang active sau khi reload
$active_tab = $_SESSION['active_tab'] ?? 'profile';
unset($_SESSION['active_tab']);

// --- 3. XÁC ĐỊNH ĐƯỜNG DẪN AVATAR ĐỂ HIỂN THỊ ---
$has_custom_avatar = !empty($user['avatar']) && file_exists("../upload/avatar_user/" . $user['avatar']);
if ($has_custom_avatar) {
    $avatar_url = "../upload/avatar_user/" . $user['avatar'];
} else {
    $initials = mb_strtoupper(mb_substr($user['username'], 0, 2, 'UTF-8'), 'UTF-8');
    $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=random&color=fff&size=128";
}

// Hàm phụ: Dịch trạng thái đơn hàng sang tiếng Việt
function translateOrderStatus($status)
{
    switch ($status) {
        case 'pending':
            return '<span style="color: #ff9800;">Chờ xác nhận</span>';
        case 'processing':
            return '<span style="color: #2196f3;">Đang xử lý</span>';
        case 'shipping':
            return '<span style="color: #9c27b0;">Đang giao hàng</span>';
        case 'completed':
            return '<span style="color: #4caf50;">Đã giao</span>';
        case 'cancelled':
            return '<span style="color: #f44336;">Đã hủy</span>';
        default:
            return '<span>' . htmlspecialchars($status) . '</span>';
    }
}
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="../assets/css/style_profile.css">
<style>
    .order-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .order-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
    }

    .order-card-header {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .order-id {
        font-weight: bold;
        color: #333;
    }

    .order-status {
        font-weight: 600;
        font-size: 14px;
    }

    .order-card-body {
        margin-bottom: 15px;
        color: #555;
    }

    .order-total {
        font-size: 18px;
        color: #ee4d2d;
        font-weight: bold;
        margin-top: 5px;
    }

    .order-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px dashed #eee;
        padding-top: 15px;
    }

    .btn-cancel {
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
        background: #fff;
        color: #ff4d4f;
        border: 1px solid #ff4d4f;
    }

    .btn-cancel:hover {
        background: #ff4d4f;
        color: #fff;
    }
</style>

<div class="profile-wrapper">
    <div class="profile-container">

        <div class="profile-sidebar">
            <div class="user-brief">
                <img src="<?php echo $avatar_url; ?>" id="sidebar-avatar" alt="Avatar">
                <div>
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p style="font-size: 12px; color: #777;"><i class="fas fa-pencil-alt"></i> Sửa hồ sơ</p>
                </div>
            </div>
            <ul class="profile-menu">
                <li><a onclick="switchTab('profile', this)"
                        class="menu-link <?php echo $active_tab == 'profile' ? 'active' : ''; ?>"><i
                            class="far fa-user"></i> Tài khoản của tôi</a></li>
                <li><a onclick="switchTab('password', this)"
                        class="menu-link <?php echo $active_tab == 'password' ? 'active' : ''; ?>"><i
                            class="fas fa-lock"></i> Đổi mật khẩu</a></li>
                <li><a onclick="switchTab('orders', this)"
                        class="menu-link <?php echo $active_tab == 'orders' ? 'active' : ''; ?>"><i
                            class="fas fa-clipboard-list"></i> Đơn mua</a></li>
                <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                    <a href="../auth/logout.php" style="color: #DB4437;"><i class="fas fa-sign-out-alt"></i> Đăng
                        xuất</a>
                </li>
            </ul>
        </div>

        <div class="profile-content">

            <div id="tab-profile" class="tab-content <?php echo $active_tab == 'profile' ? 'active' : ''; ?>">
                <div class="profile-header">
                    <h2>Hồ sơ của tôi</h2>
                    <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                </div>

                <form action="" method="POST" enctype="multipart/form-data" class="profile-body-split">
                    <input type="hidden" name="form_action" value="update_profile">

                    <div class="profile-form-area">
                        <div class="profile-form">
                            <div class="form-group"><label>Tên đăng nhập</label>
                                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly
                                    style="background: #f9f9f9;">
                            </div>
                            <div class="form-group"><label>Họ và Tên</label>
                                <input type="text" name="fullname"
                                    value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                                    placeholder="Nhập họ và tên">
                            </div>
                            <div class="form-group"><label>Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                                    required>
                            </div>
                            <div class="form-group"><label>Số điện thoại</label>
                                <input type="text" name="phone"
                                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                    placeholder="Nhập số điện thoại">
                            </div>
                            <div class="form-group">
                                <label>Giới tính</label>
                                <select name="gender">
                                    <option value="male" <?php echo ($user['gender'] == 'male') ? 'selected' : ''; ?>>Nam
                                    </option>
                                    <option value="female" <?php echo ($user['gender'] == 'female') ? 'selected' : ''; ?>>
                                        Nữ</option>
                                    <option value="other" <?php echo ($user['gender'] == 'other') ? 'selected' : ''; ?>>
                                        Khác</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Ngày sinh</label>
                                <input type="date" name="dob"
                                    value="<?php echo !empty($user['date_of_birth']) ? $user['date_of_birth'] : ''; ?>">
                            </div>
                            <div class="form-group"><label>Địa chỉ</label>
                                <input type="text" name="address"
                                    value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                                    placeholder="Nhập địa chỉ nhận hàng">
                            </div>

                            <div class="form-group"><button type="submit" class="btn-save">Lưu Thay Đổi</button></div>
                        </div>
                    </div>

                    <div class="profile-avatar-area">
                        <div class="avatar-preview-box">
                            <img src="<?php echo $avatar_url; ?>" id="image-preview" alt="Avatar">
                        </div>
                        <input type="file" id="file-upload" name="avatar" accept=".jpg, .jpeg, .png"
                            style="display: none;" onchange="previewImage(event)">
                        <button type="button" class="btn-upload"
                            onclick="document.getElementById('file-upload').click()">Chọn Ảnh</button>
                        <div class="avatar-note">Dung lượng file tối đa 10 MB<br>Định dạng: .JPEG, .PNG</div>
                    </div>
                </form>
            </div>

            <div id="tab-password" class="tab-content <?php echo $active_tab == 'password' ? 'active' : ''; ?>">
                <div class="profile-header">
                    <h2>Đổi Mật Khẩu</h2>
                    <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
                </div>
                <form action="" method="POST" class="pw-form">
                    <input type="hidden" name="form_action" value="change_password">
                    <div class="form-group"><label>Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group"><label>Mật khẩu mới</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group"><label>Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn-save">Xác Nhận Đổi</button>
                </form>
            </div>

            <div id="tab-orders" class="tab-content <?php echo $active_tab == 'orders' ? 'active' : ''; ?>">
                <div class="profile-header">
                    <h2>Đơn hàng của tôi</h2>
                    <p>Quản lý và theo dõi các đơn hàng bạn đã đặt</p>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-invoice"></i>
                        <p>Chưa có đơn hàng nào</p>
                    </div>
                <?php else: ?>
                    <div class="order-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-card-header">
                                    <span class="order-id">Mã đơn:
                                        #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                    <span class="order-status"><?php echo translateOrderStatus($order['status']); ?></span>
                                </div>
                                <div class="order-card-body">
                                    <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                    <div class="order-total">
                                        Tổng tiền: <?php echo number_format($order['total_amount'] ?? 0, 0, ',', '.'); ?>đ
                                    </div>
                                </div>
                                <div class="order-actions">
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <form action="" method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');"
                                            style="margin:0;">
                                            <input type="hidden" name="action" value="cancel_order">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" class="btn-cancel">Hủy đơn</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
    // --- THÔNG BÁO CƠ BẢN BẰNG ALERT ---
    <?php if (isset($_SESSION['flash_msg'])): ?>
        alert('<?php echo $_SESSION['flash_msg']; ?>');
        <?php
        unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>

    // Các hàm phụ trợ
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById('image-preview');
            var sidebarOutput = document.getElementById('sidebar-avatar');
            output.src = reader.result;
            sidebarOutput.src = reader.result;
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function switchTab(tabId, element) {
        var contents = document.getElementsByClassName('tab-content');
        for (var i = 0; i < contents.length; i++) {
            contents[i].classList.remove('active');
        }
        document.getElementById('tab-' + tabId).classList.add('active');

        var menus = document.getElementsByClassName('menu-link');
        for (var i = 0; i < menus.length; i++) {
            menus[i].classList.remove('active');
        }
        if (element) element.classList.add('active');
    }
</script>

<?php include '../includes/footer.php'; ?>