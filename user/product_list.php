<<<<<<< Updated upstream
!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm - FD Tech</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../assets/css/components/product_card.css">
</head>
<body style="background-color: #FFFFFF; font-family: 'Inter', sans-serif;">

    <div style="background: #0B2A4A; padding: 20px; color: white; text-align: center; font-size: 24px; font-weight: bold;">FD TECH - HEADER MẪU</div>

    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 16px; margin-bottom: 64px;">
        
        <div style="padding: 16px 0; font-size: 14px; color: #6C757D;">
            <a href="index.php" style="color: #0B2A4A; font-weight: 600; text-decoration: none;">Trang chủ</a> / <span>Tất cả sản phẩm</span>
        </div>

        <div class="layout-with-sidebar" style="display: flex; gap: 32px; align-items: flex-start;">
            
            <aside class="sidebar" style="width: 260px; flex-shrink: 0; background: #FFFFFF; padding: 24px; border: 1px solid #F4F6F9; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h2 style="font-size: 20px; color: #0B2A4A; margin-bottom: 24px; border-bottom: 2px solid #F4F6F9; padding-bottom: 12px;">BỘ LỌC CHI TIẾT</h2>
                <form action="#">
                    <h3 style="font-size: 14px; margin-bottom: 8px;">Tìm kiếm</h3>
                    <input type="text" style="width: 100%; padding: 10px; border: 1px solid #6C757D; border-radius: 4px; margin-bottom: 16px;" placeholder="Tên sản phẩm...">

                    <h3 style="font-size: 14px; margin-bottom: 8px;">Danh mục</h3>
                    <label style="display: block; margin-bottom: 8px; cursor: pointer;"><input type="radio" name="cat" checked> Tất cả</label>
                    <label style="display: block; margin-bottom: 8px; cursor: pointer;"><input type="radio" name="cat"> Headphones</label>
                    <label style="display: block; margin-bottom: 8px; cursor: pointer;"><input type="radio" name="cat"> Keyboards</label>
                    <label style="display: block; margin-bottom: 8px; cursor: pointer;"><input type="radio" name="cat"> Mouse</label>
                    <label style="display: block; margin-bottom: 16px; cursor: pointer;"><input type="radio" name="cat"> Others</label>

                    <button type="button" style="width: 100%; padding: 12px; background: #23B5D3; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s;">ÁP DỤNG LỌC</button>
                </form>
            </aside>

            <main class="main-content" style="flex-grow: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="font-size: 24px; color: #0B2A4A; margin: 0;">SẢN PHẨM NỔI BẬT</h2>
                    <span style="color: #6C757D; font-size: 14px;">Hiển thị 8 sản phẩm</span>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px;">
                    
                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=1" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Mouse 1</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">MOUSE</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Chuột Gaming God Cane VD 31</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">1.400.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=2" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Keyboard 1</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">KEYBOARDS</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Bàn phím cơ Light God Game</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">1.460.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=3" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Headphone 1</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">HEADPHONES</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Tai nghe F9 Night God Game</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">1.400.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=4" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Sạc</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">OTHERS</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Củ sạc nhanh 30W Ighti God</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">1.450.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=5" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Lót Chuột</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">OTHERS</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Lót chuột RGB FD Matrix Cực Đại</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">350.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=6" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Keyboard 2</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">KEYBOARDS</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Bàn phím giả cơ FD K100</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">450.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=7" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Mouse 2</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">MOUSE</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Chuột không dây FD Office M1</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">250.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                    <div style="border: 1px solid #F4F6F9; border-radius: 8px; padding: 16px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;">
                        <a href="product_detail.php?id=8" style="text-decoration: none;">
                            <div style="height: 180px; background: #F4F6F9; margin-bottom: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-weight:bold; color: #23B5D3;">Ảnh Giá đỡ</div>
                            <p style="font-size: 12px; color: #6C757D; font-weight: bold;">OTHERS</p>
                            <h3 style="font-size: 16px; color: #333333; margin: 8px 0; height: 40px; overflow: hidden;">Giá đỡ tai nghe FD Stand Pro</h3>
                            <p style="font-size: 20px; font-weight: bold; color: #0B2A4A; margin-bottom: 16px;">200.000 đ</p>
                        </a>
                        <button style="width: 100%; padding: 10px; background: #23B5D3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Thêm vào giỏ</button>
                    </div>

                </div>
                
                <div style="display: flex; justify-content: center; gap: 8px; margin-top: 40px;">
                    <a href="#" style="padding: 8px 16px; background: #0B2A4A; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">1</a>
                    <a href="#" style="padding: 8px 16px; background: white; color: #0B2A4A; border: 1px solid #F4F6F9; text-decoration: none; border-radius: 4px; font-weight: bold;">2</a>
                    <a href="#" style="padding: 8px 16px; background: white; color: #0B2A4A; border: 1px solid #F4F6F9; text-decoration: none; border-radius: 4px; font-weight: bold;">3</a>
                </div>

            </main>
        </div>
    </div>

    <script src="../assets/js/product.js"></script>
