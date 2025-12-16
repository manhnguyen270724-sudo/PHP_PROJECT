<?php
require_once("../model/connect.php");

$product = null;
$errorMsg = '';
$successMsg = '';

// Validate product ID
if (!isset($_GET['idProduct']) || !is_numeric($_GET['idProduct'])) {
    $errorMsg = 'ID sản phẩm không hợp lệ';
} else {
    $idProduct = (int)$_GET['idProduct'];
    
    // Messages
    if (isset($_GET['es'])) $successMsg = 'Bạn đã sửa sản phẩm thành công!';
    if (isset($_GET['ef'])) $errorMsg = 'Sửa sản phẩm thất bại!';
    if (isset($_GET['error'])) $errorMsg = 'Đã xảy ra lỗi. Vui lòng thử lại.';

    // Fetch product
    try {
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $idProduct]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) $errorMsg = 'Sản phẩm không tồn tại';
    } catch (PDOException $e) {
        error_log('Product fetch error: ' . $e->getMessage());
        $errorMsg = 'Lỗi khi tải sản phẩm';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa sản phẩm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- admin form styles moved to css/style.css -->
</head>
<body>
<div class="container py-5">
    <h2 class="text-center text-primary mb-4">Chỉnh sửa sản phẩm</h2>

    <div class="row justify-content-center">
        <div class="col-lg-8 form-section">
            <?php if ($successMsg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMsg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMsg): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMsg); ?></div>
            <?php elseif ($product): ?>
                <?php $thumbImage = "../" . htmlspecialchars($product['image']); ?>
                <form action="productedit-back.php?idProduct=<?= (int)$product['id']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" class="form-control" name="txtName" value="<?= htmlspecialchars($product['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Danh mục sản phẩm</label>
                        <select class="form-select" name="category" required>
                            <?php
                            try {
                                $currentCatId = (int)$product['category_id'];
                                $sql = "SELECT id, name FROM categories ORDER BY name ASC";
                                $stmt = $conn->query($sql);
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categories as $cat) {
                                    $selected = ($cat['id'] == $currentCatId) ? "selected" : "";
                                    echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['name']) . "</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option value=''>Lỗi tải danh mục</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hình ảnh sản phẩm</label>
                        <input class="form-control mb-2" type="file" name="FileImage" accept="image/*">
                        <div>
                            <img src="<?= $thumbImage; ?>" class="thumb-img" alt="<?= htmlspecialchars($product['name']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả sản phẩm</label>
                        <textarea class="form-control" name="txtDescript" rows="4"><?= htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Giá sản phẩm</label>
                            <input type="number" class="form-control" name="txtPrice" value="<?= (float)$product['price']; ?>" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phần trăm giảm (nếu có)</label>
                            <input type="number" class="form-control" name="txtSalePrice" value="<?= (float)$product['saleprice']; ?>" min="0" max="100" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số lượng sản phẩm</label>
                        <input type="number" class="form-control" name="txtNumber" value="<?= (int)$product['quantity']; ?>" min="0" step="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Từ khóa tìm kiếm</label>
                        <input type="text" class="form-control" name="txtKeyword" value="<?= htmlspecialchars($product['keyword']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tình trạng sản phẩm</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="1" <?= ($product['status'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Còn hàng</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="0" <?= ($product['status'] == 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Hết hàng</label>
                        </div>
                    </div>

                    <button type="submit" name="editProduct" class="btn btn-warning btn-lg btn-submit"><i class="fa fa-pen"></i> Chỉnh sửa sản phẩm</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Không tìm thấy sản phẩm cần chỉnh sửa.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
