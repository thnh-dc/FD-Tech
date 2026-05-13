<?php
session_start();
include '../config/database.php';

// --- THÔNG TIN ADMIN CỨNG ---
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// Biến lưu thông báo hiển thị bằng alert
$alert_msg = '';

// KIỂM TRA XEM CÓ THÔNG BÁO TỪ TRANG KHÁC TRUYỀN SANG KHÔNG (Ví dụ: Đăng ký thành công)
if (isset($_SESSION['flash_msg'])) {
    $alert_msg = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']);
    unset($_SESSION['flash_type']); // Xóa luôn type nếu có để dọn dẹp session
}

// --- XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG NHẬP ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 1. KIỂM TRA XEM CÓ PHẢI LÀ ADMIN CỨNG KHÔNG
    if ($login_input === ADMIN_USER && $password === ADMIN_PASS) {
        // Cấp cờ session để bắt đầu bước xác minh 2 lớp
        $_SESSION['pending_admin_login'] = true;
        $_SESSION['admin_step'] = 1;

        // Chuyển hướng ngay lập tức sang trang xác minh
        header("Location: admin_verify.php");
        exit();
    }
    // 2. NẾU KHÔNG PHẢI ADMIN -> KIỂM TRA DATABASE (USER BÌNH THƯỜNG)
    else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login_input, $login_input]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công -> Lưu session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['avatar'] = $user['avatar'];

                // Chuyển hướng ngay lập tức về trang chủ
                header("Location: ../user/index.php");
                exit();
            } else {
                $alert_msg = 'Sai tên đăng nhập hoặc mật khẩu!';
            }
        } catch (PDOException $e) {
            $alert_msg = 'Lỗi hệ thống: Không thể đăng nhập lúc này.';
            error_log($e->getMessage());
        }
    }
}
?>

<?php
$page_title = "Đăng nhập - FD Tech";
include '../auth/includes/auth_header.php';
?>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-branding">
                <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo" onerror="this.style.display='none'">
                <h1>FD TECH</h1>
                <p>Nền tảng mua sắm đồ chơi công nghệ<br>và phụ kiện chơi game dành cho bạn</p>
            </div>

            <div class="login-form-box">
                <div class="form-header">
                    <h2 class="form-title">Đăng nhập</h2>
                </div>
                <form action="" method="POST">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Email hoặc Tên đăng nhập" required
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="input-group">
                        <input type="password" name="password" placeholder="Mật khẩu" required>
                    </div>

                    <button type="submit" class="btn-login">Đăng nhập</button>

                    <a href="forgot_password.php" class="forgot-pw">Quên mật khẩu</a>

                    <div class="register-link">
                        Bạn mới biết đến FD Tech? <a href="register.php">Đăng ký</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (!empty($alert_msg)): ?>
        <script>
            alert('<?php echo $alert_msg; ?>');
        </script>
    <?php endif; ?>

<?php include '../includes/footer.php'; ?>