<?php
require_once "../config/database.php";

$p = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM products"));
$o = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM orders"));
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM users"));
?>

<h2>Admin Dashboard</h2>

<div>
Tổng sản phẩm: <?= $p['total'] ?>
</div>

<div>
Tổng đơn hàng: <?= $o['total'] ?>
</div>

<div>
Tổng user: <?= $u['total'] ?>
</div>