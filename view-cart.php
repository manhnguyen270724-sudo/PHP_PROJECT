<?php
session_start();
require_once('model/connect.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Giỏ hàng - MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logohong.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>


<div class="container" style="min-height: 500px; margin-top: 20px;">
    <ul class="breadcrumb">
        <li><a href="index.php">Trang chủ</a></li>
        <li class="active">Giỏ hàng của bạn</li>
    </ul>

    <div id="cart-content">
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th width="120">Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $key => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                            // Xử lý đường dẫn ảnh: xóa '../' nếu đang ở trang chủ
                            $imgSrc = str_replace('../', '', $item['image']);
                        ?>
                        <tr id="item-<?= $item['id'] ?>">
                            <td><img src="<?= $imgSrc ?>" width="60" style="border: 1px solid #ddd;"></td>
                            <td><?= $item['name'] ?></td>
                            <td><?= number_format($item['price']) ?> đ</td>
                            <td>
                                <input type="number" class="form-control text-center update-qty" 
                                       data-id="<?= $item['id'] ?>" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="1">
                            </td>
                            <td class="item-subtotal-<?= $item['id'] ?>"><strong><?= number_format($subtotal) ?> đ</strong></td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-item" data-id="<?= $item['id'] ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <a href="index.php" class="btn btn-info"><i class="fa fa-arrow-left"></i> Tiếp tục mua hàng</a>
                </div>
                <div class="col-md-6 text-right">
                    <h3 style="margin-top:0">Tổng tiền: <span id="cart-total" class="text-danger"><?= number_format($total) ?> đ</span></h3>
                    <a href="checkout.php" class="btn btn-success btn-lg">Tiến hành thanh toán <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning text-center">
                <h3>Giỏ hàng đang trống!</h3>
                <a href="index.php" class="btn btn-primary">Quay lại mua sắm</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("model/footer.php"); ?>
<script src="js/cart-ajax.js"></script>

</body>
</html>