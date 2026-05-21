<?php
// Bắt buộc phải có session_start() ở đầu file gốc (nếu file profile.php đã có thì không cần thêm)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    // Xử lý Hủy đơn
    if ($_POST['action'] == 'cancel_order') {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending' or 'processing'");
            if ($stmt->execute([$_POST['order_id'], $user_id])) {
                $_SESSION['noti_message'] = 'Đã hủy đơn hàng thành công!';
                $_SESSION['noti_type'] = 'success';
            }
        } catch (PDOException $e) {
            $_SESSION['noti_message'] = 'Lỗi khi hủy đơn hàng!';
            $_SESSION['noti_type'] = 'error';
        }
        header("Location: profile.php?action=orders");
        exit();
    } 
    // Xử lý Xác nhận đã nhận hàng
    elseif ($_POST['action'] == 'confirm_received') {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND user_id = ? AND status = 'shipped'");
            if ($stmt->execute([$_POST['order_id'], $user_id])) {
                $_SESSION['noti_message'] = 'Xác nhận đã nhận hàng thành công!';
                $_SESSION['noti_type'] = 'success';
            }
        } catch (PDOException $e) {
            $_SESSION['noti_message'] = 'Lỗi khi xác nhận đơn hàng!';
            $_SESSION['noti_type'] = 'error';
        }
        header("Location: profile.php?action=orders");
        exit();
    }
}

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

<style>
    .custom-modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4);
        display: none; /* Ẩn mặc định */
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.2s ease-in-out;
    }
    .custom-modal-box {
        background: #fff;
        padding: 25px 30px;
        border-radius: 8px;
        text-align: center;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .custom-modal-box h3 { margin-top: 0; color: #333; font-size: 20px; }
    .custom-modal-box p { color: #555; margin-bottom: 25px; line-height: 1.5; }
    .custom-modal-actions { display: flex; justify-content: center; gap: 15px; }
    .custom-modal-actions button {
        padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; font-weight: 500; transition: 0.2s;
    }
    .btn-modal-cancel { background: #f1f1f1; color: #333; }
    .btn-modal-cancel:hover { background: #e2e2e2; }
    .btn-modal-confirm { background: #26aa99; color: #fff; }
    .btn-modal-confirm:hover { background: #209082; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

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
            // CHỈ CẦN SELECT TỪ BẢNG order_items, KHÔNG CẦN JOIN NỮA
            $st_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $st_items->execute([$order['id']]);
            $items = $st_items->fetchAll(PDO::FETCH_ASSOC);

            // Lấy tên sản phẩm đầu tiên từ order_items
            $first_name = !empty($items) ? $items[0]['product_name'] : 'Đơn hàng #' . $order['id'];
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
                        // Lấy ảnh và tên trực tiếp từ order_items
                        $img_link = !empty($it['product_image']) ? $it['product_image'] : '';
                        $p_name = !empty($it['product_name']) ? $it['product_name'] : 'Sản phẩm không xác định';
                        ?>
                        <div class="order-item">
                            <div class="order-item-img">
                                <img src="<?= htmlspecialchars($img_link) ?>" alt="Product" onerror="this.src='https://via.placeholder.com/70?text=No+Image'">
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

                        <?php if ($order['status'] == 'pending' or 'processing'): ?>
                            <form id="form-cancel-<?= $order['id'] ?>" action="" method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="cancel_order">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="button" class="btn-cancel-order" onclick="openConfirmModal('cancel', <?= $order['id'] ?>)">Hủy đơn hàng</button>
                            </form>
                        <?php elseif ($order['status'] == 'shipped'): ?>
                            <form id="form-receive-<?= $order['id'] ?>" action="" method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="confirm_received">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="button" class="btn-confirm-received" style="background-color: #26aa99; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 14px; margin-left: 10px;" onclick="openConfirmModal('receive', <?= $order['id'] ?>)">Đã nhận được hàng</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div id="custom-confirm-modal" class="custom-modal-overlay">
    <div class="custom-modal-box">
        <h3 id="modal-title">Xác nhận</h3>
        <p id="modal-message">Bạn có chắc chắn muốn thực hiện thao tác này?</p>
        <div class="custom-modal-actions">
            <button onclick="closeConfirmModal()" class="btn-modal-cancel">Đóng</button>
            <button id="modal-confirm-btn" onclick="submitConfirmForm()" class="btn-modal-confirm">Xác nhận</button>
        </div>
    </div>
</div>

<script>
    // Toggle xem chi tiết đơn hàng
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

    // Biến lưu trữ form hiện tại cần submit
    let currentFormIdToSubmit = null;

    // Mở modal xác nhận
    function openConfirmModal(actionType, orderId) {
        const modal = document.getElementById('custom-confirm-modal');
        const title = document.getElementById('modal-title');
        const message = document.getElementById('modal-message');

        if (actionType === 'cancel') {
            title.innerText = 'Xác nhận hủy đơn';
            message.innerHTML = 'Bạn có chắc muốn hủy đơn hàng <strong>#' + orderId + '</strong> này không?';
            currentFormIdToSubmit = 'form-cancel-' + orderId;
        } else if (actionType === 'receive') {
            title.innerText = 'Xác nhận đã nhận hàng';
            message.innerHTML = 'Bạn xác nhận đã nhận được đơn hàng <strong>#' + orderId + '</strong> này thành công?';
            currentFormIdToSubmit = 'form-receive-' + orderId;
        }

        modal.style.display = 'flex';
    }

    // Đóng modal
    function closeConfirmModal() {
        document.getElementById('custom-confirm-modal').style.display = 'none';
        currentFormIdToSubmit = null;
    }

    // Submit form sau khi bấm Xác nhận trên Modal
    function submitConfirmForm() {
        if (currentFormIdToSubmit) {
            document.getElementById(currentFormIdToSubmit).submit();
        }
    }
</script>

<?php include '../includes/notification.php'; ?>