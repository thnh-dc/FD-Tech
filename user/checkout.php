<?php
session_start();
include '../includes/header.php';
include '../config/database.php';

$user_id = $_SESSION['user_id'] ?? 0;// lấy user

$stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");//lấy giỏ hàng
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

$cartItems = $stmt->fetchAll();

$total = 0;
foreach($cartItems as $row){
    $total += $row['price'] * $row['quantity'];
}
?>

<div class="container">

<?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>

    <!-- ===== SUCCESS ===== -->
    <div class="success-page-wrapper">
        <div class="success-card">
            <h1 class="success-title">Đặt hàng thành công!</h1>
            <p class="success-message">
                Đơn hàng của bạn đã được ghi nhận.
            </p>

            <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    </div>

<?php elseif(count($cartItems) > 0): ?>

    <!-- ===== CHECKOUT ===== -->
    <div class="checkout-layout">

    <form action="process_checkout.php" method="POST">

        <!-- THÔNG TIN -->
        <div class="checkout-section">
            <h3>📍 Thông tin nhận hàng</h3>

            <input type="text" name="fullname" placeholder="Họ tên" required>
            <input type="text" name="phone" placeholder="SĐT" required>
            <textarea name="address" placeholder="Địa chỉ" required></textarea>
        </div>

        <!-- SẢN PHẨM -->
        <div class="checkout-section">
            <h3>📦 Sản phẩm</h3>

            <?php foreach($cartItems as $item): ?>
                <div class="checkout-item">
                    <p><?= $item['name'] ?></p>
                    <p><?= number_format($item['price']) ?>₫ x <?= $item['quantity'] ?></p>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- THANH TOÁN -->
        <div class="checkout-section">
            <h3>💳 Thanh toán</h3>

            <label>
                <input type="radio" name="payment_method" value="cod" checked>
                COD
            </label>

            <label>
                <input type="radio" name="payment_method" value="bank">
                Chuyển khoản
            </label>
        </div>

        <!-- TỔNG -->
        <div class="checkout-section">

            <p>Tổng tiền: 
                <b><?= number_format($total) ?>₫</b>
            </p>

            <button type="submit" class="btn btn-primary">
                Xác nhận đặt hàng
            </button>

        </div>

    </form>

    </div>

<?php else: ?>

    <!-- ===== GIỎ TRỐNG ===== -->
    <div class="empty-cart-container">
        <h2>Oppss, bạn chưa có sản phẩm để thanh toán.</h2>
        <a href="cart.php" class="btn btn-primary">Quay lại giỏ hàng</a>
    </div>

<?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>