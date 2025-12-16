<?php
// FILE: user/register-back.php
session_start();

// Tắt báo lỗi hiển thị ra màn hình để tránh lộ đường dẫn file (Security)
// error_reporting(0); 

require_once '../model/connect.php';

// --- CẤU HÌNH PHPMAILER ---
// Đảm bảo đường dẫn này đúng với cấu trúc thư mục của bạn
require_once '../PHPMaile/PHPMailer-master/src/Exception.php';
require_once '../PHPMaile/PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMaile/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_POST['submit'])) {
    header('Location: register.php');
    exit;
}

// Lấy và làm sạch dữ liệu đầu vào
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// 1. Validate cơ bản
if ($fullname === '' || $username === '' || $password === '' || $email === '') {
    // Nên có thông báo lỗi cụ thể
    echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.history.back();</script>";
    exit;
}

if ($password !== $confirmPassword) {
    echo "<script>alert('Mật khẩu nhập lại không khớp!'); window.history.back();</script>";
    exit;
}

try {
    // 2. Kiểm tra trùng lặp (Username hoặc Email)
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
    $stmt->execute([':username' => $username, ':email' => $email]);
    
    if ($stmt->fetch()) {
        echo "<script>alert('Tên đăng nhập hoặc Email đã tồn tại!'); window.history.back();</script>";
        exit;
    }

    // 3. Insert vào DB
    // QUAN TRỌNG: Phải mã hóa mật khẩu để hàm password_verify bên login hoạt động được
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fullname, username, password, email, phone, address, role) 
            VALUES (:fullname, :username, :password, :email, :phone, :address, 1)";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':fullname' => $fullname,
        ':username' => $username,
        ':password' => $passwordHash, // Đã sửa thành mật khẩu mã hóa
        ':email'    => $email,
        ':phone'    => $phone,
        ':address'  => $address
    ]);

    if ($result) {
        // --- 4. GỬI EMAIL ---
        $mail = new PHPMailer(true);
        try {
            // Cấu hình Server
            $mail->isSMTP();
            $mail->CharSet    = 'UTF-8';
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // --- THÔNG TIN TÀI KHOẢN GMAIL ---
            // Lưu ý: Gmail người gửi phải trùng với Username để tránh lỗi xác thực
            $mail->Username   = 'manh.nguyen270724@gmail.com'; 
            $mail->Password   = 'suzr ajfb slrb wzbz'; // Mật khẩu ứng dụng 16 ký tự
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Người gửi & Người nhận
            $mail->setFrom('manh.nguyen270724@gmail.com', 'MyLiShop Support');
            $mail->addAddress($email, $fullname);

            // Nội dung
            $mail->isHTML(true);
            $mail->Subject = 'Chào mừng đến với MyLiShop - Đăng ký thành công';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <h3 style='color: #FF6B6B;'>Xin chào $fullname,</h3>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại <b>MyLiShop</b>.</p>
                    <p>Thông tin đăng nhập của bạn:</p>
                    <ul>
                        <li>Tên đăng nhập: <b>$username</b></li>
                        <li>Mật khẩu: <i>(Mật khẩu bạn đã nhập)</i></li>
                    </ul>
                    <p>Vui lòng đăng nhập để bắt đầu mua sắm!</p>
                    <hr>
                    <small>Đây là email tự động, vui lòng không trả lời.</small>
                </div>
            ";
            $mail->AltBody = "Xin chào $fullname. Bạn đã đăng ký thành công tài khoản MyLiShop với username: $username.";

            $mail->send();
        } catch (Exception $e) {
            // Ghi log lỗi vào file thay vì hiện ra màn hình để tránh làm người dùng hoang mang
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }

        // Đăng ký thành công -> Chuyển sang trang login
        echo "<script>alert('Đăng ký thành công! Vui lòng kiểm tra email.'); window.location.href='login.php?rs=success';</script>";
        exit;
    } else {
        echo "<script>alert('Đăng ký thất bại. Vui lòng thử lại!'); window.history.back();</script>";
        exit;
    }

} catch (PDOException $e) {
    // Ghi log lỗi hệ thống
    error_log("Database Error: " . $e->getMessage());
    echo "Lỗi hệ thống! Vui lòng liên hệ quản trị viên.";
}
?>