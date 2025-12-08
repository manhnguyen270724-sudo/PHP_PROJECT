<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
require_once '../model/connect.php';

if (!isset($_POST['submit'])) {
    header('Location: register.php');
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// Basic validation
if ($fullname === '' || $username === '' || $password === '' || $email === '') {
    header('Location: register.php?rf=fail');
    exit;
}

// Check username/email uniqueness
try {
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
    $stmt->execute([':username' => $username, ':email' => $email]);
    if ($stmt->fetch()) {
        header('Location: register.php?rf=fail');
        exit;
    }

    // Hash password using password_hash
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $conn->prepare('INSERT INTO users (fullname, username, password, email, phone, address, role) VALUES (:fullname, :username, :password, :email, :phone, :address, :role)');
    $res = $insert->execute([
        ':fullname' => $fullname,
        ':username' => $username,
        ':password' => $passwordHash,
        ':email' => $email,
        ':phone' => $phone === '' ? null : $phone,
        ':address' => $address,
        ':role' => 1
    ]);

    if ($res) {
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