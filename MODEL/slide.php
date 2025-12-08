<?php 
echo "<h3 class='title text-center'>SLIDE - PNV 27</h3>";
require_once("connect.php");

// Lấy danh sách slide
$sql = "SELECT id, image FROM slides WHERE status = 1 ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalSlides = count($slides);
?>

<!-- IMPORT BOOTSTRAP 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-4">

<?php if ($totalSlides > 0) { ?>

    <div id="pnvCarousel" class="carousel slide" data-bs-ride="carousel">

        <!-- Indicators -->
        <div class="carousel-indicators">
            <?php foreach ($slides as $i => $s) { ?>
                <button type="button" data-bs-target="#pnvCarousel" data-bs-slide-to="<?= $i ?>"
                    class="<?= $i == 0 ? 'active' : '' ?>" aria-current="<?= $i == 0 ? 'true' : 'false' ?>">
                </button>
            <?php } ?>
        </div>

        <!-- Slides -->
        <div class="carousel-inner">
            <?php foreach ($slides as $index => $slide) { ?>
                <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($slide['image']) ?>" 
                        class="d-block w-100 slide-img"
                        alt="Slide <?= $slide['id'] ?>">
                </div>
            <?php } ?>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#pnvCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#pnvCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>

<?php } else { ?>

    <div class="text-center p-5">
        <p>Không có slide nào để hiển thị.</p>
    </div>

<?php } ?>

</div>

<!-- title style moved to main stylesheet -->
