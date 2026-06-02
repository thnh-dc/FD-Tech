<?php
header('Content-Type: application/json');
require_once '../config/database.php'; // Đảm bảo đúng đường dẫn file database của bạn

$action = $_GET['action'] ?? '';

// 1. API LẤY DANH SÁCH CHO TRANG CHỦ HOẶC ADMIN
if ($action == 'list') {
    $status_filter = isset($_GET['client']) ? "WHERE status = 1" : "";
    $stmt = $pdo->query("SELECT * FROM group_images $status_filter ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// 2. API THAY ĐỔI TRẠNG THÁI (ẨN / HIỆN)
if ($action == 'toggle_status') {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? 1;
    
    $stmt = $pdo->prepare("UPDATE group_images SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    echo json_encode(['success' => true]);
    exit;
}

// 3. API XÓA ẢNH
if ($action == 'delete') {
    $id = $_POST['id'] ?? 0;
    // Xóa file ảnh vật lý trên server trước
    $stmt_file = $pdo->prepare("SELECT image_url FROM group_images WHERE id = ?");
    $stmt_file->execute([$id]);
    $img = $stmt_file->fetch();
    if ($img && file_exists($img['image_url'])) {
        unlink($img['image_url']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM group_images WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
}

// 4. API UPLOAD / THÊM MỚI BANNER HOẶC POPUP
if ($action == 'add') {
    $title = $_POST['title'] ?? 'Không có tiêu đề';
    $type = $_POST['type'] ?? 'banner';
    $link_to = $_POST['link_to'] ?? '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../upload/adv/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $filename = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Lưu đường dẫn tương đối để từ trang chủ hoặc admin đều gọi được
            $db_url = "../upload/adv/" . $filename;
            
            $stmt = $pdo->prepare("INSERT INTO group_images (title, type, image_url, link_to, status) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$title, $type, $db_url, $link_to]);
            
            header("Location: manage_images.php?success=1");
            exit;
        }
    }
    header("Location: manage_images.php?error=1");
    exit;
}