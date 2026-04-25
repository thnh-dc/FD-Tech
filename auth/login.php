<?php
session_start();
include '../config/database.php';

// --- XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT ĐĂNG NHẬP ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['username']); 
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login_input, $login_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: ../user/index.php");
            exit();
        } else {
            echo "<script>alert('Sai tên đăng nhập hoặc mật khẩu!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi hệ thống: Không thể đăng nhập lúc này.');</script>";
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - FD Tech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style_login.css">
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    
</head>
<body>

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
    <header class="auth-header">
        <div class="auth-header-container">
            <div class="auth-header-left">
                <a href="/FD-Tech/user/index.php" class="auth-logo">
                    <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo" onerror="this.style.display='none'">
                    <span class="auth-brand">FD<span>TECH</span></span>
                </a>
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
</body>
</html>