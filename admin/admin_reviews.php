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

$page_title = 'Đánh giá khách hàng';
$page_icon = 'fa-regular fa-comment';
$custom_css = '
    <link rel="stylesheet" href="/FD-Tech/assets/css/style_admin_review.css">';
include 'includes/header.php';
?>

<div class="container dashboard-container">
    <section class="section-block">
        <div class="review-container">
            <?php if (isset($_GET['message']) && $_GET['message'] === 'updated'): ?>
                <div class="reply-box">
                    <strong>Thông báo:</strong> Cập nhật trạng thái đánh giá thành công.
                </div>
            <?php elseif (isset($_GET['message']) && $_GET['message'] === 'replied'): ?>
                <div class="reply-box">
                    <strong>Thông báo:</strong> Lưu phản hồi đánh giá thành công.
                </div>
            <?php endif; ?>

            <form method="GET" class="filter-bar">
                <select name="product_id" class="searchable-select" style="min-width: 250px;">
                    <option value="">-- Tất cả sản phẩm --</option>
                    <?php foreach ($products as $p): ?>
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
                <a href="admin_reviews.php" class="clear-filter-btn">Xóa bộ lọc</a>
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
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $r): ?>
                            <tr>
                                <td><?= (int)$r['id'] ?></td>

                                <td>
                                    <?= htmlspecialchars($r['user_name'] ?? 'Không xác định') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($r['product_name'] ?? 'Sản phẩm không tồn tại') ?>
                                </td>

                                <td>
                                    <div style="color: #ffc107; margin-bottom: 5px;">
                                        <?= str_repeat('⭐', (int)$r['rating']) ?>
                                    </div>

                                    <div>
                                        <?= nl2br(htmlspecialchars($r['comment'] ?? '')) ?>
                                    </div>

                                    <?php if (!empty($r['admin_reply'])): ?>
                                        <div class="reply-box">
                                            <strong>Admin đã trả lời:</strong><br>
                                            <?= nl2br(htmlspecialchars($r['admin_reply'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($r['status'] === 'show'): ?>
                                        <span class="review-status-show">Đang hiện</span>
                                    <?php else: ?>
                                        <span class="review-status-hidden">Đã ẩn</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <form method="POST" style="margin-bottom: 10px;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="review_id" value="<?= (int)$r['id'] ?>">

                                        <?php if ($r['status'] === 'show'): ?>
                                            <input type="hidden" name="new_status" value="hidden">
                                            <button type="submit" class="btn-hide" style="width: 100%;">
                                                Ẩn hiển thị
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="new_status" value="show">
                                            <button type="submit" class="btn-show" style="width: 100%;">
                                                Hiện đánh giá
                                            </button>
                                        <?php endif; ?>
                                    </form>

                                    <form method="POST" class="reply-form">
                                        <input type="hidden" name="action" value="reply">
                                        <input type="hidden" name="review_id" value="<?= (int)$r['id'] ?>">

                                        <textarea 
                                            name="admin_reply" 
                                            rows="3" 
                                            placeholder="Nhập câu trả lời..."
                                        ><?= htmlspecialchars($r['admin_reply'] ?? '') ?></textarea>

                                        <button type="submit">
                                            Lưu phản hồi
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">
                                Không tìm thấy đánh giá nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

</main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="/FD-Tech/assets/js/script_dashboard.js"></script>
</body>
</html>