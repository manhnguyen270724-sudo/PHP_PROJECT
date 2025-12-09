<?php
require_once('model/connect.php');

$prd = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

if (isset($_GET['ls'])) {
    echo "<script>alert('Bạn đã đăng nhập thành công!');</script>";
}
?>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/header.css">
<!-- bootrap -->
 <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script> -->

<header class="bg-white shadow-sm">

    <div class="container header-top">

        <!-- Logo -->
        <a href="index.php">
            <img src="images/logohong.png" class="logo-img" alt="MyLiShop">
        </a>

        <!-- Account -->
        <div class="header-account">
            <?php if (!empty($_SESSION['username'])): ?>
                <span><i class="fa fa-user"></i> 
                    <?= htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="user/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fa fa-sign-out"></i> Đăng xuất
                </a>
            <?php else: ?>
                <a href="user/login.php" class="btn btn-outline-dark btn-sm">Đăng nhập</a>
                <a href="user/register.php" class="btn btn-dark btn-sm">Đăng ký</a>
            <?php endif; ?>
        </div>

    </div>

    <!-- MENU NAVIGATION -->
    <div class="header-menu">
        <div class="container d-flex justify-content-between align-items-center">

            <nav>
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="introduceshop.php">Dịch vụ</a></li>

                    <li>
                        <a href="#">Sản phẩm ▼</a>
                        <ul class="dropdown">
                            <li><a href="fashionboy.php">Thời trang nam</a></li>
                            <li><a href="fashiongirl.php">Thời trang nữ</a></li>
                            <li><a href="newproduct.php">Hàng mới về</a></li>
                        </ul>
                    </li>

                    <li><a href="lienhe.php">Liên hệ</a></li>
                </ul>
            </nav>

            <!-- Right: Search + Cart -->
            <div class="header-right">

                <form class="header-search" action="search.php" method="POST">
                    <input type="text" name="search" placeholder="Nhập từ khóa..." required>
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>

                <a href="view-cart.php" class="header-cart">
                    <i class="fa fa-shopping-cart fa-lg"></i>
                    <span class="badge"><?= $prd ?></span>
                </a>

            </div>

        </div>
    </div>

</header>
