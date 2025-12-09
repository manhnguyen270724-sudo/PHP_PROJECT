<?php
session_start();
require_once('model/connect.php');

// Xử lý cập nhật số lượng
if (isset($_POST['update_cart'])) {
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $id => $qty) {
            $qty = (int)$qty;
            if ($qty > 0) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $id) {
                        if ($qty <= $item['max_quantity']) {
                            $item['quantity'] = $qty;
                        }
                        break;
                    }
                }
            }
        }
    }
    header('Location: view-cart.php?updated=success');
    exit;
}

// Xử lý xóa sản phẩm
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $removeId) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            break;
        }
    }
    header('Location: view-cart.php?removed=success');
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalAmount = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Giỏ hàng - Fashion MyLiShop</title>
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
        <li class="active">Giỏ hàng</li>
    </ul>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Đã thêm sản phẩm vào giỏ hàng!</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-info">Đã cập nhật giỏ hàng!</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['removed'])): ?>
        <div class="alert alert-warning">Đã xóa sản phẩm khỏi giỏ hàng!</div>
    <?php endif; ?>

    <div class="panel panel-default">
        <div class="panel-heading" style="background-color: #f0ad4e; color: white;">
            <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Giỏ hàng của bạn</h3>
        </div>
        <div class="panel-body">
            
            <?php if (empty($cart)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-shopping-cart" style="font-size: 80px; color: #ddd;"></i>
                    <h4 class="mt-3">Giỏ hàng trống</h4>
                    <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                    <a href="index.php" class="btn btn-primary mt-3">
                        <i class="fa fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
            <?php else: ?>
                
                <form method="POST" action="view-cart.php">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="active">
                                    <th style="width: 100px;">Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th style="width: 150px;">Đơn giá</th>
                                    <th style="width: 150px;">Số lượng</th>
                                    <th style="width: 150px;">Thành tiền</th>
                                    <th style="width: 100px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $totalAmount += $subtotal;
                                ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars($item['image']); ?>" 
                                                 alt="<?= htmlspecialchars($item['name']); ?>"
                                                 class="img-thumbnail" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <strong><?= htmlspecialchars($item['name']); ?></strong>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <strong class="text-danger">
                                                <?= number_format($item['price']); ?> đ
                                            </strong>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <input type="number" 
                                                   name="quantity[<?= $item['id']; ?>]" 
                                                   value="<?= $item['quantity']; ?>"
                                                   min="1" 
                                                   max="<?= $item['max_quantity']; ?>"
                                                   class="form-control" 
                                                   style="width: 80px;">
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <strong class="text-primary">
                                                <?= number_format($subtotal); ?> đ
                                            </strong>
                                        </td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <a href="view-cart.php?remove=<?= $item['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="warning">
                                    <td colspan="4" class="text-right"><h5>Tổng cộng:</h5></td>
                                    <td colspan="2">
                                        <h4 class="text-danger" style="margin: 0;">
                                            <?= number_format($totalAmount); ?> đ
                                        </h4>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-6">
                            <button type="submit" name="update_cart" class="btn btn-info">
                                <i class="fa fa-refresh"></i> Cập nhật giỏ hàng
                            </button>
                            <a href="index.php" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Tiếp tục mua
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="checkout.php" class="btn btn-success btn-lg">
                                <i class="fa fa-credit-card"></i> Thanh toán
                            </a>
                        </div>
                    </div>
                </form>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php include("model/footer.php"); ?>
</body>
</html>