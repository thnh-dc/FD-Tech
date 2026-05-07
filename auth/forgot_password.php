<?php
session_start();
include '../config/database.php';

// Xác định đang ở bước 1 (nhập email/sđt) hay bước 2 (đặt mật khẩu mới)
$step = $_SESSION['reset_step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- BƯỚC 1: KIỂM TRA DATA (EMAIL HOẶC SỐ ĐT) ---
    if (isset($_POST['step_1'])) {
        $contact_info = trim($_POST['contact_info']);
        
        try {
            // Kiểm tra trong database có Email hoặc Số điện thoại này không
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ? OR phone = ?");
            $stmt->execute([$contact_info, $contact_info]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Nếu CÓ trong data -> Lưu ID user lại và chuyển sang form đổi mật khẩu
                $_SESSION['reset_step'] = 2;
                $_SESSION['reset_user_id'] = $user['id'];
                
                $_SESSION['flash_msg'] = 'Tìm thấy tài khoản của ' . $user['username'] . '!';
                $_SESSION['flash_type'] = 'success';
            } else {
                // Nếu KHÔNG CÓ -> Báo lỗi
                $_SESSION['flash_msg'] = 'Không tìm thấy Email hoặc Số điện thoại trong hệ thống!';
                $_SESSION['flash_type'] = 'error';
            }
        } catch (PDOException $e) {
            $_SESSION['flash_msg'] = 'Lỗi hệ thống!';
            $_SESSION['flash_type'] = 'error';
        }
    }

    // --- BƯỚC 2: CẬP NHẬT MẬT KHẨU MỚI ---
    if (isset($_POST['step_2'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            // Mã hóa mật khẩu mới
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $user_id = $_SESSION['reset_user_id'];

            try {
                // Cập nhật mật khẩu mới vào Database
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);

                // Đổi xong -> Xóa các biến bước 1, 2
                unset($_SESSION['reset_step']);
                unset($_SESSION['reset_user_id']);

                // Gán thông báo thành công để mang về trang login
                $_SESSION['flash_msg'] = 'Đổi mật khẩu thành công! Bạn có thể đăng nhập ngay.';
                $_SESSION['flash_type'] = 'success';
                
                // QUAY LẠI TRANG LOGIN ĐỂ ĐĂNG NHẬP
                header("Location: login.php");
                exit();

            } catch (PDOException $e) {
                $_SESSION['flash_msg'] = 'Lỗi cập nhật mật khẩu!';
                $_SESSION['flash_type'] = 'error';
            }
        } else {
            $_SESSION['flash_msg'] = 'Mật khẩu xác nhận không khớp!';
            $_SESSION['flash_type'] = 'error';
        }
    }
}

// Xử lý nút quay lại Đăng nhập
if (isset($_GET['action']) && $_GET['action'] == 'cancel') {
    unset($_SESSION['reset_step']);
    unset($_SESSION['reset_user_id']);
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - FD Tech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .reset-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .reset-box h2 { color: #333; margin-bottom: 10px; font-size: 24px; }
        .reset-box p { color: #666; margin-bottom: 25px; font-size: 14px; line-height: 1.5; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px; transition: 0.3s; }
        .form-group input:focus { border-color: #ee4d2d; outline: none; }
        .btn-submit { width: 100%; background: #ee4d2d; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; transition: 0.3s; }
        .btn-submit:hover { background: #d73a1c; }
        .cancel-link { display: inline-block; margin-top: 20px; color: #555; text-decoration: none; font-size: 14px; }
        .cancel-link:hover { color: #ee4d2d; text-decoration: underline; }
    </style>
</head>
<body>

<div class="reset-box">
    <?php if ($step == 1): ?>
        <i class="fas fa-search-location" style="font-size: 40px; color: #ee4d2d; margin-bottom: 15px;"></i>
        <h2>Quên mật khẩu?</h2>
        <p>Vui lòng nhập Email hoặc Số điện thoại của bạn để kiểm tra hệ thống.</p>
        
        <form method="POST">
            <input type="hidden" name="step_1" value="1">
            <div class="form-group">
                <input type="text" name="contact_info" placeholder="Nhập Email hoặc Số điện thoại" required>
            </div>
            <button type="submit" class="btn-submit">KIỂM TRA DATA</button>
        </form>

    <?php elseif ($step == 2): ?>
        <i class="fas fa-key" style="font-size: 40px; color: #26aa99; margin-bottom: 15px;"></i>
        <h2>Tạo mật khẩu mới</h2>
        <p>Tài khoản hợp lệ! Vui lòng tạo mật khẩu mới cho tài khoản của bạn.</p>
        
        <form method="POST">
            <input type="hidden" name="step_2" value="1">
            <div class="form-group">
                <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required minlength="6">
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Xác nhận lại mật khẩu mới" required minlength="6">
            </div>
            <button type="submit" class="btn-submit" style="background: #26aa99;">ĐỔI MẬT KHẨU</button>
        </form>
    <?php endif; ?>
    
    <a href="?action=cancel" class="cancel-link"><i class="fas fa-arrow-left"></i> Quay lại Đăng nhập</a>
</div>

<?php if (isset($_SESSION['flash_msg'])): ?>
<script>
    Swal.fire({
        icon: '<?php echo $_SESSION['flash_type']; ?>',
        title: 'Thông báo',
        text: '<?php echo $_SESSION['flash_msg']; ?>',
        timer: 2500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
</script>
<?php 
    unset($_SESSION['flash_msg']);
    unset($_SESSION['flash_type']);
endif; 
?>

</body>
</html>