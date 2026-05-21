<?php
session_start();
require_once '../config/database.php';

$step = $_SESSION['reset_step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // KIỂM TRA THÔNG TIN & TẠO MÃ OTP GIẢ LẬP
    if (isset($_POST['step_1'])) {
        $user_input = trim($_POST['user_input']);
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR phone = ?) AND role != 'admin'");
            $stmt->execute([$user_input, $user_input]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Sinh mã OTP
                $fake_otp = rand(100000, 999999);
                
                $_SESSION['reset_step'] = 2; 
                $_SESSION['reset_user_id'] = $user['id'];
                $_SESSION['system_otp'] = $fake_otp; // Lưu OTP vào session để check

                $step = 2; 
                $_SESSION['noti_message'] = "Hệ thống đã gửi mã! MÃ XÁC THỰC CỦA BẠN LÀ: " . $fake_otp;
                $_SESSION['noti_type'] = 'success';
                
                header("Location: forgot_password.php");
                exit();
            } else {
                $_SESSION['noti_message'] = 'Thông tin xác minh sai!';
                $_SESSION['noti_type'] = 'error';
            }
        } catch (PDOException $e) {
            $_SESSION['noti_message'] = 'Lỗi hệ thống không xử lý được.';
            $_SESSION['noti_type'] = 'error';
        }
    }

    // KIỂM TRA MÃ OTP NGƯỜI DÙNG NHẬP
    if (isset($_POST['step_2'])) {
        $user_otp = trim($_POST['otp_input']);
        $system_otp = $_SESSION['system_otp'] ?? '';

        if (!empty($system_otp) && $user_otp == $system_otp) {
            $_SESSION['reset_step'] = 3;
            $step = 3;
            
            $_SESSION['noti_message'] = 'Mã xác thực chính xác! Vui lòng đặt mật khẩu mới.';
            $_SESSION['noti_type'] = 'success';
            
            header("Location: forgot_password.php");
            exit();
        } else {
            $_SESSION['noti_message'] = 'Mã xác thực không đúng! Vui lòng kiểm tra lại.';
            $_SESSION['noti_type'] = 'error';
        }
    }

    // TIẾN HÀNH ĐỔI MẬT KHẨU MỚI 
    if (isset($_POST['step_3'])) {
        $new_pass = $_POST['new_password'];
        $conf_pass = $_POST['confirm_password'];

        if (strlen($new_pass) < 6) {
            $_SESSION['noti_message'] = 'Mật khẩu mới phải từ 6 ký tự trở lên!';
            $_SESSION['noti_type'] = 'error';
        } elseif ($new_pass === $conf_pass) {
            $hashed_password = password_hash($new_pass, PASSWORD_BCRYPT);
            $user_id = $_SESSION['reset_user_id'] ?? 0;
            
            try {
                if ($user_id > 0) {
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? AND role != 'admin'");
                    $stmt->execute([$hashed_password, $user_id]);

                    unset($_SESSION['reset_step'], $_SESSION['reset_user_id'], $_SESSION['system_otp']);

                    $_SESSION['noti_message'] = 'Đổi mật khẩu thành công! Vui lòng đăng nhập.';
                    $_SESSION['noti_type'] = 'success';
                    header("Location: login.php");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['noti_message'] = 'Lỗi cập nhật dữ liệu mật khẩu!';
                $_SESSION['noti_type'] = 'error';
            }
        } else {
            $_SESSION['noti_message'] = 'Mật khẩu xác nhận không trùng khớp!';
            $_SESSION['noti_type'] = 'error';
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'cancel') {
    unset($_SESSION['reset_step'], $_SESSION['reset_user_id'], $_SESSION['system_otp']);
    header("Location: login.php");
    exit();
}
?>

<?php
$page_title = 'Quên mật khẩu - FD Tech';
include '../includes/auth_header.php';
?>

<div class="form-header">
    <h2 class="form-title">
        <?php 
            if ($step == 1) echo 'Lấy lại mật khẩu';
            elseif ($step == 2) echo 'Nhập mã xác thực';
            else echo 'Mật khẩu mới';
        ?>
    </h2>
</div>

<?php if ($step == 1): ?>
    <form action="" method="POST">
        <input type="hidden" name="step_1" value="1">
        <div class="input-group">
            <input type="text" name="user_input" placeholder="Nhập Email hoặc Số điện thoại" required>
        </div>
        <button type="submit" class="btn-login">Gửi mã xác thực</button>
    </form>

<?php elseif ($step == 2): ?>
    <form action="" method="POST">
        <input type="hidden" name="step_2" value="1">
        <p style="font-size: 13px; color: #555; margin-bottom: 15px; text-align: center;">
            Mã xác thực đã được gửi đến thiết bị của bạn. Vui lòng kiểm tra thông báo Toast để lấy mã.
        </p>
        <div class="input-group">
            <input type="text" name="otp_input" placeholder="Nhập mã xác thực gồm 6 số" maxlength="6" required autocomplete="off">
        </div>
        <button type="submit" class="btn-login" style="background-color: #1a9bb8; border-color: #1a9bb8;">Xác minh mã</button>
    </form>

<?php else: ?>
    <form action="" method="POST">
        <input type="hidden" name="step_3" value="1">
        <div class="input-group">
            <input type="password" name="new_password" placeholder="Mật khẩu mới (Tối thiểu 6 ký tự)" required minlength="6">
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required minlength="6">
        </div>
        <button type="submit" class="btn-login" style="background-color: #1a9bb8; border-color: #1a9bb8;">Đổi mật khẩu</button>
    </form>
<?php endif; ?>

<div class="register-link" style="margin-top: 25px;">
    <a href="?action=cancel"><i class="fas fa-arrow-left"></i> Hủy bỏ & Quay lại</a>
</div>

</div>
</div>
</div> 

<?php include '../includes/footer.php'; ?>
<?php include '../includes/notification.php'; ?>
</body>
</html>