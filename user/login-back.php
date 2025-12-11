<?php
session_start();
require_once('../model/connect.php');

if (!isset($_POST['submit'])) {
    header('Location: ../user/login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không hợp lệ!';
    header('Location: ../user/login.php?error=wrong');
    exit;
}

try {
    $sql = 'SELECT id, username, password FROM users WHERE username = :username LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $hash = $user['password'];

        // Support bcrypt/password_hash and legacy md5. If legacy md5 matches, upgrade to password_hash.
        $valid = false;
        if (password_verify($password, $hash)) {
            $valid = true;
        } elseif (strlen($hash) === 32 && md5($password) === $hash) {
            // legacy md5 match; upgrade to stronger hash
            $valid = true;
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $uSql = 'UPDATE users SET password = :ph WHERE id = :id';
                $uStmt = $conn->prepare($uSql);
                $uStmt->execute([':ph' => $newHash, ':id' => $user['id']]);
            } catch (Exception $e) {
                // non-fatal: continue login even if upgrade fails
                error_log('Password upgrade failed for user ' . $user['id'] . ': ' . $e->getMessage());
            }
        }

        if ($valid) {
            // Successful login
            $_SESSION['username'] = $user['username'];
            $_SESSION['id-user'] = $user['id'];
            header('Location: ../view-cart.php?ls=success');
            exit;
        }
    }

    // Authentication failed
    $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không hợp lệ!';
    header('Location: ../user/login.php?error=wrong');
    exit;
} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    $_SESSION['error'] = 'Lỗi hệ thống. Vui lòng thử lại sau.';
    header('Location: ../user/login.php?error=wrong');
    exit;
}
?>
<?php
