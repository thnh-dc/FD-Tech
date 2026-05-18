<?php
// --- 1. XỬ LÝ HỦY ĐƠN HÀNG ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel_order') {
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$_POST['order_id'], $user_id]);
    } catch (PDOException $e) {
    }
    echo "<script>window.location.href='profile.php?action=orders';</script>";
    exit();
}

// --- 2. LẤY DANH SÁCH ĐƠN HÀNG ---
$current_status = $_GET['status'] ?? 'all';

try {
    if ($current_status == 'all') {
        $stmt = $pdo->prepare("
            SELECT o.*, u.phone AS user_phone 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT o.*, u.phone AS user_phone 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = ? AND o.status = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id, $current_status]);
    }
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}

// --- HÀM DỊCH TRẠNG THÁI (Màu xanh Shopee) ---
function translateOrderStatus($status)
{
    $labels = [
        'pending' => 'Chờ xác nhận',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đang giao hàng',
        'completed' => 'Đã giao',
        'cancelled' => 'Đã hủy'
    ];
    $text = $labels[$status] ?? $status;
    return '<span style="color: #26aa99; font-weight: 500;">' . htmlspecialchars($text) . '</span>';
}
?>

<link rel="stylesheet" href="../assets/css/style_profile_order.css">
<div class="profile-orders-header">
    <h2>Đơn hàng của tôi</h2>
    <p>Theo dõi tình trạng các đơn hàng đã đặt</p>
</div>

<div class="order-filter-tabs">
    <a href="profile.php?action=orders&status=all" class="<?= $current_status == 'all' ? 'active' : '' ?>">Tất cả</a>
    <a href="profile.php?action=orders&status=pending" class="<?= $current_status == 'pending' ? 'active' : '' ?>">Chờ xác nhận</a>
    <a href="profile.php?action=orders&status=processing" class="<?= $current_status == 'processing' ? 'active' : '' ?>">Đang xử lý</a>
    <a href="profile.php?action=orders&status=shipped" class="<?= $current_status == 'shipped' ? 'active' : '' ?>">Đang giao</a>
    <a href="profile.php?action=orders&status=completed" class="<?= $current_status == 'completed' ? 'active' : '' ?>">Đã giao</a>
    <a href="profile.php?action=orders&status=cancelled" class="<?= $current_status == 'cancelled' ? 'active' : '' ?>">Đã hủy</a>
</div>

<?php if (empty($orders)): ?>
    <div class="order-empty-state">
        <p>Bạn chưa có đơn hàng nào.</p>
    </div>
<?php else: ?>
    <div>
        <?php foreach ($orders as $order):
            $st_items = $pdo->prepare("
                SELECT oi.*, p.name AS original_name, p.image_url 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?
            ");
            $st_items->execute([$order['id']]);
            $items = $st_items->fetchAll(PDO::FETCH_ASSOC);

            $first_name = !empty($items) ? ($items[0]['original_name'] ?? $items[0]['product_name']) : 'Đơn hàng #' . $order['id'];
            ?>
            <div class="order-card">
                
                <div class="order-summary">
                    <div class="order-summary-left">
                        <span class="order-summary-title"><?= htmlspecialchars($first_name) ?></span>
                        <div class="order-summary-status"><?= translateOrderStatus($order['status']) ?></div>
                    </div>
                    
                    <div class="order-summary-price">
                        <span>Giá tiền: </span>
                        <strong><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong>
                    </div>

                    <button type="button" class="btn-toggle-detail" onclick="toggleOrder(<?= $order['id'] ?>, this)">
                        Xem chi tiết
                    </button>
                </div>

                <div id="detail-<?= $order['id'] ?>" class="order-detail-box">
                    
                    <div class="order-info-row">
                        <span>Mã đơn: <strong>#<?= $order['id'] ?></strong></span>
                        <span>Ngày đặt: <strong><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong></span>
                    </div>

                    <div class="order-address-row">
                        <?php 
                            $display_address = !empty($order['shipping_address']) ? $order['shipping_address'] : 'Chưa cập nhật địa chỉ';
                            $display_phone = !empty($order['user_phone']) ? $order['user_phone'] : ''; 
                        ?>
                        <strong>Địa chỉ giao hàng: </strong> 
                        <span><?= htmlspecialchars($display_address) ?></span>
                        <?php if (!empty($display_phone)): ?>
                            <span> - <strong>SĐT:</strong> <?= htmlspecialchars($display_phone) ?></span>
                        <?php endif; ?>
                    </div>

                    <?php foreach ($items as $it):
                        $img_link = !empty($it['image_url']) ? $it['image_url'] : ($it['product_image'] ?? '');
                        $p_name = !empty($it['original_name']) ? $it['original_name'] : ($it['product_name'] ?? '');
                        ?>
                        <div class="order-item">
                            <div class="order-item-img">
                                <img src="<?= $img_link ?>" alt="Product" onerror="this.src='https://via.placeholder.com/70?text=No+Image'">
                            </div>
                            <div class="order-item-info">
                                <div class="order-item-name"><?= htmlspecialchars($p_name) ?></div>
                                <div class="order-item-meta">
                                    Số lượng: <?= $it['quantity'] ?> | Giá: <span class="order-item-price"><?= number_format($it['price'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="order-footer">
                        <div class="order-total-text">
                            <span>Tổng thanh toán: </span>
                            <span class="order-total-amount"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                        </div>

                        <?php if ($order['status'] == 'pending'): ?>
                            <form action="" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');" style="margin: 0;">
                                <input type="hidden" name="action" value="cancel_order">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn-cancel-order">Hủy đơn hàng</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    function toggleOrder(id, btn) {
        var box = document.getElementById('detail-' + id);
        if (box.style.display === "none" || box.style.display === "") {
            box.style.display = "block";
            btn.innerText = "Đóng";
        } else {
            box.style.display = "none";
            btn.innerText = "Xem chi tiết";
        }
    }
</script>