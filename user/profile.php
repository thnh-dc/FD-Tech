<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Kết nối Database chuẩn PDO
require_once '../config/database.php';

// Kiểm tra nếu chưa đăng nhập thì đá về trang login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. LẤY THÔNG TIN USER TỪ DATABASE ---
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy xuất dữ liệu!");
}

// --- 2. XỬ LÝ LƯU DỮ LIỆU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_action'])) {
    
    // NẾU LÀ FORM CẬP NHẬT HỒ SƠ
    if ($_POST['form_action'] == 'update_profile') {
        $full_name = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $avatar_name = $user['avatar'] ?? ''; 
        $upload_error = '';

        // Xử lý upload ảnh
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['avatar']['name'];
            $filesize = $_FILES['avatar']['size'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $upload_error = 'Chỉ chấp nhận file ảnh định dạng JPG, JPEG hoặc PNG!';
            } elseif ($filesize > 1048576) { 
                $upload_error = 'Dung lượng ảnh vượt quá 1MB!';
            } else {
                $new_filename = $user_id . "_" . time() . "." . $ext;
                $upload_dir = "../assets/uploads/avatars/";
                
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                    $avatar_name = $new_filename; 
                    if(!empty($user['avatar']) && file_exists($upload_dir . $user['avatar'])){
                        unlink($upload_dir . $user['avatar']);
                    }
                } else {
                    $upload_error = 'Lỗi hệ thống không thể lưu file ảnh.';
                }
            }
        }

        // CHUYỂN HƯỚNG MƯỢT MÀ BẰNG SESSION FLASH MESSAGE
        if ($upload_error) {
            $_SESSION['flash_msg'] = $upload_error;
            $_SESSION['flash_type'] = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, avatar = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $phone, $avatar_name, $user_id]);
                
                $_SESSION['full_name'] = $full_name;
                $_SESSION['flash_msg'] = 'Cập nhật hồ sơ thành công!';
                $_SESSION['flash_type'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['flash_msg'] = 'Lỗi cập nhật Database!';
                $_SESSION['flash_type'] = 'error';
            }
        }
        header("Location: profile.php");
        exit();
    }

    // NẾU LÀ FORM ĐỔI MẬT KHẨU
    if ($_POST['form_action'] == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $_SESSION['flash_msg'] = 'Mật khẩu xác nhận không khớp!';
            $_SESSION['flash_type'] = 'error';
        } else {
            try {
                if (password_verify($current_password, $user['password'])) {
                    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hashed, $user_id]);
                    
                    $_SESSION['flash_msg'] = 'Đổi mật khẩu thành công!';
                    $_SESSION['flash_type'] = 'success';
                } else {
                    $_SESSION['flash_msg'] = 'Mật khẩu hiện tại không đúng!';
                    $_SESSION['flash_type'] = 'error';
                }
            } catch (PDOException $e) {
                $_SESSION['flash_msg'] = 'Lỗi hệ thống khi đổi mật khẩu.';
                $_SESSION['flash_type'] = 'error';
            }
        }
        header("Location: profile.php");
        exit();
    }
}

// --- 3. XÁC ĐỊNH ĐƯỜNG DẪN AVATAR ĐỂ HIỂN THỊ ---
$has_custom_avatar = !empty($user['avatar']) && file_exists("../assets/uploads/avatars/" . $user['avatar']);
if ($has_custom_avatar) {
    $avatar_url = "../assets/uploads/avatars/" . $user['avatar'];
} else {
    $initials = mb_strtoupper(mb_substr($user['username'], 0, 2, 'UTF-8'), 'UTF-8');
    $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=random&color=fff&size=128";
}
?>

<?php include '../includes/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../assets/css/style_profile.css">

