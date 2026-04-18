<?php 
    session_start();
    $id = $_SESSION['id'] ?? 0; //Lấy user
    
    $custom_css='
        <link rel="stylesheet" href="../assets/css/style_cart.css">';
    include '../includes/header.php';
    require_once '../config/database.php'; // config database
    
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->execute([$id]);
    $cartItems = $stmt->fetchAll();//Hiện sản phẩm trong giỏ hàng từ user

    $total = 0;
    if(isset($cartItems)){
        foreach($cartItems as $item){
            $total += $item['price'] * $item['quantity'];
        }
    }//Tính tổng
?>

<div class="container">
    <section class="section-block">
        <h1 class="page-title">Giỏ Hàng Của Bạn</h1>
        <div class="card shadow-card">

        <?php if(isset($cartItems) && count($cartItems) > 0): ?> <!-- Xử lí logic: Nếu có dữ liệu sản phẩm thì hiển tt sản phẩm -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($cartItems as $item): ?>
                    <tr class="cart-item"> 
                        <td><?= $item['name'] ?></td>
                        <td class="item-price"><?= $item['price'] ?></td>
                        <td class="item-quantity"><?= $item['quantity'] ?></td>
                        <td class="item-subtotal"><?= $item['price'] * $item['quantity'] ?>₫</td>
                        <td>
                            <button class="btn btn-danger btn-delete" data-id="<?= $item['id'] ?>">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-summary">
                <p class="summary-text">
                    Tổng thanh toán: 
                    <span class="price-highlight"><?= $total ?>₫</span>
                </p>

                <a href="product_list.php">
                    <button class="btn btn-primary btn-large">
                        Tiến Hành Đặt Hàng
                    </button>
                </a>
            </div>

        <?php else: ?> <!-- Ngược lại ko có sản phẩm thì hiện giỏ hàng trống -->

            <div class="empty-cart-container">
                <svg class="empty-cart-icon" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>

                <h2 class="empty-cart-title">Giỏ hàng trống</h2>
                <p class="empty-cart-desc">Không có sản phẩm nào trong giỏ hàng</p>

                <a href="index.php">
                    <button class="btn btn-primary btn-large">
                        Về trang chủ
                    </button>
                </a>
            </div>

        <?php endif; ?>

        </div>
    </section>
</div>
<script src="../assets/js/script_cart.js"></script>
<?php include '../includes/footer.php'; ?>