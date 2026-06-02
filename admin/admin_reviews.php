<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
        $id = (int)$_POST['review_id'];
        $status = $_POST['new_status'];
        $stmt = $pdo->prepare("UPDATE product_reviews SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        header("Location: admin_reviews.php");
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] === 'reply') {
        $id = (int)$_POST['review_id'];
        $reply = trim($_POST['admin_reply']);
        $stmt = $pdo->prepare("UPDATE product_reviews SET admin_reply = ? WHERE id = ?");
        $stmt->execute([$reply, $id]);
        header("Location: admin_reviews.php");
        exit;
    }
}

$whereClause = "WHERE 1=1";
$params = [];

$filter_product = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$filter_rating = isset($_GET['rating']) ? $_GET['rating'] : '';

if ($filter_product > 0) {
    $whereClause .= " AND r.product_id = ?";
    $params[] = $filter_product;
}

if ($filter_rating === 'low') {
    $whereClause .= " AND r.rating <= 3";
} elseif (in_array($filter_rating, ['1','2','3','4','5'])) {
    $whereClause .= " AND r.rating = ?";
    $params[] = $filter_rating;
}

$stmt = $pdo->prepare("
    SELECT r.*, u.username as user_name, p.name as product_name 
    FROM product_reviews r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN products p ON r.product_id = p.id
    $whereClause
    ORDER BY r.id DESC
");
$stmt->execute($params);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtProd = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
$products = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đánh giá - FD Tech</title>
    
    <!-- Thêm thư viện jQuery và Select2 để hỗ trợ ô tìm kiếm -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: top; }
        th { background: #1a365d; color: #fff; }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        select, button, input { padding: 8px 12px; border-radius: 4px; border: 1px solid #ccc; }
        button { background: #1a365d; color: white; cursor: pointer; border: none; font-weight: bold; }
        .btn-hide { background: #dc2626; padding: 6px 10px; }
        .btn-show { background: #10b981; padding: 6px 10px; }
        .reply-box { margin-top: 10px; background: #f8f9fa; padding: 10px; border-left: 3px solid #1a365d; font-size: 0.9em; }
        .reply-form { display: flex; flex-direction: column; gap: 8px; margin-top: 15px; }
        textarea { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; resize: vertical; box-sizing: border-box; }
        
        /* Chỉnh lại chiều cao của Select2 cho bằng với các input khác */
        .select2-container .select2-selection--single {
            height: 35px;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 33px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quản lý đánh giá sản phẩm</h2>
        
        <form method="GET" class="filter-bar">
            <!-- Đã thêm class searchable-select vào đây -->
            <select name="product_id" class="searchable-select" style="min-width: 250px;">
                <option value="">-- Tất cả sản phẩm --</option>
                <?php foreach($products as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filter_product == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="rating">
                <option value="">-- Tất cả mức sao --</option>
                <option value="low" <?= $filter_rating === 'low' ? 'selected' : '' ?>>Đánh giá thấp (1-3 Sao)</option>
                <option value="5" <?= $filter_rating === '5' ? 'selected' : '' ?>>5 Sao</option>
                <option value="4" <?= $filter_rating === '4' ? 'selected' : '' ?>>4 Sao</option>
                <option value="3" <?= $filter_rating === '3' ? 'selected' : '' ?>>3 Sao</option>
                <option value="2" <?= $filter_rating === '2' ? 'selected' : '' ?>>2 Sao</option>
                <option value="1" <?= $filter_rating === '1' ? 'selected' : '' ?>>1 Sao</option>
            </select>
            
            <button type="submit">Lọc đánh giá</button>
            <a href="admin_reviews.php" style="padding: 8px 12px; background: #666; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">Xóa bộ lọc</a>
        </form>

        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="15%">Khách hàng</th>
                    <th width="20%">Sản phẩm</th>
                    <th width="35%">Nội dung đánh giá</th>
                    <th width="10%">Trạng thái</th>
                    <th width="15%">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reviews as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['user_name']) ?></td>
                        <td><?= htmlspecialchars($r['product_name']) ?></td>
                        <td>
                            <div style="color: #ffc107; margin-bottom: 5px;"><?= str_repeat('⭐', $r['rating']) ?></div>
                            <div><?= nl2br(htmlspecialchars($r['comment'])) ?></div>
                            
                            <?php if(!empty($r['admin_reply'])): ?>
                                <div class="reply-box">
                                    <strong>Admin đã trả lời:</strong><br>
                                    <?= nl2br(htmlspecialchars($r['admin_reply'])) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $r['status'] === 'show' ? '<span style="color:#10b981;font-weight:bold;">Đang hiện</span>' : '<span style="color:#dc2626;font-weight:bold;">Đã ẩn</span>' ?>
                        </td>
                        <td>
                            <form method="POST" style="margin-bottom: 10px;">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                <?php if($r['status'] === 'show'): ?>
                                    <input type="hidden" name="new_status" value="hidden">
                                    <button type="submit" class="btn-hide" style="width: 100%;">Ẩn hiển thị</button>
                                <?php else: ?>
                                    <input type="hidden" name="new_status" value="show">
                                    <button type="submit" class="btn-show" style="width: 100%;">Hiện đánh giá</button>
                                <?php endif; ?>
                            </form>

                            <form method="POST" class="reply-form">
                                <input type="hidden" name="action" value="reply">
                                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                <textarea name="admin_reply" rows="3" placeholder="Nhập câu trả lời..." required><?= htmlspecialchars($r['admin_reply'] ?? '') ?></textarea>
                                <button type="submit" style="background: #0284c7;">Lưu phản hồi</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($reviews)): ?>
                    <tr><td colspan="6" style="text-align: center;">Không tìm thấy đánh giá nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Script khởi tạo tính năng gõ để tìm kiếm cho khung chọn sản phẩm -->
    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                placeholder: "-- Gõ để tìm sản phẩm --",
                language: {
                    noResults: function() {
                        return "Không tìm thấy sản phẩm";
                    }
                }
            });
        });
    </script>
</body>
</html>