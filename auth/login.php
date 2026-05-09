<?php
session_start();
include '../config/database.php';

// --- THÔNG TIN ADMIN CỨNG ---
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// Các biến lưu thông báo
$flash_msg = '';
$flash_type = '';
$redirect_url = '';

// --- XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG NHẬP ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 1. KIỂM TRA XEM CÓ PHẢI LÀ ADMIN CỨNG KHÔNG
    if ($login_input === ADMIN_USER && $password === ADMIN_PASS) {
        // Cấp cờ session để bắt đầu bước xác minh 2 lớp
        $_SESSION['pending_admin_login'] = true;
        $_SESSION['admin_step'] = 1;

        $flash_msg = 'Vui lòng xác minh bảo mật 2 lớp!';
        $flash_type = 'info';
        $redirect_url = 'admin_verify.php'; // Chuyển sang trang xác minh admin
    }
    // 2. NẾU KHÔNG PHẢI ADMIN -> KIỂM TRA DATABASE (USER BÌNH THƯỜNG)
    else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login_input, $login_input]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $_SESSION['avatar'] = $user['avatar'];

                // --- THÊM THÔNG BÁO THÀNH CÔNG TẠI ĐÂY ---
                $flash_msg = 'Chào bạn, ' . $user['username'] . '!';
                $flash_type = 'success';
                $redirect_url = '../user/index.php'; // Đường dẫn trang chủ
            } else {
                $flash_msg = 'Sai tên đăng nhập hoặc mật khẩu!';
                $flash_type = 'error';
            }
        } catch (PDOException $e) {
            $flash_msg = 'Lỗi hệ thống: Không thể đăng nhập lúc này.';
            $flash_type = 'error';
            error_log($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - FD Tech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style_login.css">
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/footer.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <header class="auth-header">
        <div class="auth-header-container">
            <div class="auth-header-left">
                <a href="/FD-Tech/user/index.php" class="auth-logo">
                    <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo" onerror="this.style.display='none'">
                    <span class="auth-brand">FD<span>TECH</span></span>
                </a>
            </div>
            <div class="auth-header-right">
                <a href="#">Bạn cần giúp đỡ?</a>
            </div>
        </div>
    </header>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-branding">
                <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo" onerror="this.style.display='none'">
                <h1>FD TECH</h1>
                <p>Nền tảng mua sắm đồ chơi công nghệ<br>và phụ kiện chơi game hàng đầu</p>
            </div>

            <div class="login-form-box">
                <div class="form-header">
                    <h2 class="form-title">Đăng nhập</h2>
                    <div class="qr-login" title="Đăng nhập bằng mã QR">
                        <i class="fas fa-qrcode"></i>
                    </div>
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

                    <div class="register-link" style="margin-top: 25px;">
                        Bạn mới biết đến FD Tech? <a href="register.php">Đăng ký</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <?php if (!empty($flash_msg)): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $flash_type; ?>',
                // Cập nhật title thông minh hơn để bắt được trạng thái Info của Admin
                title: '<?php
                if ($flash_type == "success")
                    echo "Đăng nhập thành công!";
                elseif ($flash_type == "info")
                    echo "Xác minh Admin!";
                else
                    echo "Đăng nhập thất bại!";
                ?>',
                                text: '<?php echo $flash_msg; ?>',
                    timer: 1000, // Đợi 1.5 giây để khách kịp thấy thông báo
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                            }).then(function () {
                        // Nếu có link chuyển hướng (đăng nhập thành công HOẶC đang sang bước xác minh admin)
                        <?php if (!empty($redirect_url)): ?>
                            window.location.href = '<?php echo $redirect_url; ?>';
                        <?php endif; ?>
                    });
        </script>
    <?php endif; ?>

</body>

</html>