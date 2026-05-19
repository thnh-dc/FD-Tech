<?php
session_start();
include '../config/database.php';

// Biến lưu trạng thái thông báo hiển thị tại trang
$alert_msg = '';

// --- XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG KÝ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // 1. KIỂM TRA ĐỊNH DẠNG DỮ LIỆU
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]{2,19}$/', $username)) {
        $alert_msg = 'Tên đăng nhập phải từ 3-20 ký tự, không chứa ký tự đặc biệt và phải bắt đầu bằng chữ cái!';
    } elseif (strlen($password) < 6) {
        $alert_msg = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } elseif ($password !== $confirm_password) {
        $alert_msg = 'Mật khẩu xác nhận không khớp! Vui lòng nhập lại.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // 2. Kiểm tra xem Username đã bị ai đăng ký chưa
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->rowCount() > 0) {
                $alert_msg = 'Tên đăng nhập đã tồn tại!';
            } else {
                // 3. Lưu vào Database
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $email]);

                // Đăng ký thành công -> Gán session và nhảy sang Login
                $_SESSION['flash_msg'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $alert_msg = 'Lỗi hệ thống: Không thể đăng ký lúc này.';
        }
    }
}
?>

<?php
$page_title = 'Đăng Ký - FD Tech';
include '../includes/auth_header.php';
?>

<div class="form-header">
    <h2 class="form-title">Đăng ký</h2>
</div>

<form action="" method="POST">
    <div class="input-group">
        <input type="text" name="username" placeholder="Tên đăng nhập (Bắt đầu bằng chữ cái)" required
            pattern="^[a-zA-Z][a-zA-Z0-9]{2,19}$"
            title="Tên đăng nhập phải từ 3-20 ký tự, bắt đầu bằng chữ cái, không chứa khoảng trắng"
            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
    </div>

    <div class="input-group">
        <input type="email" name="email" placeholder="Email" required
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
    </div>

    <div class="input-group">
        <input type="password" name="password" placeholder="Mật khẩu (Tối thiểu 6 ký tự)" required minlength="6">
    </div>

    <div class="input-group">
        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required minlength="6">
    </div>

    <button type="submit" class="btn-login">ĐĂNG KÝ</button>

    <div class="register-link" style="margin-top: 25px;">
        Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a>
    </div>
</form>

</div>
</div>
</div> <?php include '../includes/footer.php'; ?>

<?php if (!empty($alert_msg)): ?>
    <script>
        setTimeout(function() {
            alert('<?php echo $alert_msg; ?>');
        }, 20);
    </script>
<?php endif; ?>

</body>

</html>