<?php
    session_start();
    require_once '../auth/user_only.php';
    require_once '../auth/check_login.php';
    include '../config/database.php';


    $custom_css='<link rel="stylesheet" href="/FD-Tech/assets/css/style_checkout.css">';
    include '../includes/header.php';

    $selectedItems = $_POST['selected_items'] ?? '';
    // chuyển thành mảng
    $selectedArray = explode(',', $selectedItems);
    // tạo placeholder ?,?,?
    $placeholders = implode(',', array_fill(0, count($selectedArray), '?'));

    $stmt = $pdo->prepare("
    SELECT c.id, c.quantity, p.name, p.price, p.image_url
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    AND c.id IN ($placeholders)
    ");

    $id = $_SESSION['user_id'] ?? 0;
    $par = array_merge([$id], $selectedArray);
    $stmt->execute($par);
    // lấy thông tin user
    $stmtUser = $pdo->prepare("SELECT full_name, phone, address FROM users WHERE id = ?");
    $stmtUser->execute([$id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Tính tổng
    $total = 0;
    foreach($cartItems as $row){
        $total += $row['price'] * $row['quantity'];
    }
?>
<div class="container">
<?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="success-page-wrapper">
        <div class="success-card">
            <h1 class="success-title">Đặt hàng thành công!</h1>
            <p class="success-message">
                Đơn hàng của bạn đã được ghi nhận.
            </p>

            <button onclick="window.location.href='index.php'" 
                    class="btn btn-primary">
                Tiếp tục mua sắm
            </button>
        </div>
    </div>

<?php elseif(count($cartItems) > 0): ?>

    <!-- checkout -->
    <div class="checkout-layout">

    <form action="../user/action_checkout/process_checkout.php" method="POST">
        <input type="hidden" name="selected_items" value="<?= htmlspecialchars($selectedItems) ?>">
        <!-- THÔNG TIN -->
        <div class="checkout-section">
            <h3>📍 Thông tin nhận hàng</h3>

            <input type="text" name="fullname" value="<?= $user['full_name'] ?? '' ?>" required>
            <input type="text" name="phone" value="<?= $user['phone'] ?? '' ?>" required>
            <textarea name="address" required><?= $user['address'] ?? '' ?></textarea>
        </div>

        <!-- sản phẩm -->
        <div class="checkout-section">
            <h3>📦 Sản phẩm</h3>

            <?php foreach($cartItems as $item): ?>
                <div class="checkout-item">
                    
                    <img src="<?= $item['image_url'] ?>" class="checkout-img">

                    <div class="checkout-info">
                        <p class="checkout-name"><?= $item['name'] ?></p>
                        <p class="checkout-price">
                            <?= number_format($item['price']) ?>₫ x <?= $item['quantity'] ?>
                        </p>
                    </div>

                </div>
            <?php endforeach; ?>

        </div>
        <!-- thanh toán -->
        <div class="checkout-section">
            <h3>💳 Thanh toán</h3>

            <label>
                <input type="radio" name="payment_method" value="cod" checked>
                Thanh toán khi nhận hàng
            </label>
        </div>
        <!-- tổng -->
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
    <!-- Nếu chưa có sản phẩm thì ... -->
    <div class="empty-cart-container">
        <h2>Oppss, bạn chưa có sản phẩm để thanh toán.</h2>
        <button onclick="window.location.href='cart.php'" 
                    class="btn btn-primary">
                Quay lại giỏ hàng của bạn 
            </button>
    </div>
<?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>