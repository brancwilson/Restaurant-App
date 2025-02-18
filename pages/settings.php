<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>

<h1>Settings</h1>
<p>Welcome to the settings page. Here you can manage your account and preferences.</p>
<a href="logout.php" class="button">Logout</a>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>