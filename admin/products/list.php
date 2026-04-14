<?php
require_once "../../config/database.php";

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<h2>Danh sách sản phẩm</h2>

<a href="add.php">+ Thêm sản phẩm</a>

<table border="1" width="100%" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Ảnh</th>
        <th>Action</th>
    </tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['price'] ?></td>
    <td><?= $row['stock_quantity'] ?></td>
    <td>
        <img src="../../assets/images/<?= $row['image_url'] ?>" width="60">
    </td>
    <td>
        <a href="edit.php?id=<?= $row['id'] ?>">Sửa</a>
        <a href="delete.php?id=<?= $row['id'] ?>">Xóa</a>
    </td>
</tr>
<?php } ?>

</table>