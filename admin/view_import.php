<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/database.php';
require_once __DIR__ . '../../auth/check_admin.php';

$import_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($import_id <= 0) {
    die("Mã phiếu nhập kho không hợp lệ!");
}

try {
    // Truy vấn thông tin chung của phiếu và thông tin nhà cung cấp đối tác
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

    // Truy vấn danh sách toàn bộ các linh kiện nằm trong phiếu nhập đó
    $stmt_details = $pdo->prepare("
        SELECT iod.*, p.name AS product_name
        FROM import_order_details iod
        JOIN products p ON iod.product_id = p.id
        WHERE iod.import_order_id = ?
    ");
    $stmt_details->execute([$import_id]);
    $details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Hệ thống gặp lỗi kết nối: " . $e->getMessage());
}

$page_title = 'Chi tiết phiếu nhập #' . $order['id'];
$page_icon = 'fa-solid fa-file-invoice';
// Nhúng chung file CSS tĩnh hóa đơn nhập kho
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/list_imports.css">';

include 'includes/header.php';
?>

<div class="imports-container">
    <div class="imports-card">
        
        <div class="imports-header-flex">
            <div class="imports-title">
                <h2><i class="fa-solid fa-receipt"></i> Chi tiết phiếu nhập kho hàng #<?= $order['id'] ?></h2>
                <p>Thông tin chứng từ gốc đối chiếu hàng hóa lưu trữ vật lý trong hệ thống.</p>
            </div>
            
            <div class="import-actions-toolbar">
                <button type="button" onclick="window.print();" class="btn-imports-action btn-imports-success">
                    <i class="fa-solid fa-print"></i> In chứng từ
                </button>
                <button type="button" onclick="exportToExcel(<?= $order['id'] ?>);" class="btn-imports-action btn-imports-warning">
                    <i class="fa-solid fa-file-excel"></i> Xuất File Excel
                </button>
                <a href="list_imports.php" class="btn-imports-action btn-imports-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại lịch sử
                </a>
            </div>
        </div>

        <div class="view-import-meta">
            <div class="meta-box-item">
                <h4><i class="fa-solid fa-handshake"></i> Nhà cung cấp đối tác</h4>
                <p><?= htmlspecialchars($order['supplier_name']) ?></p>
            </div>
            <div class="meta-box-item">
                <h4><i class="fa-solid fa-phone"></i> Điện thoại liên hệ</h4>
                <p><?= htmlspecialchars($order['supplier_phone'] ?? 'Chưa cập nhật') ?></p>
            </div>
            <div class="meta-box-item">
                <h4><i class="fa-solid fa-location-dot"></i> Địa chỉ văn phòng</h4>
                <p><?= htmlspecialchars($order['supplier_address'] ?? 'Chưa cập nhật') ?></p>
            </div>
            <div class="meta-box-item">
                <h4><i class="fa-regular fa-calendar-days"></i> Thời gian nhập kho</h4>
                <p><?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <div style="background: #fdf2f8; border-left: 4px solid #f472b6; padding: 15px; border-radius: 6px; margin-bottom: 25px; font-size: 0.9rem;">
            <strong style="color: #be185d;"><i class="fa-solid fa-comment-dots"></i> Ghi chú nội bộ:</strong> 
            <span style="color: #4c0519; margin-left: 5px;">
                <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có ghi chú đính kèm nào cho chứng từ này.' ?>
            </span>
        </div>

        <div class="imports-table-wrapper">
            <table class="imports-table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">STT</th>
                        <th>Tên sản phẩm linh kiện PC / Laptop nhận vào</th>
                        <th style="width: 150px; text-align: center;">Số lượng nhận</th>
                        <th style="width: 200px; text-align: right;">Đơn giá nhập gốc</th>
                        <th style="width: 220px; text-align: right;">Thành tiền linh kiện</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $stt = 1;
                    foreach ($details as $item): 
                        $subtotal = $item['quantity'] * $item['import_price'];
                    ?>
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: bold;"><?= $stt++ ?></td>
                            <td style="font-weight: 600; color: #1e293b;">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #2563eb; font-variant-numeric: tabular-nums;">
                                <?= number_format($item['quantity']) ?> cái
                            </td>
                            <td style="text-align: right; font-weight: bold; color: #475569; font-variant-numeric: tabular-nums;">
                                <?= number_format($item['import_price'], 0, ',', '.') ?>₫
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #0f172a; font-variant-numeric: tabular-nums;">
                                <?= number_format($subtotal, 0, ',', '.') ?>₫
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="import-grand-total-box">
            <div class="total-box-content">
                <span>TỔNG PHẢI THANH TOÁN ĐỐI TÁC:</span>
                <h2><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</h2>
            </div>
        </div>

        <div class="signature-section">
            <div>
                <p class="signature-title">Người lập phiếu nhập</p>
                <p class="signature-sub">(Ký, đóng dấu và ghi rõ họ tên)</p>
                <div class="signature-space">Hệ thống Admin FD Tech</div>
            </div>
            <div>
                <p class="signature-title">Đại diện đơn vị giao hàng</p>
                <p class="signature-sub">(Ký nhận bàn giao tài sản vật lý)</p>
                <div class="signature-space">Xác nhận đã nhận đủ hàng</div>
            </div>
        </div>

    </div>
</div>

</main>
</div>
<script src="../assets/js/script_dashboard.js"></script>

<script>
function exportToExcel(importId) {
    // Nhân bản bảng dữ liệu linh kiện để xử lý xuất dữ liệu
    var table = document.querySelector(".imports-table").cloneNode(true);
    
    // Khởi tạo cấu trúc header của file Excel hỗ trợ font UTF-8 tiếng Việt hiển thị mượt mà
    var excelFile = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
    excelFile += "<head><meta charset='utf-8'></head><body>";
    
    // Ghi các thông tin hành chính phiếu vào file Excel
    excelFile += "<h2>CHI TIẾT PHIẾU NHẬP KHO HÀNG #" + importId + "</h2>";
    excelFile += "<p>Nhà cung cấp đối tác: " + document.querySelector(".view-import-meta div:nth-child(1) p").innerText + "</p>";
    excelFile += "<p>Thời gian ghi nhận hệ thống: " + document.querySelector(".view-import-meta div:nth-child(4) p").innerText + "</p><br>";
    
    // Đổ cấu trúc bảng dữ liệu vào file
    excelFile += table.outerHTML;
    excelFile += "</body></html>";

    // Tạo liên kết tải ảo (Blob object) và ép trình duyệt tải tập tin về thiết bị người dùng
    var blob = new Blob([excelFile], { type: "application/vnd.ms-excel;charset=utf-8;" });
    var link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.style.display = "none";
    link.download = "Phieu_Nhap_Kho_#" + importId + ".xls";
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
</body>
</html>