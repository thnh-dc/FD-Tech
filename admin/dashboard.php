<?php 
include 'config.php'; 

// 1. Đếm tổng số đơn hàng
$sql_orders = "SELECT id FROM orders";
$res_orders = mysqli_query($conn, $sql_orders);
$total_orders = mysqli_num_rows($res_orders);

// 2. Tính tổng doanh thu
$sql_revenue = "SELECT SUM(total_price) as total FROM orders WHERE status='completed'";
$res_revenue = mysqli_query($conn, $sql_revenue);
$row_revenue = mysqli_fetch_assoc($res_revenue);
$total_money = number_format($row_revenue['total'] ?? 0, 0, ',', '.') . "đ";
?>

<!DOCTYPE html>
<html>
<head>
    <title>FD Tech - Admin Demo</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h2>Bảng Điều Khiển</h2>
            <div class="user">Chào Admin!</div>
        </header>

        <div class="cards-container">
            <div class="card card-blue">
                <i class="fas fa-shopping-cart"></i>
                <div class="info">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Đơn hàng</p>
                </div>
            </div>

            <div class="card card-green">
                <i class="fas fa-money-bill-wave"></i>
                <div class="info">
                    <h3><?php echo $total_money; ?></h3>
                    <p>Doanh thu</p>
                </div>
            </div>
        </div>

        <div class="data-table">
            <h3>Sản phẩm vừa thêm</h3>
            <table>
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_prod = "SELECT * FROM products ORDER BY id DESC LIMIT 5";
                    $res_prod = mysqli_query($conn, $sql_prod);
                    while($row = mysqli_fetch_assoc($res_prod)) {
                    ?>
                    <tr>
                        <td><img src="uploads/<?php echo $row['image']; ?>" width="50"></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">Sửa</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete">Xóa</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>