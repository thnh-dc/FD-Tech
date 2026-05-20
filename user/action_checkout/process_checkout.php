<?php
    session_start();
    require_once '../../config/database.php';

    $user_id = $_SESSION['user_id'] ?? 0;

    // Lấy dữ liệu form
    $address = $_POST['address'] ?? '';
    $selectedItems = $_POST['selected_items'] ?? '';

    if (empty($selectedItems)) {
        header("Location: ../cart.php?error=no_items");
        exit;
    }

    $selectedArray = explode(',', $selectedItems);
    $placeholders = implode(',', array_fill(0, count($selectedArray), '?'));
    
    $stmt = $pdo->prepare("
        SELECT c.product_id, c.quantity, p.price, c.id, p.name AS product_name, p.image_url AS product_image
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        AND c.id IN ($placeholders)
    ");
    $par = array_merge([$user_id], $selectedArray);
    $stmt->execute($par);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tính tổng
    $total = 0;
    foreach($cartItems as $item){
        $total += $item['price'] * $item['quantity'];
    }

    // Bắt đầu transaction
    $pdo->beginTransaction();

    try {
        // 2. LƯU ORDERS
        $stmt = $pdo->prepare("
            INSERT INTO orders(user_id, total_amount, shipping_address)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user_id, $total, $address]);

        $order_id = $pdo->lastInsertId();

        // 3. LƯU ORDER_ITEMS
        $stmtItem = $pdo->prepare("
            INSERT INTO order_items(order_id, product_id, quantity, price, product_name, product_image)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach($cartItems as $item){
            $stmtItem->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['product_name'],
                $item['product_image']
            ]);
        }

        // 4. XOÁ GIỎ HÀNG
        $placeholders = implode(',', array_fill(0, count($selectedArray), '?'));

        $stmt = $pdo->prepare("
            DELETE FROM cart_items 
            WHERE user_id = ?
            AND id IN ($placeholders)
        ");

        $params = array_merge([$user_id], $selectedArray);
        $stmt->execute($params);
        
        $pdo->commit();

        header("Location: ../checkout.php?status=success");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Lỗi: " . $e->getMessage();
    } 
?>