<?php 
// 1. NHẬN ID TỪ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// 2. MẢNG DỮ LIỆU ĐỒNG BỘ 100% VỚI LIST (Thêm Specs cho Tab)
$products = [
    1 => ['name'=>'Chuột Gaming God Cane VD 31 Siêu Nhẹ', 'price'=>1400000, 'old'=>1800000, 'desc'=>'Chuột gaming siêu nhẹ chỉ 63g. Cảm biến 16000 DPI siêu chuẩn xác cho game thủ FPS.', 'specs'=>['Cảm biến'=>'16000 DPI', 'Kết nối'=>'Wireless/Type-C', 'Trọng lượng'=>'63g', 'Bảo hành'=>'24 Tháng'], 'img'=>'https://via.placeholder.com/600x600/0B2A4A/FFFFFF?text=Mouse'],
    2 => ['name'=>'Bàn phím cơ Light God Game FD 37 Pro', 'price'=>1460000, 'old'=>0, 'desc'=>'Switch quang học siêu bền. Hệ thống LED RGB 16.8 triệu màu nháy theo nhạc siêu đẹp.', 'specs'=>['Loại phím'=>'Full-size Cơ', 'Switch'=>'Quang Học', 'LED'=>'RGB 16.8M', 'Bảo hành'=>'24 Tháng'], 'img'=>'https://via.placeholder.com/600x600/23B5D3/FFFFFF?text=Keyboard'],
    3 => ['name'=>'Tai nghe vòm 7.1 F9 Night God', 'price'=>1400000, 'old'=>1700000, 'desc'=>'Tai nghe over-ear cách âm tuyệt đối. Âm thanh vòm 7.1 hỗ trợ chơi game FPS cực định vị.', 'specs'=>['Âm thanh'=>'Vòm 7.1', 'Mic'=>'Chống ồn AI', 'Kết nối'=>'USB', 'Bảo hành'=>'12 Tháng'], 'img'=>'https://via.placeholder.com/600x600/0B2A4A/FFFFFF?text=Headset'],
    4 => ['name'=>'Củ sạc nhanh GaN 30W Ighti God', 'price'=>450000, 'old'=>600000, 'desc'=>'Công nghệ GaN siêu nhỏ gọn. Sạc nhanh chuẩn PD an toàn chống cháy nổ tuyệt đối.', 'specs'=>['Công suất'=>'30W Max', 'Cổng'=>'1 Type-C', 'Công nghệ'=>'GaN', 'Bảo hành'=>'18 Tháng'], 'img'=>'https://via.placeholder.com/600x600/23B5D3/FFFFFF?text=Charger'],
    5 => ['name'=>'Lót chuột RGB Matrix Cực Đại', 'price'=>350000, 'old'=>0, 'desc'=>'Kích thước 800x300mm trải dài bàn. Viền khâu viền LED RGB cực sáng và bền bỉ.', 'specs'=>['Kích thước'=>'800x300x4mm', 'Chất liệu'=>'Vải trơn trượt', 'LED'=>'RGB 14 Chế độ', 'Bảo hành'=>'Lỗi 1 đổi 1'], 'img'=>'https://via.placeholder.com/600x600/0B2A4A/FFFFFF?text=Mousepad'],
    6 => ['name'=>'Bàn phím giả cơ FD K100', 'price'=>450000, 'old'=>550000, 'desc'=>'Bàn phím Membrane gõ nảy. Khung kim loại nguyên khối chắc chắn, LED Rainbow rực rỡ.', 'specs'=>['Loại phím'=>'Giả cơ', 'Chất liệu'=>'Nhôm', 'LED'=>'Rainbow', 'Bảo hành'=>'12 Tháng'], 'img'=>'https://via.placeholder.com/600x600/23B5D3/FFFFFF?text=Keyboard+2'],
    7 => ['name'=>'Chuột không dây FD Office M1 Silent', 'price'=>250000, 'old'=>350000, 'desc'=>'Phím bấm Silent chống ồn dùng cho văn phòng. Thời lượng pin trâu lên đến 12 tháng.', 'specs'=>['DPI'=>'1200', 'Kết nối'=>'Wireless 2.4G', 'Pin'=>'12 Tháng', 'Bảo hành'=>'12 Tháng'], 'img'=>'https://via.placeholder.com/600x600/0B2A4A/FFFFFF?text=Office+Mouse'],
    8 => ['name'=>'Giá đỡ tai nghe nhôm nguyên khối', 'price'=>200000, 'old'=>0, 'desc'=>'Nhôm CNC cao cấp nguyên khối. Lót cao su chống trơn trượt bảo vệ tai nghe tối đa.', 'specs'=>['Chất liệu'=>'Nhôm CNC', 'Chiều cao'=>'25cm', 'Đế'=>'Cao su chống trượt', 'Bảo hành'=>'Không'], 'img'=>'https://via.placeholder.com/600x600/23B5D3/FFFFFF?text=Stand'],
    9 => ['name'=>'Cáp sạc Type-C bọc dù siêu bền 2m', 'price'=>150000, 'old'=>250000, 'desc'=>'Cáp dài 2 mét bọc dù chống đứt gãy. Hỗ trợ sạc nhanh 60W và truyền dữ liệu tốc độ cao.', 'specs'=>['Độ dài'=>'2 Mét', 'Chất liệu'=>'Bọc dù', 'Công suất sạc'=>'60W (3A)', 'Bảo hành'=>'1 Đổi 1'], 'img'=>'https://via.placeholder.com/600x600/0B2A4A/FFFFFF?text=Cable'],
    10=> ['name'=>'Webcam Full HD 1080p tích hợp Micro', 'price'=>850000, 'old'=>1100000, 'desc'=>'Sắc nét Full HD 1080p. Micro kép lọc ồn cực tốt phục vụ cho học tập trực tuyến, họp hành.', 'specs'=>['Độ phân giải'=>'1080p 30fps', 'Micro'=>'Kép, Lọc ồn', 'Kết nối'=>'USB Plug&Play', 'Bảo hành'=>'12 Tháng'], 'img'=>'https://via.placeholder.com/600x600/23B5D3/FFFFFF?text=Webcam']
];

