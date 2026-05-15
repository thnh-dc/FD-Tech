<?php
session_start();
include '../config/database.php';

$step = $_SESSION['reset_step'] ?? 1;
$alert_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // BƯỚC 1: KIỂM TRA DATA
    if (isset($_POST['step_1'])) {
        $user_input = trim($_POST['user_input']);
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
            $stmt->execute([$user_input, $user_input]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['reset_step'] = 2;
                $_SESSION['reset_user_id'] = $user['id'];
                header("Location: forgot_password.php");
                exit();
            } else {
                $alert_msg = 'Không tìm thấy Email hoặc Số điện thoại này trong hệ thống!';
            }
        } catch (PDOException $e) {
            $alert_msg = 'Lỗi hệ thống: Không thể xử lý yêu cầu lúc này.';
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

                unset($_SESSION['reset_step'], $_SESSION['reset_user_id']);

                $_SESSION['flash_msg'] = 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.';
                header("Location: login.php");
                exit();
            } catch (PDOException $e) {
                $alert_msg = 'Lỗi cập nhật mật khẩu!';
            }
        } else {
            $alert_msg = 'Mật khẩu xác nhận không khớp!';
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'cancel') {
    unset($_SESSION['reset_step'], $_SESSION['reset_user_id']);
    header("Location: login.php");
    exit();
}
?>

<?php
$page_title = 'Quên mật khẩu - FD Tech';
include '../includes/auth_header.php';
?>

<div class="form-header">
    <h2 class="form-title"><?php echo ($step == 1) ? 'Lấy lại mật khẩu' : 'Mật khẩu mới'; ?></h2>
</div>

<?php if ($step == 1): ?>
    <form action="" method="POST">
        <input type="hidden" name="step_1" value="1">
        <div class="input-group">
            <input type="text" name="user_input" placeholder="Nhập Email hoặc Số điện thoại" required>
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
            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required minlength="6">
        </div>
        <button type="submit" class="btn-login" style="background-color: #1a9bb8; border-color: #1a9bb8;">Đổi mật khẩu</button>
    </form>
<?php endif; ?>

<div class="register-link" style="margin-top: 25px;">
    <a href="?action=cancel"><i class="fas fa-arrow-left"></i> Quay lại Đăng nhập</a>
</div>

</div>
</div>
</div> <?php include '../includes/footer.php'; ?>

<?php if (!empty($alert_msg)): ?>
    <script>
        alert('<?php echo $alert_msg; ?>');
    </script>
<?php endif; ?>

</body>

</html>