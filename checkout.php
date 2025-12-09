<?php
session_start();
require_once('model/connect.php');

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: view-cart.php');
    exit;
}

$cart = $_SESSION['cart'];
$totalAmount = 0;
foreach ($cart as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Thanh toán - Fashion MyLiShop</title>
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
</head>
<body>

<?php include("model/header.php"); ?>

<div class="container" style="margin-top: 20px; margin-bottom: 50px;">
    <ul class="breadcrumb">
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="view-cart.php">Giỏ hàng</a></li>
        <li class="active">Thanh toán</li>
    </ul>

    <h2 class="text-center" style="margin-bottom: 30px;">
        <i class="fa fa-credit-card"></i> Thông tin thanh toán
    </h2>

    <form action="checkout-process.php" method="POST" id="checkoutForm">
        <div class="row">
            
            <div class="col-md-7">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-user"></i> Thông tin người nhận</h3>
                    </div>
                    <div class="panel-body">
                        
                        <div class="form-group">
                            <label>Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Nhập họ và tên" required>
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_phone" class="form-control" placeholder="Nhập số điện thoại" pattern="[0-9]{10,11}" required>
                            <p class="help-block">Ví dụ: 0397450200</p>
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control" placeholder="Nhập email" required>
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ <span class="text-danger">*</span></label>
                            <textarea name="customer_address" class="form-control" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Ghi chú đơn hàng (tùy chọn)</label>
                            <textarea name="order_note" class="form-control" rows="3" placeholder="Ghi chú về đơn hàng..."></textarea>
                        </div>

                    </div>
                </div>

                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-money"></i> Phương thức thanh toán</h3>
                    </div>
                    <div class="panel-body">
                        <div class="radio">
                            <label>
                                <input type="radio" name="payment_method" id="cod" value="cod" checked>
                                <strong>Thanh toán khi nhận hàng (COD)</strong>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                <strong>Chuyển khoản ngân hàng</strong>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="payment_method" id="momo" value="momo">
                                <strong>Ví điện tử MoMo</strong>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Đơn hàng của bạn</h3>
                    </div>
                    <div class="panel-body">
                        
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-right">Tạm tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $item): 
                                        $subtotal = $item['price'] * $item['quantity'];
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($item['name']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?= number_format($item['price']); ?> đ × <?= $item['quantity']; ?>
                                                </small>
                                            </td>
                                            <td class="text-right">
                                                <?= number_format($subtotal); ?> đ
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="clearfix" style="margin-bottom: 10px;">
                            <span class="pull-left">Tạm tính:</span>
                            <strong class="pull-right"><?= number_format($totalAmount); ?> đ</strong>
                        </div>

                        <div class="clearfix" style="margin-bottom: 10px;">
                            <span class="pull-left">Phí vận chuyển:</span>
                            <strong class="pull-right text-success">Miễn phí</strong>
                        </div>

                        <hr>

                        <div class="clearfix" style="margin-bottom: 20px;">
                            <h4 class="pull-left" style="margin: 0;">Tổng cộng:</h4>
                            <h4 class="pull-right text-danger" style="margin: 0;"><?= number_format($totalAmount); ?> đ</h4>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            <i class="fa fa-check-circle"></i> Đặt hàng
                        </button>
                        <a href="view-cart.php" class="btn btn-default btn-block">
                            <i class="fa fa-arrow-left"></i> Quay lại giỏ hàng
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<?php include("model/footer.php"); ?>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const phone = document.querySelector('input[name="customer_phone"]').value;
    const phoneRegex = /^[0-9]{10,11}$/;
    
    if (!phoneRegex.test(phone)) {
        e.preventDefault();
        alert('Số điện thoại không hợp lệ!');
        return false;
    }
});
</script>

</body>
</html>