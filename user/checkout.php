<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($_GET['status']) && $_GET['status'] == 'success' ? 'Đặt hàng thành công' : 'Thanh Toán'; ?> - FD Tech</title>
    
    <link rel="stylesheet" href="../assets/css/style_chung.css">
    
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <link rel="stylesheet" href="../assets/css/style_done.css">
    <?php else: ?>
        <link rel="stylesheet" href="../assets/css/style_checkout.css">
    <?php endif; ?>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light-area">
    <?php include '../includes/header.php' ?>

    <div class="container">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="success-page-wrapper">
                <div class="success-card">
                    <svg class="success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    
                    <h1 class="success-title">Đặt hàng thành công!</h1>
                    <p class="success-message">
                        Cảm ơn bạn đã tin tưởng và mua sắm tại FD Tech.<br>
                        Đơn hàng của bạn đang được hệ thống xác nhận và sẽ sớm được giao đến bạn.
                    </p>
                    
                    <div class="success-actions">
                        <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                        <a href="profile.php#orders" class="btn btn-secondary">Xem lịch sử đơn hàng</a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="checkout-layout">
                <form action="process_checkout.php" method="POST" id="checkout-form">
                    
                    <div class="checkout-section delivery-trigger" id="btn-open-delivery">
                        <div class="section-heading" style="border:none; margin:0; padding:0; display:flex; justify-content:space-between;">
                            <span>📍 Thông tin nhận hàng</span>
                        </div>
                        <div id="display-delivery" class="delivery-info-display" style="margin-top: 12px;">
                            <p style="color: var(--danger);">* Vui lòng nhấp vào đây để nhập thông tin nhận hàng</p>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3 class="section-heading">📦 Sản phẩm của bạn</h3>
                        <div id="checkout-items-list">
                            </div>
                        <div class="promo-code-box" style="margin-top:16px;">
                            <input type="text" placeholder="Mã giảm giá (nếu có)" class="form-control">
                            <button type="button" class="btn btn-secondary">Áp dụng</button>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3 class="section-heading">💳 Hình thức thanh toán</h3>
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <span>Thanh toán tiền mặt khi nhận hàng (COD)</span>
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="bank_transfer">
                            <span>Chuyển khoản ngân hàng</span>
                        </label>
                    </div>

                    <div class="checkout-section">
                        <div class="summary-row">
                            <span>Tổng tiền sản phẩm:</span>
                            <span id="subtotal">0₫</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Tổng thanh toán:</span>
                            <span id="final-total">0₫</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-large btn-full">XÁC NHẬN ĐẶT HÀNG</button>
                    </div>
                    <div id="deliveryModal" class="modal-overlay"> <div class="modal-content" id="modal-content-box"> <span class="modal-close" id="btn-close-modal">&times;</span>
                        <h3 class="section-heading">📍 Thông tin nhận hàng</h3>
                        
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="fullname" id="input-fullname" class="form-control" placeholder="Nhập tên người nhận" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" id="input-phone" class="form-control" placeholder="Nhập số điện thoại" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Địa chỉ chi tiết</label>
                            <textarea name="address" id="input-address" class="form-control" rows="3" placeholder="Số nhà, tên đường, phường/xã..." required></textarea>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-full" id="btn-confirm-delivery">Lưu thông tin</button> 
                    </div>
                </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script src="/FD-Tech/assets/js/script_checkout.js?v=1"></script>
    <?php include '../includes/footer.php' ?>
</body>
</html>