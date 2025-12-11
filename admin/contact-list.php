<?php
/**
 * Admin Contact List - MyLiShop
 * Quản lý thông tin liên hệ từ khách hàng
 */
session_start();
require_once('../model/connect.php');

// Xử lý xóa liên hệ
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM contacts WHERE id = :id");
        $stmt->execute([':id' => $deleteId]);
        header('Location: contact-list.php?success=deleted');
        exit;
    } catch (PDOException $e) {
        error_log('Contact delete error: ' . $e->getMessage());
        header('Location: contact-list.php?error=delete_failed');
        exit;
    }
}

// Lấy danh sách liên hệ
try {
    $sql = "SELECT id, name, email, title, contents, created 
            FROM contacts 
            ORDER BY created DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalContacts = count($contacts);
} catch (PDOException $e) {
    error_log('Contact fetch error: ' . $e->getMessage());
    $contacts = [];
    $totalContacts = 0;
}

// Thông báo
$successMsg = '';
$errorMsg = '';
if (isset($_GET['success'])) {
    $successMsg = 'Xóa liên hệ thành công!';
}
if (isset($_GET['error'])) {
    $errorMsg = 'Có lỗi xảy ra, vui lòng thử lại!';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý liên hệ - MyLiShop Admin</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/contactlist-style.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-shopping-bag"></i> MyLiShop</h3>
            <small>Admin Panel</small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="productlist.php">
                <i class="fas fa-box"></i> Quản lý sản phẩm
            </a>
            <a class="nav-link" href="productadd.php">
                <i class="fas fa-plus-circle"></i> Thêm sản phẩm
            </a>
            <a class="nav-link" href="category-list.php">
                <i class="fas fa-tags"></i> Danh mục
            </a>
            <a class="nav-link" href="order-list.php">
                <i class="fas fa-shopping-cart"></i> Đơn hàng
            </a>
            <a class="nav-link active" href="contact-list.php">
                <i class="fas fa-envelope"></i> Liên hệ
            </a>
            <a class="nav-link" href="slide-list.php">
                <i class="fas fa-images"></i> Slides & Banner
            </a>
            <hr>
            <a class="nav-link" href="../index.php" target="_blank">
                <i class="fas fa-globe"></i> Xem website
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="header-bar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-envelope text-warning"></i> Quản lý liên hệ
                    </h4>
                    <small class="text-muted">Danh sách liên hệ từ khách hàng</small>
                </div>
                <div>
                    <span class="badge bg-danger"><?= $totalContacts ?> liên hệ</span>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($successMsg): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMsg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errorMsg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Contact List -->
        <div class="content-card">
            
            <?php if ($totalContacts > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Họ tên</th>
                                <th width="15%">Email</th>
                                <th width="20%">Tiêu đề</th>
                                <th width="25%">Nội dung</th>
                                <th width="12%">Ngày gửi</th>
                                <th width="8%" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td><strong>#<?= $contact['id'] ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($contact['name']) ?>
                                        <?php
                                        $created = strtotime($contact['created']);
                                        $now = time();
                                        $diff = $now - $created;
                                        if ($diff < 86400): // 24 giờ
                                        ?>
                                            <span class="badge-new">MỚI</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                                            <?= htmlspecialchars($contact['email']) ?>
                                        </a>
                                    </td>
                                    <td><strong><?= htmlspecialchars($contact['title']) ?></strong></td>
                                    <td>
                                        <div class="contact-preview">
                                            <?= htmlspecialchars($contact['contents']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($contact['created'])) ?><br>
                                            <?= date('H:i', strtotime($contact['created'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info action-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal<?= $contact['id'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="contact-list.php?delete_id=<?= $contact['id'] ?>" 
                                           class="btn btn-sm btn-danger action-btn"
                                           onclick="return confirm('Bạn có chắc muốn xóa liên hệ này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- View Modal -->
                                <div class="modal fade" id="viewModal<?= $contact['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-envelope"></i> 
                                                    Chi tiết liên hệ #<?= $contact['id'] ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Họ và tên:</strong><br>
                                                        <?= htmlspecialchars($contact['name']) ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Email:</strong><br>
                                                        <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                                                            <?= htmlspecialchars($contact['email']) ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <strong>Tiêu đề:</strong><br>
                                                    <?= htmlspecialchars($contact['title']) ?>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <strong>Nội dung:</strong>
                                                    <div class="border p-3 bg-light" style="white-space: pre-wrap;">
<?= htmlspecialchars($contact['contents']) ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-0">
                                                    <strong>Thời gian gửi:</strong><br>
                                                    <i class="fas fa-clock text-muted"></i> 
                                                    <?= date('d/m/Y H:i:s', strtotime($contact['created'])) ?>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-reply"></i> Trả lời Email
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Đóng
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có liên hệ nào từ khách hàng</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="content-card">
                    <h6><i class="fas fa-chart-bar text-primary"></i> Thống kê</h6>
                    <hr>
                    <p class="mb-2">
                        <strong>Tổng số liên hệ:</strong> 
                        <span class="float-end"><?= $totalContacts ?></span>
                    </p>
                    <p class="mb-2">
                        <strong>Liên hệ hôm nay:</strong> 
                        <span class="float-end">
                            <?php
                            $today = date('Y-m-d');
                            $todayCount = 0;
                            foreach ($contacts as $c) {
                                if (date('Y-m-d', strtotime($c['created'])) == $today) {
                                    $todayCount++;
                                }
                            }
                            echo $todayCount;
                            ?>
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong>Liên hệ tuần này:</strong> 
                        <span class="float-end">
                            <?php
                            $weekAgo = date('Y-m-d', strtotime('-7 days'));
                            $weekCount = 0;
                            foreach ($contacts as $c) {
                                if (strtotime($c['created']) >= strtotime($weekAgo)) {
                                    $weekCount++;
                                }
                            }
                            echo $weekCount;
                            ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>