$sp = isset($products[$id]) ? $products[$id] : $products[1];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết - <?php echo $sp['name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style_product.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="header-vip">
        <div class="container header-flex">
            <h2>FD TECH V.I.P</h2>
            <div class="header-tools">
                <button id="btnDarkmode" class="btn-darkmode">🌙 Tối</button>
                <div class="cart-wrap">
                    🛒 Giỏ hàng <span class="cart-badge" id="cartBadge">0</span>
                    <div class="cart-dropdown">
                        <div class="cart-dropdown-header">Sản phẩm mới thêm</div>
                        <div class="cart-items" id="cartItems"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div style="margin: 24px 0; color: var(--text-muted); font-size: 14px;">
            <a href="product_list.php" style="color: var(--secondary); font-weight: bold;">Cửa hàng</a> / <?php echo $sp['name']; ?>
        </div>

        <div class="detail-layout">
            <div class="detail-left">
                <div class="main-img-box" id="imgBoxZoom">
                    <img id="mainImgView" src="<?php echo $sp['img']; ?>" alt="Ảnh chính">
                </div>
                <div class="thumb-list">
                    <div class="thumb-item active"><img src="<?php echo $sp['img']; ?>"></div>
                    <div class="thumb-item"><img src="https://via.placeholder.com/600x600/0B2A4A/FFFFFF?text=Goc+2"></div>
                    <div class="thumb-item"><img src="https://via.placeholder.com/600x600/23B5D3/FFFFFF?text=Goc+3"></div>
                </div>
                <div style="text-align:center; font-size: 12px; color: var(--text-muted); margin-top: 10px;">🔍 Rê chuột vào ảnh để soi chi tiết</div>
            </div>

            <div class="detail-right">
                <h1 class="detail-title"><?php echo $sp['name']; ?></h1>
                
                <div class="detail-price-box">
                    <?php echo number_format($sp['price'], 0, ',', '.'); ?> ₫
                    <?php if($sp['old'] > 0): ?>
                        <span style="font-size: 18px; color: var(--text-muted); text-decoration: line-through; font-weight: normal; margin-left: 10px;">
                            <?php echo number_format($sp['old'], 0, ',', '.'); ?> ₫
                        </span>
                    <?php endif; ?>
                </div>

                <div class="detail-desc">
                    <strong>Đặc điểm nổi bật:</strong><br>
                    <?php echo $sp['desc']; ?>
                </div>

                <input type="hidden" id="pd_id" value="<?php echo $id; ?>">
                <input type="hidden" id="pd_name" value="<?php echo $sp['name']; ?>">
                <input type="hidden" id="pd_price" value="<?php echo $sp['price']; ?>">
                <input type="hidden" id="pd_img" value="<?php echo $sp['img']; ?>">

                <div class="qty-box">
                    <strong>Số lượng:</strong>
                    <div class="qty-input-group">
                        <button type="button" id="btnMinus" class="qty-btn">-</button>
                        <input type="text" id="qtyInput" class="qty-input" value="1" readonly>
                        <button type="button" id="btnPlus" class="qty-btn">+</button>
                    </div>
                </div>

                <div class="action-btns">
                    <button class="btn btn-add-cart" id="btnAddToCart">THÊM VÀO GIỎ HÀNG</button>
                    <button class="btn btn-buy-now" onclick="alert('Đang chuyển hướng thanh toán!')">MUA NGAY</button>
                </div>
            </div>
        </div>

        <div class="detail-bottom">
            <div class="tabs-header">
                <button class="tab-btn active" data-target="tab-desc">Bài viết đánh giá</button>
                <button class="tab-btn" data-target="tab-specs">Thông số kỹ thuật</button>
            </div>
            
            <div class="tab-content active" id="tab-desc">
                <p><?php echo $sp['desc']; ?></p>
                <img src="https://via.placeholder.com/800x400/0B2A4A/FFFFFF?text=Banner+Quang+Cao" style="width:100%; border-radius: 8px; margin: 20px 0;">
                <p>Sản phẩm phân phối chính hãng bởi FD Tech. Giao hàng hỏa tốc trong 2H nội thành.</p>
            </div>

            <div class="tab-content" id="tab-specs">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid var(--border-color);">
                    <?php foreach($sp['specs'] as $k => $v): ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid var(--border-color); font-weight: bold; width: 30%; background: var(--bg-body);"><?php echo $k; ?></td>
                            <td style="padding: 10px; border: 1px solid var(--border-color);"><?php echo $v; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    </div>

    <script src="../assets/js/script_product.js"></script>
</body>
</html>