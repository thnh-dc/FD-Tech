<?php 
    $custom_css='
        <link rel="stylesheet" href="../assets/css/style_auth.css">';
    include '../includes/header.php'; ?>
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

            <form action="#" method="POST">
                <div class="input-group">
                    <input type="text" placeholder="Email/Số điện thoại/Tên đăng nhập" required>
                </div>
                
                <div class="input-group">
                    <input type="password" placeholder="Mật khẩu" required>
                </div>

                <button type="submit" class="btn-login">Đăng nhập</button>

                <a href="#" class="forgot-pw">Quên mật khẩu</a>

                <div class="divider">HOẶC</div>

                <div class="social-login">
                    <button type="button" class="btn-social facebook">
                        <i class="fab fa-facebook"></i> Facebook
                    </button>
                    <button type="button" class="btn-social google">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="18px" height="18px">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.7 17.74 9.5 24 9.5z"></path>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                            <path fill="none" d="M0 0h48v48H0z"></path>
                        </svg> 
                        Google
                    </button>
                </div>

                <div class="register-link">
                    Bạn mới biết đến FD Tech? <a href="register.php">Đăng ký</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
