<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_action']) && $_POST['form_action'] == 'change_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['flash_msg'] = 'Mật khẩu xác nhận không khớp!';
    } else {
        try {
            if (password_verify($current_password, $user['password'])) {
                $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$new_hashed, $user_id]);
                $_SESSION['flash_msg'] = 'Đổi mật khẩu thành công!';
            } else {
                $_SESSION['flash_msg'] = 'Mật khẩu hiện tại không đúng!';
            }
        } catch (PDOException $e) {
            $_SESSION['flash_msg'] = 'Lỗi hệ thống khi đổi mật khẩu.';
        }
    }
    header("Location: profile.php?action=password");
    exit();
}
?>
<div class="profile-header">
    <h2>Đổi Mật Khẩu</h2>
    <p>Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
</div>
<form action="" method="POST" class="pw-form">
    <input type="hidden" name="form_action" value="change_password">
    <div class="form-group"><label>Mật khẩu hiện tại</label><input type="password" name="current_password" required></div>
    <div class="form-group"><label>Mật khẩu mới</label><input type="password" name="new_password" required></div>
    <div class="form-group"><label>Xác nhận mật khẩu</label><input type="password" name="confirm_password" required></div>
    <button type="submit" class="btn-save">Xác Nhận Đổi</button>
</form>