<?php
// Không gọi session_start() ở đây nếu các file parent đã gọi.
// Sử dụng check để tránh lỗi "session already started"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/connect.php'); // Dùng đường dẫn tuyệt đối an toàn

$prd = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<header class="bg-white shadow-sm sticky-top">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <a href="index.php">
                <img src="images/logohong.png" alt="MyLiShop" style="height: 60px;">
            </a>

            <div class="d-flex gap-2">
                <?php if (!empty($_SESSION['username'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa fa-user me-1"></i> <?= htmlspecialchars($_SESSION['username']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Hồ sơ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="user/logout.php">Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="user/login.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Đăng nhập</a>
                    <a href="user/register.php" class="btn btn-primary btn-sm rounded-pill px-3">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
            <ul class="nav me-auto">
                <li class="nav-item"><a href="index.php" class="nav-link text-dark fw-bold px-3">TRANG CHỦ</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fw-bold px-3" href="#" data-bs-toggle="dropdown">SẢN PHẨM</a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="fashionboy.php">Thời trang nam</a></li>
                        <li><a class="dropdown-item" href="fashiongirl.php">Thời trang nữ</a></li>
                        <li><a class="dropdown-item" href="newproduct.php">Hàng mới về</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="lienhe.php" class="nav-link text-dark fw-bold px-3">LIÊN HỆ</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <form action="search.php" method="POST" class="input-group" style="width: 250px;">
                    <input type="text" name="search" class="form-control rounded-start-pill border-end-0" placeholder="Tìm kiếm..." required>
                    <button class="btn btn-outline-secondary rounded-end-pill border-start-0 bg-white" type="submit">
                        <i class="fa fa-search text-primary"></i>
                    </button>
                </form>

                <a href="view-cart.php" class="position-relative btn btn-light rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa fa-shopping-cart text-dark"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $prd ?>
                    </span>
                </a>
            </div>
        </div>
    </div>
</header>