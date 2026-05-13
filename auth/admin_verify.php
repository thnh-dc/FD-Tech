<?php
session_start();
if (!isset($_SESSION['pending_admin_login'])) {
    header("Location: login.php");
    exit();
}

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
define('ADMIN_EMAIL', 'admin@gmail.com');
define('ADMIN_PHONE', '19001000');
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
            $_SESSION['user_id'] = 1;
            $_SESSION['role'] = 'admin';
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

<?php
$page_title = "Xác minh Admin - FD Tech";
$use_sweetalert = true;
include('../auth/includes/auth_header.php');
?>

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
                </div>

                <?php if ($step == 1): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="verify_step_1" value="1">
                        <div class="input-group"><input type="text" name="username" placeholder="Tên đăng nhập"
                                required></div>
                        <div class="input-group"><input type="password" name="password" placeholder="Mật khẩu" required>
                        </div>
                        <div class="input-group"><input type="email" name="email" placeholder="Email" required></div>
                        <div class="input-group"><input type="text" name="phone" placeholder="SĐT" required>
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