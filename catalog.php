<?php
require __DIR__ . '/core/mainconfig.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: /login.php"); 
    exit; 
}

$q = $mysqli->real_escape_string($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'all';

$where = "WHERE 1";

if ($type !== 'all') {
    $where .= " AND type = '" . $mysqli->real_escape_string($type) . "'";
}

$items = $mysqli->query("
    SELECT catalog.*, users.username AS creator_name
    FROM catalog
    LEFT JOIN users ON users.id = catalog.creator_id
    $where
    AND catalog.name LIKE '%$q%'
    ORDER BY catalog.id DESC
");
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Catalog - <?php echo $sitename; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/catalog.css">
    <link rel="icon" type="image/png" href="/../img/short.png?v=2">
</head>

<body>

<?php require __DIR__ . '/core/nav.php'; ?>

<div class="container mt-4">

    <div class="row">

        <div class="col-lg-3 mb-4">
            <h1 class="mb-3">Categories</h1>

            <div class="filter-list">
                <a href="?type=all" class="filter-item">All Items</a>
                <a href="?type=Hat" class="filter-item">Hats</a>
                <a href="?type=Face" class="filter-item">Faces</a>
                <a href="?type=Shirt" class="filter-item">Shirts</a>
                <a href="?type=Pants" class="filter-item">Pants</a>
                <a href="?type=Gear" class="filter-item">Gear</a>
                <a href="?type=Accessory" class="filter-item">Accessories</a>
            </div>
        </div>

        <div class="col-lg-9">

            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="q" class="form-control"
                           placeholder="Search catalog..."
                           value="<?php echo htmlspecialchars($q); ?>">

                    <button class="btn btn-primary">Search</button>
                </div>
            </form>

            <div class="row g-4">

                <?php while ($i = $items->fetch_assoc()): ?>

                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <div class="catalog-tile">
                    <img src="<?php echo $i['thumbnail']; ?>" class="catalog-thumb" alt="">

                    <div class="catalog-info">
                        <div class="catalog-name"><?php echo htmlspecialchars($i['name']); ?></div>

                        <div class="catalog-meta">
                        Updated: <?php echo $i['created_at']; ?><br>
                        Creator: <?php echo htmlspecialchars($i['creator_name'] ?? 'Unknown'); ?>
                    </div>

                    <div class="catalog-extra">
                        Sold: <?php echo $i['sold']; ?> copies<br>
                        Favorited: <?php echo $i['favorited']; ?> times
                    </div>
                    </div>
                </div>
            </div>

                <?php endwhile; ?>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
