<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/database.php';
require_once __DIR__ . '/check_admin.php';

try {
    $query = "SELECT io.*, s.name AS supplier_name 
              FROM import_orders io
              JOIN suppliers s ON io.supplier_id = s.id
              ORDER BY io.created_at DESC";
    $imports = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi truy vấn dữ liệu: " . $e->getMessage());
}

$page_title = 'Lịch sử nhập kho';
include 'includes/header.php';
?>

<div class="admin-container" style="display: flex; min-height: 100vh; background: #f8fafc;">
    
    <main class="admin-main" style="flex: 1; padding: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="color: #1e293b; font-size: 1.6rem; font-weight: 700; margin: 0;">📜 Lịch sử nhập kho hàng</h2>
                <p style="color: #64748b; margin: 5px 0 0 0;">Danh sách toàn bộ các hóa đơn nhập hàng hóa của FD Tech</p>
            </div>
            <a href="add_import.php" style="background: #2563eb; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px;">
                ➕ Nhập lô hàng mới
            </a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div style="background: #dcfce7; border-left: 4px solid #10b981; color: #15803d; padding: 12px; margin-bottom: 20px; border-radius: 4px; font-weight: 500;">
                ✓ <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                <thead>
                    <tr style="background: #f1f5f9; color: #475569; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 15px 20px; font-weight: 600;">Mã đơn</th>
                        <th style="padding: 15px 20px; font-weight: 600;">Nhà cung cấp</th>
                        <th style="padding: 15px 20px; font-weight: 600; text-align: right;">Tổng chi phí vốn</th>
                        <th style="padding: 15px 20px; font-weight: 600; text-align: center;">Trạng thái</th>
                        <th style="padding: 15px 20px; font-weight: 600;">Ghi chú đợt nhập</th>
                        <th style="padding: 15px 20px; font-weight: 600;">Ngày giờ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($imports)): ?>
                        <tr>
                            <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8; font-style: italic;">
                                Chưa có chứng từ nhập kho nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($imports as $item): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 15px 20px; font-weight: bold; color: #2563eb;">
                                    #NK-<?= str_pad($item['id'], 5, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td style="padding: 15px 20px; font-weight: 500; color: #334155;">
                                    <?= htmlspecialchars($item['supplier_name']) ?>
                                </td>
                                <td style="padding: 15px 20px; text-align: right; font-weight: bold; color: #0f172a;">
                                    <?= number_format($item['total_amount'], 0, ',', '.') ?>₫
                                </td>
                                <td style="padding: 15px 20px; text-align: center;">
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
                                        Đã nhập kho
                                    </span>
                                </td>
                                <td style="padding: 15px 20px; color: #64748b;">
                                    <?= !empty($item['note']) ? htmlspecialchars($item['note']) : '<span style="color:#cbd5e1;">Không có ghi chú</span>' ?>
                                </td>
                                <td style="padding: 15px 20px; color: #64748b; font-size: 0.9rem;">
                                    <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>