<?php
session_start();
include '../config/database.php';

$step = $_SESSION['reset_step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // BƯỚC 1: KIỂM TRA DATA
    if (isset($_POST['step_1'])) {
        $user_input = trim($_POST['user_input']);
        try {
            // Kiểm tra Email hoặc Username
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$user_input, $user_input]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['reset_step'] = 2;
                $_SESSION['reset_user_id'] = $user['id'];
                $_SESSION['flash_msg'] = 'Tìm thấy tài khoản ' . $user['username'] . '!';
                $_SESSION['flash_type'] = 'success';
                header("Location: forgot_password.php");
                exit();
            } else {
                $_SESSION['flash_msg'] = 'Không tìm thấy Email/Tên đăng nhập!';
                $_SESSION['flash_type'] = 'error';
            }
        } catch (PDOException $e) {
            $_SESSION['flash_msg'] = 'Lỗi hệ thống!';
            $_SESSION['flash_type'] = 'error';
        }
    }

    // BƯỚC 2: ĐỔI MẬT KHẨU
    if (isset($_POST['step_2'])) {
        $new_pass = $_POST['new_password'];
        $conf_pass = $_POST['confirm_password'];

        if ($new_pass === $conf_pass) {
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $user_id = $_SESSION['reset_user_id'];
            try {
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);

                unset($_SESSION['reset_step']);
                unset($_SESSION['reset_user_id']);
                $_SESSION['flash_msg'] = 'Đổi mật khẩu thành công!';
                $_SESSION['flash_type'] = 'success';
                header("Location: login.php");
                exit();
            } catch (PDOException $e) {
                $_SESSION['flash_msg'] = 'Lỗi cập nhật!';
                $_SESSION['flash_type'] = 'error';
            }
        } else {
            $_SESSION['flash_msg'] = 'Mật khẩu không khớp!';
            $_SESSION['flash_type'] = 'error';
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'cancel') {
    unset($_SESSION['reset_step']);
    unset($_SESSION['reset_user_id']);
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - FD Tech</title>
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
                <span style="font-size: 24px; margin-left: 15px; padding-left: 15px; border-left: 1px solid #ccc;">Khôi
                    phục mật khẩu</span>
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
                    <h2 class="form-title"><?php echo ($step == 1) ? 'Lấy lại mật khẩu' : 'Mật khẩu mới'; ?></h2>
                    <div class="qr-login" title="Trợ giúp">
                        <i class="fas fa-question-circle"></i>
                    </div>
                </div>

                <?php if ($step == 1): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="step_1" value="1">
                        <div class="input-group">
                            <input type="text" name="user_input" placeholder="Nhập Email hoặc Tên đăng nhập" required>
                        </div>
                        <button type="submit" class="btn-login">Kiểm tra thông tin</button>
                    </form>
                <?php else: ?>
                    <form action="" method="POST">
                        <input type="hidden" name="step_2" value="1">
                        <div class="input-group">
                            <input type="password" name="new_password" placeholder="Mật khẩu mới" required minlength="6">
                        </div>
                        <div class="input-group">
                            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required
                                minlength="6">
                        </div>
                        <button type="submit" class="btn-login"
                            style="background-color: #26aa99; border-color: #26aa99;">Đổi mật khẩu</button>
                    </form>
                <?php endif; ?>

                <div class="register-link" style="margin-top: 25px;">
                    <a href="?action=cancel"><i class="fas fa-arrow-left"></i> Quay lại Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <?php if (isset($_SESSION['flash_msg'])): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION['flash_type']; ?>',
                text: '<?php echo $_SESSION['flash_msg']; ?>',
                timer: 1000, showConfirmButton: false, toast: true, position: 'top-end'
            });
        </script>
        <?php unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_type']); ?>
    <?php endif; ?>

</body>

</html>