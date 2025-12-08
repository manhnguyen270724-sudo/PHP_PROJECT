<?php 
echo "<h3 class='title text-center'>PARTNER - PNV 27</h3>";
require_once("connect.php");

// Lấy partner có status = 3 (hoặc thay thành 2 nếu cần)
$sql = "SELECT id, image FROM slides WHERE status = 3 ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container my-4 partner-section">

    <div class="row g-3">
        <?php if (!empty($partners)) { ?>

            <?php foreach ($partners as $partner) { ?>
                <div class="col-md-2 col-sm-1">
                    <div class="partner-item">
                        <img src="<?= htmlspecialchars($partner['image']) ?>" 
                             class="img-fluid" 
                             alt="partner <?= $partner['id'] ?>">
                    </div>
                </div>
            <?php } ?>

        <?php } else { ?>
            <div class="col-12">
                <p class="text-center">Không có partner nào để hiển thị.</p>
            </div>
        <?php } ?>
    </div>

</div>

<!-- partner styles moved to `css/style.css` -->

    .partner-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    .title {
        font-weight: bold;
        margin-bottom: 25px;
        color: #2c3e50;
    }
</style>
