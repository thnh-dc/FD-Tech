<?php
session_start();
include '../config/database.php';
require_once __DIR__ . '/check_admin.php';

// Tiếp nhận tham số từ bộ lọc trên giao diện
$filter = $_GET['filter'] ?? 'all'; 
$sort = $_GET['sort'] ?? 'sold_desc'; 
$search = trim($_GET['search'] ?? '');

try {
    // Câu lệnh SQL tối ưu: SUM số lượng từ bảng order_items dựa trên đúng cấu trúc database của bạn
    $sql = "SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.stock_quantity, 
                p.image_url,
                c.name AS cat_name,
                COALESCE((SELECT SUM(oi.quantity) FROM order_items oi WHERE oi.product_id = p.id), 0) as total_sold
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE ?";

    $params = ["%$search%"];

    // Phân hệ xử lý tính năng nâng cao theo yêu cầu của nhiệm vụ
    if ($filter === 'low_stock') {
        $sql .= " AND p.stock_quantity <= 5"; // Cảnh báo sắp hết hàng
    } elseif ($filter === 'overstock') {
        // Hàng tồn nhiều (trên 15 cái) nhưng sức bán bằng 0 => Hàng ế lâu ngày
        $sql .= " AND p.stock_quantity >= 15 AND (SELECT SUM(oi.quantity) FROM order_items oi WHERE oi.product_id = p.id) IS NULL";
    } elseif ($filter === 'out_of_stock') {
        $sql .= " AND p.stock_quantity <= 0"; // Cháy hàng
    }

    // Xử lý sắp xếp dữ liệu bảng
    if ($sort === 'sold_desc') {
        $sql .= " ORDER BY total_sold DESC";
    } elseif ($sort === 'stock_desc') {
        $sql .= " ORDER BY p.stock_quantity DESC";
    } elseif ($sort === 'stock_asc') {
        $sql .= " ORDER BY p.stock_quantity ASC";
    } else {
        $sql .= " ORDER BY p.id DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- KHỐI ĐẾM THÔNG TIN THỐNG KÊ NHANH (Mục Tổng quan) ---
    $total_items = $pdo->query("SELECT SUM(stock_quantity) FROM products")->fetchColumn() ?? 0;
    $low_stock_count = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity > 0 AND stock_quantity <= 5")->fetchColumn();
    $out_of_stock_count = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= 0")->fetchColumn();

} catch (PDOException $e) {
    die("Lỗi truy vấn kho hàng: " . $e->getMessage());
}

$page_title = 'Kiểm kê kho hàng';
$page_icon = 'fa-solid fa-warehouse';
// Khai báo nhúng tệp CSS độc lập cho trang kho hàng
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/warehouse.css">';

include 'includes/header.php';
?>

<div class="warehouse-container">
    
    <div class="warehouse-stats-grid">
        <div class="stat-card-box">
            <div class="stat-icon-wrapper" style="background: #e0f2fe; color: #0369a1;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="stat-info-text">
                <p>TỔNG LINH KIỆN TRONG KHO</p>
                <h3><?= number_format($total_items) ?> <span style="font-size: 0.9rem; font-weight: normal; color: #64748b;">cái</span></h3>
            </div>
        </div>

        <div class="stat-card-box">
            <div class="stat-icon-wrapper" style="background: #ffedd5; color: #c2410c;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-info-text">
                <p>SẢN PHẨM SẮP HẾT HÀNG</p>
                <h3><?= $low_stock_count ?> <span style="font-size: 0.9rem; font-weight: normal; color: #64748b;">mã</span></h3>
            </div>
        </div>

        <div class="stat-card-box">
            <div class="stat-icon-wrapper" style="background: #fee2e2; color: #b91c1c;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="stat-info-text">
                <p>SẢN PHẨM ĐÃ CHÁY HÀNG</p>
                <h3><?= $out_of_stock_count ?> <span style="font-size: 0.9rem; font-weight: normal; color: #64748b;">mã</span></h3>
            </div>
        </div>
    </div>

    <div class="warehouse-toolbar">
        <form method="GET" action="manage_warehouse.php" class="toolbar-flex-layout">
            
            <div class="filter-btn-group">
                <a href="manage_warehouse.php?filter=all&sort=<?= $sort ?>&search=<?= urlencode($search) ?>" 
                   class="btn-filter-item" style="<?= $filter === 'all' ? 'background: #0f172a; color:#fff; border-color:#0f172a;' : 'background:#fff; color:#334155;' ?>">
                    <i class="fa-solid fa-list"></i> Tất cả linh kiện
                </a>
                <a href="manage_warehouse.php?filter=low_stock&sort=<?= $sort ?>&search=<?= urlencode($search) ?>" 
                   class="btn-filter-item" style="<?= $filter === 'low_stock' ? 'background: #ea580c; color:#fff; border-color:#ea580c;' : 'background:#fff; color:#334155;' ?>">
                    <i class="fa-solid fa-triangle-exclamation"></i> Sắp hết hàng (≤5)
                </a>
                <a href="manage_warehouse.php?filter=out_of_stock&sort=<?= $sort ?>&search=<?= urlencode($search) ?>" 
                   class="btn-filter-item" style="<?= $filter === 'out_of_stock' ? 'background: #ef4444; color:#fff; border-color:#ef4444;' : 'background:#fff; color:#334155;' ?>">
                    <i class="fa-solid fa-circle-minus"></i> Đã hết hàng (0)
                </a>
                <a href="manage_warehouse.php?filter=overstock&sort=<?= $sort ?>&search=<?= urlencode($search) ?>" 
                   class="btn-filter-item" style="<?= $filter === 'overstock' ? 'background: #7c3aed; color:#fff; border-color:#7c3aed;' : 'background:#fff; color:#334155;' ?>">
                    <i class="fa-solid fa-hourglass-half"></i> Hàng tồn đọng lâu ngày
                </a>
            </div>

            <div class="search-sort-group">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                
                <select name="sort" class="select-sort-control">
                    <option value="sold_desc" <?= $sort === 'sold_desc' ? 'selected' : '' ?>>Sắp xếp: Bán chạy nhất</option>
                    <option value="stock_desc" <?= $sort === 'stock_desc' ? 'selected' : '' ?>>Sắp xếp: Tồn kho giảm dần</option>
                    <option value="stock_asc" <?= $sort === 'stock_asc' ? 'selected' : '' ?>>Sắp xếp: Tồn kho tăng dần</option>
                </select>

                <div class="search-input-box">
                    <i class="fa-solid fa-magnifying-glass icon-search-embedded"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="<?= htmlspecialchars($search) ?>" 
                        class="input-search-field" 
                        placeholder="Tìm theo tên linh kiện..."
                    >
                </div>
            </div>
            
        </form>
    </div>

    <div class="warehouse-table-wrapper">
        <table class="warehouse-data-table">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Mã sản phẩm</th>
                    <th>Thông tin linh kiện</th>
                    <th style="text-align: right;">Đơn giá bán lẻ</th>
                    <th style="text-align: center;">Tổng đã bán</th>
                    <th style="text-align: center;">Số lượng tồn kho</th>
                    <th>Trạng thái hiển thị</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $p): 
                        $stock = (int)$p['stock_quantity'];
                        $img_url = $p['image_url'];
                        $src = (filter_var($img_url, FILTER_VALIDATE_URL)) ? $img_url : "../upload/product_image/" . $img_url;
                        if (empty($img_url)) {
                            $src = "../assets/images/logo-fd.jpg";
                        }
                    ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold; color: #64748b;">#<?= $p['id'] ?></td>
                            <td>
                                <div class="product-info-cell">
                                    <img src="<?= htmlspecialchars($src) ?>" class="product-img-thumbnail" alt="Linh kiện" onerror="this.src='../assets/images/logo-fd.jpg'">
                                    <div>
                                        <h4 class="product-name-txt"><?= htmlspecialchars($p['name']) ?></h4>
                                        <p class="product-category-sub"><i class="fa-solid fa-layer-group"></i> <?= htmlspecialchars($p['cat_name'] ?? 'Chưa phân loại') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #0f172a;">
                                <?= number_format($p['price'], 0, ',', '.') ?>₫
                            </td>
                            <td style="text-align: center; font-weight: bold; color: #2563eb;">
                                <i class="fa-solid fa-cart-shopping" style="font-size: 0.8rem; color:#94a3b8;"></i> <?= (int)$p['total_sold'] ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($stock <= 0): ?>
                                    <span class="badge-stock badge-danger"><i class="fa-solid fa-circle-exmark"></i> Cháy hàng: 0 cái</span>
                                <?php elseif ($stock <= 5): ?>
                                    <span class="badge-stock badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Nguy cấp: <?= $stock ?> cái</span>
                                <?php else: ?>
                                    <span class="badge-stock badge-in"><i class="fa-solid fa-circle-check"></i> An toàn: <?= $stock ?> cái</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($stock <= 0): ?>
                                    <span class="badge-visibility" style="color: #ef4444;">
                                        <i class="fa-solid fa-eye-slash"></i> Ẩn tự động
                                    </span>
                                <?php else: ?>
                                    <span class="badge-visibility" style="color: #16a34a;">
                                        <i class="fa-solid fa-eye"></i> Đang hiển thị
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-box">
                                <i class="fa-solid fa-warehouse"></i>
                                <h3>Kho dữ liệu trống hoặc không tìm thấy kết quả</h3>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</div>
<script src="../assets/js/script_dashboard.js"></script>
<script src="../assets/js/warehouse.js"></script>
</body>
</html>