<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/database.php'; 
require_once __DIR__ . '/check_admin.php';

// Tự động tạo 2 nhà cung cấp mẫu nếu database trống để bạn có dữ liệu test
$checkSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
if ($checkSuppliers == 0) {
    $pdo->query("INSERT INTO suppliers (name, phone, address) VALUES 
        ('Nhà Phân Phối FPT Synnex', '02473007108', 'Tòa nhà FPT, Cầu Giấy, Hà Nội'),
        ('ASUS Việt Nam', '18006588', 'Lầu 7, Tòa nhà Viettel, Quận 10, TP. HCM')
    ");
}

$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT id, name, price FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $admin_id = $_SESSION['user_id']; 

    $product_ids = $_POST['product_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $import_prices = $_POST['import_prices'] ?? [];

    if (!empty($supplier_id) && !empty($product_ids)) {
        try {
            $pdo->beginTransaction(); 

            $total_amount = 0;
            foreach ($product_ids as $index => $p_id) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($import_prices[$index] ?? 0);
                $total_amount += ($qty * $price);
            }

            $stmt_order = $pdo->prepare("INSERT INTO import_orders (supplier_id, admin_id, total_amount, status, note) VALUES (?, ?, ?, 'completed', ?)");
            $stmt_order->execute([$supplier_id, $admin_id, $total_amount, $note]);
            $import_order_id = $pdo->lastInsertId(); 

            $stmt_detail = $pdo->prepare("INSERT INTO import_order_details (import_order_id, product_id, quantity, import_price) VALUES (?, ?, ?, ?)");
            $stmt_update_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");

            foreach ($product_ids as $index => $p_id) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($import_prices[$index] ?? 0);

                if (!empty($p_id) && $qty > 0) {
                    $stmt_detail->execute([$import_order_id, $p_id, $qty, $price]);
                    $stmt_update_stock->execute([$qty, $p_id]);
                }
            }

            $pdo->commit(); 
            header("Location: list_imports.php?msg=Nhập kho thành công!");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack(); 
            $msg = "Có lỗi xảy ra: " . $e->getMessage();
        }
    } else {
        $msg = "Vui lòng chọn Nhà cung cấp và thêm ít nhất một sản phẩm hợp lệ!";
    }
}

$page_title = 'Tạo phiếu nhập kho';
include 'includes/header.php';
?>

