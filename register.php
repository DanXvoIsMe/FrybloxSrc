<?php
require "core/mainconfig.php";

if (isset($_SESSION['user_id'])) {
    header("Location: /home/index");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hash);

    if ($stmt->execute()) {
        $success = "Account created successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Register for <?php echo $sitename; ?></title>
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

                    <h3 class="text-center mb-4">Create a <?php echo $sitename; ?> Account</h3>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

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

                        <button class="btn btn-primary w-100">Sign Up</button>
                    </form>

                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php">Login</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
