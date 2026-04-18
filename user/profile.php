<?php 
$custom_css='
    <link rel="stylesheet" href="../assets/css/profile.css">';
include '../includes/header.php'; ?>
<?php 
// Đoạn code này để kiểm tra xem bạn đang ở trang nào nhằm hiển thị màu sắc Menu cho đúng
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<div class="profile-wrapper">
    <div class="profile-container">
        
        <div class="profile-sidebar">
            <div class="user-brief">
                <img src="../assets/images/default-avatar.png" alt="Avatar" onerror="this.src='https://via.placeholder.com/50'">
                <div>
                    <h3>Name user</h3>
                </div>
            </div>
            
            <ul class="profile-menu">
                <li><a href="profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>"><i class="far fa-user"></i> Tài khoản của tôi</a></li>
                <li><a href="orders.php" class="<?= $current_page == 'orders.php' ? 'active' : '' ?>"><i class="fas fa-clipboard-list"></i> Đơn mua</a></li>
                <li><a href="notifications.php" class="<?= $current_page == 'notifications.php' ? 'active' : '' ?>"><i class="far fa-bell"></i> Thông báo</a></li>
                <li><a href="vouchers.php" class="<?= $current_page == 'vouchers.php' ? 'active' : '' ?>"><i class="fas fa-ticket-alt"></i> Kho Voucher</a></li>
                <li><a href="favorites.php" class="<?= $current_page == 'favorites.php' ? 'active' : '' ?>"><i class="far fa-heart"></i> Sản phẩm yêu thích</a></li>
                
                <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                    <a href="logout.php" style="color: #DB4437;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </div>

        <div class="profile-content">
            <div class="profile-header">
                <h2>Hồ sơ của tôi</h2>
                <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
            </div>

            <form action="#" method="POST" class="profile-form">
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" value="" readonly style="background: #f9f9f9;">
                </div>
                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" value="" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="" required>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" value="" required>
                </div>
                <div class="form-group">
                    <label>Giới tính</label>
                    <select>
                        <option value="male">Nam</option>
                        <option value="female" selected>Nữ</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" value="">
                </div>
                <div class="form-group full-width">
                    <button type="submit" class="btn-save">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>

    </div>
</div>
<?php include '../includes/footer.php'; ?>