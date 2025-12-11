<?php
require_once('../model/connect.php');

// Validate order ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orderlist.php');
    exit;
}

$orderId = (int)$_GET['id'];

// Lấy thông tin đơn hàng
try {
    $sql = "SELECT 
        o.id as order_id,
        o.total,
        o.date_order,
        o.status,
        u.id as user_id,
        u.fullname,
        u.phone,
        u.email,
        u.address
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: orderlist.php');
        exit;
    }

    // Lấy danh sách sản phẩm trong đơn hàng
    $sqlProducts = "SELECT 
        p.id,
        p.name,
        p.image,
        p.price,
        p.saleprice,
        po.quantity,
        (CASE 
            WHEN p.saleprice > 0 THEN p.saleprice * po.quantity
            ELSE p.price * po.quantity
        END) as subtotal
    FROM product_order po
    JOIN products p ON po.product_id = p.id
    WHERE po.order_id = :order_id";
    
    $stmtProducts = $conn->prepare($sqlProducts);
    $stmtProducts->execute([':order_id' => $orderId]);
    $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Order detail fetch error: ' . $e->getMessage());
    header('Location: orderlist.php');
    exit;
}

// Xác định trạng thái
$statusClass = 'warning';
$statusText = 'Chờ xử lý';
if ($order['status'] == 1) {
    $statusClass = 'success';
    $statusText = 'Đã hoàn thành';
} elseif ($order['status'] == 2) {
    $statusClass = 'danger';
    $statusText = 'Đã hủy';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $order['order_id'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .order-detail-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .print-btn {
            background-color: #6c757d;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold">Chi tiết đơn hàng #<?= $order['order_id'] ?></h3>
        <div>
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fa fa-print"></i> In đơn hàng
            </button>
            <a href="orderlist.php" class="btn btn-primary">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-md-6 mb-4">
            <div class="card order-detail-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-info-circle"></i> Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <p><span class="info-label">Mã đơn hàng:</span> #<?= $order['order_id'] ?></p>
                    <p><span class="info-label">Ngày đặt:</span> <?= date('d/m/Y H:i:s', strtotime($order['date_order'])) ?></p>
                    <p><span class="info-label">Trạng thái:</span> 
                        <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                    </p>
                    <p><span class="info-label">Tổng tiền:</span> 
                        <strong class="text-danger"><?= number_format($order['total']) ?> đ</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="col-md-6 mb-4">
            <div class="card order-detail-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa fa-user"></i> Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <p><span class="info-label">Họ và tên:</span> <?= htmlspecialchars($order['fullname']) ?></p>
                    <p><span class="info-label">Số điện thoại:</span> <?= htmlspecialchars($order['phone']) ?></p>
                    <p><span class="info-label">Email:</span> <?= htmlspecialchars($order['email']) ?></p>
                    <p><span class="info-label">Địa chỉ:</span> <?= htmlspecialchars($order['address']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết sản phẩm -->
    <div class="card order-detail-card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fa fa-shopping-cart"></i> Chi tiết sản phẩm</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($product['image']) ?>" 
                                         class="product-img" 
                                         alt="<?= htmlspecialchars($product['name']) ?>">
                                </td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td>
                                    <?php if ($product['saleprice'] > 0): ?>
                                        <del class="text-muted"><?= number_format($product['price']) ?> đ</del><br>
                                        <strong class="text-danger"><?= number_format($product['saleprice']) ?> đ</strong>
                                    <?php else: ?>
                                        <?= number_format($product['price']) ?> đ
                                    <?php endif; ?>
                                </td>
                                <td><?= $product['quantity'] ?></td>
                                <td><strong><?= number_format($product['subtotal']) ?> đ</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                            <td><strong class="text-danger fs-5"><?= number_format($order['total']) ?> đ</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Cập nhật trạng thái -->
    <div class="card order-detail-card no-print">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fa fa-edit"></i> Cập nhật trạng thái</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="order-update-status.php" class="row align-items-end">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <div class="col-md-6">
                    <label class="form-label">Chọn trạng thái mới</label>
                    <select name="new_status" class="form-select" required>
                        <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Cập nhật trạng thái
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>