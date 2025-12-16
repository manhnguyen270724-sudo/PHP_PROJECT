<?php
session_start();
require_once('../model/connect.php');

// Lấy thống kê cơ bản
try {
    // Tổng số sản phẩm
    $stmt = $conn->query("SELECT COUNT(*) as total FROM products");
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Tổng số đơn hàng
    $stmt = $conn->query("SELECT COUNT(*) as total FROM orders");
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Đơn hàng chờ xử lý
    $stmt = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 0");
    $pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Tổng doanh thu (chỉ đơn hàng đã hoàn thành - status = 1)
    $stmt = $conn->query("SELECT SUM(total) as revenue FROM orders WHERE status = 1");
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;

    // Tổng số khách hàng
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Tổng số liên hệ chưa xử lý
    $stmt = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE status = 0");
    $totalContacts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Tổng số danh mục
    $stmt = $conn->query("SELECT COUNT(*) as total FROM categories");
    $totalCategories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Sản phẩm mới nhất (5 sản phẩm)
    $stmt = $conn->query("SELECT id, name, image, price, created FROM products ORDER BY id DESC LIMIT 5");
    $recentProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. ĐƠN HÀNG MỚI NHẤT (5 đơn)
    // SỬA LOGIC: Ưu tiên lấy tên/sđt từ bảng orders (cho khách vãng lai)
    $sqlRecentOrders = "SELECT 
        o.id, 
        o.total, 
        o.date_order, 
        o.status, 
        COALESCE(NULLIF(o.customer_name, ''), u.fullname, 'Khách vãng lai') as fullname, 
        COALESCE(NULLIF(o.customer_phone, ''), u.phone, '---') as phone
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.date_order DESC
    LIMIT 5";
    $stmt = $conn->query($sqlRecentOrders);
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Sản phẩm sắp hết hàng (số lượng < 5)
    $stmt = $conn->query("
        SELECT id, name, quantity, image
        FROM products
        WHERE quantity < 5
        ORDER BY quantity ASC
        LIMIT 5
    ");
    $lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Dashboard stats error: ' . $e->getMessage());
    $totalProducts = $totalOrders = $totalContacts = $totalCategories = $pendingOrders = $totalUsers = 0;
    $totalRevenue = 0;
    $recentProducts = $recentOrders = $lowStockProducts = [];
}

// Xử lý thông báo
$successMsg = '';
$errorMsg = '';
if (isset($_GET['addps'])) $successMsg = 'Thêm sản phẩm thành công!';
if (isset($_GET['ps'])) $successMsg = 'Xóa sản phẩm thành công!';
if (isset($_GET['us'])) $successMsg = 'Cập nhật trạng thái đơn hàng thành công!';
if (isset($_GET['pf'])) $errorMsg = 'Thao tác thất bại!';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MyLiShop</title>
    <link rel="icon" type="image/png" href="images/logohong.png">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-shopping-bag"></i> MyLiShop</h3>
            <small>Admin Panel</small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link active" href="index.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="productlist.php">
                <i class="fas fa-box"></i> Quản lý sản phẩm
            </a>
            <a class="nav-link" href="productadd.php">
                <i class="fas fa-plus-circle"></i> Thêm sản phẩm
            </a>
            <a class="nav-link" href="category-list.php">
                <i class="fas fa-tags"></i> Danh mục
            </a>
            <a class="nav-link" href="orderlist.php">
                <i class="fas fa-shopping-cart"></i> Đơn hàng
            </a>
            <a class="nav-link" href="contact-list.php">
                <i class="fas fa-envelope"></i> Liên hệ
            </a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="../index.php" target="_blank">
                <i class="fas fa-globe"></i> Xem website
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Header Bar -->
        <div class="header-bar">
            <div>
                <h4 class="mb-0">Dashboard</h4>
                <small class="text-muted">Chào mừng đến với trang quản trị MyLiShop</small>
            </div>
            <div>
                <span class="text-muted me-3">
                    <i class="far fa-calendar"></i> 
                    <?php echo date('d/m/Y'); ?>
                </span>
                <span class="text-muted">
                    <i class="far fa-clock"></i> 
                    <?php echo date('H:i'); ?>
                </span>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($successMsg): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMsg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errorMsg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3><?= $totalProducts ?></h3>
                    <p>Tổng sản phẩm</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3><?= $totalOrders ?></h3>
                    <p>Đơn hàng</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3><?= $totalContacts ?></h3>
                    <p>Liên hệ mới</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3><?= $totalCategories ?></h3>
                    <p>Danh mục</p>
                </div>
            </div>
        </div>

        <!-- Recent Content -->
        <div class="row">
            <!-- Recent Products -->
            <div class="col-md-6">
                <div class="content-card">
                    <h5><i class="fas fa-box text-primary"></i> Sản phẩm mới nhất</h5>
                    
                    <?php if (!empty($recentProducts)): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hình</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentProducts as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="../<?= htmlspecialchars($p['image'] ?? '') ?>" 
                                                 class="product-thumb" 
                                                 alt="<?= htmlspecialchars($p['name'] ?? '') ?>">
                                        </td>
                                        <td><?= htmlspecialchars($p['name'] ?? '') ?></td>
                                        <td><strong><?= number_format($p['price'] ?? 0) ?>đ</strong></td>
                                        <td>
                                            <a href="product-edit.php?idProduct=<?= $p['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary action-btn">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="productlist.php" class="btn btn-outline-primary btn-sm">
                            Xem tất cả <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Chưa có sản phẩm nào</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-md-6">
                <div class="content-card">
                    <h5><i class="fas fa-shopping-cart text-success"></i> Đơn hàng mới nhất</h5>
                    
                    <?php if (!empty($recentOrders)): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $o): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary">#<?= $o['id'] ?></span></td>
                                        <td>
                                            <div><?= htmlspecialchars($o['fullname']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($o['phone']) ?></small>
                                        </td>
                                        <td><strong class="text-success"><?= number_format($o['total']) ?>đ</strong></td>
                                        <td>
                                            <a href="order-detail.php?id=<?= $o['id'] ?>" 
                                               class="btn btn-sm btn-outline-info action-btn">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="orderlist.php" class="btn btn-outline-success btn-sm">
                            Xem tất cả <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Chưa có đơn hàng nào</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-card">
            <h5><i class="fas fa-bolt text-warning"></i> Thao tác nhanh</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="productadd.php" class="btn btn-primary w-100">
                        <i class="fas fa-plus-circle"></i> Thêm sản phẩm mới
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="orderlist.php" class="btn btn-success w-100">
                        <i class="fas fa-list"></i> Quản lý đơn hàng
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="contact-list.php" class="btn btn-warning w-100">
                        <i class="fas fa-envelope"></i> Xem liên hệ
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="../index.php" target="_blank" class="btn btn-info w-100">
                        <i class="fas fa-globe"></i> Xem website
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>