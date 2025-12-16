<?php
require_once("model/connect.php");
error_reporting(0);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Fetch related products (same category, exclude current)
$stmtRelated = $conn->prepare("SELECT id, image, name, price, saleprice FROM products WHERE category_id = :cat AND id != :id LIMIT 4");
$stmtRelated->execute([
    ':cat' => $product['category_id'],
    ':id' => $id
]);
$relatedProducts = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);

$productImage = !empty($product['image']) ? $product['image'] : "images/no-image.jpg";
$productName = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
$productDesc = htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8');
$productPrice = number_format($product['price']);
$hasSale = $product['saleprice'] > 0;
$salePrice = $hasSale ? number_format($product['price'] - ($product['price'] * $product['saleprice'] / 100)) : null;
$isInStock = $product['status'] == 1 || $product['quantity'] > 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiêu đề trang - Fashion MyLiShop</title>
    
    <link rel="icon" type="image/png" href="images/logohong.png">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="admin/bower_components/font-awesome/css/font-awesome.min.css">
    
    <!-- Bootstrap 3 -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js" charset="utf-8"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <!-- Animate CSS -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
    
    <!-- Custom JS -->
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Header -->
<?php include("model/header.php"); ?>

<!-- Breadcrumb -->
<div class="container">
    <nav aria-label="breadcrumb" class="mt-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $productName; ?></li>
        </ol>
    </nav>
</div>

<!-- Product Detail -->
<div class="container">
    <div class="detail-container">
        <div class="row g-0">
            
            <!-- Product Gallery -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <img src="<?= $productImage; ?>" alt="<?= $productName; ?>" class="main-image">
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info-section">
                    
                    <!-- Badges -->
                    <?php if ($product['category_id'] == 3): ?>
                        <span class="product-badge badge-new">
                            <i class="fas fa-star"></i> Sản phẩm mới
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($hasSale): ?>
                        <span class="product-badge badge-sale">
                            <i class="fas fa-fire"></i> Giảm <?= $product['saleprice']; ?>%
                        </span>
                    <?php endif; ?>
                    
                    <!-- Product Title -->
                    <h1 class="product-title"><?= $productName; ?></h1>
                    
                    <!-- Price Section -->
                    <div class="price-section">
                        <?php if ($hasSale): ?>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="current-price"><?= $salePrice; ?>đ</p>
                                <span class="original-price"><?= $productPrice; ?>đ</span>
                                <span class="save-badge">Tiết kiệm <?= number_format($product['price'] * $product['saleprice'] / 100); ?>đ</span>
                            </div>
                        <?php else: ?>
                            <p class="current-price"><?= $productPrice; ?>đ</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="stock-status <?= $isInStock ? 'in-stock' : 'out-of-stock'; ?>">
                        <i class="fas <?= $isInStock ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        <span><?= $isInStock ? 'Còn hàng' : 'Hết hàng'; ?></span>
                    </div>
                    
                    <!-- Description -->
                    <?php if (!empty($productDesc)): ?>
                    <div class="description-section">
                        <h3 class="description-title">
                            <i class="fas fa-info-circle"></i>
                            Mô tả sản phẩm
                        </h3>
                        <p class="description-text"><?= $productDesc; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Action Buttons -->
                    <?php if ($isInStock): ?>
                    <div class="action-buttons">
                        <a href="addcart.php?id=<?= $id; ?>" class="btn btn-add-cart">
                            <i class="fas fa-shopping-cart"></i>
                            Thêm vào giỏ hàng
                        </a>
                        <a href="addcart.php?id=<?= $id; ?>" class="btn btn-buy-now">
                            <i class="fas fa-bolt"></i>
                            Mua ngay
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        Sản phẩm hiện đang hết hàng. Vui lòng quay lại sau!
                    </div>
                    <?php endif; ?>
                    
                    <!-- Features List -->
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <span class="feature-text">Giao hàng toàn quốc - Nhanh chóng</span>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <span class="feature-text">Thanh toán khi nhận hàng (COD)</span>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <span class="feature-text">Đổi trả miễn phí trong 15 ngày</span>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <span class="feature-text">Bảo hành chính hãng - Uy tín</span>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <span class="feature-text">Hỗ trợ 24/7 - Tận tâm</span>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<div class="container">
    <div class="related-products">
        <h2 class="related-title">Sản phẩm liên quan</h2>
        
        <div class="row g-4">
            <?php foreach ($relatedProducts as $related): 
                $relatedPrice = number_format($related['price']);
                $relatedSalePrice = $related['saleprice'] > 0 
                    ? number_format($related['price'] - ($related['price'] * $related['saleprice'] / 100)) 
                    : null;
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="detail.php?id=<?= $related['id']; ?>" class="text-decoration-none">
                    <div class="related-card">
                        <img src="<?= htmlspecialchars($related['image']); ?>" 
                             alt="<?= htmlspecialchars($related['name']); ?>" 
                             class="related-image">
                        <div class="related-body">
                            <h5 class="related-name"><?= htmlspecialchars($related['name']); ?></h5>
                            <div class="related-price">
                                <?php if ($relatedSalePrice): ?>
                                    <?= $relatedSalePrice; ?>đ
                                    <small class="text-muted text-decoration-line-through ms-2" style="font-size: 0.85rem;">
                                        <?= $relatedPrice; ?>đ
                                    </small>
                                <?php else: ?>
                                    <?= $relatedPrice; ?>đ
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Footer -->
<div class="container">
    <?php include("model/footer.php"); ?>
</div>

<!-- Back to top -->
<a href="#" class="back-to-top">
    <i class="fas fa-arrow-up"></i>
</a>

<script>
    // Back to top button
    const backToTop = document.querySelector('.back-to-top');
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'flex';
        } else {
            backToTop.style.display = 'none';
        }
    });
    
    backToTop.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>

</body>
</html>