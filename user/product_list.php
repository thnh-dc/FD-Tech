<?php 
// MẢNG DỮ LIỆU ĐỘNG PHP (10 Sản phẩm)
$products = [
    1 => ['id'=>1, 'cat'=>'pc', 'name'=>'Chuột Gaming God Cane VD 31 Siêu Nhẹ', 'price'=>1400000, 'old'=>1800000, 'sale'=>'-22%', 'rating'=>4.8, 'img'=>'https://via.placeholder.com/300x300/0B2A4A/FFFFFF?text=Mouse', 'desc'=>'Chuột gaming siêu nhẹ chỉ 63g. Cảm biến 16000 DPI siêu chuẩn xác.'],
    2 => ['id'=>2, 'cat'=>'pc', 'name'=>'Bàn phím cơ Light God Game FD 37 Pro', 'price'=>1460000, 'old'=>0, 'sale'=>'MỚI', 'rating'=>5.0, 'img'=>'https://via.placeholder.com/300x300/23B5D3/FFFFFF?text=Keyboard', 'desc'=>'Switch quang học siêu bền. Hệ thống LED RGB 16.8 triệu màu nháy theo nhạc.'],
    3 => ['id'=>3, 'cat'=>'audio', 'name'=>'Tai nghe vòm 7.1 F9 Night God', 'price'=>1400000, 'old'=>1700000, 'sale'=>'-18%', 'rating'=>4.7, 'img'=>'https://via.placeholder.com/300x300/0B2A4A/FFFFFF?text=Headset', 'desc'=>'Tai nghe over-ear cách âm tuyệt đối. Âm thanh vòm 7.1 hỗ trợ chơi game FPS.'],
    4 => ['id'=>4, 'cat'=>'acc', 'name'=>'Củ sạc nhanh GaN 30W Ighti God', 'price'=>450000, 'old'=>600000, 'sale'=>'-25%', 'rating'=>4.9, 'img'=>'https://via.placeholder.com/300x300/23B5D3/FFFFFF?text=Charger', 'desc'=>'Công nghệ GaN siêu nhỏ gọn. Sạc nhanh chuẩn PD an toàn chống cháy nổ.'],
    5 => ['id'=>5, 'cat'=>'acc', 'name'=>'Lót chuột RGB Matrix Cực Đại', 'price'=>350000, 'old'=>0, 'sale'=>'', 'rating'=>4.6, 'img'=>'https://via.placeholder.com/300x300/0B2A4A/FFFFFF?text=Mousepad', 'desc'=>'Kích thước 800x300mm trải dài bàn. Viền khâu LED RGB cực sáng.'],
    6 => ['id'=>6, 'cat'=>'pc', 'name'=>'Bàn phím giả cơ FD K100', 'price'=>450000, 'old'=>550000, 'sale'=>'-18%', 'rating'=>4.5, 'img'=>'https://via.placeholder.com/300x300/23B5D3/FFFFFF?text=Keyboard+2', 'desc'=>'Bàn phím Membrane gõ nảy. Khung kim loại chắc chắn, LED Rainbow.'],
    7 => ['id'=>7, 'cat'=>'pc', 'name'=>'Chuột không dây FD Office M1', 'price'=>250000, 'old'=>350000, 'sale'=>'-28%', 'rating'=>4.8, 'img'=>'https://via.placeholder.com/300x300/0B2A4A/FFFFFF?text=Office+Mouse', 'desc'=>'Phím bấm Silent chống ồn. Thời lượng pin trâu lên đến 12 tháng.'],
    8 => ['id'=>8, 'cat'=>'acc', 'name'=>'Giá đỡ tai nghe nhôm nguyên khối', 'price'=>200000, 'old'=>0, 'sale'=>'', 'rating'=>4.9, 'img'=>'https://via.placeholder.com/300x300/23B5D3/FFFFFF?text=Stand', 'desc'=>'Nhôm CNC cao cấp nguyên khối. Lót cao su chống trơn trượt.'],
    9 => ['id'=>9, 'cat'=>'acc', 'name'=>'Cáp sạc Type-C bọc dù siêu bền 2m', 'price'=>150000, 'old'=>250000, 'sale'=>'-40%', 'rating'=>4.7, 'img'=>'https://via.placeholder.com/300x300/0B2A4A/FFFFFF?text=Cable', 'desc'=>'Cáp dài 2 mét bọc dù chống đứt gãy. Hỗ trợ sạc nhanh và truyền dữ liệu.'],
    10=> ['id'=>10, 'cat'=>'audio', 'name'=>'Webcam Full HD 1080p tích hợp Micro', 'price'=>850000, 'old'=>1100000, 'sale'=>'-22%', 'rating'=>4.6, 'img'=>'https://via.placeholder.com/300x300/23B5D3/FFFFFF?text=Webcam', 'desc'=>'Sắc nét Full HD 1080p. Micro lọc ồn cực tốt phục vụ học tập trực tuyến.']
];

