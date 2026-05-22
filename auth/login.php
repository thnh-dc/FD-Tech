<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$login_input, $login_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            if (isset($user['status']) && $user['status'] === 'blocked') {
                $_SESSION['noti_message'] = 'Tài khoản bạn bị khoá vui lòng dùng tài khoản khác!';
                $_SESSION['noti_type'] = 'error';
            } 
            elseif (isset($user['role']) && $user['role'] === 'admin') {
                $_SESSION['pending_admin_login'] = true;
                $_SESSION['admin_step'] = 1;

                $_SESSION['auth_admin_id'] = $user['id']; 
                
                header("Location: admin_verify.php");
                exit();
            } 

            else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['avatar'] = $user['avatar'];

                header("Location: ../user/index.php");
                exit();
            }

        } else {
            $_SESSION['noti_message'] = 'Sai tên đăng nhập hoặc mật khẩu!';
            $_SESSION['noti_type'] = 'error';
        }
    } catch (PDOException $e) {
        $_SESSION['noti_message'] = 'Lỗi hệ thống: Không thể đăng nhập lúc này.';
        $_SESSION['noti_type'] = 'error';
    }
}
?>

<?php
$page_title = 'Đăng nhập - FD Tech';
include '../includes/auth_header.php'; 
?>

<div class="form-header">
    <h2 class="form-title">Đăng nhập</h2>
</div>

<form action="" method="POST">
    <div class="input-group">
        <input type="text" name="username" placeholder="Email hoặc Tên đăng nhập" required
            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
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
<?php include '../includes/notification.php'; ?>

</body>
</html>