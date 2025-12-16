<?php
// FILE: user/login-back.php
session_start();
require_once('../model/connect.php');

if (!isset($_POST['submit'])) {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Location: login.php?error=empty_fields');
    exit;
}

try {
    // --- 1. KIỂM TRA TÀI KHOẢN ADMIN TRƯỚC ---
    // Vì bảng admin tách riêng, ta phải query bảng admin trước
    $stmtAdmin = $conn->prepare("SELECT * FROM admin WHERE username = :u AND password = :p LIMIT 1");
    $stmtAdmin->execute([
        ':u' => $username,
        ':p' => $password // So sánh trực tiếp password thường
    ]);
    
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Nếu là Admin
        $_SESSION['admin'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['role'] = 'admin'; // Đánh dấu quyền
        header('Location: ../admin/index.php');
        exit;
    }

    // --- 2. NẾU KHÔNG PHẢI ADMIN, KIỂM TRA USER ---
    $stmtUser = $conn->prepare("SELECT * FROM users WHERE username = :u AND password = :p LIMIT 1");
    $stmtUser->execute([
        ':u' => $username,
        ':p' => $password // So sánh trực tiếp password thường
    ]);

    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Nếu là User thường
        $_SESSION['username'] = $user['username'];
        $_SESSION['id_user'] = $user['id']; // Lưu ý tên biến session để dùng ở cart
        $_SESSION['role'] = 'user';
        
        // Chuyển hướng về trang chủ hoặc trang giỏ hàng
        header('Location: ../index.php');
        exit;
    }

    // --- 3. ĐĂNG NHẬP THẤT BẠI ---
    header('Location: login.php?error=invalid_credentials');
    exit;

} catch (PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
}
?>