<?php
require __DIR__ . '/../core/mainconfig.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: /login.php"); 
    exit; 
}

$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_results = $mysqli->query("SELECT COUNT(*) FROM feed")->fetch_row()[0];
$total_pages = ceil($total_results / $limit);

$sort = $_GET['sort'] ?? 'relevance';

switch ($sort) {
    case 'popular':
        $order = "ORDER BY games.visits DESC";
        break;
    case 'favorited':
        $order = "ORDER BY games.favorites DESC";
        break;
    case 'featured':
        $order = "ORDER BY games.id DESC";
        break;
    case 'myfavorites':
        $order = ($loggedin) ? "ORDER BY games.favorites DESC" : "ORDER BY games.visits DESC";
        break;
    case 'recent':
        $order = "ORDER BY games.id DESC";
        break;
    default:
        $order = "ORDER BY games.id DESC";
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Home - <?php echo $sitename; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="icon" type="image/png" href="/../img/short.png?v=2">
</head>
<body>

<?php require __DIR__ . '/../core/nav.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($_USER['username'] ?? 'Guest'); ?></h5>
                    <img src="/../img/fake.png" class="home-random-img mb-3" alt="">
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-3"><?php echo $sitename; ?> News</h4>
                    <div class="home-news-item d-flex justify-content-between">
                        <span class="home-news-title">Rat</span>
                        <span class="home-news-time">1 day ago</span>
                    </div>
                    <div class="home-news-desc">idk</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <?php if ($loggedin): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="/home/post_feed.php" class="d-flex gap-2">
                        <input type="text" name="content" class="form-control" placeholder="Share something...">
                        <button class="btn btn-primary">Post</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <h4 class="fw-bold mb-3">Feed</h4>

            <?php
            $stmt = $mysqli->prepare("
                SELECT feed.*, users.username 
                FROM feed 
                JOIN users ON users.id = feed.user_id 
                ORDER BY feed.id DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $feed = $stmt->get_result();

            while ($row = $feed->fetch_assoc()):
            ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body home-feed-item">
                    <div class="card-body text-left">
                        <h5 class="mb-1"><?php echo htmlspecialchars($row['username']); ?></h5>
                        <p class="mb-2"><?php echo htmlspecialchars($row['content']); ?></p>
                        <div class="text-muted small">
                            <span class="feed-time"><?php echo $row['created_at']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

            <?php if ($total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center mt-4">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>

        <div class="col-lg-3 mb-4">
            <h5 class="fw-bold mb-3">Recently Played</h5>
            <div class="d-flex align-items-center gap-3 home-game-item">
                <img src="/img/tempplace.png" class="home-thumb" alt="">
                <div class="d-flex flex-column">
                    <span class="home-game-title">Starter Place</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>