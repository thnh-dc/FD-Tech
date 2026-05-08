<?php  
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// --- THIẾT LẬP KẾT NỐI MYSQL ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fd-tech"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// --- TRUY VẤN DỮ LIỆU SẢN PHẨM ---
$sql = "SELECT * FROM products WHERE id = ?"; 
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sp = $result->fetch_assoc();
} else {
    die("<h2 style='text-align:center; margin-top:50px;'>Sản phẩm không tồn tại hoặc đã bị xóa!</h2>");
}

$stmt->close();
$conn->close();

include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/product_style.css?v=<?php echo time(); ?>">

<header class="header-vip">
    <div class="container header-flex">
        <h2>FD TECH</h2>
        <div class="header-tools">
            <div class="cart-wrap">🛒 Giỏ hàng <span class="cart-badge" id="cartBadge">0</span></div>
        </div>
    </div>
</header>

<div class="container" style="margin-bottom: 64px;">
    
    <div style="padding: 16px 0; font-size: 14px; color: var(--text-muted);">
        <a href="index.php" style="color: var(--primary); font-weight: 600;">Trang chủ</a> / 
        <a href="product_list.php" style="color: var(--primary); font-weight: 600;">Sản phẩm</a> / 
        <span style="color: var(--text-dark);"><?php echo htmlspecialchars($sp['name']); ?></span>
    </div>

    <div class="detail-layout">
        
        <div class="detail-left">
            <div id="zoomContainer" class="main-img-view">
                <img id="zoomImage" src="<?php echo htmlspecialchars($sp['image_url']); ?>" alt="Ảnh">
            </div>
            <div class="thumb-list">
                <div class="thumb-item active"><img src="<?php echo htmlspecialchars($sp['image_url']); ?>"></div>
                <div class="thumb-item"><img src="https://via.placeholder.com/400/F4F6F9/23B5D3?text=Goc+2"></div>
            </div>
            <p style="text-align: center; font-size: 12px; margin-top: 10px; color: var(--text-muted);">🔍 Rê chuột vào ảnh để phóng to</p>
        </div>
        
        <div class="detail-right">
            <span style="background: var(--bg-light); color: var(--secondary); padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: bold;"><?php echo htmlspecialchars($sp['cat'] ?? 'Điện tử'); ?></span>
            
            <h1 class="detail-title"><?php echo htmlspecialchars($sp['name']); ?></h1>
            
            <div style="font-size: 40px; color: var(--primary); font-weight: 900; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 24px;">
                <?php echo number_format($sp['price'], 0, ',', '.'); ?> ₫
            </div>
            
            <div style="margin-bottom: 24px; font-size: 16px;">
                Tình trạng kho: <span style="color: #28A745; font-weight: bold;">Còn hàng</span>
            </div>

            <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 32px;">
                <?php echo $sp['desc'] ?? 'Đang cập nhật mô tả...'; ?>
            </p>
            
            <input type="hidden" id="pd_id" value="<?php echo $id; ?>">
            <input type="hidden" id="pd_name" value="<?php echo htmlspecialchars($sp['name']); ?>">
            <input type="hidden" id="pd_price" value="<?php echo $sp['price']; ?>">
            <input type="hidden" id="pd_img" value="<?php echo htmlspecialchars($sp['image_url']); ?>">

            <form action="#" style="display: flex; gap: 16px;">
                <div class="qty-box">
                    <button type="button" id="btnMinus" class="qty-btn">-</button>
                    <input type="text" id="qtyInput" value="1" class="qty-input" readonly>
                    <button type="button" id="btnPlus" class="qty-btn">+</button>
                </div>
                <button type="button" id="btnAddToCart" style="flex: 1; background: var(--secondary); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;">THÊM VÀO GIỎ HÀNG</button>
            </form>
        </div>
    </div>
</div>
<script src="../assets/js/product_script.js"></script>
<?php include '../includes/footer.php'?>