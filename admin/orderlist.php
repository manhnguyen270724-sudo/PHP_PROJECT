<?php
require_once('../model/connect.php');

// Xử lý thông báo
$alert = '';
if (isset($_GET['us'])) {
    $alert = '<div class="alert alert-success">Cập nhật trạng thái đơn hàng thành công!</div>';
} elseif (isset($_GET['uf'])) {
    $alert = '<div class="alert alert-danger">Cập nhật trạng thái thất bại!</div>';
} elseif (isset($_GET['ds'])) {
    $alert = '<div class="alert alert-success">Xóa đơn hàng thành công!</div>';
} elseif (isset($_GET['df'])) {
    $alert = '<div class="alert alert-danger">Xóa đơn hàng thất bại!</div>';
}

// Lấy danh sách đơn hàng với thông tin chi tiết
$sql = "SELECT 
    o.id as order_id,
    o.total,
    o.date_order,
    o.status,
    u.fullname,
    u.phone,
    u.email,
    u.address,
    GROUP_CONCAT(CONCAT(p.name, ' (x', po.quantity, ')') SEPARATOR ', ') as products
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN product_order po ON po.order_id = o.id
LEFT JOIN products p ON po.product_id = p.id
GROUP BY o.id
ORDER BY o.date_order DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Quản lý đơn hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .badge-pending { background-color: #ffc107; }
        .badge-completed { background-color: #28a745; }
        .badge-cancelled { background-color: #dc3545; }
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: 0.3s;
        }
        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
        .order-body {
            padding: 15px;
        }
        .status-select {
            max-width: 200px;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Quản lý đơn hàng</h3>
        <a href="index.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <?= $alert ?>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="0" <?= isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="1" <?= isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="2" <?= isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date'] ?? '' ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Lọc
                    </button>
                    <a href="orderlist.php" class="btn btn-secondary">
                        <i class="fa fa-refresh"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Chưa có đơn hàng nào.
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <strong>Mã đơn: #<?= $order['order_id'] ?></strong>
                        </div>
                        <div class="col-md-3">
                            <i class="fa fa-calendar"></i> 
                            <?= date('d/m/Y H:i', strtotime($order['date_order'])) ?>
                        </div>
                        <div class="col-md-3">
                            <?php
                            $statusClass = 'badge-pending';
                            $statusText = 'Chờ xử lý';
                            if ($order['status'] == 1) {
                                $statusClass = 'badge-completed';
                                $statusText = 'Đã hoàn thành';
                            } elseif ($order['status'] == 2) {
                                $statusClass = 'badge-cancelled';
                                $statusText = 'Đã hủy';
                            }
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </div>
                        <div class="col-md-3 text-end">
                            <strong class="text-danger"><?= number_format($order['total']) ?> đ</strong>
                        </div>
                    </div>
                </div>
                <div class="order-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fa fa-user"></i> Thông tin khách hàng</h6>
                            <p class="mb-1"><strong>Họ tên:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
                            <p class="mb-1"><strong>SĐT:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                            <p class="mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fa fa-shopping-bag"></i> Sản phẩm</h6>
                            <p><?= htmlspecialchars($order['products']) ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <form method="POST" action="order-update-status.php" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                            <label class="mb-0">Cập nhật trạng thái:</label>
                            <select name="new_status" class="form-select status-select" required>
                                <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Đã hoàn thành</option>
                                <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-save"></i> Lưu
                            </button>
                        </form>
                        <div>
                            <a href="order-detail.php?id=<?= $order['order_id'] ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> Xem chi tiết
                            </a>
                            <a href="order-delete.php?id=<?= $order['order_id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">
                                <i class="fa fa-trash"></i> Xóa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Thống kê -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Thống kê</h5>
            <div class="row text-center">
                <?php
                $totalOrders = count($orders);
                $pendingOrders = count(array_filter($orders, fn($o) => $o['status'] == 0));
                $completedOrders = count(array_filter($orders, fn($o) => $o['status'] == 1));
                $totalRevenue = array_sum(array_column($orders, 'total'));
                ?>
                <div class="col-md-3">
                    <h3><?= $totalOrders ?></h3>
                    <p class="text-muted">Tổng đơn hàng</p>
                </div>
                <div class="col-md-3">
                    <h3><?= $pendingOrders ?></h3>
                    <p class="text-muted">Chờ xử lý</p>
                </div>
                <div class="col-md-3">
                    <h3><?= $completedOrders ?></h3>
                    <p class="text-muted">Đã hoàn thành</p>
                </div>
                <div class="col-md-3">
                    <h3><?= number_format($totalRevenue) ?> đ</h3>
                    <p class="text-muted">Tổng doanh thu</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>