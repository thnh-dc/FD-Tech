<?php
require_once __DIR__ . '/../../config/database.php';

function autoCancelUnpaidBankOrders($pdo, $expireMinutes = 15)
{
    try {
        $stmtOrders = $pdo->prepare("SELECT id, user_id, used_points FROM orders WHERE payment_method = 'bank' AND payment_status = 'unpaid' AND status = 'pending' AND created_at <= DATE_SUB(NOW(), INTERVAL ? MINUTE)");
        $stmtOrders->execute([$expireMinutes]);
        $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) return 0;

        $cancelledCount = 0;

        foreach ($orders as $order) {
            $order_id = (int)$order['id'];
            $pdo->beginTransaction();

            try {
                $stmtCheck = $pdo->prepare("SELECT id, user_id, status, payment_status, used_points FROM orders WHERE id = ? LIMIT 1 FOR UPDATE");
                $stmtCheck->execute([$order_id]);
                $currentOrder = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$currentOrder || $currentOrder['status'] !== 'pending' || $currentOrder['payment_status'] !== 'unpaid') {
                    $pdo->rollBack();
                    continue;
                }

                $stmtItems = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $stmtItems->execute([$order_id]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                if (empty($items)) {
                    $pdo->rollBack();
                    continue;
                }

                $stmtReturnStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
                foreach ($items as $item) $stmtReturnStock->execute([(int)$item['quantity'], (int)$item['product_id']]);

                $used_points = (int)($currentOrder['used_points'] ?? 0);
                $order_user_id = (int)$currentOrder['user_id'];
                $refund_note = '';

                if ($used_points > 0) {
                    $stmtRefundPoint = $pdo->prepare("UPDATE users SET point = COALESCE(point, 0) + ? WHERE id = ?");
                    $stmtRefundPoint->execute([$used_points, $order_user_id]);

                    $stmtRefundHistory = $pdo->prepare("INSERT INTO fd_point_transactions(user_id, order_id, type, points, description) VALUES (?, ?, 'refund', ?, ?)");
                    $stmtRefundHistory->execute([$order_user_id, $order_id, $used_points, 'Hoàn lại ' . $used_points . ' FDp do đơn hàng #FD-' . $order_id . ' bị tự động hủy vì quá thời gian thanh toán']);

                    $refund_note = ', đã hoàn lại ' . $used_points . ' FDp';
                }

                $stmtCancel = $pdo->prepare("UPDATE orders SET status = 'cancelled', note = CONCAT(IFNULL(note, ''), ?), updated_at = NOW() WHERE id = ? AND status = 'pending' AND payment_status = 'unpaid'");
                $stmtCancel->execute([' | Tự động hủy do quá thời gian thanh toán' . $refund_note, $order_id]);

                if ($stmtCancel->rowCount() <= 0) {
                    $pdo->rollBack();
                    continue;
                }

                $pdo->commit();
                $cancelledCount++;

            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
            }
        }

        return $cancelledCount;

    } catch (PDOException $e) {
        return 0;
    }
}
?>