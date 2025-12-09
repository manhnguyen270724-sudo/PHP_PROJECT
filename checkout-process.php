<?php
session_start();
require_once('model/connect.php');

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: view-cart.php');
    exit;
}

// Kiểm tra dữ liệu POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Lấy thông tin từ form
$customerName = trim($_POST['customer_name'] ?? '');
$customerPhone = trim($_POST['customer_phone'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? '');
$customerAddress = trim($_POST['customer_address'] ?? '');
$orderNote = trim($_POST['order_note'] ?? '');
$paymentMethod = $_POST['payment_method'] ?? 'cod';

// Validate dữ liệu
if (empty($customerName) || empty($customerPhone) || empty($customerEmail) || empty($customerAddress)) {
    header('Location: checkout.php?error=missing_fields');
    exit;
}

// Validate email
if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    header('Location: checkout.php?error=invalid_email');
    exit;
}

// Validate phone
if (!preg_match('/^[0-9]{10,11}$/', $customerPhone)) {
    header('Location: checkout.php?error=invalid_phone');
    exit;
}

// Tính tổng tiền
$totalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Bắt đầu transaction
try {
    $conn->beginTransaction();
    
    // 1. Tạo đơn hàng
    $orderCode = 'ORD' . date('YmdHis') . rand(1000, 9999);
    
    $sqlOrder = "INSERT INTO orders (
        order_code, 
        customer_name, 
        customer_phone, 
        customer_email, 
        customer_address, 
        total_amount, 
        payment_method, 
        order_note, 
        status, 
        created_at
    ) VALUES (
        :order_code, 
        :customer_name, 
        :customer_phone, 
        :customer_email, 
        :customer_address, 
        :total_amount, 
        :payment_method, 
        :order_note, 
        'pending', 
        NOW()
    )";
    
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([
        ':order_code' => $orderCode,
        ':customer_name' => $customerName,
        ':customer_phone' => $customerPhone,
        ':customer_email' => $customerEmail,
        ':customer_address' => $customerAddress,
        ':total_amount' => $totalAmount,
        ':payment_method' => $paymentMethod,
        ':order_note' => $orderNote
    ]);
    
    $orderId = $conn->lastInsertId();
    
    // 2. Thêm chi tiết đơn hàng
    $sqlOrderDetail = "INSERT INTO order_details (
        order_id, 
        product_id, 
        product_name, 
        product_image, 
        price, 
        quantity, 
        subtotal
    ) VALUES (
        :order_id, 
        :product_id, 
        :product_name, 
        :product_image, 
        :price, 
        :quantity, 
        :subtotal
    )";
    
    $stmtOrderDetail = $conn->prepare($sqlOrderDetail);
    
    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        
        $stmtOrderDetail->execute([
            ':order_id' => $orderId,
            ':product_id' => $item['id'],
            ':product_name' => $item['name'],
            ':product_image' => $item['image'],
            ':price' => $item['price'],
            ':quantity' => $item['quantity'],
            ':subtotal' => $subtotal
        ]);
        
        // 3. Cập nhật số lượng sản phẩm
        $sqlUpdateQty = "UPDATE products 
                        SET quantity = quantity - :quantity 
                        WHERE id = :id AND quantity >= :quantity";
        $stmtUpdateQty = $conn->prepare($sqlUpdateQty);
        $stmtUpdateQty->execute([
            ':quantity' => $item['quantity'],
            ':id' => $item['id']
        ]);
        
        // Kiểm tra có cập nhật được không
        if ($stmtUpdateQty->rowCount() == 0) {
            throw new Exception("Sản phẩm " . $item['name'] . " không đủ số lượng trong kho");
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Xóa giỏ hàng
    unset($_SESSION['cart']);
    
    // Redirect đến trang thành công
    header('Location: order-success.php?order_code=' . $orderCode);
    exit;
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollBack();
    error_log('Order processing error: ' . $e->getMessage());
    header('Location: checkout.php?error=processing_failed&message=' . urlencode($e->getMessage()));
    exit;
}
?>