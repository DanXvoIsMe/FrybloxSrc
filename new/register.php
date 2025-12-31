<?php
session_start();
require __DIR__ . '/../core/mainconfig.php';

if (isset($_SESSION['user_id'])) {
    header("Location: /home/index");
    exit;
}

// Check if registration is open
if (!$registration_is_open) {
    $error = "Registration is currently closed. Please check back later.";
    $step = 0; // Prevent form from showing
} else {
    $step = isset($_POST['step']) ? intval($_POST['step']) : 1;
}

$success = $error = "";

// ... (Rest of your POST logic stays the same)
?>

<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Register - <?php echo $sitename; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/index.css" rel="stylesheet">
</head>
<body>

<div class="d-flex justify-content-center align-items-center vh-100 position-relative">
    <div class="card text-center p-4 shadow-lg" style="max-width: 380px; width: 100%;">
        
        <h3 class="text-center mb-4">Create Account</h3>

        <?php if (!$registration_is_open): ?>
            <div class="alert alert-warning border-0">
                <i class="fas fa-lock mb-2 d-block" style="font-size: 2rem;"></i>
                Registration is currently <strong>Closed</strong>.
            </div>
            <a href="/new/login" class="btn btn-outline-primary w-100">Login</a>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php endif; ?>
    </div>
</div>
</body>
</html>