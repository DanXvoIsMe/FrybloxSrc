<?php
require __DIR__ . '/../core/mainconfig.php';

if (!$loggedin || ($_USER['role'] ?? 'normal') !== 'admin') {
    header("Location: /home");
    exit;
}

$isSuperAdmin = ($_USER['username'] === 'Kha25132');
$statusMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_site'])) {
    if (!$isSuperAdmin) {
        $statusMsg = '<div class="alert alert-danger">Only kha2513 can modify global site settings.</div>';
    } else {
        $regState = isset($_POST['reg_status']) ? 1 : 0;
        $maintState = isset($_POST['maint_status']) ? 1 : 0;
        
        $stmt = $mysqli->prepare("UPDATE site_settings SET registration_open = ?, maintenance_mode = ?");
        $stmt->bind_param("ii", $regState, $maintState);
        
        if ($stmt->execute()) {
            header("Location: settings.php?success=1");
            exit;
        }
    }
}

if (isset($_GET['success'])) {
    $statusMsg = '<div class="alert alert-success">Site settings updated successfully.</div>';
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Site Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-4">
                <a href="/adm/index.php" class="btn btn-link text-decoration-none text-muted p-0">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
                <h1 class="h3 fw-bold mt-2">Global Site Settings</h1>
            </div>

            <?php echo $statusMsg; ?>

            <div class="card border-0 shadow-sm bg-body-tertiary">
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="d-flex align-items-center justify-content-between p-3 bg-dark rounded mb-3">
                            <div>
                                <h6 class="mb-0">Registration Status</h6>
                                <small class="text-muted">Allow new users to join.</small>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" name="reg_status" <?php echo $registration_is_open ? 'checked' : ''; ?> <?php echo !$isSuperAdmin ? 'disabled' : ''; ?>>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-3 bg-dark rounded mb-4">
                            <div>
                                <h6 class="mb-0">Maintenance Mode</h6>
                                <small class="text-muted">Disable site access for non-admins.</small>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" name="maint_status" <?php echo $maintenance_mode ? 'checked' : ''; ?> <?php echo !$isSuperAdmin ? 'disabled' : ''; ?>>
                            </div>
                        </div>

                        <?php if ($isSuperAdmin): ?>
                            <button type="submit" name="update_site" class="btn btn-primary w-100 fw-bold py-2">
                                Save Global Settings
                            </button>
                        <?php else: ?>
                            <div class="alert alert-warning py-2 small mb-0 text-center">
                                <i class="fas fa-lock me-2"></i> Only <strong>kha2513</strong> can change these settings.
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-6">
                    <div class="card border-0 bg-body-tertiary p-3 text-center">
                        <small class="text-muted text-uppercase fw-bold">Signups</small>
                        <div class="h5 mt-1 mb-0">
                            <?php echo $registration_is_open ? '<span class="text-success">Open</span>' : '<span class="text-danger">Closed</span>'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 bg-body-tertiary p-3 text-center">
                        <small class="text-muted text-uppercase fw-bold">Maintenance</small>
                        <div class="h5 mt-1 mb-0">
                            <?php echo $maintenance_mode ? '<span class="text-danger">Active</span>' : '<span class="text-secondary">Inactive</span>'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>