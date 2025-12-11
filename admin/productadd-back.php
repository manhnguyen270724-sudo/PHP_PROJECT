<?php
require_once __DIR__ . '/../model/connect.php';

// Kiểm tra xem user có nhấn nút thêm không
if (!isset($_POST['addProduct'])) {
    header("Location: productadd.php");
    exit();
}

// --- 1. LẤY DỮ LIỆU FORM ---
$namePr     = trim($_POST['txtName'] ?? '');
$categoryPr = $_POST['category'] ?? '';
$pricePr    = (float)($_POST['txtPrice'] ?? 0);
$salePercent = (float)($_POST['txtSalePrice'] ?? 0); // Đây là % giảm giá (VD: 10, 20)
$quantityPr = (int)($_POST['txtNumber'] ?? 0);
$keywordPr  = trim($_POST['txtKeyword'] ?? '');
$descriptPr = trim($_POST['txtDescript'] ?? '');
$status     = 1; // Mặc định là hiển thị

// Validate dữ liệu cơ bản
if (empty($namePr) || empty($categoryPr) || $pricePr <= 0) {
    header("Location: productadd.php?notimage=invalid");
    exit();
}

// --- 2. XỬ LÝ UPLOAD ẢNH ---
if (!isset($_FILES['FileImage']) || $_FILES['FileImage']['error'] != 0) {
    header("Location: productadd.php?notimage=1"); // Lỗi không có file
    exit();
}

$file = $_FILES['FileImage'];
$fileName = $file['name'];
$fileTmp  = $file['tmp_name'];
$fileSize = $file['size'];

// Kiểm tra định dạng file (Chỉ cho phép ảnh)
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExt, $allowed)) {
    header("Location: productadd.php?notimage=6"); // Sai định dạng
    exit();
}

// Kiểm tra dung lượng (Giới hạn 5MB)
if ($fileSize > 5 * 1024 * 1024) {
    header("Location: productadd.php?notimage=4"); // File quá lớn
    exit();
}

// --- QUAN TRỌNG: TẠO ĐƯỜNG DẪN TUYỆT ĐỐI ---
// __DIR__ giúp trỏ đúng về thư mục admin, sau đó đi ra ngoài (..) vào uploads
$targetDir = __DIR__ . '/../uploads/';

// Tạo thư mục uploads nếu chưa có
if (!file_exists($targetDir)) {
    if (!mkdir($targetDir, 0777, true)) {
        header("Location: productadd.php?notimage=5"); // Không tạo được thư mục
        exit();
    }
}

// Tạo tên file mới ngẫu nhiên để tránh trùng lặp (VD: 65a8b...jpg)
$newFileName = uniqid('img_', true) . "." . $fileExt;
$destination = $targetDir . $newFileName;

// Đường dẫn sẽ lưu vào Database (tương đối để hiển thị ở web)
$dbPath = "uploads/" . $newFileName;

// --- 3. TIẾN HÀNH UPLOAD VÀ LƯU DB ---
if (move_uploaded_file($fileTmp, $destination)) {
    try {
        $sql = "INSERT INTO products 
                (name, category_id, image, description, price, saleprice, created, quantity, keyword, status)
                VALUES (:name, :category, :image, :description, :price, :sale, NOW(), :quantity, :keyword, :status)";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            ':name'        => $namePr,
            ':category'    => $categoryPr,
            ':image'       => $dbPath,      // Lưu đường dẫn uploads/ten_anh.jpg
            ':description' => $descriptPr,
            ':price'       => $pricePr,
            ':sale'        => $salePercent, // Lưu đúng số % giảm giá (VD: 10)
            ':quantity'    => $quantityPr,
            ':keyword'     => $keywordPr,
            ':status'      => $status
        ]);

        if ($result) {
            header("Location: index.php?addps=success");
        } else {
            // Xóa ảnh nếu insert DB thất bại để tránh rác
            if (file_exists($destination)) unlink($destination);
            header("Location: productadd.php?notimage=7"); // Lỗi DB
        }

    } catch (PDOException $e) {
        if (file_exists($destination)) unlink($destination);
        error_log("Add Product Error: " . $e->getMessage());
        header("Location: productadd.php?notimage=7");
    }
} else {
    header("Location: productadd.php?notimage=3"); // Lỗi khi di chuyển file
}
exit();
?>