<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($_ENV['APP_DESCRIPTION'] ?? '') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($_ENV['APP_KEYWORDS'] ?? '') ?>">
    <meta name="author" content="<?= htmlspecialchars($_ENV['APP_AUTHOR'] ?? '') ?>">

    <link rel="shortcut icon" href="<?= htmlspecialchars($_ENV['APP_ICON'] ?? '') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">

    <!-- Favicons -->
    <link href="img/favicon.png" rel="icon">
    <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="vendor/aos/aos.css" rel="stylesheet">
    <link href="vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="vendor/drift-zoom/drift-basic.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="css/main.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <title><?= isset($title) && !empty($title) ? $this->e($title) : ($_ENV['APP_NAME'] ?? '') ?></title>
</head>

<body>

    <main>
        <?= $this->section('mainContent') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vendor JS Files -->
    <script src="<?= assets("vendor/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <script src="<?= assets("vendor/php-email-form/validate.js") ?>"></script>
    <script src="<?= assets("vendor/aos/aos.js") ?>"></script>
    <script src="<?= assets("endor/purecounter/purecounter_vanilla.js") ?>"></script>
    <script src="<?= assets("vendor/glightbox/js/glightbox.min.js") ?>"></script>
    <script src="<?= assets("vendor/swiper/swiper-bundle.min.js") ?>"></script>
    <script src="<?= assets("vendor/drift-zoom/Drift.min.js") ?>"></script>

    <!-- Main JS File -->
    <script src="<?= assets("js/main.js") ?>"></script>
    <script src="<?= assets("js/functions.js") ?>"></script>
    <script src="<?= assets("js/booking.js") ?>"></script>
    <script src="<?= assets("js/Toasts.js") ?>"></script>

</body>

</html>