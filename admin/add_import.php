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

// Lấy thêm thuộc tính stock_quantity để hiển thị số lượng trong kho hiện tại
$products = $pdo->query("SELECT id, name, price, stock_quantity FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
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

<style>
/* Nhúng font chữ cao cấp */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

.premium-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    background: linear-gradient(135deg, #0f172a 30%, #2563eb);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
}

.title-underline {
    height: 4px;
    width: 60px;
    background: #2563eb;
    border-radius: 2px;
    margin-top: 8px;
}

.form-card {
    background: white; 
    padding: 28px; 
    border-radius: 12px; 
    box-shadow: 0 4px 20px rgba(0,0,0,0.04); 
    border: 1px solid #e2e8f0;
    margin-bottom: 25px;
}

.form-label-custom {
    display: block; 
    font-weight: 600; 
    margin-bottom: 8px; 
    color: #334155;
    font-size: 0.9rem;
}

.input-custom {
    width: 100%; 
    padding: 11px 14px; 
    border: 1px solid #cbd5e1; 
    border-radius: 8px;
    font-size: 0.95rem;
    color: #1e293b;
    background-color: #fff;
    transition: all 0.2s;
}

.input-custom:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
    outline: none;
}

.import-item-row {
    background: #f8fafc;
    padding: 14px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s;
}

@keyframes pulse-warning {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-pulse {
    animation: pulse-warning 2s infinite;
}
</style>

<?php
// Logic quét tự động linh kiện sắp hết hàng
$low_stock_products = $pdo->query("SELECT name, stock_quantity FROM products WHERE stock_quantity <= 5 ORDER BY stock_quantity ASC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="header-page" style="margin-bottom: 35px; display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px;">
    <div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
            <div style="background: #eff6ff; color: #2563eb; width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; box-shadow: inset 0 0 0 1px rgba(37,99,235,0.1);">
                <i class="fa-solid fa-file-import"></i>
            </div>
            <h2 class="premium-title">Tạo phiếu nhập kho hàng lẻ</h2>
        </div>
        <div class="title-underline"></div>
        <p style="color: #64748b; margin: 12px 0 0 0; font-size: 0.95rem; font-weight: 500;">Hệ thống cung ứng vật lý & Quản trị dòng vốn <span style="color: #2563eb; font-weight: 700;">FD TECH</span></p>
    </div>

    <?php if (!empty($low_stock_products)): ?>
        <div style="background: #ffffff; border: 1px solid #fed7aa; border-left: 4px solid #f97316; padding: 12px 18px; border-radius: 10px; max-width: 420px; flex: 1; box-shadow: 0 4px 12px rgba(249,115,22,0.08);">
            <div style="display: flex; align-items: center; gap: 8px; color: #ea580c; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                <i class="fa-solid fa-circle-exclamation animate-pulse"></i> Radar báo động tồn kho
            </div>
            <div style="display: grid; grid-template-columns: 1fr; gap: 5px;">
                <?php foreach ($low_stock_products as $p_low): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #475569;">
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px;">• <?= htmlspecialchars($p_low['name']) ?></span>
                        <span style="font-weight: 700; color: #dc2626;">Còn <?= $p_low['stock_quantity'] ?> cái</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($msg)): ?>
    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 14px; margin-bottom: 25px; border-radius: 8px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<form method="POST" id="importForm">
    <div class="form-card">
        <h3 style="font-size: 1.1rem; color: #0f172a; font-weight: 700; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-building-user" style="color: #2563eb; background: #eff6ff; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;"></i> Thông tin nguồn cấp hàng
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <div>
                <label class="form-label-custom">Nhà cung cấp đối tác <span style="color:#ef4444;">*</span></label>
                <select name="supplier_id" class="input-custom" required>
                    <option value="">-- Chọn đối tác cấp hàng --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?> (SĐT: <?= htmlspecialchars($sup['phone']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label-custom">Nội dung ghi chú đợt nhập</label>
                <input type="text" name="note" class="input-custom" placeholder="Ví dụ: Nhập linh kiện laptop đợt cuối tháng...">
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3 style="font-size: 1.1rem; color: #0f172a; font-weight: 700; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-boxes-stacked" style="color: #2563eb; background: #eff6ff; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;"></i> Danh mục hàng hóa tiếp nhận
        </h3>
        
        <div id="import-items-wrapper">
            <div style="display: grid; grid-template-columns: 3.5fr 1fr 1.5fr 1.5fr 40px; gap: 15px; margin-bottom: 12px; font-weight: 700; color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; padding-left: 14px;">
                <div>Sản phẩm / Linh kiện</div>
                <div style="text-align: center;">Số lượng</div>
                <div>Giá vốn (₫)</div>
                <div style="text-align: right; padding-right: 14px;">Thành tiền</div>
                <div></div>
            </div>

            <div class="import-item-row" style="display: grid; grid-template-columns: 3.5fr 1fr 1.5fr 1.5fr 40px; gap: 15px; margin-bottom: 12px; align-items: center;">
                <div>
                    <select name="product_ids[]" class="input-custom product-select" required>
                        <option value="">-- Chọn mặt hàng từ kho --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>">
                                <?= htmlspecialchars($p['name']) ?> &nbsp;---&nbsp; [Kho: <?= $p['stock_quantity'] ?>]
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <input type="number" name="quantities[]" class="input-custom qty-input" min="1" value="1" style="text-align: center;" required>
                </div>
                <div>
                    <input type="number" name="import_prices[]" class="input-custom price-input" min="0" placeholder="Giá vốn" required>
                </div>
                <div class="row-subtotal" style="font-weight: 700; color: #0f172a; text-align: right; padding-right: 14px; font-size: 1rem;">0₫</div>
                <div style="text-align: center;">
                    <button type="button" class="btn-remove-row" style="background: #fee2e2; border: none; color: #ef4444; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#fca5a5'" onmouseout="this.style.background='#fee2e2'">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="button" id="btn-add-product" style="background: #f8fafc; color: #2563eb; border: 1px dashed #cbd5e1; padding: 14px; border-radius: 10px; cursor: pointer; font-weight: 700; width: 100%; margin-top: 15px; font-size: 0.95rem; transition: all 0.2s;" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#2563eb'" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'">
            <i class="fa-solid fa-plus-circle"></i> Thêm sản phẩm khác vào phiếu nhập kho
        </button>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; background: #0f172a; padding: 25px 30px; border-radius: 15px; color: white; box-shadow: 0 10px 30px -10px rgba(15,23,42,0.5);">
        <div>
            <span style="color: #94a3b8; font-size: 0.8rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">TỔNG GIÁ TRỊ NHẬP KHO:</span>
            <h2 id="total-import-price" style="margin: 5px 0 0 0; color: #38bdf8; font-size: 2.2rem; font-weight: 800; letter-spacing: -1px;">0₫</h2>
        </div>
        <div>
            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 16px 35px; border-radius: 10px; font-weight: 700; font-size: 1.05rem; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 14px rgba(37,99,235,0.4); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.background='#1d4ed8'" onmouseout="this.style.transform='translateY(0)'; this.style.background='#2563eb'">
                <i class="fa-solid fa-cloud-arrow-up"></i> Xác nhận nhập kho & Hoàn tất
            </button>
        </div>
    </div>
</form>

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
        if (e.target.closest('.btn-remove-row')) {
            const rows = wrapper.querySelectorAll('.import-item-row');
            if (rows.length > 1) {
                e.target.closest('.import-item-row').remove();
                calculateTotal();
            } else {
                alert('Mỗi chứng từ nhập kho phải chứa ít nhất 1 sản phẩm hàng hóa!');
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

</body>
</html>