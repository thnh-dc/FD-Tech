<?php include '../includes/header.php'; ?>

<?php 
// Đoạn code này để kiểm tra xem bạn đang ở trang nào nhằm hiển thị màu sắc Menu cho đúng
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<style>
    .profile-wrapper {
        background-color: #f5f5fa; 
        padding: 40px 20px;
        min-height: 600px;
    }

    .profile-container {
        max-width: 1040px;
        margin: 0 auto;
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    /* --- CỘT TRÁI: MENU --- */
    .profile-sidebar {
        width: 250px;
        background: white;
        border-radius: 8px;
        padding: 20px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        flex-shrink: 0;
    }

    .user-brief {
        display: flex;
        align-items: center;
        padding: 0 20px 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 10px;
    }

    .user-brief img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        border: 2px solid var(--secondary, #1a9bb8);
        object-fit: cover;
    }

    .user-brief h3 {
        font-size: 16px;
        margin: 0 0 5px;
        color: var(--text-dark, #333);
        font-weight: 600;
    }

    .profile-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .profile-menu li a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #555;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
    }

    /* Class "active" sẽ giúp nút sáng lên khi bạn đang ở trang đó */
    .profile-menu li a:hover, 
    .profile-menu li a.active {
        background-color: #f0f8ff;
        color: var(--secondary, #1a9bb8);
        font-weight: 600;
    }

    .profile-menu li a i {
        width: 25px;
        font-size: 16px;
        color: #999;
    }

    .profile-menu li a.active i, .profile-menu li a:hover i {
        color: var(--secondary, #1a9bb8);
    }

    /* --- CỘT PHẢI: NỘI DUNG --- */
    .profile-content {
        flex: 1;
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .profile-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .profile-header h2 { font-size: 20px; margin: 0; color: #333; }
    .profile-header p { font-size: 14px; color: #666; margin-top: 5px; }

    .profile-form { display: flex; flex-wrap: wrap; gap: 20px; }
    .form-group { width: calc(50% - 10px); }
    .form-group.full-width { width: 100%; }
    .form-group label { display: block; font-size: 14px; color: #555; margin-bottom: 8px; font-weight: 500; }
    .form-group input, .form-group select {
        width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; outline: none;
    }

    .btn-save {
        padding: 12px 30px; background: var(--secondary, #1a9bb8); color: white; border: none;
        border-radius: 4px; font-weight: bold; cursor: pointer; text-transform: uppercase; transition: 0.3s;
    }

    .btn-save:hover { background: #147a91; }

    @media (max-width: 768px) {
        .profile-container { flex-direction: column; }
        .profile-sidebar { width: 100%; }
        .form-group { width: 100%; }
    }
</style>

<div class="profile-wrapper">
    <div class="profile-container">
        
        <div class="profile-sidebar">
            <div class="user-brief">
                <img src="../assets/images/default-avatar.png" alt="Avatar" onerror="this.src='https://via.placeholder.com/50'">
                <div>
                    <h3>Thanh Tịnh</h3>
                    <p style="font-size: 12px; color: #777;"><i class="fas fa-pencil-alt"></i> Sửa hồ sơ</p>
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
                    <input type="text" value="thanhtinh_123" readonly style="background: #f9f9f9;">
                </div>
                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" value="Thanh Tịnh" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="thanhtinh@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" value="0987654321" required>
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
                    <input type="date" value="2000-01-01">
                </div>
                <div class="form-group full-width">
                    <button type="submit" class="btn-save">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>