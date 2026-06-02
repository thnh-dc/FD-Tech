<?php
session_start();
require_once '../config/database.php';

// Cấu hình các biến Header cho hệ thống Admin của bạn
$page_title = 'Quản lí banner, popup';
$page_icon = 'fa-solid fa-images';

// Nhúng file CSS tùy chỉnh riêng của trang này vào thẻ head hệ thống thông qua biến $custom_css
$custom_css = '<link rel="stylesheet" href="../assets/css/manage_images.css">';

// Gọi Header chung (Header sẽ tự động nhúng sidebar.php và mở thẻ <main class="main-content">)
include 'includes/header.php'; 

// Lấy danh sách ảnh hiện tại để hiển thị
$stmt = $pdo->query("SELECT * FROM group_images ORDER BY id DESC");
$images = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <div class="main-layout">
        
        <div class="form-panel">
            <div class="panel-title">
                <i class="fa-solid fa-square-plus" style="color: #007bff; margin-right: 5px;"></i> Tạo Chiến Dịch Mới
            </div>
            <form action="api_images.php?action=add" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tiêu đề chiến dịch</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Siêu Sale Công Nghệ...">
                </div>
                
                <div class="form-group">
                    <label>Phân loại vùng hiển thị</label>
                    <select name="type" class="form-control">
                        <option value="banner">Banner Carousel lớn</option>
                        <option value="popup">Popup nổi bật giữa màn hình</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Đường dẫn liên kết khi Click</label>
                    <input type="url" name="link_to" class="form-control" placeholder="https://localhost/FD-Tech/...">
                </div>
                
                <div class="form-group">
                    <label>Chọn file hình ảnh</label>
                    <div class="file-upload-wrapper">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 20px; color: #6c757d; margin-bottom: 5px;"></i>
                        <p id="file-name-text" style="font-size: 13px; color: #6c757d;">Chọn tệp hoặc kéo thả vào đây</p>
                        <input type="file" name="image" required accept="image/*" onchange="displayFileName(this)">
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Tải Lên & Áp Dụng</button>
            </form>
        </div>

        <div class="list-container-card">
            <div class="panel-title">
                <i class="fa-solid fa-images" style="color: #6c757d; margin-right: 5px;"></i> Kho Dữ Liệu Banner / Popup
            </div>
            
            <div class="grid-cards">
                <?php if (empty($images)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-photo-film" style="font-size: 28px; color: #ccc; margin-bottom: 8px; display:block;"></i>
                        Chưa có dữ liệu banner hoặc popup nào.
                    </div>
                <?php else: ?>
                    <?php foreach($images as $img): ?>
                        <div class="image-card">
                            <div class="card-preview">
                                <span class="type-badge <?php echo $img['type'] == 'banner' ? 'badge-banner' : 'badge-popup'; ?>">
                                    <?php echo $img['type'] == 'banner' ? 'Banner' : 'Popup'; ?>
                                </span>
                                <img src="../<?php echo str_replace('../', '', $img['image_url']); ?>" alt="">
                            </div>
                            <div class="card-body">
                                <div class="card-title"><?php echo htmlspecialchars($img['title']); ?></div>
                                <div class="card-link">
                                    <?php if(!empty($img['link_to'])): ?>
                                        <a href="<?php echo $img['link_to']; ?>" target="_blank"><i class="fa-solid fa-link"></i> Link điều hướng</a>
                                    <?php else: ?>
                                        <span style="color:#aaa;">Không có link</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <div class="status-pill <?php echo $img['status'] == 1 ? 'active' : 'hidden'; ?>"
                                         onclick="toggleStatus(<?php echo $img['id']; ?>, <?php echo $img['status'] == 1 ? '0' : '1'; ?>)">
                                        <i class="fa-solid <?php echo $img['status'] == 1 ? 'fa-check' : 'fa-xmark'; ?>"></i> 
                                        <?php echo $img['status'] == 1 ? 'Hiển thị' : 'Tạm ẩn'; ?>
                                    </div>
                                    <button class="btn-delete" onclick="deleteImage(<?php echo $img['id']; ?>)">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script src="../assets/js/manage_images.js"></script>

</main> </div> </body>
</html>