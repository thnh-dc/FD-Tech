<?php 
    $custom_css ='
        <link rel="stylesheet" href="../assets/css/style_done.css">
        ';
    include '../includes/header.php'; ?>

<div class="container success-page-wrapper">
    <div class="success-card">
        <svg class="success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        
        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-message">
            FD Tech xin cảm ơn!<br>
            Đơn hàng của bạn đang được hệ thống xử lý.
        </p>
        
        <a href="../user/index.php" class="btn btn-primary btn-large" style="display: inline-block; text-decoration: none;">Về Trang Chủ</a>
    </div>
</div>
<?php include '../includes/footer.php'?>