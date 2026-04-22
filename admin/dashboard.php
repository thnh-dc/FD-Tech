<?php 
require_once(__DIR__ . '/../config/database.php');
include(__DIR__ . '/../includes/header.php');

$type = $_GET['type'] ?? 'month';

// DOANH THU
if ($type === 'all') {
    $result = $conn->query("
        SELECT SUM(total_amount) as total 
        FROM orders 
        WHERE status = 'completed'
    ");
} else {
    $result = $conn->query("
        SELECT SUM(total_amount) as total 
        FROM orders 
        WHERE status = 'completed'
        AND MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
}

$row = $result->fetch_assoc();
$revenue = $row['total'] ? $row['total'] : 0;

// THỐNG KÊ
$new_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status='processing'")
                   ->fetch_assoc()['total'];

$total_products = $conn->query("SELECT COUNT(*) as total FROM products")
                       ->fetch_assoc()['total'];

$low_stock = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock_quantity < 5")
                  ->fetch_assoc()['total'];

// ĐƠN GẦN ĐÂY
$recentOrders = [];
$result = $conn->query("
    SELECT o.*, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");

while ($row = $result->fetch_assoc()) {
    $recentOrders[] = $row;
}

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

<?php include(__DIR__ . '/../includes/footer.php'); ?>