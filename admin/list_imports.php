<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/database.php';
require_once __DIR__ . '/check_admin.php';

try {
    // Truy vấn danh sách lịch sử phiếu nhập kèm tên nhà cung cấp
    $query = "SELECT io.*, s.name AS supplier_name 
              FROM import_orders io
              JOIN suppliers s ON io.supplier_id = s.id
              ORDER BY io.created_at DESC";
    $imports = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi truy vấn dữ liệu: " . $e->getMessage());
}

$page_title = 'Lịch sử nhập kho';
$page_icon = 'fa-solid fa-clock-rotate-left';
// Nhúng file CSS tách biệt cho toàn bộ hệ thống hóa đơn nhập
$custom_css = '<link rel="stylesheet" href="/FD-Tech/assets/css/list_imports.css">';

include 'includes/header.php';
?>

<div class="imports-container">
    <div class="imports-card">
        
        <div class="imports-header-flex">
            <div class="imports-title">
                <h2><i class="fa-solid fa-file-invoice-dollar"></i> Lịch sử nhập kho hàng</h2>
                <p>Danh sách toàn bộ các chứng từ kiểm kê và nhập linh kiện PC / Laptop vật lý vào xưởng.</p>
            </div>
            <a href="add_import.php" class="btn-imports-action btn-imports-primary">
                <i class="fa-solid fa-plus"></i> Lập phiếu nhập kho mới
            </a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; font-size: 0.9rem;">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="imports-table-wrapper">
            <table class="imports-table">
                <thead>
                    <tr>
                        <th style="width: 100px; text-align: center;">Mã chứng từ</th>
                        <th>Nhà cung cấp đối tác</th>
                        <th style="text-align: right; width: 180px;">Tổng tiền thanh toán</th>
                        <th style="text-align: center; width: 140px;">Trạng thái kho</th>
                        <th>Ghi chú phiếu</th>
                        <th style="width: 160px;">Thời gian lập</th>
                        <th style="width: 100px; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($imports)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 10px; display: block;"></i>
                                Chưa có bất kỳ chứng từ nhập kho nào được tạo trên hệ thống!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($imports as $item): ?>
                            <tr>
                                <td style="text-align: center; font-weight: bold; color: #64748b;">
                                    #<?= $item['id'] ?>
                                </td>
                                <td style="font-weight: 600; color: #334155;">
                                    <?= htmlspecialchars($item['supplier_name']) ?>
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a; font-variant-numeric: tabular-nums;">
                                    <?= number_format($item['total_amount'], 0, ',', '.') ?>₫
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-import-status">
                                        <i class="fa-solid fa-circle-check"></i> Đã hoàn tất
                                    </span>
                                </td>
                                <td style="color: #64748b;">
                                    <?= !empty($item['note']) ? htmlspecialchars($item['note']) : '<span style="color:#cbd5e1; font-style: italic;">Không có ghi chú</span>' ?>
                                </td>
                                <td style="color: #64748b; font-size: 0.85rem;">
                                    <i class="fa-regular fa-clock"></i> <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="view_import.php?id=<?= $item['id'] ?>" class="btn-imports-action btn-imports-secondary" style="padding: 6px 12px; font-size: 0.8rem;" title="Xem chi tiết hóa đơn">
                                        <i class="fa-solid fa-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>

</main>
</div>
<script src="../assets/js/script_dashboard.js"></script>
</body>
</html>