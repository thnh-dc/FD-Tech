<?php
session_start();
require_once '../config/database.php';

$alert_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // VALIDATION
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]{2,19}$/', $username)) {
        $alert_msg = 'Tên đăng nhập không hợp lệ!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alert_msg = 'Email không hợp lệ!';
    } elseif (strlen($password) < 6) {
        $alert_msg = 'Mật khẩu tối thiểu 6 ký tự!';
    } elseif ($password !== $confirm) {
        $alert_msg = 'Mật khẩu không khớp!';
    } else {

        try {
            // CHECK tồn tại (tối ưu hơn rowCount)
            $stmt = $pdo->prepare("SELECT 1 FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $email]);

            if ($stmt->fetch()) {
                $alert_msg = 'Username hoặc Email đã tồn tại!';
            } else {

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$username, $email, $hash]);

                $_SESSION['flash_msg'] = 'Đăng ký thành công!';
                header("Location: login.php");
                exit();
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $alert_msg = 'Lỗi hệ thống!';
        }
    }
}
?>

<?php
$page_title = "Đăng kí - FD Tech";
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
                    <h2 class="form-title">Đăng ký</h2>
                </div>

                <form action="" method="POST">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Tên đăng nhập (Bắt đầu bằng chữ cái)" required
                            pattern="^[a-zA-Z][a-zA-Z0-9]{2,19}$"
                            title="Tên đăng nhập phải từ 3-20 ký tự, bắt đầu bằng chữ cái, không chứa khoảng trắng hay ký tự đặc biệt"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="input-group">
                        <input type="password" name="password" placeholder="Mật khẩu (Tối thiểu 6 ký tự)" required
                            minlength="6">
                    </div>

                    <div class="input-group">
                        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required
                            minlength="6">
                    </div>

                    <button type="submit" class="btn-login">ĐĂNG KÝ</button>

                    <<div class="register-link">
                        Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a>
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