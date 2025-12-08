<?php
    require_once('../model/connect.php');

    // Thông báo lỗi
    $error = isset($_GET['error']) 
        ? "Vui lòng kiểm tra lại tài khoản hoặc mật khẩu!" 
        : "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- login styles moved to css/style.css -->
</head>

<body>
    <div class="login-card">
        
        <div class="login-logo">
            <img src="../images/logohong.png" alt="Logo">
        </div>

        <h4 class="text-center mb-4">Đăng nhập tài khoản</h4>

        <?php if ($error != ""): ?>
            <div class="alert alert-danger py-2"><?= $error ?></div>
        <?php endif; ?>

        <form action="login-back.php" method="POST">
            
            <!-- Username -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-light"><i class="fa fa-user"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required>
            </div>

            <!-- Password -->
            <div class="input-group mb-4">
                <span class="input-group-text bg-light"><i class="fa fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            </div>

            <!-- Submit -->
            <button type="submit" name="submit" class="btn btn-primary w-100 btn-login">
                Đăng nhập
            </button>

        </form>

        <p class="text-center mt-3">
            Chưa có tài khoản?
            <a href="register.php">Đăng ký ngay</a>
        </p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
