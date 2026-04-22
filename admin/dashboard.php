<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/config/database.php');
include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/header.php');

$type = $_GET['type'] ?? 'month';

if ($type === 'all') {
    $stmt = $pdo->query("
        SELECT SUM(total_amount) as total 
        FROM orders 
        WHERE status = 'completed'
    ");
} else {
    $stmt = $pdo->query("
        SELECT SUM(total_amount) as total 
        FROM orders 
        WHERE status = 'completed'
        AND MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
}

$row = $stmt->fetch();
$revenue = isset($row['total']) ? $row['total'] : 0;

$new_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='processing'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 5")->fetchColumn();

$recentOrders = $pdo->query("
    SELECT o.*, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
")->fetchAll();

$statusMap = [
    'completed' => ['text'=>'Hoàn tất','class'=>'badge-success'],
    'processing' => ['text'=>'Đang xử lý','class'=>'badge-warning'],
    'cancelled' => ['text'=>'Đã hủy','class'=>'badge-danger']
];
?>

<div class="admin-main">
<h1>Dashboard</h1>

<a href="?type=month" class="btn btn-secondary">Theo tháng</a>
<a href="?type=all" class="btn btn-primary">Tất cả</a>

<div class="stats-grid">
<div class="card stat-item"><span>Doanh thu</span><h2><?= number_format($revenue) ?>đ</h2></div>
<div class="card stat-item"><span>Đơn xử lý</span><h2><?= $new_orders ?></h2></div>
<div class="card stat-item"><span>Tổng sản phẩm</span><h2><?= $total_products ?></h2></div>
<div class="card stat-item"><span>Sắp hết hàng</span><h2><?= $low_stock ?></h2></div>
</div>

<div class="card">
<h3>Đơn gần đây</h3>
<table class="admin-table">
<tr><th>ID</th><th>User</th><th>Status</th><th>Total</th></tr>

<?php foreach($recentOrders as $o): 
$status = isset($statusMap[$o['status']]) ? $statusMap[$o['status']] : ['text'=>'Không rõ','class'=>'badge-danger'];
?>
<tr>
<td>#<?= $o['id'] ?></td>
<td><?= htmlspecialchars($o['username']) ?></td>
<td><span class="badge <?= $status['class'] ?>"><?= $status['text'] ?></span></td>
<td><?= number_format($o['total_amount']) ?>đ</td>
</tr>
<?php endforeach; ?>

</table>
</div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/FD-Tech/includes/footer.php'); ?>