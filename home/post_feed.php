<?php
require __DIR__ . '/../core/mainconfig.php';

if (!$loggedin) {
    header("Location: /login");
    exit;
}

$content = trim($_POST['content'] ?? '');

if ($content !== '') {
    $stmt = $mysqli->prepare("INSERT INTO feed (user_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $_USER['id'], $content);
    $stmt->execute();
}

header("Location: /home/");
exit;
