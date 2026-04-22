<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "fd_tech";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>