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

// --- XỬ LÝ LƯU DỮ LIỆU KHI NGƯỜI DÙNG BẤM NÚT SUBMIT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_action'])) {
    
    // 1. NẾU LÀ FORM CẬP NHẬT HỒ SƠ
    if ($_POST['form_action'] == 'update_profile') {
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        try {
            $stmt = $pdo->prepare("UPDATE users SET email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$email, $phone, $user_id]);
            echo "<script>alert('Cập nhật hồ sơ thành công!');</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại sau.');</script>";
            error_log($e->getMessage()); 
        }
    }

    // 2. NẾU LÀ FORM ĐỔI MẬT KHẨU
    if ($_POST['form_action'] == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
        } else {
            try {
                // Lấy mật khẩu cũ từ Database ra để so sánh
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user_db = $stmt->fetch(PDO::FETCH_ASSOC);

                // Kiểm tra xem mật khẩu hiện tại nhập vào có đúng không
                if (password_verify($current_password, $user_db['password'])) {
                    // Nếu đúng -> Mã hóa mật khẩu mới và lưu lại
                    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hashed, $user_id]);
                    
                    echo "<script>alert('Đổi mật khẩu thành công!');</script>";
                } else {
                    echo "<script>alert('Mật khẩu hiện tại không đúng!');</script>";
                }
            } catch (PDOException $e) {
                echo "<script>alert('Lỗi hệ thống khi đổi mật khẩu.');</script>";
                error_log($e->getMessage());
            }
        }
    }
}

// --- LẤY THÔNG TIN USER TỪ DATABASE ĐỂ HIỂN THỊ LÊN FORM ---
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy xuất dữ liệu!");
}
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="../assets/css/style_profile.css">

