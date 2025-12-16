<?php
require_once('../model/connect.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orderlist.php'); exit;
}
$orderId = (int)$_GET['id'];

try {
    // FIX SQL: Lấy data từ customer_* trong orders trước
    $sql = "SELECT 
        o.id as order_id, o.total, o.date_order, o.status, o.note, o.payment_method,
        COALESCE(NULLIF(o.customer_name, ''), u.fullname, 'Khách vãng lai') as fullname,
        COALESCE(NULLIF(o.customer_phone, ''), u.phone, '') as phone,
        COALESCE(NULLIF(o.customer_email, ''), u.email, '') as email,
        COALESCE(NULLIF(o.customer_address, ''), u.address, '') as address
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) { header('Location: orderlist.php'); exit; }

    // Lấy sản phẩm
    $sqlProducts = "SELECT p.name, p.image, p.price, p.saleprice, po.quantity,
        (CASE WHEN p.saleprice > 0 THEN p.saleprice ELSE p.price END) * po.quantity as subtotal
    FROM product_order po
    JOIN products p ON po.product_id = p.id
    WHERE po.order_id = :id";
    $stmtP = $conn->prepare($sqlProducts);
    $stmtP->execute([':id' => $orderId]);
    $products = $stmtP->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) { die("Lỗi DB: " . $e->getMessage()); }

$sttText = ($order['status'] == 1) ? 'Đã hoàn thành' : (($order['status'] == 2) ? 'Đã hủy' : 'Chờ xử lý');
$sttClass = ($order['status'] == 1) ? 'bg-success' : (($order['status'] == 2) ? 'bg-danger' : 'bg-warning text-dark');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn #<?= $orderId ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>@media print { .no-print { display: none; } }</style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between mb-4 no-print">
        <h3>Chi tiết đơn hàng #<?= $orderId ?></h3>
        <div>
            <button onclick="window.print()" class="btn btn-secondary"><i class="fa fa-print"></i> In</button>
            <a href="orderlist.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">Thông tin đơn hàng</div>
                <div class="card-body">
                    <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['date_order'])) ?></p>
                    <p><strong>Trạng thái:</strong> <span class="badge <?= $sttClass ?>"><?= $sttText ?></span></p>
                    <p><strong>Thanh toán:</strong> <?= strtoupper($order['payment_method'] ?? 'COD') ?></p>
                    <p><strong>Ghi chú:</strong> <?= $order['note'] ?: 'Không' ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-success text-white">Thông tin người nhận</div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
                    <p><strong>SĐT:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">Danh sách sản phẩm</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Hình</th><th>Tên</th><th>Giá</th><th>SL</th><th>Thành tiền</th></tr></thead>
                <tbody>
                    <?php foreach ($products as $p): 
                        // Fix đường dẫn ảnh nếu đang ở admin
                        $imgSrc = (strpos($p['image'], 'uploads/') === 0) ? '../'.$p['image'] : $p['image'];
                    ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($imgSrc) ?>" width="50"></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= number_format($p['saleprice'] > 0 ? $p['saleprice'] : $p['price']) ?> đ</td>
                        <td><?= $p['quantity'] ?></td>
                        <td><?= number_format($p['subtotal']) ?> đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                        <td class="text-danger fw-bold"><?= number_format($order['total']) ?> đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>