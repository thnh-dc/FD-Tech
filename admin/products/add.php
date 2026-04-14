<?php
require_once "../../config/database.php";

if(isset($_POST['submit'])){

$name = $_POST['name'];
$price = $_POST['price'];
$qty = $_POST['qty'];
$desc = $_POST['desc'];

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp, "../../assets/images/".$image);

$sql = "INSERT INTO products(name,price,stock_quantity,image_url,description)
VALUES('$name','$price','$qty','$image','$desc')";

mysqli_query($conn,$sql);

header("Location:list.php");
}
?>

<h2>Thêm sản phẩm</h2>

<form method="POST" enctype="multipart/form-data">
Tên sản phẩm<br>
<input type="text" name="name"><br>

Giá<br>
<input type="number" name="price"><br>

Số lượng<br>
<input type="number" name="qty"><br>

Ảnh<br>
<input type="file" name="image"><br>

Mô tả<br>
<textarea name="desc"></textarea><br><br>

<button name="submit">Thêm</button>
</form>