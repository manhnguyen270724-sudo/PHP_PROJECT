<?php
/**
 * Add Product Page
 * Upload form with validation and error handling
 */
require_once('../model/connect.php');

// Handle image upload error messages
$noimage = '';
if (isset($_GET['notimage'])) {
    switch($_GET['notimage']){
        case '1': $noimage = 'Vui lòng chọn file hình hợp lệ!'; break;
        case '2': $noimage = 'File không phải hình ảnh!'; break;
        case '3': $noimage = 'Upload ảnh thất bại! Vui lòng thử lại.'; break;
        case '4': $noimage = 'File quá lớn, tối đa 5MB!'; break;
        case '5': $noimage = 'Không thể tạo thư mục upload!'; break;
        case '6': $noimage = 'Chỉ chấp nhận file JPG, PNG, GIF hoặc WebP!'; break;
        case '7': $noimage = 'Lỗi lưu dữ liệu vào database!'; break;
        case 'invalid': $noimage = 'Vui lòng điền đầy đủ các trường bắt buộc!'; break;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- error-message style moved to css/style.css -->
</head>
<body>
<div class="container py-5">
    <h1 class="text-center mb-4">Thêm sản phẩm</h1>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <?php if($noimage): ?>
                <div class="error-message">
                    <strong>Lỗi:</strong> <?php echo htmlspecialchars($noimage); ?>
                </div>
            <?php endif; ?>
            
            <form action="productadd-back.php" method="POST" enctype="multipart/form-data">
                
                <!-- Tên sản phẩm -->
                <div class="mb-3">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control" name="txtName" placeholder="Nhập tên sản phẩm" required>
                </div>

                <!-- Danh mục sản phẩm -->
                <div class="mb-3">
                    <label class="form-label">Danh mục sản phẩm</label>
                    <select class="form-control" name="category" required>
                        <?php
                        $stmt = $conn->query("SELECT id, name FROM categories");
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($categories as $cat) {
                            echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Giá & Sale -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Giá sản phẩm</label>
                        <input type="number" class="form-control" name="txtPrice" placeholder="Nhập giá sản phẩm" min="20000" step="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phần trăm giảm (nếu có)</label>
                        <input type="number" class="form-control" name="txtSalePrice" placeholder="Nhập phần trăm giảm" value="0" min="0" max="100" step="1">
                    </div>
                </div>

                <!-- Số lượng -->
                <div class="mb-3">
                    <label class="form-label">Số lượng sản phẩm</label>
                    <input type="number" class="form-control" name="txtNumber" placeholder="Nhập số lượng sản phẩm" min="0" step="1" required>
                </div>

                <!-- Hình ảnh -->
                <div class="mb-3">
                    <label class="form-label">Chọn hình ảnh sản phẩm</label>
                    <input type="file" class="form-control" name="FileImage" accept="image/jpeg,image/png,image/gif,image/webp" required>
                    <div class="form-text">
                        Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP), kích thước tối đa 5MB.
                    </div>
                </div>

                <!-- Từ khóa tìm kiếm -->
                <div class="mb-3">
                    <label class="form-label">Từ khóa tìm kiếm</label>
                    <input type="text" class="form-control" name="txtKeyword" placeholder="Nhập từ khóa tìm kiếm">
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label class="form-label">Mô tả sản phẩm</label>
                    <textarea class="form-control" name="txtDescript" rows="4" placeholder="Nhập mô tả sản phẩm"></textarea>
                </div>

                <!-- Buttons -->
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <button type="submit" name="addProduct" class="btn btn-warning w-100">Thêm sản phẩm</button>
                    </div>
                    <div class="col-md-6 mb-2">
                        <button type="reset" class="btn btn-secondary w-100">Thiết lập lại</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>