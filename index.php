<?php
require_once('model/connect.php');
$prd = 0;
if (isset($_SESSION['cart'])) {
    $prd = count($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta name="title" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="description" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="keywords" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="author" content="Hôih My" />
    <meta name="author" content="Y Blir" /> -->
    <link rel="icon" type="image/png" href="images/logohong.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
    <link rel="stylesheet" type="text/css" href="admin/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js" charset="utf-8"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'> 

    <!-- customer js -->
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>
    <!-- customer css -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <!-- button top -->
    <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- background -->
    <!-- <div class="container-fluid">
    </div> -->
    <!-- /background -->

    <!-- Header -->
    <?php include("model/header.php"); ?>
    <!-- /header -->

    <div class="main">
        <!-- slide -->
        <?php include("model/slide.php"); ?>
        <!-- class="clearfix" -->

        <!-- Banner -->
        <?php include("model/banner.php"); ?>
        <!-- /banner -->
        <!-- Content -->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-main">

                        <!-- Sản phẩm mới -->
                        <div class="title-product-main">
                            <h3 class="section-title">Sản phẩm mới</h3>
                        </div>
                        <div class="content-product-main">
                            <div class="row">

                                <?php
                                $stmt = $conn->prepare("SELECT id, image, name, price FROM products WHERE category_id = 3 AND status = 0");
                                $stmt->execute();
                                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($products as $kq) {
                                    ?>
                                    <div class="col-md-3 col-sm-6 text-center">
                                        <div class="thumbnail">
                                                <div class="hoverimage1">
                                                <img class="product-image" src="<?= $kq['image']; ?>" >
                                            </div>
                                            <div class="name-product"><?= $kq['name']; ?></div>
                                            <div class="price">Giá: <?= $kq['price']; ?><sup>đ</sup></div>

                                            <div class="product-info">
                                                    <a href="addcart.php?id=<?= $kq['id']; ?>">
                                                    <button class="btn btn-primary">
                                                        <label class="heart">&hearts;</label> Mua hàng <label class="heart">&hearts;</label>
                                                    </button>
                                                </a>
                                                <a href="detail.php?id=<?= $kq['id']; ?>">
                                                    <button class="btn btn-primary">
                                                        <label class="heart">&hearts;</label> Chi tiết <label class="heart">&hearts;</label>
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Thời Trang Nam -->
                        <div class="title-product-main">
                            <h3 class="section-title">Thời Trang Nam</h3>
                        </div>
                        <div class="content-product-main">
                            <div class="row">

                                <?php
                                $stmt = $conn->prepare("SELECT id, image, name, price FROM products WHERE category_id = 1 LIMIT 8");
                                $stmt->execute();
                                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($products as $kq) {
                                    ?>
                                    <div class="col-md-3 col-sm-6 text-center">
                                        <div class="thumbnail">
                                            <div class="hoverimage1">
                                                <img class="product-image" src="<?= $kq['image']; ?>">
                                            </div>
                                            <div class="name-product"><?= $kq['name']; ?></div>
                                            <div class="price">Giá: <?= $kq['price']; ?><sup>đ</sup></div>

                                            <div class="product-info">
                                                <a href="addcart.php?id=<?= $kq['id']; ?>">
                                                    <button class="btn btn-primary">
                                                        <label class="heart">&hearts;</label> Mua hàng <label class="heart">&hearts;</label>
                                                    </button>
                                                </a>
                                                <a href="detail.php?id=<?= $kq['id']; ?>">
                                                    <button class="btn btn-primary">
                                                        <label class="heart">&hearts;</label> Chi tiết <label class="heart">&hearts;</label>
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Thời Trang Nữ -->
                        <div class="title-product-main">
                            <h3 class="section-title">Thời Trang Nữ</h3>
                        </div>
                        <div class="content-product-main">
                            <div class="row">

                                <?php
                                $stmt = $conn->prepare("SELECT id, image, name, price FROM products WHERE category_id = 2 LIMIT 8");
                                $stmt->execute();
                                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($products as $kq) {
                                    ?>
                                    <div class="col-md-3 col-sm-6 text-center">
                                        <div class="thumbnail">
                                            <div class="hoverimage1">
                                                <img class="product-image" src="<?= $kq['image']; ?>">
                                            </div>
                                            <div class="name-product"><?= $kq['name']; ?></div>
                                            <div class="price">Giá: <?= $kq['price']; ?><sup>đ</sup></div>

                                            <div class="product-info">
                                                <a href="addcart.php?id=<?= $kq['id']; ?>">
                                                    <button class="btn btn-primary">
                                                        <label class="heart">&hearts;</label> Mua hàng <label class="heart">&hearts;</label>
                                                    </button>
                                                </a>
                                                <a href="detail.php?id=<?= $kq['id']; ?>">
                                                    <button class="btn btn-primary">
                                                        <label class="heart">&hearts;</label> Chi tiết <label class="heart">&hearts;</label>
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php if (isset($_GET['order_success']) && $_GET['order_success'] == 1): ?>
            <script>
                alert("Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại MyLiShop.");
                // Xóa query param trên URL để không hiện lại khi refresh
                window.history.replaceState(null, null, window.location.pathname);
            </script>
            <?php endif; ?>


</body>

 <!-- Partner -->
<?php include("model/partner.php"); ?>
 <!-- /Partner -->

<!-- Footer -->
<?php include("model/footer.php"); ?>
<!-- /footer -->