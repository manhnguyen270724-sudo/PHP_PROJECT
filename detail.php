<?php
    require_once("model/connect.php");
    include 'model/header.php';
    error_reporting(2);

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("ID không hợp lệ");
    }

    $id = intval($_GET['id']);

    // PDO
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Không tìm thấy sản phẩm");
    }

    $thum_Image = (!empty($row['image'])) ? $row['image'] : "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $row['name']; ?> - MyLiShop</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="admin/bower_components/font-awesome/css/font-awesome.min.css">

    <!-- detail styles moved to css/style.css -->
</head>

<body>

<div class="container">
    <div class="detail-product">

        <div class="row">

            <!-- IMAGE -->
            <div class="col-md-6 col-sm-12">
                <img src="<?php echo $thum_Image; ?>" width="100%" height="450">
            </div>

            <!-- INFO -->
            <div class="col-md-6 col-sm-12">

                <h2><?php echo $row['name']; ?></h2>
                <hr>

                <?php if ($row['saleprice'] > 0): 
                    $gia = $row['price'] - ($row['price'] / 100);
                ?>
                    <p class="price">
                        <del><?php echo $row['price']; ?>đ</del>
                    </p>
                    <p class="price price-highlight">
                        <strong><?php echo $gia; ?>đ</strong>
                    </p>
                <?php else: ?>
                    <p class="price price-highlight">
                        <strong><?php echo $row['price']; ?>đ</strong>
                    </p>
                <?php endif; ?>

                <hr>

                <a href="addcart.php?id=<?php echo $row['id']; ?>">
                    <button class="btn-buy">ĐẶT MUA NGAY</button>
                </a>

                <div class="info-list">
                    <p><span class="fa fa-check-circle"></span>Giao hàng toàn quốc</p>
                    <p><span class="fa fa-check-circle"></span>Thanh toán khi nhận hàng</p>
                    <p><span class="fa fa-check-circle"></span>Đổi hàng trong 15 ngày</p>
                </div>

            </div>

        </div>

    </div>
</div>

<?php include 'model/footer.php'; ?>
</body>
</html>
