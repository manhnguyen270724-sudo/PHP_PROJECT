<?php 
require_once('../model/connect.php'); 
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>MyLiShop - Đăng ký</title>
    <link rel="icon" type="image/png" href="../images/logohong.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Đồng bộ màu sắc thương hiệu MyLiShop */
        :root {
            --primary: #FF6B6B;
            --primary-dark: #EE5A52;
        }
        body {
            background: #f5f5f7;
            font-family: 'Segoe UI', sans-serif;
        }
        /* Nút bấm gradient hồng */
        .btn-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        .btn-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        /* Màu chữ link */
        .text-primary-custom {
            color: var(--primary);
        }
        /* Hiệu ứng focus input màu hồng */
        .input-custom:focus {
            border-color: #FF6B6B; /* Fallback */
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.5);
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4 py-8">

    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">

        <div class="text-center mb-6">
            <a href="../index.php">
                <img src="../images/logohong.png" class="mx-auto w-24 mb-2 hover:scale-105 transition duration-300">
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Tạo tài khoản mới</h2>
            <p class="text-gray-500 text-sm">Tham gia cùng MyLiShop ngay hôm nay</p>
        </div>

        <?php if (isset($_GET['rf']) && $_GET['rf'] == 'fail'): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm flex items-center">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                <span>Đăng ký thất bại! Tên đăng nhập hoặc Email đã tồn tại.</span>
            </div>
        <?php endif; ?>

        <form action="register-back.php" method="POST" class="space-y-4">

            <div>
                <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Họ và tên</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="fullname"
                        class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 input-custom transition duration-200"
                        placeholder="Nhập họ tên đầy đủ" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Tên đăng nhập</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-id-card text-gray-400"></i>
                    </div>
                    <input type="text" name="username"
                        class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 input-custom transition duration-200"
                        placeholder="Chọn tên đăng nhập" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email"
                        class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 input-custom transition duration-200"
                        placeholder="example@email.com" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Số điện thoại</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-phone text-gray-400"></i>
                    </div>
                    <input type="text" name="phone" minlength="9" maxlength="11"
                        class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 input-custom transition duration-200"
                        placeholder="0123456789" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Địa chỉ</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-location-dot text-gray-400"></i>
                    </div>
                    <input type="text" name="address"
                        class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 input-custom transition duration-200"
                        placeholder="Số nhà, đường, phường, quận..." required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Mật khẩu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password"
                            class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 input-custom transition duration-200"
                            placeholder="******" required>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1 uppercase">Nhập lại</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-key text-gray-400"></i>
                        </div>
                        <input type="password" name="confirmPassword"
                            class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 input-custom transition duration-200"
                            placeholder="******" required>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit"
                class="w-full py-3 mt-4 btn-custom text-white font-bold rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                ĐĂNG KÝ
            </button>

        </form>

        <div class="text-center mt-6 pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Đã có tài khoản? 
                <a href="login.php" class="text-primary-custom font-bold hover:underline">Đăng nhập ngay</a>
            </p>
            <p class="mt-4">
                <a href="../index.php" class="text-xs text-gray-400 hover:text-gray-600 flex items-center justify-center gap-1 transition">
                    <i class="fa-solid fa-arrow-left"></i> Quay về trang chủ
                </a>
            </p>
        </div>

    </div>

</body>
</html>