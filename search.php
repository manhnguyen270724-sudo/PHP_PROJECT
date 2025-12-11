<?php
session_start(); // SỬA LỖI 1: Đưa lên đầu trang để tránh lỗi Header
require_once('model/connect.php');

// Khởi tạo biến
$searchKeyword = '';
$searchResults = [];
$totalResults = 0;
$errorMessage = '';
$hasSearched = false;

// Xử lý khi người dùng tìm kiếm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hasSearched = true;
    
    if (isset($_POST['search']) && !empty(trim($_POST['search']))) {
        $searchKeyword = trim($_POST['search']);
        $displayKeyword = htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8');
        
        try {
            // SỬA LỖI 2: Sử dụng :key1, :key2, :key3 vì ATTR_EMULATE_PREPARES = false
            // Câu lệnh SQL lấy dữ liệu để đổ ra div
            $sql = "SELECT id, image, name, price, saleprice, quantity, status 
                    FROM products 
                    WHERE name LIKE :key1 
                    OR description LIKE :key2 
                    OR keyword LIKE :key3
                    ORDER BY id DESC";
            
            $stmt = $conn->prepare($sql);
            $searchParam = "%{$searchKeyword}%";
            
            // Bind tham số riêng biệt cho từng vị trí
            $stmt->bindParam(':key1', $searchParam, PDO::PARAM_STR);
            $stmt->bindParam(':key2', $searchParam, PDO::PARAM_STR);
            $stmt->bindParam(':key3', $searchParam, PDO::PARAM_STR);
            
            $stmt->execute();
            
            // Lấy toàn bộ kết quả vào mảng
            $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalResults = count($searchResults);
            
        } catch (PDOException $e) {
            error_log('Lỗi tìm kiếm: ' . $e->getMessage());
            $errorMessage = 'Đã xảy ra lỗi khi tìm kiếm. Vui lòng thử lại sau.';
        }
        
    } else {
        $errorMessage = 'Vui lòng nhập từ khóa tìm kiếm.';
    }
}

// Các hàm hỗ trợ hiển thị
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

function calculateSalePrice($originalPrice, $salePercent) {
    return $originalPrice - ($originalPrice * $salePercent / 100);
}

function isProductAvailable($product) {
    return ($product['status'] == 1 || $product['quantity'] > 0);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm: <?php echo htmlspecialchars($searchKeyword); ?> - MyLiShop</title>
    <link rel="icon" type="image/png" href="images/logohong.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/search.css">
</head>

<body>

<?php include("model/header.php"); ?>

<div class="search-header">
    <div class="container">
        <h1 class="search-title"><i class="fas fa-search"></i> Kết quả tìm kiếm</h1>
        <?php if ($searchKeyword): ?>
            <p class="search-subtitle">Từ khóa: <span class="search-keyword"><?= htmlspecialchars($searchKeyword); ?></span></p>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($hasSearched && !$errorMessage): ?>
        
        <div class="results-summary mb-4">
            <div class="results-count">
                Tìm thấy <span class="text-danger fw-bold"><?= $totalResults; ?></span> sản phẩm
            </div>
        </div>
        
        <?php if ($totalResults > 0): ?>
            
            <div class="product-grid" id="productGrid">
                
                <?php foreach ($searchResults as $product): 
                    // Xử lý dữ liệu trước khi hiển thị
                    $productId = (int)$product['id'];
                    $productName = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
                    $productImage = htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8');
                    $originalPrice = (float)$product['price'];
                    $salePercent = (float)$product['saleprice'];
                    $hasSale = $salePercent > 0;
                    $finalPrice = $hasSale ? calculateSalePrice($originalPrice, $salePercent) : $originalPrice;
                    $isAvailable = isProductAvailable($product);
                ?>
                
                <article class="product-card" data-price="<?= $finalPrice; ?>" data-name="<?= htmlspecialchars($productName); ?>">
                    <div class="product-image-wrapper">
                        <img src="<?= $productImage; ?>" alt="<?= $productName; ?>" class="product-image" loading="lazy">
                        
                        <div class="product-badges">
                            <?php if ($hasSale): ?>
                                <span class="badge-sale"><i class="fas fa-fire"></i> -<?= $salePercent; ?>%</span>
                            <?php endif; ?>
                            
                            <?php if (!$isAvailable): ?>
                                <span class="badge-out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="product-body">
                        <h3 class="product-name"><?= $productName; ?></h3>
                        
                        <div class="product-price-section">
                            <span class="product-current-price"><?= formatPrice($finalPrice); ?></span>
                            <?php if ($hasSale): ?>
                                <span class="product-original-price"><?= formatPrice($originalPrice); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <a href="detail.php?id=<?= $productId; ?>" class="btn-view-detail">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            
                            <?php if ($isAvailable): ?>
                                <a href="addcart.php?id=<?= $productId; ?>" class="btn-add-to-cart">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
                
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="no-results text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h2 class="no-results-title">Không tìm thấy sản phẩm nào</h2>
                <a href="index.php" class="btn btn-primary mt-3"><i class="fas fa-home"></i> Về trang chủ</a>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
    
</div>

<div class="container">
    <?php include("model/footer.php"); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>