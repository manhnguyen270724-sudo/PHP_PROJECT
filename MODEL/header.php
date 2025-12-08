<?php
session_start();
require_once('model/connect.php');

// Đếm số sản phẩm trong giỏ
$prd = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Hiển thị login success
if (isset($_GET['ls'])) {
    echo "<script>alert('Bạn đã đăng nhập thành công!');</script>";
}
?>

<!-- HEADER CLEAN MINIMAL -->
<header class="py-3 border-bottom bg-white shadow-sm">
    <div class="container d-flex flex-wrap align-items-center justify-content-between">

        <!-- Logo -->
        <a href="index.php" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
            <img src="images/logohong.png" width="160" alt="MyLiShop Logo">
        </a>

        <!-- User Account -->
        <div class="text-end me-3">
            <?php if (!empty($_SESSION['username'])): ?>
                <span class="me-2"><i class="fa fa-user"></i> 
                    <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <a href="user/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fa fa-sign-out"></i> Đăng xuất
                </a>
            <?php else: ?>
                <a href="user/login.php" class="btn btn-outline-dark btn-sm me-2">Đăng nhập</a>
                <a href="user/register.php" class="btn btn-dark btn-sm">Đăng ký</a>
            <?php endif; ?>
        </div>

    </div>

    <!-- MENU -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-top mt-2">
        <div class="container">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="introduceshop.php">Dịch vụ</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Sản phẩm</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="fashionboy.php">Thời trang nam</a></li>
                            <li><a class="dropdown-item" href="fashiongirl.php">Thời trang nữ</a></li>
                            <li><a class="dropdown-item" href="newproduct.php">Hàng mới về</a></li>
                        </ul>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="lienhe.php">Liên hệ</a></li>
                </ul>

                <!-- SEARCH FORM -->
                <form class="d-flex" action="search.php" method="POST">
                    <input class="form-control me-2" type="text" name="search" placeholder="Nhập từ khóa..." required>
                    <button class="btn btn-dark" type="submit"><i class="fa fa-search"></i></button>
                </form>

                <!-- CART -->
                <a href="view-cart.php" class="btn btn-outline-dark ms-3">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="badge bg-danger"><?php echo $prd; ?></span>
                </a>

            </div>
        </div>
    </nav>
</header>
                   