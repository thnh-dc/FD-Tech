<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';

    $allowed = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    if ($id > 0 && in_array($status, $allowed)) {

        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $id]);

        if ($result) {
            echo "success";
        } else {
            echo "fail";
        }

    } else {
        echo "invalid";
    }
}