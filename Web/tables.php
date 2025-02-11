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
    <title>Select Table</title>
    <link rel="stylesheet" href="/public/style.css">
</head>
<body>
    <div class="container">
        <h1>Select a Table</h1>
        <div class="table-layout">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="table">
                    <a href="menu.php?table=<?= $i ?>">Table <?= $i ?></a>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>