<div class="profile-wrapper">
    <div class="profile-container">
        
        <div class="profile-sidebar">
            <div class="user-brief">
                <img src="<?php echo $avatar_url; ?>" id="sidebar-avatar" alt="Avatar">
                <div>
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p style="font-size: 12px; color: #777;"><i class="fas fa-pencil-alt"></i> Sửa hồ sơ</p>
                </div>
            </div>
            <ul class="profile-menu">
                <li><a onclick="switchTab('profile', this)" class="menu-link active"><i class="far fa-user"></i> Tài khoản của tôi</a></li>
                <li><a onclick="switchTab('password', this)" class="menu-link"><i class="fas fa-lock"></i> Đổi mật khẩu</a></li>
                <li><a onclick="switchTab('orders', this)" class="menu-link"><i class="fas fa-clipboard-list"></i> Đơn mua</a></li>
                <li><a onclick="switchTab('notifications', this)" class="menu-link"><i class="far fa-bell"></i> Thông báo</a></li>
                <li><a onclick="switchTab('vouchers', this)" class="menu-link"><i class="fas fa-ticket-alt"></i> Kho Voucher</a></li>
                <li><a onclick="switchTab('favorites', this)" class="menu-link"><i class="far fa-heart"></i> Sản phẩm yêu thích</a></li>
                <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                    <a href="../auth/logout.php" style="color: #DB4437;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </div>
        
        <div class="profile-content">    
            <div id="tab-profile" class="tab-content active">
                <div class="profile-header">
                    <h2>Hồ sơ của tôi</h2>
                    <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                </div>
                
                <form action="" method="POST" enctype="multipart/form-data" class="profile-body-split">
                    <input type="hidden" name="form_action" value="update_profile">
                    
                    <div class="profile-form-area">
                        <div class="profile-form">
                            <div class="form-group"><label>Tên đăng nhập</label><input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly style="background: #f9f9f9;"></div>
                            <div class="form-group"><label>Họ và Tên</label><input type="text" name="fullname" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" placeholder="Nhập họ và tên"></div>
                            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                            <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Nhập số điện thoại"></div>
                            <div class="form-group">
                                <label>Giới tính</label>
                                <select name="gender">
                                    <option value="male">Nam</option>
                                    <option value="female" selected>Nữ</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Ngày sinh</label><input type="date" name="dob" value="2000-01-01"></div>
                            <div class="form-group"><button type="submit" class="btn-save">Lưu Thay Đổi</button></div>
                        </div>
                    </div>
                    
                    <div class="profile-avatar-area">
                        <div class="avatar-preview-box">
                            <img src="<?php echo $avatar_url; ?>" id="image-preview" alt="Avatar">
                        </div>
                        <input type="file" id="file-upload" name="avatar" accept=".jpg, .jpeg, .png" style="display: none;" onchange="previewImage(event)">
                        <button type="button" class="btn-upload" onclick="document.getElementById('file-upload').click()">Chọn Ảnh</button>
                        <div class="avatar-note">Dung lượng file tối đa 1 MB<br>Định dạng: .JPEG, .PNG</div>
                    </div>
                </form>
            </div>

            <div id="tab-password" class="tab-content">
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
            </div>

            <div id="tab-orders" class="tab-content"><div class="empty-state"><i class="fas fa-file-invoice"></i><p>Chưa có đơn hàng nào</p></div></div>
            <div id="tab-notifications" class="tab-content"><div class="empty-state"><i class="far fa-bell-slash"></i><p>Chưa có thông báo nào</p></div></div>
            <div id="tab-vouchers" class="tab-content"><div class="empty-state"><i class="fas fa-ticket-alt"></i><p>Kho Voucher trống</p></div></div>
            <div id="tab-favorites" class="tab-content"><div class="empty-state"><i class="far fa-heart"></i><p>Chưa có sản phẩm yêu thích</p></div></div>

        </div>
    </div>
</div>

<script>
    // --- XỬ LÝ HIỂN THỊ POPUP THÔNG BÁO ---
    <?php if (isset($_SESSION['flash_msg'])): ?>
        Swal.fire({
            icon: '<?php echo $_SESSION['flash_type']; ?>', // 'success' hoặc 'error'
            title: '<?php echo $_SESSION['flash_type'] == "success" ? "Thành công!" : "Lỗi!"; ?>',
            text: '<?php echo $_SESSION['flash_msg']; ?>',
            timer: 2500, // Tự động đóng sau 2.5 giây
            showConfirmButton: false,
            toast: true,
            position: 'top-end' // Hiện góc trên cùng bên phải giống Shopee
        });
    <?php 
        // Xóa thông báo sau khi hiện xong để refresh không bị hiện lại
        unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_type']);
    endif; 
    ?>

    // Các hàm phụ trợ
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('image-preview');
            var sidebarOutput = document.getElementById('sidebar-avatar');
            output.src = reader.result;
            sidebarOutput.src = reader.result;
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function switchTab(tabId, element) {
        var contents = document.getElementsByClassName('tab-content');
        for (var i = 0; i < contents.length; i++) {
            contents[i].classList.remove('active');
        }
        document.getElementById('tab-' + tabId).classList.add('active');

        var menus = document.getElementsByClassName('menu-link');
        for (var i = 0; i < menus.length; i++) {
            menus[i].classList.remove('active');
        }
        if(element) element.classList.add('active');
    }
</script>
<?php include '../includes/footer.php'; ?>