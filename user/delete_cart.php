<?php
require_once '../config/database.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ?");
    $stmt->execute([$id]);

    echo "success";
}
?>