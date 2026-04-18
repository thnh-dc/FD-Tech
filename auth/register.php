<?php include '../includes/header.php'; ?>

<!-- Link CSS ngoài -->
<link rel="stylesheet" href="style_register.css">

<div class="login-wrapper">
    <div class="login-container">
        <div class="login-branding">
            <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo">
            <h1>FD TECH</h1>
            <p>Nền tảng mua sắm đồ chơi công nghệ<br>và phụ kiện chơi game hàng đầu</p>
        </div>

        <div class="login-form-box">
            <div class="form-header">
                <h2 class="form-title">Đăng ký</h2>
            </div>

            <form action="#" method="POST">
                <div class="input-group">
                    <input type="text" placeholder="Họ và tên" required>
                </div>

                <div class="input-group">
                    <input type="text" placeholder="Email hoặc Số điện thoại" required>
                </div>
                
                <div class="input-group">
                    <input type="password" placeholder="Mật khẩu" required>
                </div>

                <div class="input-group">
                    <input type="password" placeholder="Xác nhận mật khẩu" required>
                </div>

                <button type="submit" class="btn-login">ĐĂNG KÝ</button>

                <div class="terms-text">
                    Bằng việc đăng ký, bạn đồng ý với <a href="#">Điều khoản dịch vụ</a> & <a href="#">Chính sách bảo mật</a> của FD Tech
                </div>

                <div class="divider">HOẶC</div>

                <div class="social-login">
                    <button type="button" class="btn-social facebook">
                        <i class="fab fa-facebook"></i> Facebook
                    </button>
                    <button type="button" class="btn-social google">
                        <!-- SVG giữ nguyên -->
                        Google
                    </button>
                </div>

                <div class="register-link">
                    Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>