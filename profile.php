<!-- <?php
session_start();
require_once('model/connect.php');

// 1. KIỂM TRA ĐĂNG NHẬP (QUAN TRỌNG: Phải có đoạn này mới có biến $uid)
if (!isset($_SESSION['id-user'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='index.php';</script>";
    exit;
}

$uid = $_SESSION['id-user']; // Lấy ID từ session
$msg = "";
$msg_type = "";

// 2. XỬ LÝ CẬP NHẬT THÔNG TIN (Khi bấm nút Lưu)
if (isset($_POST['btn_update'])) {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);
    $new_pass = trim($_POST['new_password']);
    
    try {
        if (!empty($new_pass)) {
            // Nếu có nhập mật khẩu mới -> Cập nhật cả mật khẩu (Mã hóa MD5 cho khớp với DB cũ của bạn)
            $pass_hash = md5($new_pass); 
            $sql = "UPDATE users SET fullname = :name, email = :email, phone = :phone, address = :addr, password = :pass WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name' => $fullname, 
                ':email' => $email, 
                ':phone' => $phone, 
                ':addr' => $address, 
                ':pass' => $pass_hash, 
                ':id' => $uid
            ]);
        } else {
            // Nếu để trống mật khẩu -> Chỉ cập nhật thông tin thường
            $sql = "UPDATE users SET fullname = :name, email = :email, phone = :phone, address = :addr WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name' => $fullname, 
                ':email' => $email, 
                ':phone' => $phone, 
                ':addr' => $address, 
                ':id' => $uid
            ]);
        }
        $msg = "Cập nhật thông tin thành công!";
        $msg_type = "success";
    } catch (PDOException $e) {
        $msg = "Lỗi cập nhật: " . $e->getMessage();
        $msg_type = "danger";
    }
}

// 3. LẤY THÔNG TIN USER MỚI NHẤT
try {
    $stmtUser = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmtUser->execute([':id' => $uid]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi tải thông tin: " . $e->getMessage();
    exit;
}

// 4. LẤY LỊCH SỬ ĐƠN HÀNG
try {
    $stmtOrders = $conn->prepare("SELECT * FROM orders WHERE user_id = :id ORDER BY date_order DESC");
    $stmtOrders->execute([':id' => $uid]);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Tài khoản của tôi - MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logohong.png">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link rel="stylesheet" href="css/style.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <style>
        .profile-header { background: #f8f8f8; padding: 20px; border-bottom: 1px solid #eee; margin-bottom: 20px; }
        .nav-pills > li.active > a, .nav-pills > li.active > a:focus, .nav-pills > li.active > a:hover {
            background-color: #d9534f;
        }
        .order-status-0 { color: #f0ad4e; font-weight: bold; } /* Chờ xử lý */
        .order-status-1 { color: #5bc0de; font-weight: bold; } /* Đang giao/Xác nhận */
        .order-status-2 { color: #5cb85c; font-weight: bold; } /* Hoàn thành */
        .order-status-3 { color: #d9534f; font-weight: bold; } /* Hủy */
    </style>
</head>
<body>

<?php include("model/header.php"); ?>

<div class="container" style="min-height: 600px; margin-top: 20px;">
    
    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?> alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Thông báo:</strong> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">Xin chào, <b><?= htmlspecialchars($user['fullname'] ?? 'Bạn') ?></b></h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active"><a data-toggle="pill" href="#info"><i class="fa fa-user"></i> Thông tin tài khoản</a></li>
                        <li><a data-toggle="pill" href="#orders"><i class="fa fa-list-alt"></i> Lịch sử đơn hàng</a></li>
                        <li><a href="logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content">
                
                <div id="info" class="tab-pane fade in active">
                    <div class="panel panel-info">
                        <div class="panel-heading">Cập nhật thông tin cá nhân</div>
                        <div class="panel-body">
                            <form action="" method="POST" class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Tên đăng nhập:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Họ và tên:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Email:</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Số điện thoại:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Địa chỉ:</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">Đổi mật khẩu:</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="new_password" placeholder="Để trống nếu không muốn đổi mật khẩu">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button type="submit" name="btn_update" class="btn btn-primary"><i class="fa fa-save"></i> Lưu thay đổi</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="orders" class="tab-pane fade">
                    <div class="panel panel-info">
                        <div class="panel-heading">Đơn hàng của bạn</div>
                        <div class="panel-body">
                            <?php if (count($orders) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $od): ?>
                                                <tr>
                                                    <td>#<?= $od['id'] ?></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($od['date_order'])) ?></td>
                                                    <td class="text-danger"><b><?= number_format($od['total']) ?> đ</b></td>
                                                    <td>
                                                        <?php 
                                                            $stt = $od['status'];
                                                            if($stt == 0) echo '<span class="order-status-0">Chờ xử lý</span>';
                                                            elseif($stt == 1) echo '<span class="order-status-1">Đã hoàn thành</span>';
                                                            elseif($stt == 2) echo '<span class="order-status-3">Đã hủy</span>';
                                                            else echo '<span class="order-status-0">Khác</span>';
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Bạn chưa có đơn hàng nào.</p>
                                <center><a href="index.php" class="btn btn-success">Mua sắm ngay</a></center>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include("model/footer.php"); ?>

</body>
</html> -->