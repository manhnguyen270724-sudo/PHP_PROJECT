<?php
session_start();
require_once('model/connect.php');

// 1. Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: view-cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// 2. Lấy dữ liệu từ Form
$name = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['customer_phone'] ?? '');
$email = trim($_POST['customer_email'] ?? '');
$address = trim($_POST['customer_address'] ?? '');
$note = trim($_POST['order_note'] ?? '');
$payment = $_POST['payment_method'] ?? 'cod';

// User ID (Mặc định là 0 nếu chưa đăng nhập, vì bảng orders của bạn user_id NOT NULL DEFAULT '0')
$userId = isset($_SESSION['id-user']) ? $_SESSION['id-user'] : 0;

// Tính tổng tiền
$totalMoney = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalMoney += $item['price'] * $item['quantity'];
}

try {
    $conn->beginTransaction();

    // --- A. INSERT VÀO BẢNG `orders` ---
    // Khớp với cấu trúc: id, total, date_order, status, user_id, customer_name...
    $sqlOrder = "INSERT INTO orders (total, date_order, status, user_id, customer_name, customer_phone, customer_email, customer_address, note, payment_method) 
                 VALUES (:total, NOW(), 0, :uid, :name, :phone, :email, :addr, :note, :method)";
    
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->execute([
        ':total' => $totalMoney,
        ':uid' => $userId,
        ':name' => $name,
        ':phone' => $phone,
        ':email' => $email,
        ':addr' => $address,
        ':note' => $note,
        ':method' => $payment
    ]);
    
    $orderId = $conn->lastInsertId();

    // --- B. INSERT VÀO BẢNG `product_order` ---
    // Bảng này có: product_id, order_id, quantity
    $sqlDetail = "INSERT INTO product_order (product_id, order_id, quantity) VALUES (:pid, :oid, :qty)";
    $stmtDetail = $conn->prepare($sqlDetail);
    
    // --- C. TRỪ TỒN KHO BẢNG `products` ---
    // Sửa lỗi Invalid parameter number: Dùng tên biến khác nhau cho các tham số
    $sqlUpdateQty = "UPDATE products SET quantity = quantity - :qty_sub WHERE id = :pid_check AND quantity >= :qty_check";
    $stmtUpdateQty = $conn->prepare($sqlUpdateQty);

    foreach ($_SESSION['cart'] as $item) {
        // 1. Lưu chi tiết đơn hàng
        $stmtDetail->execute([
            ':pid' => $item['id'],
            ':oid' => $orderId,
            ':qty' => $item['quantity']
        ]);
        
        // 2. Trừ kho (Truyền đủ 3 tham số để tránh lỗi)
        $stmtUpdateQty->execute([
            ':qty_sub' => $item['quantity'],      // Số lượng cần trừ
            ':pid_check' => $item['id'],          // ID sản phẩm
            ':qty_check' => $item['quantity']     // Kiểm tra xem có đủ hàng không
        ]);

        // Kiểm tra nếu trừ kho lỗi (do hết hàng)
        if ($stmtUpdateQty->rowCount() == 0) {
            throw new Exception("Sản phẩm " . $item['name'] . " đã hết hàng.");
        }
    }

    $conn->commit();
    
    // Xóa giỏ hàng và thông báo thành công
    unset($_SESSION['cart']);
    header('Location: index.php?order_success=1');
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "<script>alert('Lỗi đặt hàng: " . $e->getMessage() . "'); window.location.href='view-cart.php';</script>";
    exit;
}
?>