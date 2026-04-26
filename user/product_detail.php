<<<<<<< Updated upstream
!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm - FD Tech</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/pages/product_detail.css">
</head>
<body style="background-color: #FFFFFF; font-family: 'Inter', sans-serif;">

    <div style="background: #0B2A4A; padding: 20px; color: white; text-align: center; font-size: 24px; font-weight: bold;">FD TECH - HEADER MẪU</div>

    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 16px; margin-bottom: 64px;">
        
        <div style="padding: 16px 0; font-size: 14px; color: #6C757D;">
            <a href="index.php" style="color: #0B2A4A; font-weight: 600; text-decoration: none;">Trang chủ</a> / 
            <a href="product_lis.php" style="color: #0B2A4A; font-weight: 600; text-decoration: none;">Sản phẩm</a> / 
            <span style="color: #333333;">Bàn phím cơ Light God Game</span>
        </div>

        <div style="display: flex; gap: 40px; margin-top: 24px;">
            
            <div id="zoomContainer" style="flex: 1; background: #F4F6F9; border-radius: 8px; display: flex; justify-content: center; align-items: center; height: 400px; cursor: crosshair; overflow: hidden; position: relative;">
                <div id="zoomImage" style="width: 100%; height: 100%; background: #ddd; display: flex; align-items: center; justify-content: center; transition: transform 0.1s ease-out;">Ảnh Bàn Phím To</div>
            </div>
            
            <div style="flex: 1;">
                <span style="background: #F4F6F9; color: #23B5D3; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: bold;">KEYBOARDS</span>
                
                <h1 style="font-size: 32px; color: #333333; margin: 16px 0;">Bàn phím cơ Light God Game FD 37</h1>
                
                <div style="font-size: 40px; color: #0B2A4A; font-weight: 900; margin-bottom: 24px; border-bottom: 1px solid #F4F6F9; padding-bottom: 24px;">
                    1.460.000 ₫
                </div>
                
                <div style="margin-bottom: 24px; font-size: 16px;">
                    Tình trạng kho: <span style="color: #28A745; font-weight: bold;">Còn hàng (Mockup)</span>
                </div>

                <p style="color: #6C757D; line-height: 1.6; margin-bottom: 32px;">
                    - Bàn phím cơ full-size dành cho game thủ.<br>
                    - Switch quang học siêu bền.<br>
                    - LED RGB 16.8 triệu màu.
                </p>
                
                <form action="#" style="display: flex; gap: 16px;">
                    <div style="display: flex; border: 1px solid #6C757D; border-radius: 4px;">
                        <button type="button" id="btnMinus" style="padding: 10px 15px; background: #F4F6F9; border: none; cursor: pointer;">-</button>
                        <input type="text" id="qtyInput" value="1" style="width: 50px; text-align: center; border: none; font-weight: bold;" readonly>
                        <button type="button" id="btnPlus" style="padding: 10px 15px; background: #F4F6F9; border: none; cursor: pointer;">+</button>
                    </div>
                    <button type="button" style="flex: 1; background: #23B5D3; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;">THÊM VÀO GIỎ HÀNG</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/product.js"></script>
</body>
</html>
=======
<?php 
$id = isset($_GET['id']) ? $_GET['id'] : 1; 
include '../includes/header.php'; 
?>

<link rel="stylesheet" href="../assets/css/product.css?v=<?php echo time(); ?>">

<div class="container" style="margin-bottom: 60px;">
    <div style="padding: 20px 0; font-size: 14px;">
        <a href="product_list.php" style="color: #6C757D;">Sản phẩm</a> / Chi tiết
    </div>

    <div class="detail-layout">
        <div class="detail-left">
            <div class="gallery-main">
                <img id="mainImgView" src="https://via.placeholder.com/400" alt="Detail Image">
            </div>
            <div style="text-align: center; color: #6C757D; font-size: 13px;">Rê chuột để phóng to ảnh</div>
        </div>

        <div class="detail-right">
            <h1 class="detail-title">Sản phẩm mã số #<?php echo $id; ?></h1>
            <div style="font-size: 32px; font-weight: 800; color: #DC3545; margin: 20px 0;">
                1.460.000₫
            </div>

            <div style="background: #F8F9FA; padding: 20px; border-radius: 8px;">
                <h3 style="margin-top: 0; font-size: 16px;">Thông tin sản phẩm:</h3>
                <ul style="padding-left: 20px; line-height: 1.8; color: #333;">
                    <li>Bảo hành chính hãng 12 tháng.</li>
                    <li>Giao hàng miễn phí toàn quốc.</li>
                    <li>Lỗi là đổi mới trong 30 ngày.</li>
                </ul>
            </div>

            <div class="action-buttons">
                <button class="btn-now">MUA NGAY</button>
                <button class="btn-outline">THÊM VÀO GIỎ HÀNG</button>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/product.js"></script>
<?php include '../includes/footer.php'; ?>
>>>>>>> Stashed changes
