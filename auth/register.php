<?php
session_start();
include '../config/database.php';

// Các biến lưu trạng thái thông báo
$flash_msg = '';
$flash_type = '';
$redirect_url = ''; // Biến để chuyển hướng nếu đăng ký thành công

// --- XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG KÝ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // 1. Kiểm tra xem 2 mật khẩu có giống nhau không
    if ($password !== $confirm_password) {
        $flash_msg = 'Mật khẩu xác nhận không khớp! Vui lòng nhập lại.';
        $flash_type = 'error';
    } else {
        // 2. Mã hóa mật khẩu để bảo mật
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // 3. Kiểm tra xem Username hoặc Email đã bị ai đăng ký chưa
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $flash_msg = 'Tên đăng nhập hoặc Email này đã có người sử dụng!';
                $flash_type = 'error';
            } else {
                // 4. Nếu chưa có ai dùng -> Lưu vào Database
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $email]);
                
                // Đăng ký thành công -> Gán thông báo và link chuyển hướng
                $flash_msg = 'Đăng ký thành công! Đang chuyển đến Đăng nhập...';
                $flash_type = 'success';
                $redirect_url = 'login.php';
            }
        } catch (PDOException $e) {
            $flash_msg = 'Lỗi hệ thống: Không thể đăng ký lúc này.';
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
    <title>Đăng Ký - FD Tech</title>
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
                    <h2 class="form-title">Đăng ký</h2>
                </div>

                <form action="" method="POST">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Tên đăng nhập" required 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Mật khẩu" required>
                    </div>

                    <div class="input-group">
                        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                    </div>

                    <button type="submit" class="btn-login">ĐĂNG KÝ</button>

                    <div class="terms-text">
                        Bằng việc đăng ký, bạn đồng ý với <a href="#">Điều khoản dịch vụ</a> & <a href="#">Chính sách bảo mật</a> của FD Tech
                    </div>

                    <div class="register-link" style="margin-top: 25px;">
                        Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a>
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
            title: '<?php echo $flash_type == "success" ? "Tuyệt vời!" : "Lỗi!"; ?>',
            text: '<?php echo $flash_msg; ?>',
            timer: 2000, // Đợi 2 giây
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        }).then(function() {
            // Nếu có link chuyển hướng (đăng ký thành công), tự động nhảy trang
            <?php if (!empty($redirect_url)): ?>
                window.location.href = '<?php echo $redirect_url; ?>';
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>