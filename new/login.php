<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: /home/index");
    exit;
}

require __DIR__ . "/../core/mainconfig.php";
$conn = $mysqli; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        
        $user_id = $user['id'];
        $sessionToken = bin2hex(random_bytes(32));
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];

        $s_stmt = $conn->prepare("INSERT INTO sessions (user_id, session_token, ip, user_agent) VALUES (?, ?, ?, ?)");
        $s_stmt->bind_param("isss", $user_id, $sessionToken, $ip, $ua);
        
        if ($s_stmt->execute()) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['session_token'] = $sessionToken;
            
            header("Location: /home/index");
            exit;
        } else {
            $error = "Session creation failed.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <?php echo $global_font; ?>
    <meta charset="UTF-8">
    <title>Login - <?php echo $sitename; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/index.css">
    <link rel="icon" type="image/png" href="/img/short.png">
</head>
<body>
    <?php require __DIR__ . '/../core/snow.php'; ?>
    <div class="d-flex justify-content-center align-items-center vh-100 position-relative" style="z-index: 10000;">
        <div class="card text-center p-4 shadow-lg" style="max-width: 380px; width: 100%;">
            <h3 class="text-center mb-4">Login</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3">
                Don’t have an account? <a href="register">Register</a>
            </p>
        </div>
    </div>
    <div class="city"></div>
</body>
</html>