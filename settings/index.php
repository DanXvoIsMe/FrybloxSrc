<?php
require __DIR__ . '/../core/mainconfig.php';
$conn = $mysqli;

if (!$loggedin || !$_USER) {
    header('Location: /Login');
    exit;
}

$blurbSuccess = $blurbError = $passwordSuccess = $passwordError = '';
$usernameSuccess = $usernameError = $twofaSuccess = $twofaError = $themeSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['form_type'] === 'blurb') {
        $newBlurb = trim($_POST['blurb'] ?? '');
        if (mb_strlen($newBlurb) <= 200) {
            $stmt = $conn->prepare('UPDATE users SET blurb = ? WHERE id = ?');
            $stmt->bind_param('si', $newBlurb, $_USER['id']);
            if ($stmt->execute()) {
                $_USER['blurb'] = $newBlurb;
                $blurbSuccess = 'Blurb updated.';
            }
        }
    }

    if ($_POST['form_type'] === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword && $newPassword === $confirmPassword && strlen($newPassword) >= 6) {
            $stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
            $stmt->bind_param('i', $_USER['id']);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            if (password_verify($currentPassword, $row['password'])) {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                $update->bind_param('si', $newHash, $_USER['id']);
                $update->execute();
                $passwordSuccess = 'Password changed.';
            }
        }
    }

    if ($_POST['form_type'] === 'theme_update') {
        $allowedThemes = ['dark', 'midnight'];
        $newTheme = $_POST['site_theme'] ?? 'dark';
        if (in_array($newTheme, $allowedThemes)) {
            $stmt = $conn->prepare('UPDATE users SET theme = ? WHERE id = ?');
            $stmt->bind_param('si', $newTheme, $_USER['id']);
            if ($stmt->execute()) {
                $_USER['theme'] = $newTheme;
                $themeSuccess = 'Appearance updated.';
            }
        }
    }

    if ($_POST['form_type'] === 'username') {
        $newUsername = trim($_POST['new_username'] ?? '');
        if ($newUsername && preg_match('/^[A-Za-z0-9_]{3,20}$/', $newUsername) && $_USER['points'] >= 1000) {
            $check = $conn->prepare('SELECT id FROM users WHERE username = ?');
            $check->bind_param('s', $newUsername);
            $check->execute();
            if ($check->get_result()->num_rows === 0) {
                $update = $conn->prepare('UPDATE users SET username = ?, points = points - 1000 WHERE id = ?');
                $update->bind_param('si', $newUsername, $_USER['id']);
                $update->execute();
                $_USER['username'] = $newUsername;
                $_USER['points'] -= 1000;
            }
        }
    }

    if ($_POST['form_type'] === 'enable_2fa' && empty($_USER['twofa_enabled'])) {
        $random = random_bytes(10);
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        for ($i = 0; $i < strlen($random); $i++) { $bits .= str_pad(decbin(ord($random[$i])), 8, '0', STR_PAD_LEFT); }
        $secret = '';
        for ($i = 0; $i + 5 <= strlen($bits); $i += 5) { $secret .= $alphabet[bindec(substr($bits, $i, 5))]; }
        $secret = substr($secret, 0, 16);
        $stmt = $conn->prepare('UPDATE users SET twofa_secret = ?, twofa_enabled = 1 WHERE id = ?');
        $stmt->bind_param('si', $secret, $_USER['id']);
        $stmt->execute();
        $_USER['twofa_secret'] = $secret;
        $_USER['twofa_enabled'] = 1;
    }

    if ($_POST['form_type'] === 'disable_2fa') {
        $stmt = $conn->prepare('UPDATE users SET twofa_secret = NULL, twofa_enabled = 0 WHERE id = ?');
        $stmt->bind_param('i', $_USER['id']);
        $stmt->execute();
        $_USER['twofa_secret'] = null;
        $_USER['twofa_enabled'] = 0;
    }

    if ($_POST['form_type'] === 'terminate_session') {
        $sid = (int)$_POST['session_id'];
        $stmt = $conn->prepare('DELETE FROM sessions WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $sid, $_USER['id']);
        $stmt->execute();
    }

    if ($_POST['form_type'] === 'terminate_others') {
        $stmt = $conn->prepare('DELETE FROM sessions WHERE user_id = ? AND session_token != ?');
        $stmt->bind_param('is', $_USER['id'], $_SESSION['session_token']);
        $stmt->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sessions = $conn->prepare('SELECT * FROM sessions WHERE user_id = ? ORDER BY last_active DESC');
$sessions->bind_param('i', $_USER['id']);
$sessions->execute();
$sessionsResult = $sessions->get_result();

$twofaQr = null;
if (!empty($_USER['twofa_enabled']) && !empty($_USER['twofa_secret'])) {
    $otpauth = "otpauth://totp/".rawurlencode($sitename).":".rawurlencode($_USER['username'])."?secret=".$_USER['twofa_secret']."&issuer=".rawurlencode($sitename);
    $twofaQr = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth);
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <?php echo $global_font; ?>
    <meta charset="utf-8">
    <title>Settings - <?php echo htmlspecialchars($_USER['username']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body[data-theme="midnight"] { --bs-body-bg: #0f0c29; --bs-tertiary-bg: #1a1635; }
        body[data-theme="midnight"] .card { background: #1a1635; border-color: #2c003e; }
        body[data-theme="midnight"] .navbar { background: #2c003e !important; }
        body[data-theme="midnight"] .list-group-item { background: #1a1635; color: white; border-color: #2c003e; }
        body[data-theme="midnight"] .list-group-item.active { background: #8e44ad; border-color: #8e44ad; }
    </style>
</head>
<body data-bs-theme="dark" data-theme="<?php echo $_USER['theme'] ?? 'dark'; ?>">

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <button class="list-group-item list-group-item-action active" data-bs-toggle="list" data-bs-target="#account"><i class="bi bi-person-circle me-2"></i>Account</button>
                <button class="list-group-item list-group-item-action" data-bs-toggle="list" data-bs-target="#profile"><i class="bi bi-pencil-square me-2"></i>Profile</button>
                <button class="list-group-item list-group-item-action" data-bs-toggle="list" data-bs-target="#theme"><i class="bi bi-palette2 me-2"></i>Appearance</button>
                <button class="list-group-item list-group-item-action" data-bs-toggle="list" data-bs-target="#security"><i class="bi bi-shield-lock-fill me-2"></i>Security</button>
                <button class="list-group-item list-group-item-action" data-bs-toggle="list" data-bs-target="#sessions"><i class="bi bi-display me-2"></i>Sessions</button>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content card p-4 shadow-sm">
                <div class="tab-pane fade show active" id="account">
                    <h4 class="mb-4">Account Settings</h4>
                    <form method="post">
                        <input type="hidden" name="form_type" value="password">
                        <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Confirm New Password</label><input type="password" name="confirm_password" class="form-control"></div>
                        <button class="btn btn-primary">Update Password</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="profile">
                    <h4 class="mb-4">Public Profile</h4>
                    <form method="post" class="mb-4">
                        <input type="hidden" name="form_type" value="blurb">
                        <label class="form-label">Blurb (Max 200)</label>
                        <textarea name="blurb" class="form-control" rows="4"><?php echo htmlspecialchars($_USER['blurb']); ?></textarea>
                        <button class="btn btn-primary mt-2">Save Blurb</button>
                    </form>
                    <hr>
                    <form method="post">
                        <input type="hidden" name="form_type" value="username">
                        <label class="form-label">Change Username (1,000 pts)</label>
                        <div class="input-group">
                            <input type="text" name="new_username" class="form-control" placeholder="New username...">
                            <button class="btn btn-warning">Apply</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="theme">
                    <h4 class="mb-4">Appearance</h4>
                    <p class="text-muted small">Select how the site looks for you.</p>
                    <form method="post">
                        <input type="hidden" name="form_type" value="theme_update">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card p-3 border text-center">
                                    <h5>Default Dark</h5>
                                    <input type="radio" name="site_theme" value="dark" <?php echo ($_USER['theme'] ?? '') == 'dark' ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- <div class="card p-3 border text-center" style="background:#0f0c29; color:white;">
                                    <h5>Midnight</h5>
                                    <input type="radio" name="site_theme" value="midnight">
                                </div> -->
                            </div>
                        </div>
                        <button class="btn btn-primary mt-4">Save Theme Preference</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="security">
                    <h4 class="mb-4">Security</h4>
                    <p class="text-muted">Protect your account by requiring a code from an authenticator app when logging in.</p>
                    <?php if (empty($_USER['twofa_enabled'])): ?>
                        <form method="post"><input type="hidden" name="form_type" value="enable_2fa"><button class="btn btn-success">Enable 2FA</button></form>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="<?php echo $twofaQr; ?>" class="mb-3 border">
                            <p>Secret Key: <code><?php echo $_USER['twofa_secret']; ?></code></p>
                            <form method="post"><input type="hidden" name="form_type" value="disable_2fa"><button class="btn btn-danger">Disable 2FA</button></form>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="sessions">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Active Sessions</h4>
                        <form method="post">
                            <input type="hidden" name="form_type" value="terminate_others">
                            <button class="btn btn-danger btn-sm">Log out all other devices</button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Device/Browser</th>
                                    <th>IP Address</th>
                                    <th>Last Active</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($s = $sessionsResult->fetch_assoc()): 
                                    $ua = $s['user_agent'];
                                    $os = preg_match('/Windows/i', $ua) ? 'Windows' : (preg_match('/Mac/i', $ua) ? 'macOS' : 'Linux/Mobile');
                                    $browser = preg_match('/Chrome/i', $ua) ? 'Chrome' : (preg_match('/Firefox/i', $ua) ? 'Firefox' : 'Other');
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo $os; ?></div>
                                        <div class="small text-muted"><?php echo $browser; ?></div>
                                    </td>
                                    <td><code><?php echo htmlspecialchars($s['ip']); ?></code></td>
                                    <td class="small"><?php echo $s['last_active']; ?></td>
                                    <td class="text-end">
                                        <form method="post">
                                            <input type="hidden" name="form_type" value="terminate_session">
                                            <input type="hidden" name="session_id" value="<?php echo $s['id']; ?>">
                                            <button class="btn btn-sm btn-link text-danger">Terminate</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const activeTab = localStorage.getItem('activeSettingsTab');
    if (activeTab) {
        const trigger = document.querySelector(`[data-bs-target="${activeTab}"]`);
        if (trigger) { (new bootstrap.Tab(trigger)).show(); }
    }
    document.querySelectorAll('button[data-bs-toggle="list"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', e => localStorage.setItem('activeSettingsTab', e.target.getAttribute('data-bs-target')));
    });
});
</script>
</body>
</html>