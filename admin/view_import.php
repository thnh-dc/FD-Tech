<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/database.php';
require_once __DIR__ . '/check_admin.php';

$import_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($import_id <= 0) {
    die("Mã phiếu nhập kho không hợp lệ!");
}

try {
    $stmt_order = $pdo->prepare("
        SELECT io.*, s.name AS supplier_name, s.phone AS supplier_phone, s.address AS supplier_address
        FROM import_orders io
        JOIN suppliers s ON io.supplier_id = s.id
        WHERE io.id = ?
    ");
    $stmt_order->execute([$import_id]);
    $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Không tìm thấy chứng từ nhập kho này trên hệ thống!");
    }

    $stmt_details = $pdo->prepare("
        SELECT iod.*, p.name AS product_name
        FROM import_order_details iod
        JOIN products p ON iod.product_id = p.id
        WHERE iod.import_order_id = ?
    ");
    $stmt_details->execute([$import_id]);
    $details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Lỗi hệ thống: " . $e->getMessage());
}

$page_title = 'Chi tiết phiếu nhập kho #' . str_pad($import_id, 5, '0', STR_PAD_LEFT);
include 'includes/header.php';
?>

<style>
/* Nhúng font chữ có chân chuyên nghiệp cho tiêu đề hóa đơn */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&display=swap');

.invoice-wrapper {
    max-width: 1000px;
    margin: 0 auto;
}
.invoice-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 40px;
    border: 1px solid #e2e8f0;
    position: relative;
}
.invoice-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    border-bottom: 2px solid #e2e8f0;
}
.invoice-table td {
    padding: 14px 16px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}
.invoice-table tr:hover td {
    background-color: #f8fafc;
}

