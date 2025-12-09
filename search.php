<?php
require_once('model/connect.php');
session_start();
// Initialize variables
$searchKeyword = '';
$searchResults = [];
$totalResults = 0;
$errorMessage = '';
$hasSearched = false;

// Handle search request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hasSearched = true;
    
    if (isset($_POST['search']) && !empty(trim($_POST['search']))) {
        $searchKeyword = trim($_POST['search']);
        
        // Sanitize search keyword (remove special characters for display)
        $displayKeyword = htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8');
        
        try {
            // Prepare and execute search query
            $sql = "SELECT id, image, name, price, saleprice, quantity, status 
                    FROM products 
                    WHERE name LIKE :keyword 
                    OR description LIKE :keyword 
                    OR keyword LIKE :keyword
                    ORDER BY id DESC";
            
            $stmt = $conn->prepare($sql);
            $searchParam = "%{$searchKeyword}%";
            $stmt->bindParam(':keyword', $searchParam, PDO::PARAM_STR);
            $stmt->execute();
            
            $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalResults = count($searchResults);
            
        } catch (PDOException $e) {
            error_log('Search query error: ' . $e->getMessage());
            $errorMessage = 'Đã xảy ra lỗi khi tìm kiếm. Vui lòng thử lại sau.';
        }
        
    } else {
        $errorMessage = 'Vui lòng nhập từ khóa tìm kiếm.';
    }
}

// Calculate cart items count
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

/**
 * Format price with Vietnamese currency
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

/**
 * Calculate sale price
 * @param float $originalPrice
 * @param float $salePercent
 * @return float
 */
function calculateSalePrice($originalPrice, $salePercent) {
    return $originalPrice - ($originalPrice * $salePercent / 100);
}

/**
 * Get product availability status
 * @param array $product
 * @return bool
 */
function isProductAvailable($product) {
    return ($product['status'] == 1 || $product['quantity'] > 0);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm<?php echo $searchKeyword ? ': ' . htmlspecialchars($searchKeyword) : ''; ?> - MyLiShop</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Tìm kiếm sản phẩm thời trang tại MyLiShop - <?php echo htmlspecialchars($searchKeyword); ?>">
    <meta name="keywords" content="tìm kiếm thời trang, <?php echo htmlspecialchars($searchKeyword); ?>, MyLiShop">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/logohong.png">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom CSS - Main Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Custom CSS - Search Page Stylesheet -->
    <link rel="stylesheet" href="css/search.css">
</head>

<body>

<!-- Header -->
<?php include("model/header.php"); ?>

<!-- Search Header -->
<div class="search-header">
    <div class="container">
        <h1 class="search-title">
            <i class="fas fa-search"></i> Kết quả tìm kiếm
        </h1>
        <?php if ($searchKeyword): ?>
            <p class="search-subtitle">
                Từ khóa: <span class="search-keyword"><?= htmlspecialchars($searchKeyword); ?></span>
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
        </ol>
    </nav>
    
    <!-- Error Message -->
    <?php if ($errorMessage): ?>
        <div class="error-alert" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Lỗi!</strong> <?= htmlspecialchars($errorMessage); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($hasSearched && !$errorMessage): ?>
        
        <!-- Results Summary -->
        <div class="results-summary">
            <div class="results-count">
                Tìm thấy <span class="number"><?= $totalResults; ?></span> sản phẩm
            </div>
            
            <?php if ($totalResults > 0): ?>
            <div class="filter-section">
                <label for="sortSelect" class="mb-0">Sắp xếp:</label>
                <select id="sortSelect" class="sort-select" aria-label="Sắp xếp sản phẩm">
                    <option value="default">Mặc định</option>
                    <option value="price-asc">Giá: Thấp đến cao</option>
                    <option value="price-desc">Giá: Cao đến thấp</option>
                    <option value="name-asc">Tên: A-Z</option>
                    <option value="name-desc">Tên: Z-A</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($totalResults > 0): ?>
            
            <!-- Product Grid -->
            <div class="product-grid" id="productGrid">
                <?php foreach ($searchResults as $product): 
                    $productId = (int)$product['id'];
                    $productName = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
                    $productImage = htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8');
                    $originalPrice = (float)$product['price'];
                    $salePercent = (float)$product['saleprice'];
                    $hasSale = $salePercent > 0;
                    $finalPrice = $hasSale ? calculateSalePrice($originalPrice, $salePercent) : $originalPrice;
                    $isAvailable = isProductAvailable($product);
                ?>
                
                <article class="product-card" data-price="<?= $finalPrice; ?>" data-name="<?= strtolower($productName); ?>">
                    <!-- Product Image -->
                    <div class="product-image-wrapper">
                        <img src="<?= $productImage; ?>" alt="<?= $productName; ?>" class="product-image" loading="lazy">
                        
                        <!-- Badges -->
                        <div class="product-badges">
                            <?php if ($hasSale): ?>
                                <span class="badge-sale">
                                    <i class="fas fa-fire"></i> -<?= $salePercent; ?>%
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!$isAvailable): ?>
                                <span class="badge-out-of-stock">
                                    <i class="fas fa-times-circle"></i> Hết hàng
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Product Body -->
                    <div class="product-body">
                        <h3 class="product-name"><?= $productName; ?></h3>
                        
                        <!-- Price -->
                        <div class="product-price-section">
                            <span class="product-current-price"><?= formatPrice($finalPrice); ?></span>
                            <?php if ($hasSale): ?>
                                <span class="product-original-price"><?= formatPrice($originalPrice); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Actions -->
                        <div class="product-actions">
                            <a href="detail.php?id=<?= $productId; ?>" class="btn-view-detail" aria-label="Xem chi tiết <?= $productName; ?>">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            
                            <?php if ($isAvailable): ?>
                                <a href="addcart.php?id=<?= $productId; ?>" class="btn-add-to-cart" title="Thêm vào giỏ hàng" aria-label="Thêm <?= $productName; ?> vào giỏ hàng">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
                
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            
            <!-- No Results -->
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h2 class="no-results-title">Không tìm thấy sản phẩm phù hợp</h2>
                <p class="no-results-text">
                    Rất tiếc, chúng tôi không tìm thấy sản phẩm nào với từ khóa 
                    "<strong><?= htmlspecialchars($searchKeyword); ?></strong>"
                </p>
                
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Quay về trang chủ
                </a>
                
                <!-- Suggestions -->
                <div class="suggestions">
                    <h4 class="suggestions-title">Gợi ý tìm kiếm:</h4>
                    <ul>
                        <li>Kiểm tra lại chính tả từ khóa</li>
                        <li>Thử sử dụng từ khóa ngắn gọn hơn</li>
                        <li>Sử dụng các từ khóa tổng quát hơn</li>
                        <li>Thử tìm với các từ đồng nghĩa</li>
                    </ul>
                </div>
            </div>
            
        <?php endif; ?>
        
    <?php endif; ?>
    
</div>

<!-- Footer -->
<div class="container">
    <?php include("model/footer.php"); ?>
</div>

<!-- Back to Top -->
<a href="#" class="back-to-top" aria-label="Lên đầu trang">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="js/search.js"></script>

</body>
</html>