<?php
require_once "../../config/database.php";

$id = $_GET['id'];

$sql = "SELECT * FROM products WHERE id=$id";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);

if(isset($_POST['submit'])){

$name = $_POST['name'];
$price = $_POST['price'];
$qty = $_POST['qty'];
$desc = $_POST['desc'];

$sql = "UPDATE products 
SET name='$name',
price='$price',
stock_quantity='$qty',
description='$desc'
WHERE id=$id";

mysqli_query($conn,$sql);

header("Location:list.php");
}
?>

<h2>Sửa sản phẩm</h2>

<form method="POST">
Tên<br>
<input type="text" name="name" value="<?= $row['name'] ?>"><br>

Giá<br>
<input type="number" name="price" value="<?= $row['price'] ?>"><br>

Số lượng<br>
<input type="number" name="qty" value="<?= $row['stock_quantity'] ?>"><br>

Mô tả<br>
<textarea name="desc"><?= $row['description'] ?></textarea><br>

<button name="submit">Cập nhật</button>
</form>