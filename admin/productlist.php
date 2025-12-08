<?php
require_once('../model/connect.php');

// Lấy danh sách sản phẩm
$sql = "SELECT id, name, image, price, saleprice, quantity FROM products ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Thông báo thêm sản phẩm
$alert = '';
if (isset($_GET['addps'])) {
    $alert = '<div class="alert alert-success">Thêm sản phẩm thành công!</div>';
} elseif (isset($_GET['addpf'])) {
    $alert = '<div class="alert alert-danger">Thêm sản phẩm thất bại!</div>';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Quản lý sản phẩm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">

    <!-- Custom -->
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
<div class="container py-5">
    <h3 class="fw-bold mb-4 text-center">Quản lý sản phẩm</h3>

    <?= $alert ?>

    <div class="mb-3 text-end">
        <a href="productadd.php" class="btn btn-success">Thêm sản phẩm mới</a>
    </div>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id']); ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($p['image']); ?>" alt="<?= htmlspecialchars($p['name']); ?>" width="100">
                    </td>
                    <td><?= htmlspecialchars($p['name']); ?></td>
                    <td>
                        <?= number_format($p['price']); ?> đ
                        <?php if ($p['saleprice'] !== null): ?>
                            <br><small class="text-success">Sale: <?= number_format($p['saleprice']); ?> đ</small>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['quantity']; ?></td>
                    <td>
                        <a href="product-edit.php?idProduct=<?= $p['id']; ?>" class="btn btn-sm btn-primary">Sửa</a>
                        <a href="product-delete.php?idProducts=<?= $p['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
