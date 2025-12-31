<?php
require __DIR__ . '/../core/mainconfig.php';

if (!$loggedin || ($_USER['role'] ?? 'normal') !== 'admin') {
    header("Location: /home");
    exit;
}

$isSuperAdmin = ($_USER['username'] === 'Kha25132');
$statusMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_maintenance'])) {
    if (!$isSuperAdmin) {
        $statusMsg = '<div class="alert alert-danger border-0 shadow-sm">Only kha2513 can toggle maintenance mode.</div>';
    } else {
        $newState = ($_POST['maintenance_state'] === '1') ? 1 : 0;

        $stmt = $mysqli->prepare("UPDATE site_settings SET maintenance_mode = ?");
        $stmt->bind_param("i", $newState);
        
        if ($stmt->execute()) {
            header("Location: /adm/maintenance.php?success=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Maintenance Mode - <?php echo $sitename; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/short.png?v=2">
</head>
<body class="bg-dark text-light">

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-4">
                <a href="/adm/index.php" class="btn btn-outline-secondary btn-sm mb-3 text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
                <h1 class="h2 fw-bold m-0 text-white">System Maintenance</h1>
                <p class="text-muted small">Control public access to the website.</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success border-0 bg-success text-white shadow-sm mb-4">
                    <i class="fas fa-check-circle me-2"></i> Database settings updated successfully!
                </div>
            <?php endif; ?>
            
            <?php echo $statusMsg; ?>

            <div class="card border-0 bg-body-tertiary shadow-lg overflow-hidden position-relative">
                <div class="card-body p-5 position-relative" style="z-index: 2;">
                    <div class="text-center mb-4">
                        <i class="fas fa-tools fs-1 <?php echo $maintenance_mode ? 'text-danger' : 'text-success'; ?> mb-3"></i>
                        <h4 class="fw-bold text-white">Status: <?php echo $maintenance_mode ? 'ENABLED' : 'DISABLED'; ?></h4>
                        <p class="text-muted">
                            <?php echo $maintenance_mode
                                ? 'The site is currently locked. Only administrators can access the frontend.' 
                                : 'The site is live. Everyone can access the content.'; ?>
                        </p>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="maintenance_state" value="<?php echo $maintenance_mode ? '0' : '1'; ?>">
                        <button type="submit" name="toggle_maintenance" class="btn <?php echo $maintenance_mode ? 'btn-success' : 'btn-danger'; ?> w-100 py-3 fw-bold text-uppercase shadow-sm">
                            <i class="fas <?php echo $maintenance_mode ? 'fa-play' : 'fa-stop'; ?> me-2"></i>
                            <?php echo $maintenance_mode ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode'; ?>
                        </button>
                    </form>
                </div>
                <i class="fas fa-tools position-absolute end-0 bottom-0 mb-n4 me-n4 opacity-25" style="font-size: 12rem; z-index: 1; color: #444;"></i>
            </div>

            <div class="mt-4 p-3 bg-dark rounded border border-secondary text-center">
                <p class="small m-0 text-muted">
                    Logged in as: <strong><?php echo htmlspecialchars($_USER['username']); ?></strong>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>