// Hàm in sao đánh giá
function renderStars($rating) {
    return str_repeat('★', floor($rating)) . str_repeat('☆', 5 - floor($rating));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm - FD Tech</title>
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
                        <a href="#" class="btn-view-cart">Xem chi tiết Giỏ Hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container" style="margin-top: 24px;">
        
        <div class="flash-sale">
            <h2 style="margin:0; font-size: 20px;">⚡ GIỜ VÀNG DEAL SỐC</h2>
            <div class="timer">Kết thúc trong: <span id="hour">02</span>:<span id="minute">15</span>:<span id="second">30</span></div>
        </div>

        <div class="filter-bar">
            <div class="filter-tags">
                <button class="filter-btn active" data-cat="all">Tất cả</button>
                <button class="filter-btn" data-cat="pc">Phím Chuột</button>
                <button class="filter-btn" data-cat="audio">Âm thanh / Hình ảnh</button>
                <button class="filter-btn" data-cat="acc">Phụ kiện khác</button>
            </div>
            <div>
                <select id="sortSelect" class="sort-select">
                    <option value="default">Sắp xếp: Mặc định</option>
                    <option value="asc">Giá: Thấp đến Cao</option>
                    <option value="desc">Giá: Cao đến Thấp</option>
                </select>
            </div>
        </div>

        <div class="product-grid-5" id="productGrid">
            <?php foreach($products as $sp): ?>
                <a href="product_detail.php?id=<?php echo $sp['id']; ?>" class="product-card fpt-card" 
                   data-cat="<?php echo $sp['cat']; ?>" data-price="<?php echo $sp['price']; ?>"
                   data-name="<?php echo $sp['name']; ?>" data-img="<?php echo $sp['img']; ?>" data-desc="<?php echo $sp['desc']; ?>">
                    
                    <?php if($sp['sale']): ?>
                        <div class="badge-sale"><?php echo $sp['sale']; ?></div>
                    <?php endif; ?>

                    <div class="img-wrap">
                        <img src="<?php echo $sp['img']; ?>" alt="Ảnh">
                        <button class="btn-quickview">👁️ Xem Nhanh</button>
                    </div>
                    
                    <h3 class="product-name"><?php echo $sp['name']; ?></h3>
                    
                    <div class="rating">
                        <?php echo renderStars($sp['rating']); ?> <span>(<?php echo rand(50, 500); ?> đánh giá)</span>
                    </div>

                    <div class="product-price">
                        <?php echo number_format($sp['price'], 0, ',', '.'); ?> ₫
                        <?php if($sp['old'] > 0): ?>
                            <span class="price-old"><?php echo number_format($sp['old'], 0, ',', '.'); ?> ₫</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal-overlay" id="quickViewModal">
        <div class="modal-content">
            <button class="btn-close">&times;</button>
            <div class="modal-left">
                <img id="modalImg" src="" alt="Zoom">
            </div>
            <div class="modal-right">
                <h2 id="modalName" style="margin-bottom: 15px; font-size: 24px; color: var(--text-main);">Tên</h2>
                <div id="modalPrice" style="color: var(--secondary); font-size: 32px; font-weight: bold; margin-bottom: 20px;">0 ₫</div>
                <div id="modalDesc" style="color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">Mô tả</div>
                <button class="btn btn-buy-now" style="width: 100%;">TỚI TRANG CHI TIẾT SẢN PHẨM</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/script_product.js"></script>
</body>
</html>