<?php
require_once('../model/connect.php');

if (!isset($_POST['addProduct'])) {
    header("Location: productadd.php");
    exit();
}

// --- Lấy dữ liệu form ---
$namePr = trim($_POST['txtName'] ?? '');
$categoryPr = $_POST['category'] ?? '';
$pricePr = (float)($_POST['txtPrice'] ?? 0);
$salePercent = (float)($_POST['txtSalePrice'] ?? 0);
$quantityPr = (int)($_POST['txtNumber'] ?? 0);
$keywordPr = trim($_POST['txtKeyword'] ?? '');
$descriptPr = trim($_POST['txtDescript'] ?? '');
$status = 1; // mặc định active

// Tính giá sau giảm
$salePricePr = ($salePercent > 0) ? round($pricePr * (100 - $salePercent) / 100) : null;

// Validate required fields
if (empty($namePr) || empty($categoryPr) || $pricePr <= 0) {
    header("Location: productadd.php?error=invalid");
    exit();
}

// --- Xử lý upload ảnh ---
if (!isset($_FILES['FileImage']) || $_FILES['FileImage']['error'] != 0) {
    header("Location: productadd.php?notimage=1");
    exit();
}

$imageTmp = $_FILES['FileImage']['tmp_name'];
$imageName = basename($_FILES['FileImage']['name']);
$imageName = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $imageName);
$targetDir = "../uploads/";
$targetFile = $targetDir . $imageName;

// Tạo thư mục upload nếu chưa tồn tại
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0777, true)) {
        header("Location: productadd.php?notimage=5");
        exit();
    }
}

// Kiểm tra thư mục có quyền ghi không
if (!is_writable($targetDir)) {
    header("Location: productadd.php?notimage=5");
    exit();
}

// Kiểm tra file ảnh
$check = getimagesize($imageTmp);
if ($check === false) {
    header("Location: productadd.php?notimage=2");
    exit();
}

// Kiểm tra định dạng file hợp lệ
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($check['mime'], $allowedTypes)) {
    header("Location: productadd.php?notimage=6");
    exit();
}

// Kiểm tra kích thước file (5MB)
$maxFileSize = 5 * 1024 * 1024;
if ($_FILES['FileImage']['size'] > $maxFileSize) {
    header("Location: productadd.php?notimage=4");
    exit();
}

// Nếu file tồn tại → đổi tên
$i = 1;
$baseName = pathinfo($imageName, PATHINFO_FILENAME);
$ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
$imageName = $baseName . "." . $ext;
$targetFile = $targetDir . $imageName;

while (file_exists($targetFile)) {
    $imageName = $baseName . "_" . $i . "." . $ext;
    $targetFile = $targetDir . $imageName;
    $i++;
}

// Di chuyển file
if (!move_uploaded_file($imageTmp, $targetFile)) {
    // Kiểm tra lỗi cụ thể
    $uploadError = $_FILES['FileImage']['error'];
    switch ($uploadError) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            header("Location: productadd.php?notimage=4");
            break;
        default:
            header("Location: productadd.php?notimage=3");
    }
    exit();
}

// --- Insert vào DB ---
try {
    $sql = "INSERT INTO products 
            (name, category_id, image, description, price, saleprice, created, quantity, keyword, status)
            VALUES (:name, :category, :image, :description, :price, :saleprice, NOW(), :quantity, :keyword, :status)";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':name' => $namePr,
        ':category' => $categoryPr,
        ':image' => "uploads/" . $imageName,
        ':description' => $descriptPr,
        ':price' => $pricePr,
        ':saleprice' => $salePricePr,
        ':quantity' => $quantityPr,
        ':keyword' => $keywordPr,
        ':status' => $status
    ]);

    // --- Redirect về trang index ---
    if ($result) {
        header("Location: index.php?addps=success");
    } else {
        header("Location: productadd.php?notimage=7");
    }
} catch (PDOException $e) {
    // Xóa file ảnh đã upload nếu insert thất bại
    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    header("Location: productadd.php?notimage=7");
}
exit();
?>