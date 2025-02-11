<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$tables = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Status</title>
    <link rel="stylesheet" href="/public/style.css">
</head>
<body>
    <div class="container">
        <h1>Table Status</h1>
        <div class="table-layout">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="table <?= isset($tables[$i]) ? 'busy' : 'free' ?>">
                    Table <?= $i ?>: <?= isset($tables[$i]) ? 'Busy' : 'Free' ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>