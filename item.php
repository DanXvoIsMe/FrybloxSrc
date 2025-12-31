<?php
require __DIR__ . '/core/mainconfig.php';

if (!isset($_GET['id'])) {
    echo "No place ID provided.";
    exit;
}

$id = intval($_GET['id']);

$place = $mysqli->query("
    SELECT games.*, users.username AS creator_name
    FROM games
    LEFT JOIN users ON users.id = games.creator_id
    WHERE games.id = $id
")->fetch_assoc();

if (!$place) {
    echo "Game not found.";
    exit;
}
?>
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
<div class="container my-4">

    <div class="row">

        <div class="col-md-8">

          <div class="card mb-3">
                <img src="<?php echo $place['thumbnail']; ?>" 
                         class="img-fluid w-100" 
                    style="max-height: 500px; object-fit: cover; border-radius: 5px;">
            </div>
            <h2 class="fw-bold"><?php echo htmlspecialchars($place['title']); ?></h2>
            <p class="text-muted">
                By <strong><?php echo htmlspecialchars($place['creator_name']); ?></strong>
            </p>

            <a href="#" class="btn btn-success btn-lg mb-3"
            data-bs-toggle="modal"
            data-bs-target="#playModal">
                <i class="fas fa-play me-2"></i> Play
            </a>
        
                    <div class="modal fade" id="playModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?php echo $sitename; ?> Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>The client doesn't work right now, stay tuned!</p>
            </div>
            </div>
        </div>
        </div>
            <div class="card">
                <div class="card-header fw-bold">
                    Description
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($place['description'])); ?>
                </div>
            </div>

        </div>

        <div class="col-md-4">

            <div class="card">
                <div class="card-header fw-bold">
                    Game Info
                </div>
                <div class="list-group list-group-flush">

                    <div class="list-group-item">
                        <strong>ID:</strong> <?php echo $place['id']; ?>
                    </div>

                    <div class="list-group-item">
                        <strong>Creator:</strong> <?php echo htmlspecialchars($place['creator_name']); ?>
                    </div>

                    <div class="list-group-item">
                        <strong>Created:</strong> 
                        <?php echo date("M j, Y", strtotime($place['created_at'])); ?>
                    </div>

                    <div class="list-group-item">
                        <strong>Visits:</strong> 
                        <?php echo number_format($place['visits']); ?>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>