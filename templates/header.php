<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'POS System') ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <header>
        <h1>POS System</h1>
        <nav>
            <a href="tables.php">Tables</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>