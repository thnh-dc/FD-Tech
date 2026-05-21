<?php
session_start();
require_once '../../auth/check_login.php';
require_once '../../auth/user_only.php';
require_once '../../config/database.php';

$pendingCheckout = $_SESSION['pending_bank_checkout'] ?? null;

if (!$pendingCheckout) {
    header("Location: ../cart.php");
    exit();
}
$selectedItems = $pendingCheckout['selected_items'];
$address = $pendingCheckout['address'];
$paymentContent = $pendingCheckout['payment_content'];
$user_id = $_SESSION['user_id'] ?? 0;
$selectedArray = array_filter(explode(',', $selectedItems));

if (empty($selectedArray)) {
    header("Location: ../cart.php?error=no_items");
    exit();
}
$placeholders = implode(',', array_fill(0, count($selectedArray), '?'));
$stmt = $pdo->prepare("
    SELECT 
        c.id,
        c.quantity,
        p.name,
        p.price,
        p.image_url
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    AND c.id IN ($placeholders)
");

$params = array_merge([$user_id], $selectedArray);
$stmt->execute($params);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/style_checkout.css">';
include '../../includes/header.php';
?>

<div class="container">
    <div class="checkout-layout">
        <div class="checkout-section">
            <h3>💳 Thông tin thanh toán</h3>
            <p>Vui lòng chuyển khoản theo thông tin bên dưới. Sau khi chuyển khoản xong, bấm nút <b>Xác nhận đã thanh toán</b> để hoàn tất đặt hàng.</p>
            <div class="bank-payment-box">
                <div class="bank-qr-box">
                    <img src="/FD-Tech/assets/images/qr-payment.jpg" alt="QR thanh toán" class="bank-qr-img">
                </div>
                <div class="bank-info-box">
                    <p><b>Ngân hàng:</b> Ngân hàng TMCP quân đội MB Bank</p>
                    <p><b>Chủ sở hữu:</b> FD TECH</p>
                    <p><b>Số tài khoản:</b> 686 102 6666</p>
                    <p><b>Số tiền:</b> <?= number_format($total, 0, ',', '.') ?>₫</p>
                    <p><b>Nội dung chuyển khoản:</b> <?= htmlspecialchars($paymentContent) ?></p>
                </div>
            </div>
        </div>
        <div class="checkout-section">
            <h3>📦 Sản phẩm thanh toán</h3>
            <?php foreach ($cartItems as $item): ?>
                <div class="checkout-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" class="checkout-img">
                    <div class="checkout-info">
                        <p class="checkout-name"><?= htmlspecialchars($item['name']) ?></p>
                        <p class="checkout-price">
                            <?= number_format($item['price'], 0, ',', '.') ?>₫ x <?= $item['quantity'] ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="checkout-section">
            <p>
                Tổng tiền:
                <b><?= number_format($total, 0, ',', '.') ?>₫</b>
            </p>
            <form action="process_checkout.php" method="POST">
                <input type="hidden" name="action" value="confirm_bank_payment">
                <button type="submit" class="btn btn-primary">
                    Xác nhận đã thanh toán
                </button>
                <button type="button" onclick="window.location.href='../cart.php'" class="btn btn-secondary">
                    Quay lại
                </button>
            </form>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>