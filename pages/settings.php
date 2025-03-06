<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../templates/header.php';
?>


<!DOCTYPE html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/../../public/js/main.js>"></script>
</head>

<body>
    <h1>Settings</h1>
    <p>Welcome to the settings page. Here you can manage your account and preferences.</p>

    <a href="additem.php" class="button">Edit Menu</a>
    <a href="options.php" class="button">Options</a>
    <a href="logout.php" class="button">Logout</a>
    
    <?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
