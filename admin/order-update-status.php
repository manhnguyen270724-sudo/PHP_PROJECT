<?php
require_once('../model/connect.php');

// Validate input
if (!isset($_POST['order_id']) || !isset($_POST['new_status'])) {
    header('Location: orderlist.php?uf=1');
    exit;
}

$orderId = (int)$_POST['order_id'];
$newStatus = (int)$_POST['new_status'];

// Validate status value
if (!in_array($newStatus, [0, 1, 2])) {
    header('Location: orderlist.php?uf=1');
    exit;
}

try {
    $sql = "UPDATE orders SET status = :status WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':status' => $newStatus,
        ':id' => $orderId
    ]);

    if ($result) {
        header('Location: orderlist.php?us=1');
    } else {
        header('Location: orderlist.php?uf=1');
    }
} catch (PDOException $e) {
    error_log('Order status update error: ' . $e->getMessage());
    header('Location: orderlist.php?uf=1');
}
exit;
?>