<?php
    require_once('model/connect.php');
    // Success
    if(isset($_GET['cs'])) {
        echo "<script type=\"text/javascript\">alert(\"Gửi liên hệ thành công!\");</script>";
    }
    else {
        echo "";
    }
    // Fail
    if(isset($_GET['cf'])) {
        echo "<script type=\"text/javascript\">alert(\"Gửi liên hệ thất bại!\");</script>";
    }
    else {
        echo "";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content = "width=device-width, initial-scale =1">
    <meta charset="utf-8">
    <meta name="title" content="Fashion MyLiShop - fashion mylishop"/>
    <meta name="description" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="keywords" content="Fashion MyLiShop - fashion mylishop" />
    <meta name="author" content="Hôih My" />
    <meta name="author" content="Y Blir" />
    <link rel="icon" type="image/png" href="../images/logohong.png">
    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="../js/mylishop.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
    <script src='../js/wow.js'></script>
    <!-- Bootstrap Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <!-- button top -->
    <a href="#" class="back-to-top"><i class="fa fa-arrow-up"></i></a>
    
    <!-- header -->
    <?php include 'model/header.php'; ?>
    <!-- /header -->
    
    <div class="container contact-container">
    <ul class="breadcrumb">
        <li><a href="../index.php">Trang chủ</a></li>
        <li>Liên hệ</li>
    </ul>
    <div class="card shadow-sm contact-card">
        <h3 class="text-center contact-title">
            THÔNG TIN LIÊN HỆ
        </h3>

        <form action="lienhe_back.php" method="POST">

            <div class="form-group">
                <label class="form-label">Họ và tên <span class="required">*</span></label>
                <input type="text" 
                       name="contact-name"
                       class="form-control input-lg rounded-input"
                       placeholder="Nhập họ và tên"
                       maxlength="255"
                       required
                       >
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input type="email"
                       name="contact-email"
                       class="form-control input-lg rounded-input"
                       placeholder="Nhập email của bạn"
                       maxlength="255"
                       required
                       >
            </div>

            <div class="form-group">
                <label class="form-label">Tiêu đề <span class="required">*</span></label>
                <input type="text"
                       name="contact-subject"
                       class="form-control input-lg rounded-input"
                       placeholder="Nhập tiêu đề liên hệ"
                       maxlength="255"
                       required
                       >
            </div>

            <div class="form-group">
                <label class="form-label">Nội dung <span class="required">*</span></label>
                <textarea name="contact-content"
                          class="form-control rounded-textarea"
                          rows="5"
                          placeholder="Nhập nội dung liên hệ..."
                          required></textarea>
            </div>

            <center>
                <button type="submit" 
                        name="sendcontact"
                        class="btn btn-primary btn-lg contact-btn">
                    Gửi liên hệ
                </button>
            </center>

        </form>
    </div>
</div>


    <!-- Maps -->
    <div class="container-fluid">
        <div class="row">
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d958.5247181884388!2d108.24206672970746!3d16.060358250494478!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31421836ed15dfc9%3A0x99c3cc369a33576c!2sPasserelles+num%C3%A9riques+Vietnam!5e0!3m2!1sen!2s!4v1513938605489" class="map-iframe" frameborder="0" allowfullscreen aria-hidden="false" tabindex="0"></iframe>
            </div><!-- /map -->
        </div><!-- /row -->
    </div><!-- /container-fluid -->
<script>
    new WOW().init();
</script>
</body>
</html>