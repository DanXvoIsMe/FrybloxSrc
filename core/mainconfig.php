<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("America/New_York");

$sitename = "FRYBLX";
$logo = "/../img/fry.png?v=2";

$global_font = "
<link href='https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap' rel='stylesheet'>
<link href='/assets/css/main.css' rel='stylesheet'>
<style>
    body, button, input, textarea, select {
        font-family: 'Source Sans Pro', sans-serif !important;
    }
</style>
";

$mysqli = new mysqli("localhost", "root", "", "fries");

if ($mysqli->connect_error) {
    die("Database connection failed.");
}

$conn = $mysqli;

$settings_query = $mysqli->query("SELECT * FROM site_settings LIMIT 1");
$site_settings = $settings_query->fetch_assoc();
$registration_is_open = (bool)($site_settings['registration_open'] ?? true);
$maintenance_mode = (bool)($site_settings['maintenance_mode'] ?? false);
$maintenance_text = $site_settings['maintenance_text'] ?? ($sitename . " is currently under maintenance. Please check back later.");

$loggedin = false;
$_USER = null;

if (!empty($_SESSION["user_id"])) {
    $uid = (int)$_SESSION["user_id"];
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        $loggedin = true;
        $_USER = $user;

        if (isset($_SESSION['session_token'])) {
            $s_stmt = $conn->prepare("SELECT id FROM sessions WHERE session_token = ? AND user_id = ?");
            $s_stmt->bind_param("si", $_SESSION['session_token'], $uid);
            $s_stmt->execute();
            
            if ($s_stmt->get_result()->num_rows === 0) {
                session_destroy();
                header("Location: /Login");
                exit;
            }
        }
    } else {
        session_destroy();
    }
}
?>