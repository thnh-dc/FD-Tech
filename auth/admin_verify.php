<?php
session_start();

// Kiểm tra xem có đi từ trang login sang không, tránh truy cập trực tiếp
if (!isset($_SESSION['pending_admin_login'])) {
    header("Location: login.php");
    exit();
}

// THÔNG TIN BẢO MẬT ADMIN CỨNG (Phải khớp với bước 1)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
define('ADMIN_EMAIL', 'admin@gmail.com');
define('ADMIN_PHONE', '0987654321');
define('ADMIN_CODE', '888888'); // Mã 6 số cứng

$step = $_SESSION['admin_step'] ?? 1;

// XỬ LÝ KHI SUBMIT FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // NẾU ĐANG Ở BƯỚC 1
    if (isset($_POST['verify_step_1'])) {
        if (
            $_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS &&
            $_POST['email'] === ADMIN_EMAIL && $_POST['phone'] === ADMIN_PHONE
        ) {

            $_SESSION['admin_step'] = 2; // Chuyển sang bước 2
            $_SESSION['flash_msg'] = 'Xác minh thành công! Nhập mã 6 số để tiếp tục.';
            $_SESSION['flash_type'] = 'success';
            header("Location: admin_verify.php");
            exit();
        } else {
            $_SESSION['flash_msg'] = 'Thông tin xác minh không chính xác!';
            $_SESSION['flash_type'] = 'error';
        }
    }

    // NẾU ĐANG Ở BƯỚC 2
    if (isset($_POST['verify_step_2'])) {
        if ($_POST['code'] === ADMIN_CODE) {
            // Xác minh hoàn tất -> Xóa các session nháp, cấp quyền đăng nhập admin
            unset($_SESSION['pending_admin_login']);
            unset($_SESSION['admin_step']);

            $_SESSION['admin_logged_in'] = true; // Cờ quan trọng để vào dashboard
            $_SESSION['flash_msg'] = 'Đăng nhập hệ thống Admin thành công!';
            $_SESSION['flash_type'] = 'success';

            header("Location: ../admin/admin_dashboard.php");
            exit();
        } else {
            $_SESSION['flash_msg'] = 'Mã xác minh (6 số) không hợp lệ!';
            $_SESSION['flash_type'] = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xác minh Quản trị viên</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .verify-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .verify-box h2 {
            text-align: center;
            color: #DB4437;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-verify {
            width: 100%;
            background: #DB4437;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-verify:hover {
            background: #c53929;
        }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #555;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="verify-box">
        <?php if ($step == 1): ?>
            <h2>BƯỚC 1: XÁC MINH DANH TÍNH</h2>
            <form method="POST">
                <input type="hidden" name="verify_step_1" value="1">
                <div class="form-group"><label>Tên đăng nhập Admin</label><input type="text" name="username" required></div>
                <div class="form-group"><label>Mật khẩu Admin</label><input type="password" name="password" required></div>
                <div class="form-group"><label>Gmail Admin</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Số điện thoại Admin</label><input type="text" name="phone" required></div>
                <button type="submit" class="btn-verify">TIẾP TỤC</button>
            </form>
        <?php elseif ($step == 2): ?>
            <h2>BƯỚC 2: MÃ BẢO MẬT</h2>
            <p style="text-align:center; color:#555; font-size: 14px; margin-bottom: 20px;">Vui lòng nhập mã PIN 6 chữ số
                dành riêng cho Admin.</p>
            <form method="POST">
                <input type="hidden" name="verify_step_2" value="1">
                <div class="form-group">
                    <input type="text" name="code" maxlength="6"
                        style="text-align: center; font-size: 24px; letter-spacing: 5px;" placeholder="------" required>
                </div>
                <button type="submit" class="btn-verify">XÁC NHẬN VÀO HỆ THỐNG</button>
            </form>
        <?php endif; ?>
        <a href="login.php" class="cancel-link">Hủy và quay lại Đăng nhập</a>
    </div>

    <?php if (isset($_SESSION['flash_msg'])): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION['flash_type']; ?>',
                title: 'Thông báo',
                text: '<?php echo $_SESSION['flash_msg']; ?>',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
        <?php
        unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_type']);
    endif;
    ?>

</body>

</html>