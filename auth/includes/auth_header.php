<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'FD Tech'; ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style_chung.css">
    <link rel="stylesheet" href="../assets/css/style_auth.css">
    <link rel="stylesheet" href="../assets/css/footer.css">

    <?php if (!empty($use_sweetalert)): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php endif; ?>
</head>

<body>

<header class="auth-header">
    <div class="auth-header-container">
        <div class="auth-header-left">
            <a href="/FD-Tech/user/index.php" class="auth-logo">
                <img src="../assets/images/logo-fd.jpg" alt="FD Tech Logo" onerror="this.style.display='none'">
                <span class="auth-brand">FD<span>TECH</span></span>
            </a>
        </div>
    </div>
</header>