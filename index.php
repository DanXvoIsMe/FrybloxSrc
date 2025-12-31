<?php
require __DIR__ . '/core/mainconfig.php';

if (isset($_SESSION['user_id'])) {
    header("Location: /home/index");
    exit;
}

?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title><?php echo $sitename; ?></title>

    <link href="/assets/css/index.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/short.png?v=2">
    <link rel="stylesheet" href="/assets/css/home.css">
</head>

<body>
<?php require __DIR__ . '/core/snow.php'; ?>
<div class="d-flex justify-content-center align-items-center vh-100 position-relative" style="z-index: 10000;">
    <div class="card text-center p-4 shadow-lg" style="max-width: 380px; width: 100%;">

        <img src="/img/fry.png" class="mx-auto mb-3" alt="Logo" style="width: 180px; object-fit: contain;">

        <p class="text-muted mb-4">
        <?php echo $sitename; ?>, a 2016 revival made by kha2513
        </p>

        <div class="d-flex justify-content-center gap-3">
            <a href="/new/login" class="btn btn-primary px-4">Login</a>
            <a href="/new/register" class="btn btn-outline-secondary px-4">Sign Up</a>
        </div>

    </div>
</div>

<div class="city"></div>
</body>
</html>
