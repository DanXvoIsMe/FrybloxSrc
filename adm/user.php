<?php
require __DIR__ . '/../core/mainconfig.php';

if (!$loggedin || ($_USER['role'] ?? 'normal') !== 'admin') {
    header("Location: /home");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$user_query = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $id);
$user_query->execute();
$userData = $user_query->get_result()->fetch_assoc();

if (!$userData) {
    header("Location: /adm/index.php");
    exit;
}

$error = "";
$isSuperAdmin = ($_USER['username'] === 'Kha25132');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status']) || isset($_POST['update_role'])) {
        if (!$isSuperAdmin) {
            $error = "Only the Super Admin (kha2513) can modify account status or roles.";
        } else {
            if (isset($_POST['update_status'])) {
                $newStatus = $_POST['status'];
                $stmt = $mysqli->prepare("UPDATE users SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $newStatus, $id);
                $stmt->execute();
            } elseif (isset($_POST['update_role'])) {
                $newRole = $_POST['role'];
                $stmt = $mysqli->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->bind_param("si", $newRole, $id);
                $stmt->execute();
            }
            if (!$error) {
                header("Location: /adm/user?id=$id&success=1");
                exit;
            }
        }
    } elseif (isset($_POST['reset_password'])) {
        $newPass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $newPass, $id);
        $stmt->execute();
        header("Location: /adm/user?id=$id&pw_success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Manage <?php echo htmlspecialchars($userData['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container py-5">
    <?php if ($error): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <a href="/adm/index.php" class="btn btn-link text-decoration-none text-muted p-0 mb-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
            <div class="d-flex align-items-center">
                <h1 class="h2 fw-bold m-0"><?php echo htmlspecialchars($userData['username']); ?></h1>
                <span class="ms-3 badge rounded-pill <?php echo ($userData['status'] === 'banned') ? 'bg-danger' : 'bg-success'; ?> text-uppercase px-3">
                    <?php echo $userData['status']; ?>
                </span>
            </div>
            <p class="text-muted small mb-0">User ID: #<?php echo $userData['id']; ?> &bull; Role: <span class="text-info"><?php echo $userData['role']; ?></span></p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-user-shield me-2"></i> Access Control</h5>
                    
                    <form method="POST" class="mb-4">
                        <label class="form-label small fw-bold text-muted">ACCOUNT STATUS</label>
                        <div class="input-group">
                            <select name="status" class="form-select border-0 shadow-none" <?php echo !$isSuperAdmin ? 'disabled' : ''; ?>>
                                <option value="active" <?php echo ($userData['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="banned" <?php echo ($userData['status'] === 'banned') ? 'selected' : ''; ?>>Banned</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary" <?php echo !$isSuperAdmin ? 'disabled' : ''; ?>>Apply</button>
                        </div>
                    </form>

                    <form method="POST">
                        <label class="form-label small fw-bold text-muted">PERMISSIONS</label>
                        <div class="input-group">
                            <select name="role" class="form-select border-0 shadow-none" <?php echo !$isSuperAdmin ? 'disabled' : ''; ?>>
                                <option value="normal" <?php echo ($userData['role'] === 'normal') ? 'selected' : ''; ?>>Normal User</option>
                                <option value="admin" <?php echo ($userData['role'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                            <button type="submit" name="update_role" class="btn btn-danger" <?php echo !$isSuperAdmin ? 'disabled' : ''; ?>>Apply</button>
                        </div>
                        <?php if(!$isSuperAdmin): ?>
                            <div class="form-text text-danger mt-2">Only kha2513 can modify these.</div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-warning"><i class="fas fa-key me-2"></i> Security</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">FORCE NEW PASSWORD</label>
                            <input type="password" name="new_password" class="form-control border-0 shadow-none bg-dark" placeholder="••••••••" required>
                        </div>
                        <button type="submit" name="reset_password" class="btn btn-warning w-100 fw-bold">Update Password</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-info"><i class="fas fa-info-circle me-2"></i> User Meta</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent border-secondary d-flex justify-content-between px-0">
                            <span class="text-muted">Username:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($userData['username']); ?></span>
                        </li>
                        <li class="list-group-item bg-transparent border-secondary d-flex justify-content-between px-0">
                            <span class="text-muted">Registered:</span>
                            <span class="small"><?php echo date("M j, Y", strtotime($userData['created_at'])); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>