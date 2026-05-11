<?php
$host = 'localhost';
$dbname = 'fd-tech';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

$cat = isset($_GET['cat']) ? $_GET['cat'] : '';
$products = [];

try {
    if ($cat !== '') {
        $stmt = $pdo->prepare("SELECT id, name, price, image_url, description FROM products WHERE category_id = :cat ORDER BY id DESC");
        $stmt->execute(['cat' => $cat]);
    } else {
        $stmt = $pdo->prepare("SELECT id, name, price, image_url, description FROM products ORDER BY id DESC");
        $stmt->execute();
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<h3 style='color:red; text-align:center;'>Lỗi truy vấn SQL: " . $e->getMessage() . "</h3>");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>FD Tech - Danh sách sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <style>
        .product-card { position: relative; }
        .product-card a { text-decoration: none; color: inherit; display: block; }
        .product-card:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container" style="margin-top: 30px;">
        <h2 class="section-title">
            <?= $cat !== '' ? 'Danh mục: ' . htmlspecialchars(strtoupper($cat)) : 'Tất cả sản phẩm' ?>
        </h2>
        <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $row): ?>
                    <div class="product-card">
                        <a href="product_detail.php?id=<?= $row['id'] ?>">
                            <?php if(isset($row['is_promotion']) && $row['is_promotion'] == 1): ?>
                                <span style="position:absolute; top:5px; left:5px; background:red; color:white; padding:2px 8px; font-size:10px; border-radius:4px; z-index:1;">SALE</span>
                            <?php endif; ?>
                            <img src="<?= htmlspecialchars($row['image_url']) ?>" style="width: 100%; height: 150px; object-fit: contain; margin-bottom: 10px; border-radius: 8px;">
                            <h3 style="font-size: 14px; margin-bottom: 8px;"><?= htmlspecialchars($row['name']) ?></h3>
                            <p style="color: #ee4d2d; font-weight: bold;"><?= number_format($row['price'], 0, ',', '.') ?> VNĐ</p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: span 4; text-align: center;">Chưa có sản phẩm nào trong danh mục này.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>