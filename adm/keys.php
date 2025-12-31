<?php
require __DIR__ . '/../core/mainconfig.php';

if (!$loggedin || ($_USER['role'] ?? 'normal') !== 'admin') {
    header("Location: /home");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_invite'])) {
    $code = $_POST['invite_key'];
    
    $stmt = $mysqli->prepare("INSERT INTO invite_keys (invite_key) VALUES (?)");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    header("Location: /adm/keys");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_invite'])) {
    $id = intval($_POST['id']);
    $stmt = $mysqli->prepare("DELETE FROM invite_keys WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: /adm/keys");
    exit;
}

$all_invites = $mysqli->query("
    SELECT invite_keys.*, users.username AS redeemer 
    FROM invite_keys 
    LEFT JOIN users ON invite_keys.used_by = users.id 
    ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Manage Invite Keys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/all.pro.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-body-tertiary border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Generate Invite</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Invite Key</label>
                            <div class="input-group">
                                <input type="text" name="invite_key" id="inviteInput" class="form-control bg-dark border-0 text-white fw-bold" value="FRYBLX-<?php echo strtoupper(bin2hex(random_bytes(8))); ?>" required>
                                <button class="btn btn-secondary" type="button" onclick="randomizeKey()">
                                    <i class="fas fa-random"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" name="create_invite" class="btn btn-primary w-100 fw-bold">Create Invite</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h5 class="fw-bold mb-3">Existing Invite Keys</h5>
            <div class="table-responsive">
                <table class="table table-dark align-middle">
                    <thead>
                        <tr class="text-white">
                            <th>Key</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($k = $all_invites->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-primary small"><?php echo htmlspecialchars($k['invite_key']); ?></td>
                            <td>
                                <?php if ($k['used_by']): ?>
                                    <span class="badge bg-secondary">Used by <?php echo htmlspecialchars($k['redeemer']); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success">Available</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?php echo $k['created_at']; ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Delete this invite?');">
                                    <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                                    <button type="submit" name="delete_invite" class="btn btn-sm btn-danger">
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

<script>
function randomizeKey() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = 'FRYBLX-';
    for (let i = 0; i < 24; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('inviteInput').value = result;
}
</script>

</body>
</html>