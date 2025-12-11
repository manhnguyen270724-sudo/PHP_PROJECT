<?php
require_once('../model/connect.php');

// Validate order ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orderlist.php?df=1');
    exit;
}

$orderId = (int)$_GET['id'];

if ($orderId <= 0) {
    header('Location: orderlist.php?df=1');
    exit;
}

try {
    // Bắt đầu transaction
    $conn->beginTransaction();

    // Xóa chi tiết đơn hàng trước (product_order)
    $sqlDeleteDetails = "DELETE FROM product_order WHERE order_id = :id";
    $stmtDetails = $conn->prepare($sqlDeleteDetails);
    $stmtDetails->execute([':id' => $orderId]);

    // Xóa đơn hàng
    $sqlDeleteOrder = "DELETE FROM orders WHERE id = :id";
    $stmtOrder = $conn->prepare($sqlDeleteOrder);
    $stmtOrder->execute([':id' => $orderId]);

    // Commit transaction
    $conn->commit();

    header('Location: orderlist.php?ds=1');
} catch (PDOException $e) {
    // Rollback nếu có lỗi
    $conn->rollBack();
    error_log('Order delete error: ' . $e->getMessage());
    header('Location: orderlist.php?df=1');
}
exit;
?>