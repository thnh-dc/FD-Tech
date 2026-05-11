<?php  
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$host = 'localhost';
$dbname = 'fd-tech';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $sp = $stmt->fetch();

    if (!$sp) {
        die("<h2 style='text-align:center; margin-top:50px;'>Sản phẩm không tồn tại!</h2>");
    }
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/product_style.css?v=<?php echo time(); ?>">

<header class="header">
    <div class="container header-flex">
        <h2>FD TECH</h2>
    </div>
</header>

<main class="container product-detail-container">
    <div class="product-layout" style="display: flex; gap: 40px; margin-top: 20px;">
        <div class="product-image-section" style="flex: 1;">
            <?php 
                $img_src = ''; 
                
                if (!empty($sp['image_url'])) {
                    $img_src = $sp['image_url'];
                } else {
                    $img_src = 'https://via.placeholder.com/500x500?text=Chua+Co+Anh'; 
                }
            ?>
            <img src="<?= htmlspecialchars($img_src); ?>" alt="<?= htmlspecialchars($sp['name'] ?? 'Sản phẩm'); ?>" style="width: 100%; border-radius: 8px; object-fit: contain;">
        </div>

        <div class="product-info-section" style="flex: 1;">
            <h1><?= htmlspecialchars($sp['name'] ?? 'Đang cập nhật'); ?></h1>
            <div style="font-size: 24px; color: #d9534f; font-weight: bold; margin: 15px 0;">
                <?= number_format($sp['price'] ?? 0, 0, ',', '.'); ?> VNĐ
            </div>
            
            <p style="color: #666; line-height: 1.6; margin-bottom: 30px;">
                <?= $sp['desc'] ?? 'Đang cập nhật mô tả chi tiết cho sản phẩm này...'; ?>
            </p>

            <form action="../user/action_cart.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="hidden" name="product_id" value="<?= $id; ?>">
                
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label>Số lượng:</label>
                    <input type="number" name="quantity" value="1" min="1" style="width: 60px; padding: 5px; text-align: center;">
                </div>

                <div style="display: flex; gap: 15px; margin-top: 10px;">
                    <button type="submit" name="action_type" value="add_to_cart" style="flex: 1; padding: 12px; background: #fff; color: #333; border: 1px solid #ccc; cursor: pointer; font-weight: bold;">
                        THÊM VÀO GIỎ HÀNG
                    </button>
                    <button type="submit" name="action_type" value="buy_now" style="flex: 1; padding: 12px; background: #007bff; color: white; border: none; cursor: pointer; font-weight: bold;">
                        MUA NGAY
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>