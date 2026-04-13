<?php include '../includes/header.php'; ?>

<style>
    /* Dùng chung style với trang Đăng nhập để đồng bộ */
    .login-wrapper {
        background-color: var(--primary); 
        min-height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .login-container {
        display: flex;
        width: 100%;
        max-width: 1040px;
        margin: 0 auto; /* Căn giữa toàn bộ cụm đăng nhập/đăng ký */
        align-items: center;
        justify-content: space-between; /* Đẩy Logo kịch trái, Form kịch phải */
    }

    .login-branding {
        width: 400px; /* Chốt cứng kích thước để khối này nép hẳn sang trái */
        text-align: center;
        color: white;
    }

    .login-branding img {
        width: 250px; 
        border-radius: 20px; 
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .login-branding h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .login-branding p {
        font-size: 16px;
        opacity: 0.9;
    }

    .login-form-box {
        width: 400px;
        background: white;
        border-radius: 8px;
        padding: 35px 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .form-header {
        margin-bottom: 25px;
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .input-group {
        margin-bottom: 15px;
        position: relative;
    }

    .input-group input {
        width: 100%;
        padding: 14px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .input-group input:focus {
        border-color: var(--secondary);
    }

    .btn-login {
        width: 100%;
        padding: 14px;
        background: var(--secondary); 
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        text-transform: uppercase;
        margin-bottom: 15px;
        margin-top: 10px;
        transition: background-color 0.3s;
    }

    .btn-login:hover {
        background: #1a9bb8;
    }

    .terms-text {
        font-size: 12px;
        color: #777;
        text-align: center;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .terms-text a {
        color: var(--secondary);
        text-decoration: none;
        font-weight: 600;
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #ccc;
        font-size: 12px;
        margin-bottom: 20px;
    }

    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #eee;
    }

    .divider::before { margin-right: 15px; }
    .divider::after { margin-left: 15px; }

    .social-login {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
    }

    .btn-social {
        flex: 1;
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #555;
        font-size: 14px;
        transition: 0.3s;
    }

    .btn-social:hover {
        background: #f9f9f9;
    }

    .btn-social.facebook i { color: #1877F2; font-size: 18px; }

    .register-link {
        text-align: center;
        font-size: 14px;
        color: rgba(0,0,0,0.54);
    }

    .register-link a {
        color: var(--secondary);
        font-weight: 600;
        text-decoration: none;
    }

    /* Reponsive cho điện thoại */
    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
            justify-content: center;
            gap: 40px;
        }
        .login-branding {
            display: none; 
        }
        .login-form-box {
            width: 100%;
            max-width: 400px;
        }
    }
</style>

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
                    Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>