<?php
session_start();
include '../config/database.php';

// --- XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG NHẬP ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form (nhờ vào thuộc tính name="username" và name="password")
    $login_input = trim($_POST['username']); 
    $password = trim($_POST['password']);

    try {
        // Tìm user trong Database (cho phép đăng nhập bằng cả Username hoặc Email)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login_input, $login_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra xem có tài khoản không và mật khẩu có khớp không
        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công -> Lưu thông tin vào Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Chuyển hướng thẳng vào trang Profile
            header("Location: ../user/index.php");
            exit();
        } else {
            // Sai thông tin -> Báo lỗi
            echo "<script>alert('Sai tên đăng nhập hoặc mật khẩu!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi hệ thống: Không thể đăng nhập lúc này.');</script>";
        error_log($e->getMessage());
    }
}
?>

<?php include '../includes/header.php'; ?>

<link rel="stylesheet" href="../assets/css/style_login.css">
<div class="login-wrapper">
    <div class="login-container">
        <div class="login-branding">
            <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo">
            <h1>FD TECH</h1>
            <p>Nền tảng mua sắm đồ chơi công nghệ<br>và phụ kiện chơi game hàng đầu</p>
        </div>

        <div class="login-form-box">
            <div class="form-header">
                <h2 class="form-title">Đăng nhập</h2>
                <div class="qr-login" title="Đăng nhập bằng mã QR">
                    <i class="fas fa-qrcode"></i>
                </div>
            </div>
            <form action="" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Email hoặc Tên đăng nhập" required>
                </div>
                
                <div class="input-group">
                    <input type="password" name="password" placeholder="Mật khẩu" required>
                </div>

                <button type="submit" class="btn-login">Đăng nhập</button>

                <a href="#" class="forgot-pw">Quên mật khẩu</a>

                <div class="register-link" style="margin-top: 25px;">
                    Bạn mới biết đến FD Tech? <a href="register.php">Đăng ký</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>