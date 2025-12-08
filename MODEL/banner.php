<?php 
echo "<h3 class='title text-center'>BANNER - PNV 27</h3>";
require_once("connect.php");

// Lấy banner có status = 2
$sql = "SELECT id, image FROM slides WHERE status = 2 ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container my-4 banner-section">

    <div class="row g-3">
        <?php if (!empty($banners)) { ?>

            <?php foreach ($banners as $banner) { ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                    <div class="banner-item">
                        <img src="<?= htmlspecialchars($banner['image']) ?>" 
                             class="img-fluid" 
                             alt="Banner <?= $banner['id'] ?>">
                    </div>
                </div>
            <?php } ?>

        <?php } else { ?>
            <div class="col-12">
                <p class="text-center">Không có banner nào để hiển thị.</p>
            </div>
        <?php } ?>
    </div>

</div>

<!-- banner styles moved to `css/style.css` -->
