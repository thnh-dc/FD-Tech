<?php 
session_start();
require_once '../auth/check_login.php';
require_once '../auth/user_only.php';
require_once '../config/database.php';
require_once '../includes/fd_member_helper.php';

$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/style_checkout.css">';
include '../includes/header.php';

$selectedItems = $_POST['selected_items'] ?? '';
$selectedArray = array_filter(explode(',', $selectedItems));

if (empty($selectedArray)) {
    $cartItems = [];
} else {
    $placeholders = implode(',', array_fill(0, count($selectedArray), '?'));
    $stmt = $pdo->prepare("SELECT c.id, c.quantity, p.name, p.price, p.discount_price, COALESCE(NULLIF(p.discount_price, 0), p.price) AS display_price, p.image_url FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? AND c.id IN ($placeholders)");
    $id = $_SESSION['user_id'] ?? 0;
    $params = array_merge([$id], $selectedArray);
    $stmt->execute($params);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$id = $_SESSION['user_id'] ?? 0;
$stmtUser = $pdo->prepare("SELECT full_name, phone, address, point FROM users WHERE id = ?");
$stmtUser->execute([$id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$user_point = (int)($user['point'] ?? 0);
$point_value = 100;
$total = 0;

foreach ($cartItems as $row) $total += $row['display_price'] * $row['quantity'];

$period_points = getUserPeriodFDp($pdo, $id);
$current_tier = getFDMemberTierByPoint($pdo, $period_points);
$member_tier_name = $current_tier['tier_name'] ?? 'Đồng';
$member_discount_percent = (float)($current_tier['discount_percent'] ?? 0);
$member_free_shipping = ((int)($current_tier['free_shipping'] ?? 0) === 1);

$shipping_carrier = 'FD Express';
$shipping_cost_original = 25000;
$shipping_cost = $member_free_shipping ? 0 : $shipping_cost_original;
$estimated_delivery_min = date('d/m/Y', strtotime('+3 days'));
$estimated_delivery_max = date('d/m/Y', strtotime('+4 days'));

$max_usable_points_by_total = (int)floor($total / $point_value);
$max_usable_points = min($user_point, $max_usable_points_by_total);

$member_discount_preview = (int)floor($total * $member_discount_percent / 100);
$final_preview_total = max($total - $member_discount_preview + $shipping_cost, 0);
?>

<div class="container">
<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>

    <div class="success-page-wrapper">
        <div class="success-card">
            <h1 class="success-title"><i class="fa-solid fa-circle-check"></i> Đặt hàng thành công!</h1>
            <p class="success-message">Đơn hàng của bạn đã được ghi nhận.</p>
            <button onclick="window.location.href='index.php'" class="btn btn-primary">
                <i class="fa-solid fa-cart-shopping"></i> Tiếp tục mua sắm
            </button>
        </div>
    </div>

<?php elseif (count($cartItems) > 0): ?>

    <form action="../user/action_checkout/process_checkout.php" method="POST" class="checkout-form">
        <input type="hidden" name="selected_items" value="<?= htmlspecialchars($selectedItems) ?>">
        <input type="hidden" name="point_value" value="<?= $point_value ?>">

        <div class="checkout-grid">

            <div class="checkout-left">
                <div class="checkout-section">
                    <h3><i class="fa-solid fa-location-dot"></i> Thông tin nhận hàng</h3>

                    <div class="checkout-form-group">
                        <input type="text" name="fullname" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Họ và tên" required>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Số điện thoại" required>
                        <textarea name="address" placeholder="Địa chỉ nhận hàng" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="checkout-section">
                    <h3><i class="fa-solid fa-truck-fast"></i> Đơn vị vận chuyển</h3>

                    <div class="shipping-preview-box">
                        <div class="shipping-preview-line">
                            <span>Đơn vị vận chuyển</span>
                            <strong><?= htmlspecialchars($shipping_carrier) ?></strong>
                        </div>

                        <div class="shipping-preview-line">
                            <span>Phí vận chuyển</span>
                            <strong>
                                <?php if ($member_free_shipping): ?>
                                    <span class="summary-discount">Miễn phí</span>
                                <?php else: ?>
                                    <?= number_format($shipping_cost, 0, ',', '.') ?>₫
                                <?php endif; ?>
                            </strong>
                        </div>

                        <?php if ($member_free_shipping): ?>
                            <p class="point-note">
                                <i class="fa-solid fa-gem"></i>
                                Thành viên <?= htmlspecialchars($member_tier_name) ?> được miễn phí vận chuyển.
                            </p>
                        <?php endif; ?>

                        <div class="shipping-preview-line">
                            <span>Giao hàng dự kiến</span>
                            <strong><?= $estimated_delivery_min ?> - <?= $estimated_delivery_max ?></strong>
                        </div>

                        <p class="point-note">
                            <i class="fa-solid fa-circle-info"></i>
                            Thời gian trên chỉ là dự kiến, đơn hàng có thể giao sớm hoặc muộn hơn !.
                        </p>
                    </div>
                </div>

                <div class="checkout-section">
                    <h3><i class="fa-solid fa-box-open"></i> Sản phẩm trong đơn (<?= count($cartItems) ?>)</h3>

                    <?php foreach ($cartItems as $item): ?>
                        <?php
                            $img = $item['image_url'] ?? '';
                            if (empty($img)) $img_src = "/FD-Tech/assets/images/logo-fd.jpg";
                            elseif (filter_var($img, FILTER_VALIDATE_URL)) $img_src = $img;
                            elseif (strpos($img, 'upload/product_image/') === 0) $img_src = "/FD-Tech/" . $img;
                            else $img_src = "/FD-Tech/upload/product_image/" . $img;
                        ?>

                        <div class="checkout-item">
                            <img src="<?= htmlspecialchars($img_src) ?>" class="checkout-img" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='/FD-Tech/assets/images/logo-fd.jpg'">

                            <div class="checkout-info">
                                <p class="checkout-name"><?= htmlspecialchars($item['name']) ?></p>

                                <p class="checkout-price">
                                    <?php if (!empty($item['discount_price']) && $item['discount_price'] > 0): ?>
                                        <span class="discount-price"><?= number_format($item['discount_price'], 0, ',', '.') ?>₫</span>
                                        <span class="old-price"><?= number_format($item['price'], 0, ',', '.') ?>₫</span>
                                    <?php else: ?>
                                        <span class="discount-price"><?= number_format($item['price'], 0, ',', '.') ?>₫</span>
                                    <?php endif; ?>

                                    <span class="checkout-qty">x<?= $item['quantity'] ?></span>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="checkout-section point-section">
                    <h3><i class="fa-solid fa-coins"></i> Sử dụng FD point</h3>

                    <div class="point-box">
                        <div class="point-current">
                            <span><i class="fa-solid fa-wallet"></i> FD point hiện có</span>
                            <strong><?= number_format($user_point, 0, ',', '.') ?> FDp</strong>
                        </div>

                        <div class="point-use-row">
                            <input type="number" name="use_points" id="usePointsInput" min="0" max="<?= $max_usable_points ?>" value="0" placeholder="Nhập số FDp muốn dùng" data-total="<?= (float)$total ?>" data-point-value="<?= (int)$point_value ?>" data-max-points="<?= (int)$max_usable_points ?>" data-member-discount-percent="<?= (float)$member_discount_percent ?>" data-shipping-cost="<?= (float)$shipping_cost ?>">
                            <button type="button" id="useAllPointsBtn" class="btn-use-all-points">
                                <i class="fa-solid fa-bolt"></i> Dùng tất cả
                            </button>
                        </div>

                        <p class="point-note"><i class="fa-solid fa-right-left"></i> Quy đổi: 10 FDp = 1.000đ</p>
                    </div>
                </div>

                <div class="checkout-section">
                    <h3><i class="fa-solid fa-credit-card"></i> Phương thức thanh toán</h3>

                    <div class="payment-method-list">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <span><i class="fa-solid fa-truck-fast"></i> Thanh toán khi nhận hàng</span>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="bank">
                            <span><i class="fa-solid fa-building-columns"></i> Chuyển khoản qua ngân hàng</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="checkout-right">
                <div class="checkout-section checkout-summary-section">
                    <h3><i class="fa-solid fa-file-invoice-dollar"></i> Thông tin thanh toán</h3>

                    <div class="summary-row">
                        <span>Tổng tiền tạm tính</span>
                        <strong id="subtotalText"><?= number_format($total, 0, ',', '.') ?>₫</strong>
                    </div>

                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <strong id="shippingCostText">
                            <?php if ($member_free_shipping): ?>
                                <span class="summary-discount">0₫</span>
                            <?php else: ?>
                                <?= number_format($shipping_cost, 0, ',', '.') ?>₫
                            <?php endif; ?>
                        </strong>
                    </div>

                    <?php if ($member_free_shipping): ?>
                        <div class="summary-row">
                            <span>Ưu đãi vận chuyển</span>
                            <strong class="summary-discount">-<?= number_format($shipping_cost_original, 0, ',', '.') ?>₫</strong>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span>Giảm bằng FDp</span>
                        <strong id="pointDiscountText" class="summary-discount">-0₫</strong>
                    </div>

                    <div class="summary-row">
                        <span>Ưu đãi FD Member</span>
                        <div class="summary-member">
                            <strong id="memberDiscountText" class="summary-discount">-0₫</strong>
                            <small id="memberDiscountLabel">
                                Thành viên <?= htmlspecialchars($member_tier_name) ?><?= $member_discount_percent > 0 ? ', chúc mừng bạn được giảm ' . number_format($member_discount_percent, 0, ',', '.') . '%' : '' ?><?= $member_free_shipping ? ', miễn phí vận chuyển' : '' ?>
                            </small>
                        </div>
                    </div>

                    <div class="summary-row total-row">
                        <span>Tổng thanh toán</span>
                        <strong id="finalTotalText"><?= number_format($final_preview_total, 0, ',', '.') ?>₫</strong>
                    </div>

                    <p class="point-note">
                        Bạn sẽ tích lũy được 
                        <strong id="earnedPointsText">
                            <?= number_format((int)floor($final_preview_total / 10000), 0, ',', '.') ?> FDp
                        </strong>
                        từ đơn hàng này
                    </p>

                    <button type="submit" class="btn btn-primary btn-checkout-submit">
                        <i class="fa-solid fa-check"></i> Xác nhận đặt hàng
                    </button>

                    <p class="checkout-policy">
                        Bằng việc xác nhận đặt hàng, bạn đồng ý với điều khoản mua hàng của FD Tech.
                    </p>
                </div>
            </div>

        </div>
    </form>

<?php else: ?>

    <div class="empty-cart-container">
        <h2><i class="fa-solid fa-cart-shopping"></i> Oppss, bạn chưa có sản phẩm để thanh toán.</h2>
        <button onclick="window.location.href='cart.php'" class="btn btn-primary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng của bạn
        </button>
    </div>

<?php endif; ?>
</div>

<script src="/FD-Tech/assets/js/script_checkout.js"></script>
<?php include '../includes/footer.php'; ?>