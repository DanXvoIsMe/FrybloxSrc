<?php
require __DIR__ . '/../core/mainconfig.php';

if (!$loggedin || ($_USER['role'] ?? 'normal') !== 'admin') {
    header("Location: /home");
    exit;
}

$search = $_GET['search'] ?? '';
$users = $mysqli->prepare("SELECT id, username, role, created_at FROM users WHERE username LIKE ? ORDER BY id DESC LIMIT 50");
$searchTerm = "%$search%";
$users->bind_param("s", $searchTerm);
$users->execute();
$result = $users->get_result();

$stats = [
    'users' => $mysqli->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'],
    'admins' => $mysqli->query("SELECT COUNT(*) AS c FROM users WHERE role='admin'")->fetch_assoc()['c'],
    'feed' => $mysqli->query("SELECT COUNT(*) AS c FROM feed")->fetch_assoc()['c'],
    'keys' => $mysqli->query("SELECT COUNT(*) AS c FROM invite_keys")->fetch_assoc()['c'] ?? 0
];
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Dashboard - <?php echo $sitename; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/short.png?v=2">
</head>
<body class="bg-dark text-light">

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block bg-body-tertiary sidebar collapse vh-100 border-end p-3">
            <div class="position-sticky">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Management</span>
                </h6>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="/adm/"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/adm/banner"><i class="fas fa-megaphone me-2"></i>Banners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/adm/feed"><i class="fas fa-comments-alt me-2"></i> Feed Posts</a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>System</span>
                </h6>
                <ul class="nav flex-column mb-auto">
                    <li class="nav-item"><a class="nav-link text-light" href="/adm/settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="/adm/keys"><i class="fas fa-key me-2"></i> Invite Keys</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="/adm/maintenance"><i class="fas fa-wrench me-2"></i> Maintenance</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="/adm/logs"><i class="fas fa-scroll me-2"></i> Logs</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <h1 class="h2">Welcome, <?php echo htmlspecialchars($_USER['username']); ?></h1>
                </div>
            </div>

                        <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden position-relative" style="min-height: 120px;">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <h3 class="mb-0 fw-bold"><?php echo number_format($stats['users']); ?></h3>
                            <p class="mb-0 opacity-75 small text-uppercase fw-bold">Total Users</p>
                        </div>
                        <i class="fas fa-user-friends position-absolute end-0 bottom-0 mb-n3 me-n3 opacity-25" style="font-size: 6rem; z-index: 1;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white border-0 shadow-sm overflow-hidden position-relative" style="min-height: 120px;">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <h3 class="mb-0 fw-bold"><?php echo number_format($stats['feed']); ?></h3>
                            <p class="mb-0 opacity-75 small text-uppercase fw-bold">Total Posts</p>
                        </div>
                        <i class="fas fa-comment-alt position-absolute end-0 bottom-0 mb-n3 me-n3 opacity-25" style="font-size: 6rem; z-index: 1;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark border-0 shadow-sm overflow-hidden position-relative" style="min-height: 120px;">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <h3 class="mb-0 fw-bold"><?php echo number_format($stats['keys']); ?></h3>
                            <p class="mb-0 opacity-75 small text-uppercase fw-bold">Invite Keys</p>
                        </div>
                        <i class="fas fa-ticket-alt position-absolute end-0 bottom-0 mb-n3 me-n3 opacity-25" style="font-size: 6rem; z-index: 1;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white border-0 shadow-sm overflow-hidden position-relative" style="min-height: 120px;">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <h3 class="mb-0 fw-bold"><?php echo number_format($stats['admins']); ?></h3>
                            <p class="mb-0 opacity-75 small text-uppercase fw-bold">Administrators</p>
                        </div>
                        <i class="fas fa-user-shield position-absolute end-0 bottom-0 mb-n3 me-n3 opacity-25" style="font-size: 6rem; z-index: 1;"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4 bg-body-tertiary">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Registrations</h5>
                            <form method="GET" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search user..." value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-sm btn-primary">Go</button>
                            </form>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Joined Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($u = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $u['id']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($u['username']); ?></td>
                                        <td>
                                            <?php echo ($u['role'] === 'admin') 
                                                ? '<span class="badge rounded-pill bg-danger-subtle text-danger border border-danger">Admin</span>' 
                                                : '<span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary">Member</span>'; 
                                            ?>
                                        </td>
                                        <td class="text-muted small"><?php echo $u['created_at']; ?></td>
                                        <td class="text-end">
                                            <a href="/adm/user?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-light"> <i class="fas fa-user-edit me-1"></i> Edit</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm bg-body-tertiary border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-3 text-muted fw-bold">System Status</h5>
                            <div class="list-group list-group-flush bg-transparent">
                                <div class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center px-0">
                                    <span>Database Connection</span>
                                    <span class="text-success small"><i class="bi bi-circle-fill me-1"></i> Online</span>
                                </div>
                                <div class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center px-0">
                                    <span>PHP Version</span>
                                    <span class="small"><?php echo phpversion(); ?></span>
                                </div>
                                <div class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center px-0">
                                    <span>Maintenance Mode</span>
                                    <span class="badge bg-secondary">Disabled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>