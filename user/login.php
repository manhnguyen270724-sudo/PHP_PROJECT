<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>MyLiShop - Đăng nhập</title>
    <link rel="icon" type="image/png" href="../images/logohong.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Màu chủ đạo MyLiShop */
        :root {
            --primary: #FF6B6B;
            --primary-dark: #EE5A52;
        }

        body {
            background: #f5f5f7;
            font-family: 'Segoe UI', sans-serif;
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .btn-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .text-primary-custom {
            color: var(--primary);
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4">

    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">

        <div class="text-center mb-8">
            <a href="../index.php">
                <img src="../images/logohong.png" class="mx-auto w-24 mb-4 hover:scale-105 transition duration-300">
            </a>
            <h2 class="text-3xl font-bold text-gray-800">Chào mừng trở lại!</h2>
            <p class="text-gray-500 mt-2 text-sm">Vui lòng đăng nhập để tiếp tục</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'wrong'): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm flex items-center">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                <span>Tên đăng nhập hoặc mật khẩu không đúng!</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['rs']) && $_GET['rs'] == 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i>
                <span>Đăng ký thành công! Hãy đăng nhập.</span>
            </div>
        <?php endif; ?>

        <form action="login-back.php" method="POST" class="space-y-6">

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tên đăng nhập</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="username"
                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition duration-200"
                        placeholder="Nhập username" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Mật khẩu</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password"
                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition duration-200"
                        placeholder="Nhập mật khẩu" required>
                </div>
                <div class="text-right mt-2">
                    <a href="#" class="text-xs text-gray-500 hover:text-pink-500 transition">Quên mật khẩu?</a>
                </div>
            </div>

            <button type="submit" name="submit"
                class="w-full py-3 mt-4 btn-custom text-white font-bold rounded-lg shadow-md transition-all duration-300">
                ĐĂNG NHẬP
            </button>

        </form>

        <div class="text-center mt-8 pt-6 border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Bạn chưa có tài khoản?
                <a href="register.php" class="text-primary-custom font-semibold hover:underline">Đăng ký ngay</a>
            </p>
            <p class="mt-4">
                <a href="../index.php" class="text-xs text-gray-400 hover:text-gray-600 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Quay về trang chủ
                </a>
            </p>
        </div>

    </div>

</body>

</html>