<?php

$servername = "localhost";
$port = 3306;
$username = "root";
$password = "";

try {
    $db_name = "THOITRANG1";
    $dsn = "mysql:host=$servername;port=$port;dbname=$db_name;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Log the error for debugging, but don't reveal DB details to users.
    error_log('Database connection error: ' . $e->getMessage());
    // Optionally show a friendly message (commented out for production):
    // echo 'Đã có lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.';
    // Stop execution to avoid further errors.
    exit;
}

?>
