<?php
session_start();
require_once('model/connect.php');

$response = ['status' => 'error', 'message' => 'Lỗi không xác định'];

if (isset($_POST['action']) && isset($_SESSION['cart'])) {
    $action = $_POST['action'];
    $id = (int)$_POST['id'];

    if ($action == 'update') {
        $qty = (int)$_POST['quantity'];
        
        // Kiểm tra tồn kho từ bảng products
        $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        if ($product && $qty <= $product['quantity']) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity'] = $qty;
                    $response['status'] = 'success';
                    $response['item_subtotal'] = $item['price'] * $qty;
                    break;
                }
            }
        } else {
            $response['message'] = 'Số lượng vượt quá tồn kho (Còn: '.$product['quantity'].')';
        }

    } elseif ($action == 'delete') {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                $response['status'] = 'success';
                break;
            }
        }
    }

    // Tính lại tổng tiền
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $response['total'] = $total;
        $response['empty'] = false;
    } else {
        $response['total'] = 0;
        $response['empty'] = true;
    }
}
echo json_encode($response);
?>