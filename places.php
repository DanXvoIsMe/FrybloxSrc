<?php
require __DIR__ . '/core/mainconfig.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: /login.php"); 
    exit; 
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Places - <?php echo $sitename; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/games.css">
    <link rel="icon" type="image/png" href="/../img/short.png?v=2">
</head>

<body>

<?php require __DIR__ . '/core/nav.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <h1 class="mb-3">Filters</h1>
            <h5 class="mb-3">Sorted By</h5>
            <div class="filter-list">
                <a href="?sort=relevance" class="filter-item">Relevance</a>
                <a href="?sort=popular" class="filter-item">Popular</a>
                <a href="?sort=favorited" class="filter-item">Most Favorited</a>
                <a href="?sort=featured" class="filter-item">Featured</a>
                <a href="?sort=myfavorites" class="filter-item">My Favorites</a>
                <a href="?sort=recent" class="filter-item">Recent Places</a>
            </div>
        </div>

        <div class="col-lg-9">
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="q" class="form-control"
                           placeholder="Search places..."
                           value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                    <button class="btn btn-primary">Search</button>
                </div>
            </form>

            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-4">
                <?php
                $q = $mysqli->real_escape_string($_GET['q'] ?? '');
                $sort = $_GET['sort'] ?? 'visits';

                $order = "ORDER BY games.visits DESC";
                if ($sort === 'new') $order = "ORDER BY games.id DESC";
                if ($sort === 'old') $order = "ORDER BY games.id ASC";
                if ($sort === 'az')  $order = "ORDER BY games.title ASC";

                $games = $mysqli->query("
                    SELECT games.*, users.username 
                    FROM games 
                    JOIN users ON users.id = games.creator_id
                    WHERE games.title LIKE '%$q%'
                    $order
                ");

                while ($g = $games->fetch_assoc()):
                ?>
                <div class="col d-flex align-items-stretch">
                    <a href="/place.php?id=<?php echo $g['id']; ?>" class="text-decoration-none w-100">
                        <div class="game-tile">
                            
                            <div class="position-relative thumb-wrapper">
                                <img src="<?php echo $g['thumbnail']; ?>" class="game-thumb" alt="">
                                
                                <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                    <?php echo $g['year']; ?>
                                </span>
                            </div>

                            <div class="mt-2 flex-grow-1">
                                <div class="game-title text-light fw-bold mb-0"><?php echo htmlspecialchars($g['title']); ?></div> 
                                <div class="game-visits text-muted small"><?php echo $g['visits']; ?> visits</div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>