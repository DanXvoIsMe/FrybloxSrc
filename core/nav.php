<?php
require_once __DIR__ . '/mainconfig.php';

$current_page = $_SERVER['REQUEST_URI'];

if (($maintenance_mode ?? false) === true) {
    // Check if the user is NOT an admin AND is NOT already on the maintenance page
    // We use strpos to see if 'maintenance' is in the URL to prevent the loop
    if ((!$loggedin || ($_USER['role'] ?? 'normal') !== 'admin') && strpos($current_page, '/maintenance/') === false) {
        header("Location: /maintenance/home.php");
        exit;
    }
}
?>
<link rel="stylesheet" href="/assets/css/snow.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
<?php echo $global_font; ?>

<?php if (($maintenance_mode ?? false) && $loggedin && ($_USER['role'] ?? '') === 'admin'): ?>
    <div class="bg-danger text-white text-center py-1 small fw-bold">
        <i class="fas fa-exclamation-triangle me-2"></i> MAINTENANCE MODE ACTIVE — Public access is restricted.
    </div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/home">
            <img src="<?php echo $logo; ?>" alt="Logo" height="33" class="me-2">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/places"><i class="fas fa-gamepad-alt mr-1"></i> Places</a></li>
                <li class="nav-item"><a class="nav-link" href="/catalog"><i class="fas fa-shopping-bag mr-1"></i> Catalog</a></li>
                <li class="nav-item"><a class="nav-link" href="/leaderboards"><i class="fas fa-list-alt mr-1"></i> Leaderboards</a></li>
                <li class="nav-item"><a class="nav-link" href="/forum"><i class="fas fa-comments-alt mr-1"></i> Forums</a></li>
                <li class="nav-item"><a class="nav-link" href="/people"><i class="fas fa-users mr-1"></i> People</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">More</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/info/credits"><i class="fas fa-trademark me-2"></i> Credits</a></li>
                        <li><a class="dropdown-item" href="/currency/trade"><i class="far fa-sync-alt me-2"></i> Trade Currency</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($loggedin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            <?php echo htmlspecialchars($_USER['username']); ?>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/me"><i class="fas fa-id-card me-2"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="/settings"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2"><a class="btn btn-outline-light" href="/login"><i class="fas fa-sign-in-alt me-1"></i> Login</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light" href="/register"><i class="fas fa-user-plus me-1"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php if ($loggedin): ?>
<nav class="navbar navbar-expand navbar-custom-secondary py-1 small shadow-sm">
    <div class="container d-flex justify-content-between">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link py-1" href="/settings">Account</a></li>
            <li class="nav-item"><a class="nav-link py-1" href="/my/character">Character</a></li>
            <li class="nav-item"><a class="nav-link py-1" href="/users">Profile</a></li>
            <li class="nav-item"><a class="nav-link py-1" href="/my/friends">Friends</a></li>
            <li class="nav-item"><a class="nav-link py-1" href="/groups">Groups</a></li>

            <?php if (($_USER['role'] ?? 'normal') === 'admin'): ?>
                <li class="nav-item"><a class="nav-link py-1 fw-bold" href="/adm/index.php">Admin</a></li>
            <?php endif; ?>
        </ul>

        <ul class="navbar-nav me-4 d-flex align-items-center">
            <li class="nav-item d-flex align-items-center me-3">
                <img src="/img/fries.svg" alt="Fries" style="height:18px; width:18px;">
                <span class="ms-1 fw-bold"><?php echo number_format($_USER['fries'] ?? 0); ?></span>
            </li>

            <li class="nav-item d-flex align-items-center">
                <img src="/img/nuggets.svg" alt="Nuggets" style="height:18px; width:18px;">
                <span class="ms-1 fw-bold"><?php echo number_format($_USER['nuggets'] ?? 0); ?></span>
            </li>
        </ul>
    </div>
</nav>
<?php endif; ?>

<?php
$active_banners = $mysqli->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY id DESC");
if ($active_banners && $active_banners->num_rows > 0): ?>
    <div class="banners-container" id="twemoji-area">
        <?php while ($banner = $active_banners->fetch_assoc()): ?>
            <div class="alert alert-<?php echo $banner['color']; ?> border-0 rounded-0 m-0 py-2 text-center small fw-bold shadow-sm">
                <div class="container">
                    <i class="fas <?php echo htmlspecialchars($banner['icon']); ?> me-2"></i> 
                    <span class="banner-text"><?php echo htmlspecialchars($banner['text']); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@twemoji/api@14.1.0/dist/twemoji.min.js"></script>
    <script>
        window.onload = function() {
            twemoji.parse(document.getElementById('twemoji-area'), {
                folder: 'svg', ext: '.svg'
            });
        }
    </script>
<?php endif; ?>

<style>
.emoji { display: inline-block; height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.2em; }
</style>

<script>
function createSnowflake() {
    const snowflake = document.createElement("div");
    snowflake.classList.add("snowflake");
    snowflake.textContent = "❄";
    snowflake.style.fontSize = (Math.random() * 10 + 10) + "px";
    snowflake.style.left = Math.random() * window.innerWidth + "px";
    snowflake.style.animationDuration = (Math.random() * 3 + 3) + "s";
    document.body.appendChild(snowflake);
    setTimeout(() => snowflake.remove(), 6000);
}
setInterval(createSnowflake, 150);
</script>