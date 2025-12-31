<?php
require __DIR__ . '/../core/mainconfig.php';

if (!$loggedin || ($_USER['role'] ?? 'normal') !== 'admin') {
    header("Location: /home");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_banner'])) {
        $text = $_POST['banner_text'];
        $color = $_POST['banner_color'];
        $icon = $_POST['banner_icon']; // New field
        $stmt = $mysqli->prepare("INSERT INTO banners (text, color, icon) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $text, $color, $icon);
        $stmt->execute();
    } elseif (isset($_POST['delete_banner'])) {
        $id = intval($_POST['banner_id']);
        $stmt = $mysqli->prepare("DELETE FROM banners WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif (isset($_POST['toggle_banner'])) {
        $id = intval($_POST['banner_id']);
        $status = intval($_POST['status']);
        $stmt = $mysqli->prepare("UPDATE banners SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        $stmt->execute();
    }
    header("Location: /adm/banner");
    exit;
}

$banners = $mysqli->query("SELECT * FROM banners ORDER BY id DESC");
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Banner Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/all.pro.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/short.png?v=2">
</head>
<body class="bg-dark text-light">

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container py-5">
    <a href="/adm/index.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-body-tertiary border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-plus-circle me-2"></i> New Banner</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Message</label>
                            <textarea name="banner_text" class="form-control border-0 bg-dark mt-1" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2">Select Icon</label>
                        <div class="row g-2"> <?php
                            $available_icons = [
                                'fa-info-circle', 'fa-exclamation-triangle', 'fa-bullhorn', 
                                'fa-star', 'fa-check-circle', 'fa-cog', 
                                'fa-gift', 'fa-fire', 'fa-bell', 'fa-bolt',
                                'fa-french-fries', 'fa-hamburger'
                            ];
                            
                            foreach ($available_icons as $index => $icon):
                            ?>
                                <div class="col-3"> <input type="radio" 
                                        class="btn-check" 
                                        name="banner_icon" 
                                        id="icon-<?php echo $index; ?>" 
                                        value="<?php echo $icon; ?>" 
                                        autocomplete="off" 
                                        <?php echo ($index === 0) ? 'checked' : ''; ?>>
                                    
                                    <label class="btn btn-outline-primary w-100 py-2 border-0 bg-dark-subtle" for="icon-<?php echo $index; ?>">
                                        <i class="fas <?php echo $icon; ?> fs-5"></i>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Color Theme</label>
                            <select name="banner_color" class="form-select border-0 bg-dark mt-1">
                                <option value="primary">Blue (Primary)</option>
                                <option value="success">Green (Success)</option>
                                <option value="danger">Red (Danger)</option>
                                <option value="warning">Yellow (Warning)</option>
                                <option value="info">Cyan (Info)</option>
                                <option value="dark">Black (Dark)</option>
                                <option value="secondary">Gray (Secondary)</option>
                            </select>
                        </div>
                        <button type="submit" name="create_banner" class="btn btn-primary w-100 fw-bold">Create Banner</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h5 class="fw-bold mb-3"><i class="fas fa-flag me-2"></i> Active Banners</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead class="table-dark text-light">
                        <tr>
                            <th class="text-white">Preview</th>
                            <th class="text-white text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($b = $banners->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="alert alert-<?php echo $b['color']; ?> py-2 mb-0 small border-0">
                                    <i class="fas <?php echo htmlspecialchars($b['icon'] ?? 'fa-info-circle'); ?> me-2"></i> 
                                    <?php echo htmlspecialchars($b['text']); ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="banner_id" value="<?php echo $b['id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $b['is_active'] ? '0' : '1'; ?>">
                                    <button type="submit" name="toggle_banner" class="btn btn-sm <?php echo $b['is_active'] ? 'btn-success' : 'btn-outline-secondary'; ?>">
                                        <i class="fas <?php echo $b['is_active'] ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="banner_id" value="<?php echo $b['id']; ?>">
                                    <button type="submit" name="delete_banner" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>