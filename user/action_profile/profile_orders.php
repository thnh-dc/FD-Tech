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

// --- 2. LẤY DANH SÁCH ĐƠN HÀNG KÈM THEO ĐỊA CHỈ ---
$current_status = $_GET['status'] ?? 'all';

try {
    // Đã thêm LEFT JOIN với bảng users để lấy địa chỉ (user_address) và số điện thoại
    if ($current_status == 'all') {
        $stmt = $pdo->prepare("
            SELECT o.*, u.address AS user_address, u.phone AS user_phone 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT o.*, u.address AS user_address, u.phone AS user_phone 
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
    $statuses = [
        'pending' => '<span style="color: #ff9800; font-weight: bold;">Chờ xác nhận</span>',
        'processing' => '<span style="color: #2196f3; font-weight: bold;">Đang xử lý</span>',
        'shipped' => '<span style="color: #9c27b0; font-weight: bold;">Đang giao hàng</span>',
        'completed' => '<span style="color: #4caf50; font-weight: bold;">Đã giao</span>',
        'cancelled' => '<span style="color: #f44336; font-weight: bold;">Đã hủy</span>'
    ];
    return $statuses[$status] ?? '<span>' . htmlspecialchars($status) . '</span>';
}
?>

<div style="margin-bottom: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h2 style="margin: 0;">Đơn hàng của tôi</h2>
    <p style="color: #666; font-size: 14px;">Theo dõi tình trạng các đơn hàng đã đặt</p>
</div>

<div class="order-filter-tabs">
    <a href="profile.php?action=orders&status=all" class="<?= $current_status == 'all' ? 'active' : '' ?>">Tất cả</a>
    <a href="profile.php?action=orders&status=pending" class="<?= $current_status == 'pending' ? 'active' : '' ?>">Chờ
        xác nhận</a>
    <a href="profile.php?action=orders&status=processing"
        class="<?= $current_status == 'processing' ? 'active' : '' ?>">Đang xử lý</a>
    <a href="profile.php?action=orders&status=shipped" class="<?= $current_status == 'shipped' ? 'active' : '' ?>">Đang
        giao</a>
    <a href="profile.php?action=orders&status=completed"
        class="<?= $current_status == 'completed' ? 'active' : '' ?>">Đã giao</a>
    <a href="profile.php?action=orders&status=cancelled"
        class="<?= $current_status == 'cancelled' ? 'active' : '' ?>">Đã hủy</a>
</div>

<?php if (empty($orders)): ?>
    <div style="text-align: center; padding: 40px; border: 1px solid #eee; border-radius: 8px;">
        <p style="color: #999;">Bạn chưa có đơn hàng nào.</p>
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
            <div style="border: 1px solid #ddd; margin-bottom: 15px; border-radius: 8px; background: #fff; overflow: hidden;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #f5f5f5;">
                    <div style="flex: 1;">
                        <strong style="font-size: 16px; color: #333; display: block; margin-bottom: 5px;">
                            <?= htmlspecialchars($first_name) ?>
                        </strong>
                        <div><?= translateOrderStatus($order['status']) ?></div>
                    </div>
                    <button type="button" onclick="toggleOrder(<?= $order['id'] ?>, this)"
                        style="padding: 8px 16px; cursor: pointer; background: #4c9aee; color: white; border: none; border-radius: 4px; font-weight: bold;">
                        Xem chi tiết
                    </button>
                </div>

                <div id="detail-<?= $order['id'] ?>" style="display: none; padding: 15px; background: #fafafa;">
                    <div
                        style="display: flex; justify-content: space-between; font-size: 13px; color: #777; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #ccc;">
                        <span>Mã đơn: <strong>#<?= $order['id'] ?></strong></span>
                        <span>Ngày đặt: <strong><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong></span>
                    </div>

                    <div
                        style="font-size: 13px; color: #444; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #ccc; line-height: 1.5;">
                        <strong>Địa chỉ giao hàng: </strong><br>
                        <?php
                        // Ưu tiên địa chỉ trong đơn hàng, nếu trống thì lấy địa chỉ user
                        $display_address = !empty($order['address']) ? $order['address'] : ($order['user_address'] ?? '');
                        $display_phone = !empty($order['phone']) ? $order['phone'] : ($order['user_phone'] ?? '');

                        if (!empty($display_address)) {
                            echo htmlspecialchars($display_address);
                            if (!empty($display_phone)) {
                                echo " - <strong>SĐT:</strong> " . htmlspecialchars($display_phone);
                            }
                        } else {
                            echo '<i>Chưa cập nhật thông tin địa chỉ</i>';
                        }
                        ?>
                    </div>

                    <?php foreach ($items as $it):
                        $img_link = !empty($it['image_url']) ? $it['image_url'] : ($it['product_image'] ?? '');
                        $p_name = !empty($it['original_name']) ? $it['original_name'] : ($it['product_name'] ?? '');
                        ?>
                        <div
                            style="display: flex; gap: 15px; margin-bottom: 12px; background: #fff; padding: 12px; border: 1px solid #eee; border-radius: 6px; align-items: center;">
                            <div
                                style="width: 70px; height: 70px; flex-shrink: 0; background: #f0f0f0; border-radius: 4px; overflow: hidden;">
                                <img src="<?= $img_link ?>" alt="Product" style="width: 100%; height: 100%; object-fit: cover;"
                                    onerror="this.src='https://via.placeholder.com/70?text=No+Image'">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 14px; margin-bottom: 5px; color: #333;">
                                    <?= htmlspecialchars($p_name) ?>
                                </div>
                                <div style="font-size: 13px; color: #666;">
                                    Số lượng: <?= $it['quantity'] ?> |
                                    Giá: <span
                                        style="color: #ee4d2d; font-weight: bold;"><?= number_format($it['price'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="text-align: right; margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                        <div style="margin-bottom: 15px;">
                            <span style="color: #555;">Tổng thanh toán: </span>
                            <strong
                                style="font-size: 18px; color: #ee4d2d;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong>
                        </div>

                        <?php if ($order['status'] == 'pending'): ?>
                            <form action="" method="POST" onsubmit="return confirm('Xác nhận hủy đơn hàng này?');">
                                <input type="hidden" name="action" value="cancel_order">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit"
                                    style="background: white; color: #dc3545; border: 1px solid #dc3545; padding: 7px 15px; cursor: pointer; border-radius: 4px; font-size: 13px;">
                                    Hủy đơn hàng
                                </button>
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