=======
<?php
// Mock Data: Mảng giả lập Database theo chuẩn
$products = [
    ['id' => 1, 'name' => 'Bàn phím cơ Light God Game FD 37 - Bản Pro 2026', 'price' => 1460000, 'old_price' => 1800000, 'discount' => '-18%', 'specs' => ['Quang học', 'RGB', 'Full-size'], 'rating' => 4.9, 'reviews' => 124, 'img' => 'https://via.placeholder.com/200x200/23B5D3/FFFFFF?text=Keyboard'],
    ['id' => 2, 'name' => 'Chuột Gaming God Cane VD 31 Siêu Nhẹ', 'price' => 1400000, 'old_price' => 1500000, 'discount' => '-6%', 'specs' => ['16000 DPI', 'Wireless', '80g'], 'rating' => 5.0, 'reviews' => 342, 'img' => 'https://via.placeholder.com/200x200/0B2A4A/FFFFFF?text=Mouse'],
    ['id' => 3, 'name' => 'Tai nghe vòm 7.1 F9 Night God Game', 'price' => 1400000, 'old_price' => 0, 'discount' => '', 'specs' => ['Over-ear', '7.1 Surround', 'Mic AI'], 'rating' => 4.8, 'reviews' => 56, 'img' => 'https://via.placeholder.com/200x200/23B5D3/FFFFFF?text=Headset'],
    ['id' => 4, 'name' => 'Lót chuột RGB Matrix Cực Đại Kín Bàn', 'price' => 350000, 'old_price' => 500000, 'discount' => '-30%', 'specs' => ['800x300mm', 'LED RGB', 'Cao su'], 'rating' => 4.7, 'reviews' => 890, 'img' => 'https://via.placeholder.com/200x200/0B2A4A/FFFFFF?text=Mousepad'],
    ['id' => 5, 'name' => 'Củ sạc nhanh GaN 30W Ighti God', 'price' => 450000, 'old_price' => 600000, 'discount' => '-25%', 'specs' => ['30W', 'Type-C', 'GaN'], 'rating' => 4.9, 'reviews' => 210, 'img' => 'https://via.placeholder.com/200x200/23B5D3/FFFFFF?text=Charger'],
    ['id' => 6, 'name' => 'Bàn phím giả cơ K100 (Bản Tiêu Chuẩn)', 'price' => 450000, 'old_price' => 0, 'discount' => '', 'specs' => ['Membrane', 'Rainbow', 'TKL'], 'rating' => 4.5, 'reviews' => 67, 'img' => 'https://via.placeholder.com/200x200/0B2A4A/FFFFFF?text=Keyboard+2'],
    ['id' => 7, 'name' => 'Chuột không dây FD Office M1 Silent', 'price' => 250000, 'old_price' => 350000, 'discount' => '-28%', 'specs' => ['Silent', 'Bluetooth', 'Pin 1 năm'], 'rating' => 4.6, 'reviews' => 450, 'img' => 'https://via.placeholder.com/200x200/23B5D3/FFFFFF?text=Office+Mouse'],
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tất cả sản phẩm - FD Tech</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/product.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="container" style="margin-bottom: 64px;">
        <div style="padding: 16px 0; font-size: 14px; color: #6C757D;">
            <a href="index.php" style="color: #0B2A4A; font-weight: 600;">Trang chủ</a> / <span>Thiết bị công nghệ</span>
        </div>

        <h2 style="color: #0B2A4A; font-size: 24px; margin-bottom: 24px; border-bottom: 2px solid #F4F6F9; padding-bottom: 12px;">ĐIỆN THOẠI & PHỤ KIỆN NỔI BẬT</h2>

        <div class="grid-5-cols">
            <?php foreach($products as $sp): ?>
                <a href="product_detail.php?id=<?php echo $sp['id']; ?>" class="product-card">
                    
                    <?php if($sp['discount'] != ''): ?>
                        <span class="discount-badge"><?php echo $sp['discount']; ?></span>
                    <?php endif; ?>

                    <div class="product-img-wrap">
                        <img src="<?php echo $sp['img']; ?>" alt="<?php echo $sp['name']; ?>">
                    </div>

                    <h3 class="product-name"><?php echo $sp['name']; ?></h3>
                    
                    <div class="specs-tags">
                        <?php foreach($sp['specs'] as $spec): ?>
                            <span><?php echo $spec; ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="price-box">
                        <div class="price-current"><?php echo number_format($sp['price'], 0, ',', '.'); ?> ₫</div>
                        <?php if($sp['old_price'] > 0): ?>
                            <div class="price-old"><?php echo number_format($sp['old_price'], 0, ',', '.'); ?> ₫</div>
                        <?php endif; ?>
                        
                        <div class="rating">
                            ★★★★★ <span>(<?php echo $sp['reviews']; ?>)</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
>>>>>>> Stashed changes
</body>
</html>