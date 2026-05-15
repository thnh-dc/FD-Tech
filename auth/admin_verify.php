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
        }
    }
}
?>

<?php
$page_title = 'Xác minh Admin - FD Tech';
$is_admin = true; // Bật cờ này lên để file auth_header.php đổi chữ thành ADMIN
include '../includes/auth_header.php';
?>

<div class="form-header">
    <h2 class="form-title"><?php echo ($step == 1) ? 'Xác thực thông tin' : 'Mã PIN bảo mật'; ?></h2>
</div>

<?php if ($step == 1): ?>
    <form action="" method="POST">
        <input type="hidden" name="verify_step_1" value="1">
        <div class="input-group">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Mật khẩu" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="phone" placeholder="Mã xác thực" required>
        </div>
        <button type="submit" class="btn-login">Xác nhận</button>
    </form>
<?php else: ?>
    <form action="" method="POST">
        <input type="hidden" name="verify_step_2" value="1">
        <div class="input-group">
            <input type="text" name="code" placeholder="Nhập mã PIN" maxlength="6" required>
        </div>
        <button type="submit" class="btn-login" style="background-color: #1a9bb8;">Vào trang quản trị</button>
    </form>
<?php endif; ?>

<div class="register-link" style="margin-top: 25px;">
    <a href="login.php" style="color: #555;">Hủy xác minh</a>
</div>

</div>
</div>
</div> <?php include '../includes/footer.php'; ?>

<?php if (isset($_SESSION['flash_msg'])): ?>
    <script>
        alert('<?php echo $_SESSION['flash_msg']; ?>');
    </script>
    <?php unset($_SESSION['flash_msg']); ?>
<?php endif; ?>

</body>

</html>