<?php 
    require_once '../config/database.php';
    session_start();

    $custom_css='
        <link rel="stylesheet" href="../assets/css/style_cart.css">';
    include '../includes/header.php';

    $stmt = $pdo->prepare("
        SELECT c.id, c.quantity, p.name, p.price
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");

    $id = $_SESSION['user_id'] ?? 0;
    $stmt->execute([$id]);

    // $stmt->execute([$id='user_id']); //user test
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = 0;
    foreach($cartItems as $item){
        $total += $item['price'] * $item['quantity'];
    }
?>

<style> /* fix layout cart - important*/
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        display: table-cell !important;
        padding: 12px 20px;
        text-align: left;
    }

    .data-table th {
        font-weight: 600;
        background: #f5f5f5;
    }
</style>

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
                        <td class="item-price"><?= number_format($item['price']) ?> vn₫</td>
                        <td class="item-quantity"><?= $item['quantity'] ?></td>
                        <td class="item-subtotal"><?=number_format( $item['price'] * $item['quantity'])?>vn₫</td>
                        <td>
                            <button class="btn btn-danger btn-delete" data-id="<?= $item['id'] ?>">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-summary">
                <p class="summary-text">
                    <b>Tổng thanh toán: </b>
                    <span class="price-highlight"><?= number_format($total) ?>vn₫</span>
                </p>
                    
                <a href="checkout.php">
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
<script src="/FD-Tech/assets/js/script_cart.js" ></script>
<?php include '../includes/footer.php'; ?>