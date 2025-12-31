<?php
define('MAINTENANCE_PAGE', true);
require __DIR__ . '/../core/mainconfig.php';

if (($maintenance_mode ?? false) === false) {
    header("Location: /home");
    exit;
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Maintenance - <?php echo $sitename; ?></title>

    <link href="/assets/css/index.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/short.png?v=2">
    <link rel="stylesheet" href="/assets/css/home.css">
</head>

<body>
<?php require __DIR__ . '/../core/snow.php'; ?>
<div class="d-flex justify-content-center align-items-center vh-100 position-relative" style="z-index: 10000;">
    <div class="card text-center p-4 shadow-lg border-0 bg-body-tertiary" style="max-width: 380px; width: 100%;">
        <img src="/img/maintenance.png" class="mx-auto mb-3" alt="Maintenance" style="width: 100px; object-fit: contain;">
        <h4 class="fw-bold text-white">Maintenance</h4>
        <p class="text-muted mb-4">
            <?php echo htmlspecialchars($maintenance_text); ?>
        </p>
    </div>
</div>

<div class="city"></div>
</body>
</html>