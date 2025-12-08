<?php

header('Content-Type: text/html; charset=utf-8');
require_once '../model/connect.php';

// Validate product ID
if (!isset($_GET['idProduct']) || !is_numeric($_GET['idProduct'])) {
    header('Location: productlist.php?error=invalid_id');
    exit;
}

$idProduct = (int)$_GET['idProduct'];

if (!isset($_POST['editProduct'])) {
    header('Location: productlist.php');
    exit;
}

// Get and sanitize input
$namePr = trim($_POST['txtName'] ?? '');
$categoryPr = (int)($_POST['category'] ?? 0);
$pricePr = (float)($_POST['txtPrice'] ?? 0);
$salePricePr = (float)($_POST['txtSalePrice'] ?? 0);
$quantityPr = (int)($_POST['txtNumber'] ?? 0);
$keywordPr = trim($_POST['txtKeyword'] ?? '');
$descriptPr = trim($_POST['txtDescript'] ?? '');
$status = (int)($_POST['status'] ?? 0);

// Validate required fields
if (empty($namePr) || $categoryPr <= 0 || $pricePr <= 0) {
    header('Location: product-edit.php?idProduct=' . $idProduct . '&error=invalid_input');
    exit;
}

// Handle image upload
$image = null;
if (isset($_FILES['FileImage']) && $_FILES['FileImage']['error'] == 0) {
    $imageTmp = $_FILES['FileImage']['tmp_name'];
    $imageName = basename($_FILES['FileImage']['name']);
    
    // Validate image
    $check = getimagesize($imageTmp);
    if ($check === false) {
        header('Location: product-edit.php?idProduct=' . $idProduct . '&error=invalid_image');
        exit;
    }
    
    // Sanitize filename
    $imageName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName);
    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $baseName = pathinfo($imageName, PATHINFO_FILENAME);
    $imageName = $baseName . '.' . $ext;
    
    $targetDir = '../uploads/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $i = 1;
    $targetFile = $targetDir . $imageName;
    while (file_exists($targetFile)) {
        $imageName = $baseName . '_' . $i . '.' . $ext;
        $targetFile = $targetDir . $imageName;
        $i++;
    }
    
    if (!move_uploaded_file($imageTmp, $targetFile)) {
        header('Location: product-edit.php?idProduct=' . $idProduct . '&error=upload_failed');
        exit;
    }
    
    $image = 'uploads/' . $imageName;
}

// Update database using prepared statement
try {
    if ($image) {
        $sql = "UPDATE products SET name = :name, category_id = :category, image = :image, 
                description = :description, price = :price, saleprice = :saleprice, 
                quantity = :quantity, keyword = :keyword, status = :status 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $namePr,
            ':category' => $categoryPr,
            ':image' => $image,
            ':description' => $descriptPr,
            ':price' => $pricePr,
            ':saleprice' => $salePricePr,
            ':quantity' => $quantityPr,
            ':keyword' => $keywordPr,
            ':status' => $status,
            ':id' => $idProduct
        ]);
    } else {
        $sql = "UPDATE products SET name = :name, category_id = :category, 
                description = :description, price = :price, saleprice = :saleprice, 
                quantity = :quantity, keyword = :keyword, status = :status 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $namePr,
            ':category' => $categoryPr,
            ':description' => $descriptPr,
            ':price' => $pricePr,
            ':saleprice' => $salePricePr,
            ':quantity' => $quantityPr,
            ':keyword' => $keywordPr,
            ':status' => $status,
            ':id' => $idProduct
        ]);
    }
    header('Location: product-edit.php?idProduct=' . $idProduct . '&es=editsuccess');
} catch (PDOException $e) {
    error_log('Product update error: ' . $e->getMessage());
    header('Location: product-edit.php?idProduct=' . $idProduct . '&ef=editfail');
}
exit;
?>