/* Định dạng chữ Độc quyền cho Tiêu đề phiếu nhập kho */
.invoice-title-premium {
    margin: 0;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 2.2rem;
    font-weight: 700;
    letter-spacing: 2px;
    background: linear-gradient(135deg, #1e3a8a, #0284c7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
}

@media print {
    body {
        background: #fff !important;
    }
    .invoice-title-premium {
        -webkit-text-fill-color: #1e3a8a !important; /* Đảm bảo khi in ra giấy mực xanh vẫn đậm rõ nét */
    }
    .sidebar, .top-navbar, .btn-print-group, .admin-profile, .page-title {
        display: none !important;
    }
    .main-content {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    .invoice-card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>

<div class="admin-container" style="display: flex; min-height: 100vh; background: #f8fafc;">
    
    <main class="admin-main" style="flex: 1; padding: 30px;">
        
        <div class="invoice-wrapper">
            <div class="btn-print-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <a href="list_imports.php" style="color: #64748b; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='#1e293b'" onmouseout="this.style.color='#64748b'">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách lịch sử
                </a>
                <button onclick="window.print();" style="background: #2563eb; color: white; border: none; padding: 10px 22px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37,99,235,0.2); transition: all 0.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                    <i class="fa-solid fa-print"></i> In phiếu nhập kho / Xuất PDF
                </button>
            </div>

            <div class="invoice-card">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f5f9; padding-bottom: 25px; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo" style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;">
                        <div>
                            <h2 style="margin: 0; color: #0f172a; font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; line-height: 1.2;">FD TECH </h2>
                            <p style="margin: 0; color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Technology & Components</p>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <h1 class="invoice-title-premium">PHIẾU NHẬP KHO</h1>
                        <span style="background: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 9999px; font-size: 0.85rem; font-weight: 700; display: inline-block; margin-top: 6px;">
                            Số phiếu: #NK-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?>
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 35px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #f1f5f9;">
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: #475569; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">📦 ĐƠN VỊ CUNG ỨNG (BÊN GIAO)</h4>
                        <p style="margin: 0 0 6px 0; font-weight: 700; color: #0f172a; font-size: 1rem;"><?= htmlspecialchars($order['supplier_name']) ?></p>
                        <p style="margin: 0 0 4px 0; color: #475569; font-size: 0.9rem;"><b style="color: #64748b;">Điện thoại:</b> <?= htmlspecialchars($order['supplier_phone']) ?></p>
                        <p style="margin: 0; color: #475569; font-size: 0.9rem; line-height: 1.4;"><b style="color: #64748b;">Địa chỉ:</b> <?= htmlspecialchars($order['supplier_address']) ?></p>
                    </div>
                    <div style="text-align: right; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h4 style="margin: 0 0 10px 0; color: #475569; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">📥 KHO TIẾP NHẬN (BÊN NHẬN)</h4>
                            <p style="margin: 0 0 6px 0; font-weight: 700; color: #0f172a; font-size: 1rem;">Kho hàng Vật lý FD Tech</p>
                        </div>
                        <div style="font-size: 0.9rem; color: #475569;">
                            <p style="margin: 0 0 4px 0;"><b style="color: #64748b;">Trạng thái:</b> <span style="color: #16a34a; font-weight: 700;">● Đã khớp kho thành công</span></p>
                            <p style="margin: 0;"><b style="color: #64748b;">Ngày nhập:</b> <?= date('d/m/Y - H:i:s', strtotime($order['created_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 60px;">STT</th>
                            <th style="text-align: left;">Tên linh kiện / Sản phẩm</th>
                            <th style="text-align: center; width: 110px;">Số lượng</th>
                            <th style="text-align: right; width: 160px;">Đơn giá vốn</th>
                            <th style="text-align: right; width: 180px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stt = 1;
                        foreach ($details as $row): 
                            $subtotal = $row['quantity'] * $row['import_price'];
                        ?>
                            <tr>
                                <td style="text-align: center; color: #64748b; font-weight: 500;"><?= $stt++ ?></td>
                                <td style="font-weight: 600; color: #1e293b; font-size: 0.95rem;"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td style="text-align: center; font-weight: 700; color: #0f172a; font-size: 1rem;"><?= number_format($row['quantity']) ?></td>
                                <td style="text-align: right; color: #475569; font-variant-numeric: tabular-nums;"><?= number_format($row['import_price'], 0, ',', '.') ?>₫</td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a; font-variant-numeric: tabular-nums; font-size: 1rem;"><?= number_format($subtotal, 0, ',', '.') ?>₫</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-top: 10px;">
                    <div style="max-width: 50%; background: #fdf2f8; padding: 12px 16px; border-radius: 6px; border-left: 4px solid #db2777;">
                        <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #9d174d; text-transform: uppercase; margin-bottom: 4px;">📝 Ghi chú chứng từ:</span>
                        <p style="margin: 0; font-style: italic; color: #be185d; font-size: 0.9rem;">
                            <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có ghi chú đính kèm kèm theo.' ?>
                        </p>
                    </div>
                    <div style="text-align: right; min-width: 300px;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: #64748b; letter-spacing: 0.5px;">TỔNG PHẢI THANH TOÁN ĐỐI TÁC:</span>
                        <h2 style="margin: 6px 0 0 0; color: #2563eb; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; font-variant-numeric: tabular-nums;"><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</h2>
                    </div>
                </div>

                <div style="margin-top: 60px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: center;">
                    <div>
                        <p style="margin: 0; font-weight: 700; color: #1e293b; font-size: 0.95rem;">Người lập phiếu nhập</p>
                        <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: #64748b;">(Ký, đóng dấu và ghi rõ họ tên)</p>
                        <div style="margin-top: 70px; color: #94a3b8; font-weight: 600; font-size: 0.9rem;">Hệ thống Admin FD Tech</div>
                    </div>
                    <div>
                        <p style="margin: 0; font-weight: 700; color: #1e293b; font-size: 0.95rem;">Đại diện đơn vị giao hàng</p>
                        <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: #64748b;">(Ký và ghi rõ họ tên)</p>
                        <div style="margin-top: 70px; color: #cbd5e1; font-style: italic; font-size: 0.9rem;">Xác nhận đã bàn giao đủ lượng vật lý</div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

</body>
</html>