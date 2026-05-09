<?php
session_start();
if (!isset($_SESSION['pending_admin_login'])) {
    header("Location: login.php");
    exit();
}

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
define('ADMIN_EMAIL', 'admin@gmail.com');
define('ADMIN_PHONE', '0987654321');
define('ADMIN_CODE', '888888');

$step = $_SESSION['admin_step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verify_step_1'])) {
        if (
            $_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS &&
            $_POST['email'] === ADMIN_EMAIL && $_POST['phone'] === ADMIN_PHONE
        ) {
            $_SESSION['admin_step'] = 2;
            header("Location: admin_verify.php");
            exit();
        } else {
            $_SESSION['flash_msg'] = 'Thông tin xác minh sai!';
            $_SESSION['flash_type'] = 'error';
        }
    }
    if (isset($_POST['verify_step_2'])) {
        if ($_POST['code'] === ADMIN_CODE) {
            $_SESSION['admin_logged_in'] = true;
            unset($_SESSION['pending_admin_login'], $_SESSION['admin_step']);
            header("Location: ../admin/admin_dashboard.php");
            exit();
        } else {
            $_SESSION['flash_msg'] = 'Mã PIN không đúng!';
            $_SESSION['flash_type'] = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác minh Admin - FD Tech</title>
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
                <span
                    style="font-size: 24px; margin-left: 15px; padding-left: 15px; border-left: 1px solid #ccc; color:#ee4d2d;">Hệ
                    thống quản trị</span>
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
                <h1>FD TECH ADMIN</h1>
                <p>Khu vực xác thực bảo mật 2 lớp<br>dành riêng cho Quản trị viên</p>
            </div>

            <div class="login-form-box">
                <div class="form-header">
                    <h2 class="form-title"><?php echo ($step == 1) ? 'Xác thực thông tin' : 'Mã PIN bảo mật'; ?></h2>
                    <div class="qr-login" title="Trợ giúp">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>

                <?php if ($step == 1): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="verify_step_1" value="1">
                        <div class="input-group"><input type="text" name="Tên đăng nhập" placeholder="Username Admin"
                                required></div>
                        <div class="input-group"><input type="password" name="Mật khẩu" placeholder="Password Admin"
                                required></div>
                        <div class="input-group"><input type="email" name="email" placeholder="Email Admin" required></div>
                        <div class="input-group"><input type="text" name="Số điện thoại" placeholder="SĐT Admin" required>
                        </div>
                        <button type="submit" class="btn-login">Xác nhận</button>
                    </form>
                <?php else: ?>
                    <form action="" method="POST">
                        <input type="hidden" name="verify_step_2" value="1">
                        <div class="input-group">
                            <input type="text" name="code" placeholder="Mã PIN (6 số)" maxlength="6"
                                style="text-align:center; letter-spacing: 5px; font-weight: bold; font-size: 18px;"
                                required>
                        </div>
                        <button type="submit" class="btn-login"
                            style="background-color: #26aa99; border-color: #26aa99;">Vào trang quản trị</button>
                    </form>
                <?php endif; ?>

                <div class="register-link" style="margin-top: 25px;">
                    <a href="login.php" style="color: #555;"><i class="fas fa-times"></i> Hủy xác minh</a>
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
                timer: 1500, showConfirmButton: false, toast: true, position: 'top-end'
            });
        </script>
        <?php unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_type']); ?>
    <?php endif; ?>

</body>

</html>