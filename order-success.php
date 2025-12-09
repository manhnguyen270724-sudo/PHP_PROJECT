<?php
session_start();
require_once('model/connect.php');

// Kiểm tra mã đơn hàng
if (!isset($_GET['order_code'])) {
    header('Location: index.php');
    exit;
}

$orderCode = $_GET['order_code'];

// Lấy thông tin đơn hàng (Code giữ nguyên)
try {
    $sql = "SELECT * FROM orders WHERE order_code = :order_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':order_code' => $orderCode]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: index.php');
        exit;
    }
    
    // Lấy chi tiết đơn hàng
    $sqlDetails = "SELECT * FROM order_details WHERE order_id = :order_id";
    $stmtDetails = $conn->prepare($sqlDetails);
    $stmtDetails->execute([':order_id' => $order['id']]);
    $orderDetails = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log('Order fetch error: ' . $e->getMessage());
    header('Location: index.php');
    exit;
}

// Map payment method
$paymentMethods = [
    'cod' => 'Thanh toán khi nhận hàng (COD)',
    'bank_transfer' => 'Chuyển khoản ngân hàng',
    'momo' => 'Ví điện tử MoMo'
];
$paymentMethodText = $paymentMethods[$order['payment_method']] ?? 'Không xác định';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đặt hàng thành công - Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logohong.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>

    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-icon {
            width: 80px;
            height: 80px;
            background: #5cb85c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .success-icon i {
            font-size: 40px;
            color: white;
            line-height: 80px; /* Căn giữa chiều dọc cho icon */
        }
        .text-center-flex {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
    </style>
</head>
<body>

<?php include("model/header.php"); ?>

<div class="container" style="margin-top: 40px; margin-bottom: 50px;">
    
    <div class="text-center" style="margin-bottom: 40px;">
        <div class="success-icon">
            <i class="fa fa-check"></i>
        </div>
        <h2 class="text-success">Đặt hàng thành công!</h2>
        <p class="lead">Cảm ơn bạn đã đặt hàng tại MyLiShop</p>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-info-circle"></i> Thông tin đơn hàng</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Mã đơn hàng:</strong>
                            <p class="text-primary"><?= htmlspecialchars($order['order_code']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Ngày đặt:</strong>
                            <p><?= date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Tổng tiền:</strong>
                            <p class="text-danger h4"><?= number_format($order['total_amount']); ?> đ</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Phương thức thanh toán:</strong>
                            <p><?= $paymentMethodText; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Chi tiết</h3>
                </div>
                <div class="panel-body">
                    <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['customer_name']); ?> - <?= htmlspecialchars($order['customer_phone']); ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['customer_address']); ?></p>
                    <hr>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">SL</th>
                                <th class="text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDetails as $detail): ?>
                            <tr>
                                <td><?= htmlspecialchars($detail['product_name']); ?></td>
                                <td class="text-center"><?= $detail['quantity']; ?></td>
                                <td class="text-right"><?= number_format($detail['subtotal']); ?> đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center" style="margin-top: 30px;">
                <a href="index.php" class="btn btn-primary btn-lg">
                    <i class="fa fa-home"></i> Về trang chủ
                </a>
            </div>

        </div>
    </div>
</div>

<?php include("model/footer.php"); ?>
</body>
</html>