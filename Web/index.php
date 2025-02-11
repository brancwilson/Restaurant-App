<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Tomato's Menu</title>
    <link rel="stylesheet" href="/public/style.css">
</head>
<body>
    <div class="container">
        <h1>Quick Tomato's Menu</h1>
        <a href="tables.php">Select Table</a>
    </div>
</body>
</html>