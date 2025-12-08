<?php
session_start();
require_once('model/connect.php');

$prd = 0;
if (isset($_SESSION['cart'])) {
    $prd = count($_SESSION['cart']);
}

$message = "Không thể tìm kiếm được, vui lòng kiểm tra lại!";
$totalnumber = 0;
$resultSearch = [];

if (isset($_POST['search']) && !empty($_POST['search'])) {
    $searchKeyword = trim($_POST['search']);

    // SQL secure
    $sql = "SELECT id, image, name, price 
            FROM products 
            WHERE name LIKE :keyword";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['keyword' => "%$searchKeyword%"]);
    $resultSearch = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalnumber = count($resultSearch);
} else {
    // Không có từ khóa
    echo $message;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="images/logohong.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="admin/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>

<a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>

<!-- background -->
<div class="container-fluid">
    <div id="bg">
        <?php
        $sqlSlide = "SELECT image FROM slides WHERE id = 1";
        $stmtSlide = $conn->query($sqlSlide);
        while ($row = $stmtSlide->fetch(PDO::FETCH_ASSOC)) { ?>
            <img class="bg-img" src="<?php echo $row['image']; ?>" alt="">
        <?php } ?>
    </div>
</div>

<!-- Header -->
<?php include("model/header.php"); ?>

    <div class="container">
        <ul class="breadcrumb">
            <li><a href="index.php">Trang chủ</a></li>
            <li>Tìm kiếm sản phẩm</li>
        </ul>

        <div class="container search-results">
        <div class="product-main">
            <div class="title-product-main">
                <h3 class="section-title">Kết Quả Tìm Kiếm</h3>
                <p class="search-info">
                    Có <?php echo $totalnumber; ?> sản phẩm được tìm thấy
                </p>
            </div>

            <div class="content-product-main">
                <div class="row">
                    <?php
                    if ($totalnumber > 0) {
                        foreach ($resultSearch as $kq) {
                    ?>
                        <div class="col-md-3 col-sm-6 text-center">
                            <div class="thumbnail">
                                <div class="hoverimage1">
                                    <img class="product-image" src="<?php echo $kq['image']; ?>">
                                </div>
                                <div class="name-product"><?php echo $kq['name']; ?></div>
                                <div class="price">Giá: <?php echo $kq['price']; ?><sup>đ</sup></div>

                                <div class="product-info">
                                    <a href="detail.php?id=<?php echo $kq['id']; ?>">
                                        <button type="button" class="btn btn-primary">
                                            <label class="heart">&hearts;</label>
                                            Chi Tiết
                                            <label class="heart">&hearts;</label>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    } else { ?>
                        <div class="error-search">
                            KÍNH CHÀO QUÝ KHÁCH VÀ XIN LỖI VÌ SẢN PHẨM BẠN TÌM KHÔNG TỒN TẠI!
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>

    <div class="container">
        <?php include("model/footer.php"); ?>
    </div>
</div>

<script> new WOW().init(); </script>
</body>
</html>
