<?php
session_start();
require_once('model/connect.php');

// Kiểm tra ID sản phẩm
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=invalid_product');
    exit;
}

$productId = (int)$_GET['id'];

// Lấy thông tin sản phẩm từ database
try {
    $stmt = $conn->prepare("SELECT id, name, image, price, saleprice, quantity FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Location: index.php?error=product_not_found');
        exit;
    }
    
    // Kiểm tra sản phẩm còn hàng
    if ($product['quantity'] <= 0) {
        header('Location: detail.php?id=' . $productId . '&error=out_of_stock');
        exit;
    }
    
    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Kiểm tra sản phẩm đã có trong giỏ chưa
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            // Kiểm tra số lượng tồn kho
            if ($item['quantity'] < $product['quantity']) {
                $item['quantity']++;
                $found = true;
            } else {
                header('Location: detail.php?id=' . $productId . '&error=max_quantity');
                exit;
            }
            break;
        }
    }
    
    // Nếu chưa có thì thêm mới
    if (!$found) {
        // Calculate actual price with sale percentage if applicable
        $salePercent = (float)$product['saleprice'];
        $finalPrice = $salePercent > 0 ? $product['price'] - ($product['price'] * $salePercent / 100) : $product['price'];
        
        $cartItem = [
            'id' => $product['id'],
            'name' => $product['name'],
            'image' => $product['image'],
            'price' => $finalPrice,
            'original_price' => $product['price'],
            'sale_percent' => $salePercent,
            'quantity' => 1,
            'max_quantity' => $product['quantity']
        ];
        $_SESSION['cart'][] = $cartItem;
    }
    
    // Redirect về trang giỏ hàng
    header('Location: view-cart.php?success=added');
    exit;
    
} catch (PDOException $e) {
    error_log('Add to cart error: ' . $e->getMessage());
    header('Location: index.php?error=database_error');
    exit;
}
?>