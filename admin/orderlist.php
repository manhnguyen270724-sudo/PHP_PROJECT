<?php
require_once('../model/connect.php');

// Xử lý thông báo
$alert = '';
if (isset($_GET['us'])) $alert = '<div class="alert alert-success">Cập nhật trạng thái thành công!</div>';
if (isset($_GET['uf'])) $alert = '<div class="alert alert-danger">Cập nhật thất bại!</div>';
if (isset($_GET['ds'])) $alert = '<div class="alert alert-success">Xóa đơn hàng thành công!</div>';
if (isset($_GET['df'])) $alert = '<div class="alert alert-danger">Xóa thất bại!</div>';

try {
    // LOGIC MỚI: Ưu tiên lấy thông tin người nhận trong bảng orders
    // Nếu customer_name trong orders rỗng thì mới lấy fullname trong users
    $sql = "SELECT 
        o.id as order_id,
        o.total,
        o.date_order,
        o.status,
        COALESCE(NULLIF(o.customer_name, ''), u.fullname, 'Khách vãng lai') as fullname_display,
        COALESCE(NULLIF(o.customer_phone, ''), u.phone, '') as phone_display,
        COALESCE(NULLIF(o.customer_address, ''), u.address, '') as address_display,
        GROUP_CONCAT(CONCAT(p.name, ' (x', po.quantity, ')') SEPARATOR ', ') as products
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN product_order po ON po.order_id = o.id
    LEFT JOIN products p ON po.product_id = p.id
    GROUP BY o.id, o.total, o.date_order, o.status, 
             o.customer_name, o.customer_phone, o.customer_address,
             u.fullname, u.phone, u.address
    ORDER BY o.date_order DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Order list error: ' . $e->getMessage());
    $orders = [];
    $alert .= '<div class="alert alert-danger">Lỗi DB: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-completed { background-color: #28a745; color: #fff; }
        .badge-cancelled { background-color: #dc3545; color: #fff; }
        .order-card { border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .order-header { background-color: #f8f9fa; padding: 15px; border-bottom: 1px solid #dee2e6; border-radius: 8px 8px 0 0; }
        .order-body { padding: 15px; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Quản lý đơn hàng</h3>
        <a href="index.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Dashboard</a>
    </div>

    <?= $alert ?>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info text-center">Chưa có đơn hàng nào.</div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="row align-items-center">
                        <div class="col-md-3"><strong>#<?= $order['order_id'] ?></strong> - <?= date('d/m/Y H:i', strtotime($order['date_order'])) ?></div>
                        <div class="col-md-3">
                            <?php
                            $stt = $order['status'];
                            $cls = ($stt == 1) ? 'badge-completed' : (($stt == 2) ? 'badge-cancelled' : 'badge-pending');
                            $txt = ($stt == 1) ? 'Hoàn thành' : (($stt == 2) ? 'Đã hủy' : 'Chờ xử lý');
                            ?>
                            <span class="badge <?= $cls ?>"><?= $txt ?></span>
                        </div>
                        <div class="col-md-3 text-end"><strong class="text-danger"><?= number_format($order['total']) ?> đ</strong></div>
                    </div>
                </div>
                <div class="order-body">
                    <div class="row">
                        <div class="col-md-5">
                            <h6 class="text-primary"><i class="fa fa-user"></i> Người nhận</h6>
                            <p class="mb-1"><strong>Tên:</strong> <?= htmlspecialchars($order['fullname_display']) ?></p>
                            <p class="mb-1"><strong>SĐT:</strong> <?= htmlspecialchars($order['phone_display']) ?></p>
                            <p class="mb-1"><strong>Đ/C:</strong> <?= htmlspecialchars($order['address_display']) ?></p>
                        </div>
                        <div class="col-md-7">
                            <h6 class="text-success"><i class="fa fa-box"></i> Sản phẩm</h6>
                            <p class="text-muted small"><?= htmlspecialchars($order['products']) ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <form method="POST" action="order-update-status.php" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                            <select name="new_status" class="form-select form-select-sm" style="width: auto;">
                                <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Đã hoàn thành</option>
                                <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>Hủy đơn</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                        </form>
                        <div>
                            <a href="order-detail.php?id=<?= $order['order_id'] ?>" class="btn btn-info btn-sm text-white"><i class="fa fa-eye"></i> Chi tiết</a>
                            <a href="order-delete.php?id=<?= $order['order_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa đơn này?')"><i class="fa fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>