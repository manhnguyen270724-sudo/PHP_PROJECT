<?php require_once('../model/connect.php'); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>MyLiShop - Đăng ký</title>
    <link rel="icon" type="image/png" href="../images/logohong.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Đăng ký tài khoản MyLiShop">
    <meta name="author" content="MyLiShop Team">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background: #f5f5f7;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4">

    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">

        <div class="text-center mb-6">
            <img src="../images/logohong.png" class="mx-auto w-32 opacity-90">
            <h2 class="text-2xl font-semibold mt-4 tracking-tight text-gray-800">
                Tạo tài khoản mới
            </h2>
        </div>

        <form action="register-back.php" method="POST" class="space-y-4">

            <!-- Full Name -->
            <div>
                <label class="text-gray-600 text-sm">Họ và tên</label>
                <div class="relative mt-1">
                    <i class="fa fa-user absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="fullname"
                        class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black/40 focus:border-black"
                        placeholder="Nhập họ tên" required>
                </div>
            </div>

            <!-- Username -->
            <div>
                <label class="text-gray-600 text-sm">Tên đăng nhập</label>
                <div class="relative mt-1">
                    <i class="fa fa-user-circle absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="username"
                        class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black/40 focus:border-black"
                        placeholder="Nhập tên đăng nhập" required>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="text-gray-600 text-sm">Email</label>
                <div class="relative mt-1">
                    <i class="fa fa-envelope absolute left-3 top-3 text-gray-400"></i>
                    <input type="email" name="email"
                        class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black/40 focus:border-black"
                        placeholder="Nhập email" required>
                </div>
            </div>

            <!-- Phone -->
            <div>
<label class="text-gray-600 text-sm">Số điện thoại</label>
                <div class="relative mt-1">
                    <i class="fa fa-phone absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="phone" minlength="9" maxlength="11"
                        class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black/40 focus:border-black"
                        placeholder="Nhập số điện thoại" required>
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="text-gray-600 text-sm">Địa chỉ</label>
                <div class="relative mt-1">
                    <i class="fa fa-location-dot absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="address"
                        class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black/40 focus:border-black"
                        placeholder="Nhập địa chỉ" required>
                </div>
            </div>

            <!-- Passwords -->
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="text-gray-600 text-sm">Mật khẩu</label>
                    <div class="relative mt-1">
                        <i class="fa fa-lock absolute left-3 top-3 text-gray-400"></i>
                        <input type="password" name="password"
                            class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black/40 focus:border-black"
                            placeholder="Nhập mật khẩu" required>
                    </div>
                </div>

                <div>
                    <label class="text-gray-600 text-sm">Nhập lại mật khẩu</label>
                    <div class="relative mt-1">
                        <i class="fa fa-lock absolute left-3 top-3 text-gray-400"></i>
                        <input type="password" name="confirmPassword"
                            class="w-full pl-10 pr-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-black/40 focus:border-black"
                            placeholder="Xác nhận mật khẩu" required>
                    </div>
                </div>

            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full py-3 mt-2 bg-black text-white text-lg rounded-xl hover:bg-gray-800 transition">
                Đăng ký
            </button>
        </form>

    </div>

</body>

</html>