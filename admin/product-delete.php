<?php
/**
 * Product Delete
 * FIXED: SQL injection, deprecated mysqli, input validation
 */
require_once '../model/connect.php';

// Validate product ID
if (!isset($_GET['idProducts']) || !is_numeric($_GET['idProducts'])) {
    header('Location: productlist.php?error=invalid_id');
    exit;
}

$idProduct = (int)$_GET['idProducts'];

if ($idProduct <= 0) {
    header('Location: productlist.php?error=invalid_id');
    exit;
}

// Delete product using PDO prepared statement
try {
    $sql = "DELETE FROM products WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':id' => $idProduct]);
    
    if ($result) {
        header('Location: productlist.php?ps=success');
    } else {
        header('Location: productlist.php?pf=fail');
    }
} catch (PDOException $e) {
    error_log('Product delete error: ' . $e->getMessage());
    header('Location: productlist.php?pf=fail');
}
exit;
?>