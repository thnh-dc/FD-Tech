<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/database.php'; 
require_once __DIR__ . '../../auth/check_admin.php';

// Tự động tạo nhà cung cấp mẫu nếu database trống
$checkSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
if ($checkSuppliers == 0) {
    $pdo->query("INSERT INTO suppliers (name, phone, address) VALUES 
        ('Nhà Phân Phối FPT Synnex', '02473007108', 'Tòa nhà FPT, Cầu Giấy, Hà Nội'),
        ('ASUS Việt Nam', '18006588', 'Lầu 7, Tòa nhà Viettel, Quận 10, TP. HCM')
    ");
}

$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Lấy đầy đủ thuộc tính để hiển thị trong select box hàng hóa
$products = $pdo->query("SELECT id, name, price, stock_quantity FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// LẤY DANH SÁCH SẢN PHẨM SẮP HẾT HÀNG (STOCK <= 5) ĐỂ HIỂN THỊ KHỐI CẢNH BÁO MÀU ĐỎ
$low_stock_products = $pdo->query("SELECT id, name, stock_quantity FROM products WHERE stock_quantity <= 5 ORDER BY stock_quantity ASC")->fetchAll(PDO::FETCH_ASSOC);

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $admin_id = $_SESSION['user_id'] ?? 1; 

    $product_ids = $_POST['product_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $import_prices = $_POST['import_prices'] ?? [];

    if (!empty($supplier_id) && !empty($product_ids)) {
        try {
            $pdo->beginTransaction();

            // 1. Tạo mới một đơn hàng nhập
            $stmt_order = $pdo->prepare("INSERT INTO import_orders (supplier_id, admin_id, total_amount, note) VALUES (?, ?, 0, ?)");
            $stmt_order->execute([$supplier_id, $admin_id, $note]);
            $import_order_id = $pdo->lastInsertId();

            $total_amount = 0;

            // 2. Chuẩn bị các câu lệnh chi tiết và tăng kho vật lý
            $stmt_detail = $pdo->prepare("INSERT INTO import_order_details (import_order_id, product_id, quantity, import_price) VALUES (?, ?, ?, ?)");
            $stmt_update_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");

            foreach ($product_ids as $index => $product_id) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($import_prices[$index] ?? 0);

                if ($product_id > 0 && $qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $total_amount += $subtotal;

                    // Lưu chi tiết phiếu nhập
                    $stmt_detail->execute([$import_order_id, $product_id, $qty, $price]);
                    
                    // Thực thi tăng số lượng tồn kho vật lý
                    $stmt_update_stock->execute([$qty, $product_id]);
                }
            }

            // 3. Cập nhật lại chính xác tổng số tiền hóa đơn
            $stmt_update_total = $pdo->prepare("UPDATE import_orders SET total_amount = ? WHERE id = ?");
            $stmt_update_total->execute([$total_amount, $import_order_id]);

            $pdo->commit();
            header("Location: list_imports.php?msg=" . urlencode("Tạo chứng từ nhập kho thành công!"));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $msg = "Hệ thống lỗi, không thể lập phiếu nhập: " . $e->getMessage();
        }
    } else {
        $msg = "Vui lòng chọn đầy đủ nhà cung cấp đối tác và ít nhất 1 linh kiện hàng hóa!";
    }
}

$page_title = 'Tạo phiếu nhập kho';
$page_icon = 'fa-solid fa-file-import';
// Nhúng tệp CSS được tách biệt riêng cho trang tạo phiếu nhập hàng
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/add_import.css">';

include 'includes/header.php';
?>

<div class="import-container">
    <div class="import-card">
        <h2 class="import-header-title">
            <i class="fa-solid fa-file-import"></i> 
            Tạo phiếu nhập kho hàng
        </h2>
        <p class="import-header-sub">Hệ thống ghi nhận chứng từ và tự động cộng dồn số lượng tồn kho vật lý của sản phẩm.</p>

        <?php if (!empty($low_stock_products)): ?>
            <div style="background: #fff5f5; border-left: 4px solid #ef4444; padding: 15px; border-radius: 6px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h4 style="margin: 0 0 10px 0; color: #e53e3e; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-triangle-exclamation" style="animation: pulse 1.5s infinite;"></i> 
                    CẢNH BÁO: LINH KIỆN SẮP HẾT HOẶC ĐÃ CHÁY HÀNG CẦN NHẬP BỔ SUNG
                </h4>
                <ul style="margin: 0; padding-left: 20px; color: #4a5568; font-size: 0.85rem; line-height: 1.6;">
                    <?php foreach ($low_stock_products as $lp): ?>
                        <li>
                            <strong><?= htmlspecialchars($lp['name']) ?></strong> 
                            (Mã số: #<?= $lp['id'] ?>) - 
                            <span style="color: #e53e3e; font-weight: bold;">Tồn kho hiện tại: <?= $lp['stock_quantity'] ?> cái</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($msg)): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="add_import.php">
            
            <div class="import-meta-grid">
                <div>
                    <label class="form-group-label"><i class="fa-solid fa-handshake"></i> Chọn nhà cung cấp đối tác</label>
                    <select name="supplier_id" class="form-control-select" required>
                        <option value="">-- Chọn đối tác cung ứng --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-group-label"><i class="fa-solid fa-note-sticky"></i> Ghi chú nội bộ phiếu nhập</label>
                    <textarea name="note" class="form-control-textarea" rows="2" placeholder="Ví dụ: Nhập hàng đợt khuyến mãi, hàng bảo hành, đối tác giao chậm..."></textarea>
                </div>
            </div>

            <div class="import-items-section">
                <h3 class="section-title"><i class="fa-solid fa-boxes-stacked"></i> Danh sách linh kiện nhập kho</h3>
                
                <div class="import-table-wrapper">
                    <table class="import-table">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm linh kiện PC / Laptop</th>
                                <th style="width: 150px; text-align: center;">Số lượng nhập</th>
                                <th style="width: 200px; text-align: right;">Đơn giá nhập kho (₫)</th>
                                <th style="width: 180px; text-align: right;">Thành tiền dự tính</th>
                                <th style="width: 70px; text-align: center;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="import-items-wrapper">
                            <tr class="import-item-row">
                                <td>
                                    <select name="product_ids[]" class="form-control-select product-select" required>
                                        <option value="">-- Click chọn linh kiện sản phẩm --</option>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>">
                                                <?= htmlspecialchars($p['name']) ?> (Kho hiện tại: <?= $p['stock_quantity'] ?> cái)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantities[]" class="form-control-input qty-input" value="1" min="1" required style="text-align: center;">
                                </td>
                                <td>
                                    <input type="number" name="import_prices[]" class="form-control-input price-input" step="any" min="0" placeholder="0" required style="text-align: right;">
                                </td>
                                <td style="text-align: right;">
                                    <span class="row-subtotal">0₫</span>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn-remove-row" title="Xóa dòng này">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" id="btn-add-row" class="btn-import btn-import-outline">
                    <i class="fa-solid fa-plus"></i> Thêm sản phẩm linh kiện mới
                </button>
            </div>

            <div class="import-summary-box">
                <p class="summary-label">TỔNG GIÁ TRỊ PHIẾU NHẬP KHO (PHẢI THANH TOÁN ĐỐI TÁC):</p>
                <h2 id="grand-total" class="summary-value">0₫</h2>
            </div>

            <div class="import-actions-bar">
                <a href="list_imports.php" class="btn-import btn-import-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại lịch sử
                </a>
                <button type="submit" class="btn-import btn-import-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Hoàn tất nhập kho
                </button>
            </div>

        </form>
    </div>
</div>

</main>
</div>
<script src="../assets/js/script_dashboard.js"></script>
<script src="../assets/js/add_import.js"></script>
</body>
</html>