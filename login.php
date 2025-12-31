<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: /home/index");
    exit;
}

require __DIR__ . "/core/mainconfig.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" type="image/png" href="img/short.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/core/nav.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">

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
                        Don’t have an account? <a href="register">Sign Up</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