<div class="admin-container" style="display: flex; min-height: 100vh; background: #f8fafc;">
    
    <main class="admin-main" style="flex: 1; padding: 30px;">
        <div class="header-page" style="margin-bottom: 25px;">
            <h2 style="color: #1e293b; font-size: 1.6rem; font-weight: 700; margin: 0;">📦 Tạo phiếu nhập kho hàng lẻ</h2>
            <p style="color: #64748b; margin: 5px 0 0 0;">Quản lý bổ sung nguồn hàng cung ứng vật lý cho hệ thống FD Tech</p>
        </div>

        <?php if (!empty($msg)): ?>
            <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="importForm">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
                <h3 style="font-size: 1.1rem; color: #334155; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Thông tin nhà cung ứng</h3>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #475569;">Chọn nhà cung cấp <span style="color:red;">*</span></label>
                        <select name="supplier_id" class="form-control" style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;" required>
                            <option value="">-- Chọn đối tác cấp hàng --</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?> (SĐT: <?= htmlspecialchars($sup['phone']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #475569;">Ghi chú nội dung đợt nhập hàng</label>
                        <input type="text" name="note" class="form-control" placeholder="Ví dụ: Nhập linh kiện laptop đợt cuối tháng..." style="width:100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
                <h3 style="font-size: 1.1rem; color: #334155; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Chi tiết danh sách hàng hóa</h3>
                <div id="import-items-wrapper">
                    <div style="display: grid; grid-template-columns: 3fr 1fr 1.5fr 1.5fr 40px; gap: 15px; margin-bottom: 10px; font-weight: bold; color: #64748b; font-size: 0.9rem;">
                        <div>Sản phẩm / Linh kiện cần nhập <span style="color:red;">*</span></div>
                        <div>Số lượng</div>
                        <div>Giá vốn nhập (₫)</div>
                        <div style="text-align: right; padding-right: 10px;">Thành tiền dự kiến</div>
                        <div></div>
                    </div>

                    <div class="import-item-row" style="display: grid; grid-template-columns: 3fr 1fr 1.5fr 1.5fr 40px; gap: 15px; margin-bottom: 12px; align-items: center;">
                        <div>
                            <select name="product_ids[]" class="form-control product-select" style="width:100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
                                <option value="">-- Chọn mặt hàng từ kho --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>"><?= htmlspecialchars($p['name']) ?> (Gốc: <?= number_format($p['price'], 0, ',', '.') ?>₫)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <input type="number" name="quantities[]" class="form-control qty-input" min="1" value="1" style="width:100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
                        </div>
                        <div>
                            <input type="number" name="import_prices[]" class="form-control price-input" min="0" placeholder="Nhập giá vốn" style="width:100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
                        </div>
                        <div class="row-subtotal" style="font-weight: 600; color: #334155; text-align: right; padding-right: 10px;">0₫</div>
                        <div>
                            <button type="button" class="btn-remove-row" style="background: none; border: none; color: #ef4444; font-size: 1.2rem; cursor: pointer;">✕</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="btn-add-product" style="background: #f1f5f9; color: #475569; border: 1px dashed #cbd5e1; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; margin-top: 10px;">
                    ➕ Thêm sản phẩm khác vào phiếu nhập kho
                </button>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; background: #1e293b; padding: 20px; border-radius: 8px; color: white;">
                <div>
                    <span style="color: #94a3b8; font-size: 0.95rem;">TỔNG TIỀN HÓA ĐƠN CẦN THANH TOÁN:</span>
                    <h2 id="total-import-price" style="margin: 5px 0 0 0; color: #38bdf8; font-size: 1.8rem; font-weight: 700;">0₫</h2>
                </div>
                <div>
                    <button type="submit" style="background: #10b981; color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; font-size: 1rem; cursor: pointer;">
                        📥 Xác nhận hoàn thành & Cộng dồn kho vật lý
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('import-items-wrapper');
    const btnAdd = document.getElementById('btn-add-product');
    const totalDisplay = document.getElementById('total-import-price');

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }

    function calculateTotal() {
        let grandTotal = 0;
        const rows = wrapper.querySelectorAll('.import-item-row');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            row.querySelector('.row-subtotal').textContent = formatMoney(subtotal);
            grandTotal += subtotal;
        });
        totalDisplay.textContent = formatMoney(grandTotal);
    }

    wrapper.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const originalPrice = selectedOption.getAttribute('data-price');
            if (originalPrice) {
                const row = e.target.closest('.import-item-row');
                const suggestedPrice = Math.round(parseFloat(originalPrice) * 0.75);
                row.querySelector('.price-input').value = suggestedPrice;
            }
            calculateTotal();
        }
    });

    wrapper.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            calculateTotal();
        }
    });

    wrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-row')) {
            const rows = wrapper.querySelectorAll('.import-item-row');
            if (rows.length > 1) {
                e.target.closest('.import-item-row').remove();
                calculateTotal();
            } else {
                alert('Một hóa đơn nhập kho bắt buộc phải có ít nhất 1 sản phẩm!');
            }
        }
    });

    btnAdd.addEventListener('click', function() {
        const firstRow = wrapper.querySelector('.import-item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('.product-select').selectedIndex = 0;
        newRow.querySelector('.qty-input').value = 1;
        newRow.querySelector('.price-input').value = '';
        newRow.querySelector('.row-subtotal').textContent = '0₫';
        wrapper.appendChild(newRow);
    });
    calculateTotal(); 
});
</script>