<div class="profile-wrapper">
    <div class="profile-container">
        
        <div class="profile-sidebar">
            <div class="user-brief">
                <img src="../assets/images/default-avatar.png" id="sidebar-avatar" alt="Avatar" onerror="this.src='https://via.placeholder.com/50'">
                <div>
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p style="font-size: 12px; color: #777;"><i class="fas fa-pencil-alt"></i> Sửa hồ sơ</p>
                </div>
            </div>
            <ul class="profile-menu">
                <li><a onclick="switchTab('profile', this)" class="menu-link active"><i class="far fa-user"></i> Tài khoản của tôi</a></li>
                <li><a onclick="switchTab('password', this)" class="menu-link"><i class="fas fa-lock"></i> Đổi mật khẩu</a></li>
                <li><a onclick="switchTab('orders', this)" class="menu-link"><i class="fas fa-clipboard-list"></i> Đơn mua</a></li>
                <li><a onclick="switchTab('notifications', this)" class="menu-link"><i class="far fa-bell"></i> Thông báo</a></li>
                <li><a onclick="switchTab('vouchers', this)" class="menu-link"><i class="fas fa-ticket-alt"></i> Kho Voucher</a></li>
                <li><a onclick="switchTab('favorites', this)" class="menu-link"><i class="far fa-heart"></i> Sản phẩm yêu thích</a></li>
                
                <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                    <a href="../auth/logout.php" style="color: #DB4437;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </div>
        <div class="profile-content">    
            <div id="tab-profile" class="tab-content active">
                <div class="profile-header">
                    <h2>Hồ sơ của tôi</h2>
                    <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                </div>
                <form action="" method="POST" enctype="multipart/form-data" class="profile-body-split">
                    <input type="hidden" name="form_action" value="update_profile">
                    
                    <div class="profile-form-area">
                        <div class="profile-form">
                            <div class="form-group"><label>Tên đăng nhập</label><input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly style="background: #f9f9f9;"></div>
                            <div class="form-group"><label>Họ và Tên</label><input type="text" name="fullname" value="" placeholder="Chưa hỗ trợ lưu DB"></div>
                            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                            <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Nhập số điện thoại"></div>
                            <div class="form-group">
                                <label>Giới tính</label>
                                <select name="gender">
                                    <option value="male">Nam</option>
                                    <option value="female" selected>Nữ</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Ngày sinh</label><input type="date" name="dob" value="2000-01-01"></div>
                            <div class="form-group"><button type="submit" class="btn-save">Lưu Thay Đổi</button></div>
                        </div>
                    </div>
                    <div class="profile-avatar-area">
                        <div class="avatar-preview-box"><img src="../assets/images/default-avatar.png" id="image-preview" alt="Avatar" onerror="this.src='https://via.placeholder.com/120'"></div>
                        <input type="file" id="file-upload" name="avatar" accept=".jpg, .jpeg, .png" style="display: none;" onchange="previewImage(event)">
                        <button type="button" class="btn-upload" onclick="document.getElementById('file-upload').click()">Chọn Ảnh</button>
                        <div class="avatar-note">Dung lượng file tối đa 1 MB<br>Định dạng: .JPEG, .PNG</div>
                    </div>
                </form>
            </div>

            <div id="tab-password" class="tab-content">
                <div class="profile-header">
                    <h2>Đổi Mật Khẩu</h2>
                    <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
                </div>
                <form action="" method="POST" class="pw-form">
                    <input type="hidden" name="form_action" value="change_password">
                    
                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn-save">Xác Nhận Đổi</button>
                </form>
            </div>

            <div id="tab-orders" class="tab-content">
                <div class="order-tabs">
                    <span style="color: var(--secondary, #1a9bb8); border-bottom: 2px solid var(--secondary, #1a9bb8);">Tất cả</span>
                    <span>Chờ thanh toán</span>
                    <span>Chờ giao hàng</span>
                    <span>Hoàn thành</span>
                    <span>Đã hủy</span>
                </div>
                <div class="empty-state">
                    <i class="fas fa-file-invoice"></i>
                    <p>Chưa có đơn hàng nào</p>
                </div>
            </div>

            <div id="tab-notifications" class="tab-content">
                <div class="profile-header"><h2>Thông báo của bạn</h2></div>
                <div class="empty-state">
                    <i class="far fa-bell-slash"></i>
                    <p>Bạn chưa có thông báo nào mới</p>
                </div>
            </div>

            <div id="tab-vouchers" class="tab-content">
                <div class="profile-header">
                    <h2>Kho Voucher</h2>
                    <p>Các mã giảm giá bạn đã lưu</p>
                </div>
                <div class="voucher-grid">
                    <div class="voucher-card">
                        <div class="vc-left">
                            <i class="fas fa-truck"></i><span style="font-size: 11px; font-weight: bold;">Freeship</span>
                        </div>
                        <div class="vc-right">
                            <h4>Giảm 15k phí vận chuyển</h4>
                            <p>Đơn tối thiểu 50k</p>
                            <p style="color: #e74c3c; margin-top: 5px;">HSD: 31/12/2026</p>
                        </div>
                    </div>
                    <div class="voucher-card">
                        <div class="vc-left" style="background: #e67e22;">
                            <i class="fas fa-ticket-alt"></i><span style="font-size: 11px; font-weight: bold;">Giảm Giá</span>
                        </div>
                        <div class="vc-right">
                            <h4>Giảm 10% Tối đa 100k</h4>
                            <p>Cho Phụ kiện Game</p>
                            <p style="color: #e74c3c; margin-top: 5px;">HSD: 15/11/2026</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-favorites" class="tab-content">
                <div class="profile-header"><h2>Sản phẩm yêu thích</h2></div>
                <div class="empty-state">
                    <i class="far fa-heart"></i>
                    <p>Bạn chưa thả tim sản phẩm nào</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('image-preview');
            var sidebarOutput = document.getElementById('sidebar-avatar');
            output.src = reader.result;
            sidebarOutput.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
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
        if(element) {
            element.classList.add('active');
        }
    }
</script>
<?php include '../includes/footer.php'; ?>