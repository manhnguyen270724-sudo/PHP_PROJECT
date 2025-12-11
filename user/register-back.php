<?php
// user/register-back.php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);

// Gọi file kết nối database
require_once '../model/connect.php';

// --- NHÚNG PHPMAILER ---
// Đảm bảo bạn đã làm Bước 1: tạo thư mục PHPMailer ngang hàng với thư mục user
require_once '../PHPMailer/Exception.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!isset($_POST['submit'])) {
    header('Location: register.php');
    exit;
}

// Lấy dữ liệu từ form
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? ''; // Lấy mật khẩu nhập lại
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// 1. Kiểm tra dữ liệu rỗng
if ($fullname === '' || $username === '' || $password === '' || $email === '') {
    header('Location: register.php?rf=fail');
    exit;
}

// 2. Kiểm tra mật khẩu nhập lại (Logic từ code của bạn)
if ($password !== $confirmPassword) {
    echo "<script>alert('Mật khẩu nhập lại không khớp!'); window.location.href='register.php';</script>";
    exit;
}

try {
    // 3. Kiểm tra trùng username hoặc email
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
    $stmt->execute([':username' => $username, ':email' => $email]);
    
    if ($stmt->fetch()) {
        // Đã tồn tại -> Quay lại form
        header('Location: register.php?rf=fail');
        exit;
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 4. Thêm vào database
    $insert = $conn->prepare('INSERT INTO users (fullname, username, password, email, phone, address, role) VALUES (:fullname, :username, :password, :email, :phone, :address, :role)');
    $res = $insert->execute([
        ':fullname' => $fullname,
        ':username' => $username,
        ':password' => $passwordHash,
        ':email' => $email,
        ':phone' => $phone === '' ? null : $phone,
        ':address' => $address,
        ':role' => 1 // Role 1: User thường
    ]);

    if ($res) {
        // --- 5. GỬI EMAIL THÔNG BÁO ---
        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            
            // =================================================================
            // THAY ĐỔI THÔNG TIN CỦA BẠN TẠI ĐÂY
            // =================================================================
            $mail->Username   = 'manh.nguyen270724@gmail.com'; // <--- Điền email của bạn
            $mail->Password   = 'zgnk ileg khuw ozxx'; 
            // =================================================================
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587; 

            // Người gửi và người nhận
            $mail->setFrom('email_cua_ban@gmail.com', 'MyLiShop Support'); // <--- Điền lại email của bạn
            $mail->addAddress($email, $fullname); 

            // Nội dung Email
            $mail->isHTML(true); 
            $mail->Subject = 'Chào mừng đến với MyLiShop - Đăng ký thành công';
            
            // Gửi kèm password chưa mã hóa để user nhớ (chỉ dùng cho mục đích học tập)
            $mail->Body    = "
                <h3>Xin chào $fullname,</h3>
                <p>Cảm ơn bạn đã đăng ký tài khoản tại <b>MyLiShop</b>.</p>
                <p>Đây là thông tin đăng nhập của bạn:</p>
                <ul>
                    <li><b>Tên đăng nhập:</b> $username</li>
                    <li><b>Mật khẩu:</b> $password</li>
                </ul>
                <p>Vui lòng đăng nhập và đổi mật khẩu nếu cần thiết.</p>
                <p>Trân trọng,<br>MyLiShop Team</p>
            ";
            $mail->AltBody = "Xin chào $fullname. User: $username, Pass: $password";

            $mail->send();
        } catch (Exception $e) {
            // Gửi lỗi thì chỉ ghi log, không chặn user đăng nhập
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
        // --- KẾT THÚC GỬI EMAIL ---

        // Đăng ký thành công -> Chuyển sang login
        header('Location: login.php?rs=success');
        exit;
    } else {
        header('Location: register.php?rf=fail');
        exit;
    }

} catch (PDOException $e) {
    error_log('Register error: ' . $e->getMessage());
    header('Location: register.php?rf=fail');
    exit;
}
?>